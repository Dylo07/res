@extends('layouts.app')

@section('content')
<div class="container">
<div class="row justify-content">
@include('inventory.inc.sidebar')
      
    

    
    <div class="col-md-8"> 
    <i class="fa-solid fa-cart-shopping"></i> Category
    <a href="/inventory/category/create" class="btn btn-success btn-sm float-end"><i class="fas fa-plus"> </i>Create Category</a>
    <hr>
    @if(Session()->has('status') )
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert">X</button>
        
        {{Session()->get('status')}}

    </div>


    @endif
    <table class="table table-bordered">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Category</th>
                <th scope="col">Edit</th>
                <th scope="col">Delete</th>



            </tr>
</thead>
    

    <tbody>
        @foreach($categories as $category)
        <tr>
            <th scope="row">{{$category->id}}</th>
            <td>{{$category->name}}</td>
            <td>
                <a href="/inventory/category/{{$category->id}}/edit" class= "btn btn-warning">Edit</a>

    </td>
    <td>
        <!-- <form action="/inventory/category/{{$category->id}}}" method="post">
                
                @csrf
                @method ('DELETE')
                <input type="submit" value="Delete" class="btn btn-danger">
                    </form> -->
    
    </td>
    </tr>


        @endforeach

    </tbody>
    </table>
    
</div>
    </div>
    </div>
@endsection

    </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/fontawesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <style type="text/css">  
        i{  
            font-size: 20px !important;  
            padding: 10px;  
        }  
    </style>  
