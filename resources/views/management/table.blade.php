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
<i class="fa fa-align-center"></i> Table
<a href="/management/table/create" class="btn btn-success btn-sm float-right"> <i class="fas fa-chair"></i> Create a Table </a>
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
            <th scope="col">Table</th>
            <th scope="col">Status</th>
            <th scope="col">Edit</th>
            <th scope="col">Delete</th>
    </tr>  
    </thead>
    <tbody>
        @foreach($tables as $table)
        <tr> 
            <td> {{$table->id}}</td>
            <td> {{$table->name}}</td>
            <td> {{$table->status}}</td>
            <td> 
                <a href= "/management/table/{{$table->id}}/edit" class="btn btn-warning">Edit</a>
            </td>
            <td> 
                <form action="/management/table/{{$table->id}}" method="post"> 
                    @csrf
                    @method('DELETE')
                    <input type="submit" value= "Delete" class="btn btn-danger">

                </form>

            </td>


        </tr>
       
@endforeach

    </tbody>
    </table>
    
</div>
    </div>
    </div>
@endsection