@extends('layouts.app')

@section('content')
<div class="container">
<div class="row justify-content">
@include('inventory.inc.sidebar')
      
    

    
    <div class="col-md-8"> 
    <i class="fa-solid fa-cart-shopping"></i>Create a Category
    
    <hr>
    @if($errors->any())
    <div class="alert alert-danger"></div>
    <ul>
        @foreach($errors->all() as $error)
        <li>{{$error}}</li>

        @endforeach
    </ul>


    @endif


    <form action="/inventory/category" method="POST" >
        @csrf
        <div class="form-group"> 
            <label for="categoryName">Category Name</lable>
            <input type="text" name="name" class="form-control" placeholder="Category...">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
    </form>


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
</div>
</div>
@endsection