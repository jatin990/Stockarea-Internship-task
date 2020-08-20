<?php

namespace App\Http\Controllers;

use App\Book;
use App\Jobs\QueueMail;
use DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class bookController extends Controller
{
    function __construct()
    {
        return $this->middleware('auth:api');
    }

    public function add(Request $request){
       $data=$request->validateWithBag('errors',[
           'name'=>'required|string',
        ]);
        $user=Auth::user();
        $data['user_id']=$user->id;
        $book=DB::transaction(function () use ($data){
            return Book::create($data);
        });
        $details = ['email' => $user->email,'message'=>'A book has been added'];
        QueueMail::dispatch($details);
        return response()->json($book);
   }

   public function remove(Request $request){
       $data=$request->validate([
           'id'=>'exists:books|numeric',
           'deltype'=>['required',Rule::in(['1','0'])]
       ]);
       $user=Auth::user();
       $book=Book::find($data['id']);
       if($book->user_id==$user->id){
            $res=DB::transaction(function () use($data,$book){
             if($data['deltype']=='0')
                 return $book->delete();
             else
                 return $book->forceDelete();
            });
            $details = ['email' => $user->email,'message'=>'A book has been deleted'];
            QueueMail::dispatch($details);
            if($res) return response()->json(['error'=>'0','id'=>$data['id']]);
    }
       return response()->json(['error'=>'1','message'=>'couldn\'t delete']);
   }


   public function update(Request $request){
        $data=$request->validate([
        'name'=>'required|string',
        'id'=>'exists:books|numeric',
        ]);
        $user=Auth::user();
        $book=Book::find($data['id']);
        if($book->user_id==$user->id){
            $res=DB::transaction(function () use($data,$book){
                return $book->update(['name'=>$data['name']]);
            });
            if($res) return response()->json(['error'=>'0','id'=>$data['id'],'name'=>$data['name']]);
        }
       return response()->json(['error'=>'1','message'=>'couldn\'t update']);

   }
}
