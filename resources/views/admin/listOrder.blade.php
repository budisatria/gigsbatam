@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">List Order ( {{$orders->count()}} )</div>
				<div class="panel-body">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					        <th>Gig</th>
					        <th>Biaya</th>
					        <th>Type</th>
					        <th>Penyewa</th>
					        <th>Disewa</th>
					        <th>Status</th>
					        <th>Aksi</th>
					      </tr>
					    </thead>
					    <tbody>
							@if(!$orders->isEmpty())	
								@foreach($orders as $order)
									<?php
										if($order->status == '0')
											$status = 'Belum Bayar';
										if($order->status == '1')
											$status = 'Menunggu Verifikasi Pembayaran';
										if($order->status == '2')
											$status = 'Lunas';
										if($order->status == '3')
											$status = 'Selesai';
										if($order->status == '4')
											$status = 'Selesai! Saldo telah ditambahkan';
										if($order->status == '5')
											$status = 'Batal';
									?>
									<tr>
										<td>{{$order->gig['0']->nama_gig}}</td>
										<td>{{$order->total_biaya}}</td>
										<td>{{$order->type_sewa}}</td>
									@if($order->type_sewa == 'hireband' || $order->type_sewa == 'hiremusisi')
										<td>{{$order->sbj['0']->first_name}}</td>
									@elseif($order->type_sewa == 'bandhire' || $order->type_sewa == 'musisihire')
										<td>{{$order->obj['0']->first_name}}</td>
									@endif
									@if($order->type_sewa == 'hireband')
										<td>{{$order->obj['0']->nama_grupband}}</td>
									@elseif($order->type_sewa == 'hiremusisi')
										<td>{{$order->obj['0']->name}}</td>
									@elseif($order->type_sewa == 'musisihire')
										<td>{{$order->sbj['0']->name}}</td>
									@elseif($order->type_sewa == 'bandhire')
										<td>{{$order->sbj['0']->nama_grupband}}</td>
									@endif
										<td>{{$status}}</td>
										<td><a href={{url('admin/edit/order/'.$order->id)}}>Edit</td>
									</tr>
								@endforeach
							@endif
					    </tbody>
					</table>
				</div>
			</div>
        </div>
    </div>
</div>
@endsection