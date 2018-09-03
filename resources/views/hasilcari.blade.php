@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
        @if(is_object($listband))
			@if(!$listband->isEmpty())
				<h2 class="text-center">Hasil Pencarian</h2>
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

							@if(Auth::guard('user')->user())
							<div class="col-xs-6 col-md-4">
		                        <section class="text-center">
		                        	<span>Rp.{{$_listband->harga}} / Jam</span>
	                        		<a href={{url('sewa-band/'.$_listband->slug)}} class="btn btn-black btn-block" role="button">SEWA</a>
	                        	</section>
		                    </div>
	                    	@endif
	                    	
							@if(Auth::guard('user')->user())

		                    @elseif(Auth::guard('musician')->user())

		                    @else
		                        <div class="col-xs-6 col-md-4">
		                        	<section class="text-center">
		                        		<span>Rp.{{$_listband->harga}} / Jam</span>
	                        			<a href={{url('sewa-band/'.$_listband->slug)}} class="btn btn-black btn-block" role="button">SEWA</a>
	                        		</section>
		                    	</div>
		                    @endif
						</div>
						</div>
					</div>
				@endforeach
			@else
				<p class="center">Tidak ada band ditemukan.</p>
			@endif
		@else
			@if($listband != null)
				<h2 class="text-center">Hasil Pencarian</h2>	
				@foreach($listband as $_listband)
					<div class="panel panel-default">
						<div class="panel-heading"><a href={{ url('/band/'.$_listband->slug) }}>{{$_listband->nama_grupband}}</a>
						</div>
						<div class="panel-body">
							<div class="row">
								<div class="col-xs-12 col-sm-6 col-md-8">							
									<p>Wilayah : {{$_listband->Wilayah}} </p>
									<p>Tipe : {{$_listband->tipe}} </p>									
								</div>

							@if(Auth::guard('user')->user())
	                        	<div class="col-xs-6 col-md-4">
		                        	<section class="text-center">
		                        		<span>Rp.{{$_listband->harga}} / Jam</span>
	                        			<a href={{url('sewa-band/'.$_listband->slug)}} class="btn btn-black btn-block" role="button">SEWA</a>
	                        		</section>
		                    </div>
	                    	@endif
	                    	
							@if(Auth::guard('user')->user())

		                    @elseif(Auth::guard('musician')->user())

		                    @else
		                        <div class="col-xs-6 col-md-4">
		                        	<section class="text-center">
		                        		<span>Rp.{{$_listband->harga}} / Jam</span>
	                        			<a href={{url('sewa-band/'.$_listband->slug)}} class="btn btn-black btn-block" role="button">SEWA</a>
	                        		</section>
		                    </div>
		                    @endif
						</div>
						</div>
					</div>
				@endforeach
			@else
				<p class="center">Tidak ada band ditemukan.</p>
			@endif
		@endif
        </div>
    </div>
</div>
@endsection