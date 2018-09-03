@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">List Organizer ( {{$users->count()}} )</div>
				<div class="panel-body">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					        <th>Firstname</th>
					        <th>Lastname</th>
					        <th>Email</th>
					        <th>Aktif</th>
					        <th>Aksi</th>
					      </tr>
					    </thead>
					    <tbody>
							@if(!$users->isEmpty())	
								@foreach($users as $user)
									<tr>
										<td>{{$user->first_name}}</td>
										<td>{{$user->last_name}}</td>
										<td>{{$user->email}}</td>
										<td>{{$user->aktif}}</td>
										<td><a href={{url('admin/edit/user/'.$user->id)}}>Edit</td>
									</tr>
								@endforeach
							@endif
					    </tbody>
					</table>
				</div>
			</div>
        </div>
    </div>
</div>
@endsection