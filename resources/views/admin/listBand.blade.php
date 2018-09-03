@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">List Band ( {{$bands->count()}} )</div>
				<div class="panel-body">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					        <th>Nama Band</th>
					        <th>Wilayah</th>
					        <th>Harga Sewa</th>
					        <th>Admin Band</th>
					        <th>Aktif</th>
					        <th>Aksi</th>
					      </tr>
					    </thead>
					    <tbody>
							@if(!$bands->isEmpty())	
								@foreach($bands as $band)
									<tr>
										<td>{{$band->nama_grupband}}</td>
										<td>{{$band->Wilayah}}</td>
										<td>{{$band->harga}}</td>
										<td>{{$band->admin['0']->name}}</td>
										<td>{{$band->aktif}}</td>
										<td><a href={{url('admin/edit/band/'.$band->id)}}>Edit</td>
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