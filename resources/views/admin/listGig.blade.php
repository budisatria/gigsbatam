@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">List Gig ( {{$gigs->count()}} )</div>
				<div class="panel-body">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					        <th>Nama Gig</th>
					        <th>Tanggal Mulai</th>
					        <th>Tanggal Selesai</th>
					        <th>Organizer</th>
					        <th>Aktif</th>
					        <th>Aksi</th>
					      </tr>
					    </thead>
					    <tbody>
							@if(!$gigs->isEmpty())	
								@foreach($gigs as $gig)
									<tr>
										<td>{{$gig->nama_gig}}</td>
										<td>{{$gig->tanggal_mulai}}</td>
										<td>{{$gig->tanggal_selesai}}</td>
										<td>{{$gig->organizer['0']->first_name}}</td>
										<td>{{$gig->aktif}}</td>
										<td><a href={{url('admin/edit/gig/'.$gig->id)}}>Edit</td>
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