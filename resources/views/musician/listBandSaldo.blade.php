@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
         <ul class="nav nav-tabs">
            <li><a href={{url('musician/saldo/'.Auth::guard('musician')->user()->slug)}}>Saldo Musisi </a></li>
            <li class="active"><a href={{url('listband/saldo/'.Auth::guard('musician')->user()->slug)}}>Saldo Band </a></li>
        </ul>

		@if($listband != null)
			<div class="panel panel-default">
				<div class="panel-heading">
					@foreach($listband as $band)
						<a href={{ url('/band/saldo/'.$band->slug) }}>{{$band->nama_grupband}}</a> | 
					@endforeach
				</div>
				<div class="panel-body">
					@if($listband != null)
						<p>Pilih List Band diatas</p>
					@else
						<p>Tidak ada Band</p>
					@endif
				</div>
			</div>
		@endif
		
        </div>
    </div>
</div>
@endsection