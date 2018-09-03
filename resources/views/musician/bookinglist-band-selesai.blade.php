@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
         <ul class="nav nav-tabs">
            
        </ul>

		@if(!$sewaband->isEmpty())
			@foreach($sewaband as $_sewaband)
				<div class="panel panel-default">
					<div class="panel-heading">Request dari <a href={{ url('/user/'.$_sewaband->organizer->slug) }}>{{$_sewaband->organizer->first_name}}</a>
					</div>
					<div class="panel-body">
						<?php 
						$datestart = date('d M Y', strtotime($_sewaband->gig->tanggal_mulai));
						$dateend = date('d M Y', strtotime($_sewaband->gig->tanggal_selesai));
						$timestart = explode(" ", $_sewaband->gig->tanggal_mulai);
						$timeout = explode(" ", $_sewaband->gig->tanggal_selesai);
						if($_sewaband->status_request == '0')
							$status = 'Menunggu konfirmasi';
						else
							$status = 'Diterima';
						?>
						<p>Di acara : {{$_sewaband->gig->nama_gig}}</p>
						<p>Lokasi : {{$_sewaband->gig->lokasi}}</p>
						<p>Dari : {{$datestart}} , {{$timestart[1]}}</p>
						<p>Sampai : {{$dateend}} , {{$timeout[1]}}</p>
						<br/>
						<p>Status Permintaan: <b>{{$status}}</b></p>

						@if($_sewaband->status_request == 1)
							@if($_sewaband->status == 0)
								<p>Status Booking : <b>Belum Bayar</b></p>
							@elseif($_sewaband->status == 1)
								<p>Status Booking : <b>Menunggu Verifikasi Pembayaran</b></p>
							@elseif($_sewaband->status == 2)
								<p>Status Booking : <b>LUNAS!</b></p>
							@elseif($_sewaband->status == 3)
								<p>Status Booking : <b>SELESAI!</b></p>
								@if(!$_sewaband->review->isEmpty())
									<?php 
										for($i=0;$i<$_sewaband->review[0]->nilai;$i++){
											echo "<i class='fa fa-star'></i>";
										}
									?>
								@endif
							@elseif($_sewaband->status == 4)
								<p>Status Booking : <b>SELESAI! Dana telah di transfer ke <a href={{ url('/musician/'.Auth::guard('musician')->user()->slug) }}>{{Auth::guard('musician')->user()->name}}</a></b></p>
								@if(!$_sewaband->review->isEmpty())
									<?php 
										for($i=0;$i<$_sewaband->review[0]->nilai;$i++){
											echo "<i class='fa fa-star'></i>";
										}
									?>
								@endif
							@else
								<p>Status Booking : <b>BATAL!</b></p>
							@endif
							<h3>Total Bayar : Rp. <b>{{$_sewaband->total_biaya}}</b></h3>
						@endif
					</div>
				</div>
			@endforeach	
		@endif
		
        </div>
    </div>
</div>
@endsection