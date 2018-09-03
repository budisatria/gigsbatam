@extends('layouts.app')

@section('content')
<div class="container">
@if(Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
			
            <div class="panel panel-default">			
					<div class="panel-body">
						<div class="col-md-6 col-md-offset-3">
							<img name="aboutme" class="text-center" src={!! Cloudder::show($gigs->photo_gig, array("crop" => "scale", "width" => 430, "height" => 400)) !!}>
														
								<h1 class="text-center">{{$gigs->nama_gig}}</h1>
								@if(Auth::guard('user')->user())
									@if($gigs->user_id == Auth::guard('user')->user()->id)
										<a class="btn btn-block btn-black" href={{ url('/edit-gig/'.$gigs->slug) }}>Edit</a>
										<br>
									@endif
								@endif								
								<p>Tanggal Main : {{$gigs->tanggal_mulai}} s/d {{$gigs->tanggal_selesai}}</p>								
								<p>Lokasi : {{$gigs->lokasi}} - {{$gigs->detail_lokasi}}</p>					
								<p>Deskripsi : {{$gigs->deskripsi}}</p>

								<h3>Daftar Penawar</h3>
								@if(!$offer->isEmpty())
									@foreach($offer as $penawar)
										@if($penawar->type_sewa == 'bandhire')											
											<p><a href={{url('band/'.$penawar->penawar[0]->slug)}}>{{$penawar->penawar[0]->nama_grupband}}</a></p>
										@elseif($penawar->type_sewa == 'musisihire')											
											<p><a href={{url('musician/'.$penawar->penawar[0]->slug)}}>{{$penawar->penawar[0]->name}}</a></p>
										@endif
									@endforeach
								@else
									<p>Tidak ada Penawar</p>
								@endif
								@if(Auth::guard('musician')->user())
								<?php
									$sewa = App\Sewa::where('gig_id', $gigs->id)->where('status_request', 1)->get();
								?>
								@if($sewa->isEmpty())
									<br/>									
									<a href="#" data-toggle="modal" data-target="#modalOffer" class="btn btn-block btn-black">Tawarkan</a>
								@endif
							@endif
						
						</div>						
					</div>
            </div>			
        </div>
    </div>
</div>


<!-- MODAL BERIKAN TAWARAN -->
@if(Auth::guard('musician')->user())
	<div class="modal  fade" id="modalOffer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
	  <div class="modal-dialog" role="document">
		<div class="modal-content">

		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel">Offer Gig - {{$gigs->nama_gig}}</h4>
		  </div>
		
		  <div class="modal-body" id="modaladd">
		  	<div class="col-md-12">
				<div class="col-md-6">
					<p>Offer sebagai musisi</p>					
					<a class="btn btn-black btn-block" href={{url('offer-gig/'.$gigs->id)}}>Tawarkan Saya</a>
				</div>
				<div class="col-md-6">
					<p>Offer sebagai Band</p>
					{{ Form::open(['route'=>['add.offer',$gigs->id],'role'=> 'form', 'class' => 'ui reply form']) }}
						<select class="form-control" name="band" id="band" required="true">
							<?php $bands = App\Grupband::where('admin_id', Auth::guard('musician')->user()->id)->get()?>
							<option required value="">- Pilih Band -</option>
							@foreach($bands as $band)
								<option required value="{{$band->id}}">{{$band->nama_grupband}}</option>
							@endforeach
						</select>
						<button class="btn btn-block btn-black">Tawarkan Kami</button>
					{{ Form::close() }}
				</div>
			</div>
		  </div>

		<div class="modal-footer">

		</div>

		</div>
	  </div>
	</div>
@endif
@endsection