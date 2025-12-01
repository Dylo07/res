@extends('layouts.app')

@section('content')
<div class="container">
<iv class="row justify-content">
@include('inventory.inc.sidebar')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/fontawesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <style type="text/css">  
        i{  
            font-size: 20px !important;  
            padding: 10px;  
        }  
    </style>  
<div class="col-md-8">
<i class="fa-solid fa-bowl-rice"></i>  Edit a Menu 
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
          
    <form action="/inventory/menu/{{$menu->id}}} " method="POST" endtype="multipart/form-data2"> 
        @csrf
        @method('PUT')
<div class="form-group">
<lable for="menuName"> Menu Name</lable>
<input type="text" name="name" value="{{$menu->name}}" class="form-controll" placeholder="Menu...">

    </div>
    
    
<lable for="menuPrice"> Price

</lable>
<div class="input-group mb-3"> 
    <span class="input-group-text"> Rs </span>
    
    <input type="text" name="price" value="{{$menu->price}}" class="form-control" aria-label="Amount (to the nearest dollor )">

    </div> 

   

  
<div class="mb-3">
  <label for="formFile" class="form-label">Image</label>
  <input class="form-control" type="file" id="formFile">
</div>

<div Class="form-group">
<label for="Description"> Description</label>
<input typr="text" name="description" value="{{$menu->description}}" class="form-control" placeholder="Description...">
    
    <label for="Category">Category</label>
<select class="form-control" name="category_id">
@foreach ($categories as $category)
<option value= "{{$category->id}}" {{$menu->category_id === $category->id ? 'selected': ''}}> {{$category->name}}</option>

@endforeach
    </select>
</div>

<div class="form-group">
   
    <button type="submit" class="btn btn-primary">Save</button>

    </form>
</div>
@endsection