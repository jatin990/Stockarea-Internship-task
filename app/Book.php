<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name','user_id'
    ];

    public function user(){
        return $this->BelongsTo('App\User');
    }
}

