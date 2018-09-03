@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
		@if(!$listband->isEmpty())	
			@foreach($listband as $_listband)
				<div class="panel panel-default">
					<div class="panel-heading">Ada ({{$_listband->sewa}}) Organizer yang Menyewa <a href={{ url('/band/'.$_listband->slug) }}>{{$_listband->nama_grupband}}</a>
					</div>
					<div class="panel-body">
						<p>{{$_listband->deskripsi}}</p>
						<p><a href={{url('listsewa/band/'.$_listband->slug)}}>Lihat Booking-an</a></p>
					</div>
				</div>
			@endforeach
		@else
			<p class="center">Anda tidak memiliki booking-an Band</p>
		@endif
        </div>
    </div>
</div>
@endsection