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
						<div class="panel-heading"><a href={{ url('/band/'.$_listband->slug) }}>{{$_listband->nama_grupband}}</a> - 
						<span>{{$_listband->basis}}</span>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-8">							
									<p>Wilayah : {{$_listband->Wilayah}} </p>
									<p>Tipe : {{$_listband->tipe}} </p>										
								</div>

								@if($_listband->admin_id == Auth::guard('musician')->user()->id)
								<div class="col-xs-6 col-md-4">
					                <section class="text-center">
										<a class="btn btn-black" href={{ url('/edit-band/'.$_listband->slug) }}>Edit Band</a>
									</section>
								</div>
								@endif
						</div>
						</div>
					</div>
			@endforeach
		@else
			<p class="center">Anda belum memiliki band</p>
		@endif
        </div>
    </div>
</div>
@endsection