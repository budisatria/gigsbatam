@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
         <ul class="nav nav-tabs">
            <li><a href="{{url('listsewa')}}">Semua </a></li>
            <li class="active"><a href="{{url('listsewa-band')}}">Sewa band </a></li>
            <li><a href="{{url('listsewa-musisi')}}">Sewa Musisi </a></li>
        </ul>
		@if(!$sewa->isEmpty())	
			@foreach($sewa as $_sewa)
				<div class="panel panel-default">
					<div class="panel-heading">Sewa <a href={{ url('/band/'.$_sewa->band->slug) }}>{{$_sewa->band->nama_grupband}}</a>
					</div>
					<div class="panel-body">
						<div class="col-md-8">
							<?php 
							$datestart = date('d M Y', strtotime($_sewa->gig->tanggal_mulai));
							$dateend = date('d M Y', strtotime($_sewa->gig->tanggal_selesai));
							$timestart = explode(" ", $_sewa->gig->tanggal_mulai);
							$timeout = explode(" ", $_sewa->gig->tanggal_selesai);
							if($_sewa->status_request == '0')
								$status = 'Menunggu konfirmasi';
							elseif($_sewa->status_request == '1')
								$status = 'Diterima';
							else
								$status = 'Ditolak';
							?>
							<p>Di acara : {{$_sewa->gig->nama_gig}}</p>
							<p>Lokasi : {{$_sewa->gig->lokasi}}</p>
							<p>Dari : {{$datestart}} , {{$timestart[1]}}</p>
							<p>Sampai : {{$dateend}} , {{$timeout[1]}}</p>
							<br/>
							<p>Status Permintaan: <b>{{$status}}</b></p>

							@if($_sewa->status_request == 1)
								<h3>Total Bayar : Rp. <b>{{$_sewa->total_biaya}}</b></h3>
								<b>
								@if($_sewa->status == 0)
									<p>Segara lakukan pembayaran, jika lewat 24 Jam penyewaan akan dibatalkan</p>
									<p><a href={{url('konfirmasi-pembayaran/'.$_sewa->id)}}>Konfirmasi Pembayaran</a></p>									
								@elseif($_sewa->status == 1)
									<p>Menunggu Verifikasi Pembayaran</p>
								@elseif($_sewa->status == 2)
									<p>LUNAS!</p>
								@elseif($_sewa->status == 3)
									<p>Selesai!</p>									
									@if($_sewa->review->isEmpty())
										<p><a href={!! url('add-review/'.$_sewa->id) !!}>Berikan Review</a></p>
									@else
										<?php 
											for($i=0;$i<$_sewa->review[0]->nilai;$i++){
												echo "<i class='fa fa-star'></i>";
											}
										?>
									@endif
								@elseif($_sewa->status == 4)
									<p>SELESAI! Dana telah di transfer ke <a href={{ url('/band/'.$_sewa->band->slug) }}>{{$_sewa->band->nama_grupband}}</a></p>
									@if($_sewa->review->isEmpty())
										<p><a href={!! url('add-review/'.$_sewa->id) !!}>Berikan Review</a></p>
									@else
										<?php 
											for($i=0;$i<$_sewa->review[0]->nilai;$i++){
												echo "<i class='fa fa-star'></i>";
											}

										?>
									@endif
								@else
									<p>Batal</p>
								@endif
								</b>
							@endif
						</div>
						<div class="col-md-4">
							@if($_sewa->gig->photo_gig != null)
								<img src={!! Cloudder::show($_sewa->gig->photo_gig, ['format' => 'jpg', 'crop' => 'fit', 'width' => '150', 'height'=> '150']) !!} alt="Logo" class="widthfull">
							@endif
						</div>
					</div>
				</div>
			@endforeach
		@endif
        </div>
    </div>
</div>

@endsection