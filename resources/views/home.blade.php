@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                        <input type="text" name="book" id="">
                        <button id="add">Add book</button>
                        <div class="books">
                            <h2>Books:</h2>
                            @forelse ($user->books as $book)
                                <div id="{{$book->id}}"><span>{{$book->name}}</span>  <button class="softdel" onclick="delete1(this)">remove (temporarily)</button>  <button class="harddel" onclick="delete1(this)">Delete(permanently)</button><button class="edit" onclick="edit(this)">Edit</button></div>
                            @empty 
                            <p>No books to show</p>
                            @endforelse
                        </div>                   
                </div>
            </div>
        </div>
    </div>
    <input type="text" style="display:none;" name="" id="edit">
</div>
<script>
    let authtoken = "Bearer " + "{{$authorizationToken}}";
let csrf = $('meta[name="csrf-token"]').attr("content");
let editi = document.getElementById("edit");
function edit(book) {
    if (book.innerText == "update") {
        $.ajax({
            method: "post",
            headers: {
                Authorization: authtoken,
                "X-CSRF-TOKEN": csrf
            },
            url: '{{route("book.update")}}',
            data: {
                id: book.parentNode.id,
                name: book.parentNode.firstChild.value
            },
            success: function(data) {
                let newb = document.getElementById(data.id);
                if (data.error != "1") {
                    newb.firstChild.remove();
                    var book1 = document.createElement("span");
                    book1.innerHTML = data.name;
                    newb.prepend(book1);
                } else {
                    newb.append(data.message);
                }
                newb.lastChild.innerText = "Edit";
            }
        });
    } else {
        let div = book.parentNode;
        let tempedit = editi.cloneNode(true);
        tempedit.setAttribute("value", div.firstChild.innerText);
        div.firstChild.innerText = "";
        tempedit.style.display = "inline";
        div.prepend(tempedit);
        book.innerText = "update";
    }
}

function delete1(book) {
    let del = book.innerText;
    del = del == "remove (temporarily)" ? 0 : 1;
    $.ajax({
        method: "post",
        headers: {
            Authorization: authtoken,
            "X-CSRF-TOKEN": csrf
        },
        url: '{{route("book.remove")}}',
        data: {
            id: $(book)
                .parent()
                .attr("id"),
            deltype: del
        },
        success: function(data) {
            if (data.error != "1") $("div#" + data.id).remove();
            else {
                $("div#" + data.id).append(data.message);
            }
        }
    });
}
let books = $(".books");
$("button#add").on("click", function() {
    $.ajax({
        method: "post",
        headers: {
            Authorization: authtoken,
            "X-CSRF-TOKEN": csrf
        },
        url: '{{route("book.add")}}',
        data: { name: $("input:text").val() },
        success: function(data) {
            $(".books>p").hide();
            books.append(
                '<div id="' +
                    data.id +
                    '">' +
                    data.name +
                    '<button class="softdel" onclick="delete1(this)">remove (temporarily)</button><button class="harddel" onclick="delete1(this)">Delete(permanently)</button></div>'
            );
        }
    });
});

</script>
@endsection
