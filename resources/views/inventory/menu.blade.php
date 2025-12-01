@extends('layouts.app')

@section('content')
<div class="container">
<div class="row justify-content">
    @include('inventory.inc.sidebar')
 
<div class="col-md-8">
<i class="fa-solid fa-bowl-rice"></i> Menu
<a href="/inventory/menu/create" class="btn btn-success btn-sm float-end"><i class="fas fa-plus"></i> Create Menu </a>
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
           
            <th scope="col">Name</th>
            <th scope="col">Quantity</th>
            
            
           
           
    </tr>  
    </thead>
    <tbody> 

    </tbody>
    @foreach($menus as $menu)
    <tr> 
       
        <td> {{$menu->name}} </td>
        <td> {{$menu->stock}} </td>
        
        
        
        

        </form> </td>
    </tr>

    @endforeach

    
    </table>
    
</div>
    </div>
    </div>
@endsection
   
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