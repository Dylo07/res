@extends('layouts.app')

@section('content')
<div class="container">
<div class="row justify-content">
    @include('management.inc.sidebar')
    
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/fontawesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
    <style type="text/css">  
        i{  
            font-size: 20px !important;  
            padding: 10px;  
        }  
    </style>  
    </link>
    </link>
<div class="col-md-8">
<i class="fa-solid fa-bowl-rice"></i> Menu
<a href="/management/menu/create" class="btn btn-success btn-sm float-right"><i class="fas fa-plus"></i> Create Menu </a>
    <hr>
    @if(Session()->has('status'))
    <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert"></button>
        {{Session()->get('status')}}
        
    </div>
    @endif
    <table class="table table-bordered">
        <thead>
          <tr>
            <th scope="col">ID</th>
            <th scope="col">Name</th>
            <th scope="col">Price</th>
            <th scope="col">Picture</th>
            <th scope="col">Description</th>
            <th scope="col">Category</th>
            <th scope="col">Edit</th>
            <th scope="col">Delete</th>
    </tr>  
    </thead>
    <tbody> 

    </tbody>
    @foreach($menus as $menu)
    <tr> 
        <td> {{$menu->id}} </td>
        <td> {{$menu->name}} </td>
        <td> {{$menu->price}} </td>
        <td> 
            <img src=" {{asset('menu_images')}}/{{$menu->image}}" alt="{{

            $menu->name}}" width="120px" height="120px" class="img-thumbnail">
        </td>
        <td> {{$menu->description}} </td>
        <td> {{$menu->category->name}} </td>
        <td> <a href="/management/menu/{{$menu->id}}/edit" class="btn btn-warning">Edit</a></td>
        <td> 
            <form action="/management/menu/{{$menu->id}}" method="post">
            @csrf

            @method('DELETE')
            <input type="submit" value="Delete" class="btn btn_danger">


        </form> </td>
    </tr>

    @endforeach

    
    </table>
    
</div>
    </div>
    </div>
@endsection