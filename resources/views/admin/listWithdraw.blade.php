@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">List Withdraw ( {{$withdraw->count()}} )</div>
				<div class="panel-body">
					<table class="table table-bordered">
					    <thead>
					      <tr>
					      	<?php $i =1; ?>
					        <th>No.</th>
					        <th>Jumlah Withdraw</th>
					        <th>Saldo Akhir</th>
					        <th>Dari akun</th>
					        <th>Jenis akun</th>
					        <th>Status</th>
					        <th>Tgl. Pengajuan</th>
					        <th>Aksi</th>
					      </tr>
					    </thead>
					    <tbody>
							@if(!$withdraw->isEmpty())	
								@foreach($withdraw as $wd)
									<?php
										if($wd->status == '0')
											$status = 'Withdraw';
										if($wd->status == '1')
											$status = 'Dana telah ditransfer';
									?>
									<tr>
										<td>{{$i++}}</td>
										<td>{{$wd->jumlah}}</td>
										<td>{{$wd->saldo_akhir}}</td>
									@if($wd->saldo->type_pemilik == 'musisi')
										<td>{{$wd->owner->name}}</td>
									@else
										<td>{{$wd->owner->nama_grupband}}</td>
									@endif
										<td>{{$wd->saldo->type_pemilik}}</td>
										<td>{{$status}}</td>
										<td>{{$wd->created_at}}</td>
										<td><a href={{url('admin/edit/withdraw/'.$wd->id)}}>Edit</td>
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