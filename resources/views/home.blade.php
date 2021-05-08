@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>


            </div>
        </div>
    </div>


  <h2> Table</h2>
            
  <table class="table">
    <thead>
      <tr>
        <th>id</th>
        <th>seller name</th>
        <th>Email</th>
      </tr>
    </thead>
    <tbody>
       
            
        @foreach ($sellers as $seller)
         <tr>
            <td>{{ $seller->pk_seller_account_id }}</td>
            <td>{{ $seller->seller_account_name }}</td> 
            <td>{{ $seller->seller_account_primary_email }}</td> 
            <td><a href="{{url('/delete-seller').'/'.$seller->pk_seller_account_id}}">Delete</a></td> 
        </tr>
        @endforeach 

    </tbody>
  </table>


</div>
@endsection
