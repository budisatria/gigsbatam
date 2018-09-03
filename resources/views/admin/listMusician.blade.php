@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">List Musisi ( {{$musisis->count()}} )</div>
				<div class="panel-body">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					        <th>Name</th>
					        <th>Email</th>
					        <th>Wilayah</th>
					        <th>Harga Sewa</th>
					        <th>Aktif</th>
					        <th>Aksi</th>
					      </tr>
					    </thead>
					    <tbody>
							@if(!$musisis->isEmpty())	
								@foreach($musisis as $musisi)
									<tr>
										<td>{{$musisi->name}}</td>
										<td>{{$musisi->email}}</td>
										<td>{{$musisi->Wilayah}}</td>
										<td>{{$musisi->harga_sewa}}</td>
										<td>{{$musisi->aktif}}</td>
										<td><a href={{url('admin/edit/musisi/'.$musisi->id)}}>Edit</td>
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