@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
        @if(is_object($listmusisi))
			@if(!$listmusisi->isEmpty())
				<h2 class="text-center">Hasil Pencarian</h2>
				@foreach($listmusisi as $_listmusisi)
					<div class="panel panel-default">
						<div class="panel-heading"><a href={{ url('/musician/'.$_listmusisi->slug) }}>{{$_listmusisi->name}}</a> - <span>{{$_listmusisi->basis}}</span>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-8">							
									<p>Wilayah : {{$_listmusisi->Wilayah}} </p>
									<p>Tipe : {{$_listmusisi->tipe}} </p>									
								</div>
														
								@if(Auth::guard('user')->user())
								<div class="col-xs-6 col-md-4">
									<section class="text-center">
										<span>Rp.{{$_listmusisi->harga_sewa}} / Jam</span>
		                        		<a href={{url('sewa-musisi/'.$_listmusisi->slug)}} class="btn btn-black btn-block" role="button">SEWA</a>
		                        	</section>
		                        </div>
		                    	@endif
		                    	
		                    	
								@if(Auth::guard('user')->user())

			                    @elseif(Auth::guard('musician')->user())

			                    @else
			                    <div class="col-xs-6 col-md-4">
			                        <section class="text-center">
										<span>Rp.{{$_listmusisi->harga_sewa}} / Jam</span>
		                        		<a href={{url('sewa-musisi/'.$_listmusisi->slug)}} class="btn btn-black btn-block" role="button">SEWA</a>
		                        	</section>
			                    </div>
			                    @endif
		                    </div>
						</div>
					</div>
				@endforeach
			@else
				<p class="center">Tidak ada musisi ditemukan.</p>
			@endif
		@else
			@if($listmusisi != null)
				<h2 class="text-center">Hasil Pencarian</h2>
				@foreach($listmusisi as $_listmusisi)
					<div class="panel panel-default">
						<div class="panel-heading"><a href={{ url('/musician/'.$_listmusisi->slug) }}>{{$_listmusisi->name}}</a> - <span>{{$_listmusisi->basis}}</span>
						</div>
						<div class="panel-body">
						<div class="row">
							<div class="col-xs-12 col-sm-6 col-md-8">							
									<p>Wilayah : {{$_listmusisi->Wilayah}} </p>
									<p>Tipe : {{$_listmusisi->tipe}} </p>									
							</div>

							@if(Auth::guard('user')->user())						
							<div class="col-xs-6 col-md-4">
	                        	<section class="text-center">
										<span>Rp.{{$_listmusisi->harga_sewa}} / Jam</span>
		                        		<a href={{url('sewa-musisi/'.$_listmusisi->slug)}} class="btn btn-black btn-block" role="button">SEWA</a>
		                        </section>
	                        </div>
	                    	@endif
	                    	
							@if(Auth::guard('user')->user())

		                    @elseif(Auth::guard('musician')->user())

		                    @else
		                    <div class="col-xs-6 col-md-4">
		                        <section class="text-center">
										<span>Rp.{{$_listmusisi->harga_sewa}} / Jam</span>
		                        		<a href={{url('sewa-musisi/'.$_listmusisi->slug)}} class="btn btn-black btn-block" role="button">SEWA</a>
		                        </section>
		                    </div>
		                    @endif
						</div>
					</div>
					</div>
				@endforeach
			@else
				<p class="center">Tidak ada musisi ditemukan.</p>
			@endif
		@endif
        </div>
    </div>
</div>
@endsection