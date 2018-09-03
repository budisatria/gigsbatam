@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-heading">Edit - <a href={{ url('/user/'.$organizer->slug) }}>{{$organizer->first_name}} {{$organizer->last_name}}</a>
					</div>
					<div class="panel-body">
						<form class="bootstrap-form-with-validation" action="save/{{$organizer->slug}}" method="POST" enctype="multipart/form-data">
							<div class="col-md-12">
								<div class="col-md-6">  
									<div class="form-group">
										<label>First Name</label>
										<input class="form-control" type="text" name="first_name" id="text-input" placeholder="Nama Band" required value="{{$organizer->first_name}}">
									</div>
								</div>
								<div class="col-md-6">  
									<div class="form-group"> 
										<label>Last Name</label>
										<input class="form-control" type="text" name="last_name" id="text-input" placeholder="Deskripsi Band" required value="{{$organizer->last_name}}">
									</div>
								</div>
								<div class="col-md-6"> 
									<div class="form-group">
										<label>Email</label>
										<input class="form-control" type="text" name="email" id="text-input" placeholder="Wilayah" required value="{{$organizer->email}}">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Add Photo :</label>
										<img name="cover" src={!! Cloudder::show($organizer->photo, array("crop" => "scale", "width" => 100, "height" => '')) !!}>
										<input name="photo" id="photo" type="file" class="btn">
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
							<button type="submit" class="btn btn-success">Simpan</button>
						</div>
						</form>
					</div>
				</div>
        </div>
    </div>
</div>
@endsection