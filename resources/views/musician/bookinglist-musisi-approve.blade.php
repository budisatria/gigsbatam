@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
         <ul class="nav nav-tabs">
            <li><a href="{{url('listsewa/musisi')}}">Request </a></li>
            <li class="active"><a href="{{url('listsewa/musisi/approve')}}">Approve </a></li>
            <li><a href="{{url('listsewa/musisi/selesai')}}">Selesai </a></li>
        </ul>

		@if(!$sewamusisi->isEmpty())
			@foreach($sewamusisi as $_sewamusisi)
				<div class="panel panel-default">
					<div class="panel-heading">Request dari <a href={{ url('/user/'.$_sewamusisi->organizer->slug) }}>{{$_sewamusisi->organizer->first_name}}</a>
					</div>
					<div class="panel-body">
						<?php 
						$datestart = date('d M Y', strtotime($_sewamusisi->gig->tanggal_mulai));
						$dateend = date('d M Y', strtotime($_sewamusisi->gig->tanggal_selesai));
						$timestart = explode(" ", $_sewamusisi->gig->tanggal_mulai);
						$timeout = explode(" ", $_sewamusisi->gig->tanggal_selesai);
						if($_sewamusisi->status_request == '0')
							$status = 'Menunggu konfirmasi';
						else
							$status = 'Diterima';
						?>
						<p>Di acara : {{$_sewamusisi->gig->nama_gig}}</p>
						<p>Lokasi : {{$_sewamusisi->gig->lokasi}}</p>
						<p>Dari : {{$datestart}} , {{$timestart[1]}}</p>
						<p>Sampai : {{$dateend}} , {{$timeout[1]}}</p>
						<br/>
						<p>Status Permintaan: <b>{{$status}}</b></p>

						@if($_sewamusisi->status_request == 1)
							@if($_sewamusisi->status == 0)
								<p>Status Booking : <b>Belum Bayar</b></p>
							@elseif($_sewamusisi->status == 1)
								<p>Status Booking : <b>Menunggu Verifikasi Pembayaran</b></p>
							@elseif($_sewamusisi->status == 2)
								<p>Status Booking : <b>LUNAS!</b></p>
							@elseif($_sewamusisi->status == 3)
								<p>Status Booking : <b>SELESAI!</b></p>
								<p><a href="">Berikan Review</a></p>
							@elseif($_sewamusisi->status == 4)
								<p>Status Booking : <b>SELESAI! Dana telah di transfer ke <a href={{ url('/user/'.$_sewamusisi->organizer->slug) }}>{{$_sewamusisi->organizer->first_name}}</a></b></p>
								<p><a href="">Berikan Review</a></p>
							@else
								<p>Status Booking : <b>BATAL!</b></p>
							@endif
							<h3>Total Bayar : Rp. <b>{{$_sewamusisi->total_biaya}}</b></h3>
						@endif
					</div>
				</div>
			@endforeach	
		@endif
		
        </div>
    </div>
</div>
@endsection