@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
	<?php $bankakun = App\Musician::join('bank_musisi', 'musicians.id','=','bank_musisi.musician_id')
                                ->join('banks', 'bank_musisi.bank_id','=','banks.id')
                                ->where('musicians.id', Auth::guard('musician')->user()->id)->first();
    ?>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-heading">Edit Akun - <a href={{ url('/musician/'.Auth::guard('musician')->user()->slug) }}>{{ Auth::guard('musician')->user()->name }}</a>
					</div>
					<div class="panel-body">
						<form class="bootstrap-form-with-validation" action="musician/akun/update" method="POST">
							<div class="col-md-12">

								<div class="col-md-6">  
									<div class="form-group">
										<label>Email</label>
										<input class="form-control" type="email" name="email" id="text-input" placeholder="Email" required value="{{ Auth::guard('musician')->user()->email }}" disabled=>
									</div>
								</div>
								<div class="col-md-6">  
									<div class="form-group">
										<label>No.HP</label>
										<input class="form-control" type="text" name="no_telp" id="text-input" placeholder="No HP" required value="{{ Auth::guard('musician')->user()->no_telp }}">
									</div>
								</div>
								<div class="col-md-6">  
									<div class="form-group"> 
										<label>Nomor Rekening</label>
										<input class="form-control" type="text" name="no_rek" id="text-input" placeholder="No. Rekening" required @if($bankakun != null) value="{{ $bankakun->no_rek }}" @endif>
									</div>
								</div>
								<div class="col-md-6"> 
									<div class="form-group">
										<label>Nama Pemilik Rekening</label>
										<input class="form-control" type="text" name="atas_nama" id="text-input" placeholder="Nama pemilik" required @if($bankakun != null) value="{{ $bankakun->atas_nama }}" @endif>
									</div>
								</div>
								<div class="col-md-6"> 
									<div class="form-group">
										<label>Nama Bank</label>
										<input class="form-control" type="text" name="nama_bank" id="text-input" placeholder="Nama Bank" required @if($bankakun != null) value="{{ $bankakun->nama_bank }}" @endif>
									</div>
								</div>
								
							</div>
						</div>
						<div class="modal-footer">
							<div class="col-md-2">
								<a class="btn btn-primary" href="{{ url('/my-password') }}">Change Password</a>
							</div>
							<div class="col-md-10">
								<button type="submit" class="btn btn-success">Simpan</button>
							</div>
						</div>
						</form>
					</div>
				</div>
        </div>
    </div>
</div>
@endsection