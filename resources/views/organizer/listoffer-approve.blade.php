@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
         <ul class="nav nav-tabs">
            <li><a href="{{url('listoffer')}}">Semua </a></li>
            <li><a href="{{url('listoffer-pending')}}">Penawaran Belum Disetujui </a></li>
            <li class="active"><a href="{{url('listoffer-approve')}}">Penawaran Disetujui </a></li>
            <li><a href="{{url('listoffer-finish')}}">Penawaran Selesai </a></li>
        </ul>

		@if(!$sewas->isEmpty())	
			@foreach($sewas as $_sewa)
				<div class="panel panel-default">
					<div class="panel-heading">{{$_sewa->gig[0]->nama_gig}}</div>
					<div class="panel-body">
					<?php 
						$datestart = date('d M Y', strtotime($_sewa->gig[0]->tanggal_mulai));
						$dateend = date('d M Y', strtotime($_sewa->gig[0]->tanggal_selesai));
						$timestart = explode(" ", $_sewa->gig[0]->tanggal_mulai);
						$timeout = explode(" ", $_sewa->gig[0]->tanggal_selesai);
						if($_sewa->status_request == '0')
							$status = 'Menunggu konfirmasi';
						else
							$status = 'Diterima';
					?>
					<p>Lokasi : {{$_sewa->gig[0]->lokasi}}</p>
					<p>Dari : {{$datestart}} , {{$timestart[1]}}</p>
					<p>Sampai : {{$dateend}} , {{$timeout[1]}}</p>
					<br/>
					<p>Status Permintaan: <b>{{$status}}</b></p>
					@if($_sewa->status_request == '1')
						@if($_sewa->status == 0)
							<p>Segara lakukan pembayaran, jika lewat 24 Jam penyewaan akan dibatalkan</p>					
							<p><a href={{url('konfirmasi-pembayaran/'.$_sewa->id)}}>Konfirmasi Pembayaran</a></p>							
						@elseif($_sewa->status == 1)
							<p>Menunggu Verifikasi Pembayaran</p>
						@elseif($_sewa->status == 2)
							<p>LUNAS!</p>
						@elseif($_sewa->status == 3)
							<p>Selesai!</p>
							@if($_sewa->status_request == 1 && $_sewa->review->isEmpty())
								<p><a href={!! url('add-review/'.$_sewa->id) !!}>Berikan Review</a></p>
							@elseif($_sewa->status_request == 1 && $_sewa->review != null)
								<?php 
									for($i=0;$i<$_sewa->review[0]->nilai;$i++){
										echo "<i class='fa fa-star'></i>";
									}
								?>
							@endif
						@elseif($_sewa->status == 4)
							<p>Dana Telah Ditransfer</p>
							@if($_sewa->status_request == 1 && $_sewa->review->isEmpty())
								<p><a href={!! url('add-review/'.$_sewa->id) !!}>Berikan Review</a></p>
							@elseif($_sewa->status_request == 1 && $_sewa->review != null)
								<?php 
									for($i=0;$i<$_sewa->review[0]->nilai;$i++){
										echo "<i class='fa fa-star'></i>";
									}
								?>
							@endif					
						@endif
					@endif
					<h3><b>Penawar : </b></h3>
					@foreach($_sewa->listpenawar as $penawars)
					<br/>
						@if($penawars->type_sewa == 'bandhire')
							<?php
								$penawar = App\Grupband::where('id',$penawars->subject_id)->get();
							?>
							@foreach($penawar as $penawarband)
								<a href={{url('band/'.$penawarband->slug)}}>{{$penawarband->nama_grupband}} </a>
								<p>Total Bayar : Rp. <b>{{$penawars->total_biaya}}</b></p>
								@if($penawars->status_request == '0')
									<p><a href={{url('confirmoffer-band/'.$penawars->id.'/'.$penawarband->slug)}}>Terima</a> | <a href={{url('canceloffer-band/'.$penawars->id.'/'.$penawarband->slug)}}>Tolak</a></p>
								@elseif($penawars->status_request == '1')
									<p>DITERIMA</p>
								@else
									<p>DITOLAK</p>
								@endif
							@endforeach
						@elseif($penawars->type_sewa == 'musisihire')
							<?php
								$penawar = App\Musician::where('id',$penawars->subject_id)->get();
							?>
							@foreach($penawar as $penawarmusisi)
								<a href={{url('musician/'.$penawarmusisi->slug)}}>{{$penawarmusisi->name}} </a>
								<p>Total Bayar : Rp. <b>{{$penawars->total_biaya}}</b></p>
								@if($penawars->status_request == '0')
									<p><a href={{url('confirmoffer-musisi/'.$penawars->id.'/'.$penawarmusisi->slug)}}>Terima</a> | <a href={{url('canceloffer-musisi/'.$penawars->id.'/'.$penawarmusisi->slug)}}>Tolak</a></p>
								@elseif($penawars->status_request == '1')
									<p>DITERIMA</p>
								@else
									<p>DITOLAK</p>
								@endif
							@endforeach
						@endif
					@endforeach
					</div>
				</div>
			@endforeach
		@endif
		
        </div>
    </div>
</div>
@endsection