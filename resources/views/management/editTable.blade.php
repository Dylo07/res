@extends('layouts.app')

@section('content')
<div class="container">
<div class="row justify-content">
    <div class="col-md-4"> 
        <div class="list-group">
        
        <a href="/management/category" class="list-group-item list-group-item-action"><i class="fa fa-align-center"></i> Category</a>
            <a class="list-group-item list-group-item-action"> <i class="fa-solid fa-bowl-rice"></i> Menu</a>
            <a class="list-group-item list-group-item-action"><i class="fa-solid fa-chair"></i> Table</a>
            <a class="list-group-item list-group-item-action"><i class="fa-solid fa-user"></i> User </a>
</div>
    <div class="col-md-8"> </div>
</div>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/fontawesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <style type="text/css">  
        i{  
            font-size: 20px !important;  
            padding: 10px;  
        }  
    </style>  
<div class="col-md-8">
<i class="fa fa-chair"></i>  Edit a Table 
    <hr>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
          
    <form action="/management/table/{{$table->id}} " method="POST">
        @csrf
        @method('PUT')
<div class="form-group">
<lable for="tableName"> Table Name</lable>
<input type="text" name="name" value="{{$table->name}}" class="form-controll" placeholder="table...">

    </div>
    <button type="submit" class="btn btn-warning">Edit</button>

    </form>
</div>
@endsection