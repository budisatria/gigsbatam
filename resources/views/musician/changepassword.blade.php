@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-heading">Change Password - <a href={{ url('/musician/'.Auth::guard('musician')->user()->slug) }}>{{ Auth::guard('musician')->user()->name }}</a>
					</div>
					<div class="panel-body">
						<form class="bootstrap-form-with-validation" action="musician/password/update" method="POST">
							<div class="col-md-12">

								<div class="col-md-6">  
									<div class="form-group"> 
										<label>Password Lama</label>
										<input class="form-control" type="password" name="pass" id="text-input" placeholder="old password" required>
									</div>
								</div>
								<div class="col-md-6"> 
									<div class="form-group">
										<label>Password Baru</label>
										<input class="form-control" type="password" name="newpass" id="text-input" placeholder="New Password" required >
									</div>
								</div>
								<div class="col-md-6"> 
									<div class="form-group">
										<label>Konfirmasi Password</label>
										<input class="form-control" type="password" name="confirmpass" id="text-input" placeholder="Confirm new pass" required >
									</div>
								</div>
								
							</div>
						</div>
						<div class="modal-footer">	
							<button type="submit" class="btn btn-success">Simpan</button>
						</div>
						</form>
					</div>
				</div>
        </div>
    </div>
</div>
@endsection