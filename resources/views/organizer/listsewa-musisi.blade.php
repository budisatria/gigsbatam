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
            <li><a href="{{url('listsewa-band')}}">Sewa band </a></li>
            <li class="active"><a href="{{url('listsewa-musisi')}}">Sewa Musisi </a></li>
        </ul>

		@if(!$sewamusisi->isEmpty())
			@foreach($sewamusisi as $_sewamusisi)
				<div class="panel panel-default">
					<div class="panel-heading">Sewa <a href={{ url('/musician/'.$_sewamusisi->musisi->slug) }}>{{$_sewamusisi->musisi->name}}</a>
					</div>
					<div class="panel-body">
						<div class="col-md-8">
							<?php 
							$datestart = date('d M Y', strtotime($_sewamusisi->gig->tanggal_mulai));
							$dateend = date('d M Y', strtotime($_sewamusisi->gig->tanggal_selesai));
							$timestart = explode(" ", $_sewamusisi->gig->tanggal_mulai);
							$timeout = explode(" ", $_sewamusisi->gig->tanggal_selesai);
							if($_sewamusisi->status_request == '0')
								$status = 'Menunggu konfirmasi';
							elseif($_sewamusisi->status_request == '1')
								$status = 'Diterima';
							else
								$status = 'Ditolak';
							?>
							<p>Di acara : {{$_sewamusisi->gig->nama_gig}}</p>
							<p>Lokasi : {{$_sewamusisi->gig->lokasi}}</p>
							<p>Dari : {{$datestart}} , {{$timestart[1]}}</p>
							<p>Sampai : {{$dateend}} , {{$timeout[1]}}</p>
							<br/>
							<p>Status Permintaan: <b>{{$status}}</b></p>

							@if($_sewamusisi->status_request == 1)
								<h3>Total Bayar : Rp. <b>{{$_sewamusisi->total_biaya}}</b></h3>
								<b>
								@if($_sewamusisi->status == 0)
									<p>Segara lakukan pembayaran, jika lewat 24 Jam penyewaan akan dibatalkan</p>
									<p><a href={{url('konfirmasi-pembayaran/'.$_sewamusisi->id)}}>Konfirmasi Pembayaran</a></p>							
								@elseif($_sewamusisi->status == 1)
									<p>Menunggu Verifikasi Pembayaran</p>
								@elseif($_sewamusisi->status == 2)
									<p>LUNAS!</p>
								@elseif($_sewamusisi->status == 3)
									<p>SELESAI!</p>
									@if($_sewamusisi->review->isEmpty())
										<p><a href={!! url('add-review/'.$_sewamusisi->id) !!}>Berikan Review</a></p>
									@else
										<?php 
											for($i=0;$i<$_sewamusisi->review[0]->nilai;$i++){
												echo "<i class='fa fa-star'></i>";
											}
										?>
									@endif
								@elseif($_sewamusisi->status == 4)
									<p>SELESAI! Dana telah di transfer ke <a href={{ url('/musician/'.$_sewamusisi->musisi->slug) }}>{{$_sewamusisi->musisi->name}}</a></p>
									@if($_sewamusisi->review->isEmpty())
										<p><a href={!! url('add-review/'.$_sewamusisi->id) !!}>Berikan Review</a></p>
									@else
										<?php 
											for($i=0;$i<$_sewamusisi->review[0]->nilai;$i++){
												echo "<i class='fa fa-star'></i>";
											}
										?>
									@endif
								@else
									<p>BATAL</p>
								@endif
								</b>
							@endif
						</div>
						<div class="col-md-4">
							@if($_sewamusisi->gig->photo_gig != null)
								<img src={!! Cloudder::show($_sewamusisi->gig->photo_gig, ['format' => 'jpg', 'crop' => 'fit', 'width' => '150', 'height'=> '150']) !!} alt="Logo" class="widthfull">
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