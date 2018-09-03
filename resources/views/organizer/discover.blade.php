@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
		@if(!$musisi->isEmpty())	
			@foreach($musisi as $_musisi)
				<div class="panel panel-default">
					<div class="panel-heading"><a href={{ url('/musician/'.$_musisi->slug) }}>{{$_musisi->name}}</a> - <span>{{$_musisi->basis}}</span>
					</div>
					<div class="panel-body">
						<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-8">							
									<p>Wilayah : {{$_musisi->Wilayah}} </p>
									<p>Tipe : {{$_musisi->tipe}} </p>									
								</div>
														
								@if(Auth::guard('user')->user())
								<div class="col-xs-6 col-md-4">
									<section class="text-center">
										<span>Rp.{{$_musisi->harga_sewa}} / Jam</span>
		                        		<a href={{url('sewa-musisi/'.$_musisi->slug)}} class="btn btn-black btn-block" role="button">SEWA</a>
		                        	</section>
		                        </div>
		                    	@endif
		                    	
		                    	
								@if(Auth::guard('user')->user())

			                    @elseif(Auth::guard('musician')->user())

			                    @else
			                    <div class="col-xs-6 col-md-4">
			                        <section class="text-center">
										<span>Rp.{{$_musisi->harga_sewa}} / Jam</span>
		                        		<a href={{url('sewa-musisi/'.$_musisi->slug)}} class="btn btn-black btn-block" role="button">SEWA</a>
		                        	</section>
			                    </div>
			                    @endif
		                    </div>
					</div>
				</div>
			@endforeach
		@else
			<p class="center">Tidak ada musisi</p>
		@endif
        </div>
         <div class="col-md-10 col-md-offset-1">
		@if(!$band->isEmpty())	
			@foreach($band as $_band)
				<div class="panel panel-default">
					<div class="panel-heading"><a href={{ url('/band/'.$_band->slug) }}>{{$_band->nama_grupband}}</a> - <span>{{$_band->basis}}</span>
					</div>
					<div class="panel-body">
						<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-8">							
									<p>Wilayah : {{$_band->Wilayah}} </p>
									<p>Tipe : {{$_band->tipe}} </p>										
								</div>

							@if(Auth::guard('user')->user())
							<div class="col-xs-6 col-md-4">
		                        <section class="text-center">
		                        	<span>Rp.{{$_band->harga}} / Jam</span>
	                        		<a href={{url('sewa-band/'.$_band->slug)}} class="btn btn-black btn-block" role="button">SEWA</a>
	                        	</section>
		                    </div>
	                    	@endif
	                    	
							@if(Auth::guard('user')->user())

		                    @elseif(Auth::guard('musician')->user())

		                    @else
		                        <div class="col-xs-6 col-md-4">
		                        	<section class="text-center">
		                        		<span>Rp.{{$_band->harga}} / Jam</span>
	                        			<a href={{url('sewa-band/'.$_band->slug)}} class="btn btn-black btn-block" role="button">SEWA</a>
	                        		</section>
		                    	</div>
		                    @endif
						</div>
					</div>
				</div>
			@endforeach
		@else
			<p class="center">Tidak ada band</p>
		@endif
        </div>
    </div>
</div>
@endsection