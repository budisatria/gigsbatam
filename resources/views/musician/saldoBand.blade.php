@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
         <ul class="nav nav-tabs">
            <li><a href={{url('musician/saldo/'.Auth::guard('musician')->user()->slug)}}>Saldo Musisi </a></li>
            <li class="active"><a href={{url('listband/saldo/'.Auth::guard('musician')->user()->slug)}}>Saldo Band </a></li>
        </ul>

        @if($detail == null)
			<div class="panel panel-default">
				<div class="panel-heading">
					Saldo <a href={{url('band/'.$bands->slug)}}>{{$bands->nama_grupband}}</a> : Rp. 0
					@if($bands->admin_id == Auth::guard('musician')->user()->id)
						<a href="#" class="btn btn-primary" disabled>Tarik Dana</a>
					@endif
				</div>
				<div class="panel-body">
					<p>Tidak ada detail saldo</p>
				</div>
			</div>
        @endif

		@if($saldoband != null)
			<div class="panel panel-default">
				<div class="panel-heading">
					Saldo <a href={{url('band/'.$bands->slug)}}>{{$bands->nama_grupband}}</a> : Rp. {{$saldoband->saldo}}
					@if($saldoband->saldo < 10000)
						@if($bands->admin_id == Auth::guard('musician')->user()->id)
							<a href="#" class="btn btn-primary" disabled>Tarik Dana</a>
						@endif
					@else
						@if($bands->admin_id == Auth::guard('musician')->user()->id)
							<a href="#" class="btn btn-primary" data-toggle="modal" data-target="#modalWithdraw">Tarik Dana</a>
						@endif
					@endif
				</div>
				<div class="panel-body">
					@foreach($detail as $trx)						
						@foreach($trx->listsewa as $sewa)
							<b>{{$sewa->gig[0]->nama_gig}}</b>
							<p>{{$sewa->type_sewa}}</p>
							<p><b>+</b> Rp. {{$sewa->total_biaya}}</p>
							<hr/>
						@endforeach
					@endforeach
				</div>
			</div>
		@endif
		
        </div>
    </div>
</div>

@if($saldoband != null)
	<!-- MODAL Add ANGGOTA BAND -->
	@if($bands->admin_id == Auth::guard('musician')->user()->id)
		<div class="modal  fade" id="modalWithdraw" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
		  <div class="modal-dialog" role="document">
			<div class="modal-content">

			  <div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="myModalLabel">Tarik Dana Dari Saldo - {{$bands->nama_grupband}}</h4>
			  </div>
			
			  <div class="modal-body" id="modaladd">
				{{ Form::open(['route'=>['add.withdraw',$saldoband->id],'role'=> 'form', 'class' => 'ui reply form']) }}
					<div class="col-md-12">
						<div class="col-md-6">  
	                    	<div class="form-group">
	                    		<label>Saldo Tersedia</label> 
								<input type="number" class="form-control" value="{{$saldoband->saldo}}" readonly>
	                    	</div>
	                    </div>
	                	<div class="col-md-12">  
	                    	<div class="form-group">
	                    		<label>Total Penarikan</label> 
								<input type="number" class="form-control" name="jumlah" required="required" min="10000" max="{{$saldoband->saldo}}">
							</div>
						</div>
					</div>

			  </div>
				
				<div class="modal-footer">
					<button type="submit" class="btn btn-success">Kirim</button>
					{{ Form::close() }}				
				</div>

			</div>
		  </div>
		</div>
	@endif
@endif
@endsection