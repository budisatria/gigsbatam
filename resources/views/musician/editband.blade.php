@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-heading">Edit - <a href={{ url('/band/'.$band->slug) }}>{{$band->nama_grupband}}</a>
					</div>
					<div class="panel-body">
						<form class="bootstrap-form-with-validation" action="save/{{$band->slug}}" method="POST" enctype="multipart/form-data">
							<div class="col-md-12">
								<div class="col-md-6">  
									<div class="form-group required">
										<label class="control-label">Nama Band</label>
										<input class="form-control" type="text" name="name" id="text-input" placeholder="Nama Band" required value="{{$band->nama_grupband}}">
									</div>
								</div>
								<div class="col-md-6">  
									<div class="form-group required">
										<label class="control-label">Basis Band</label>
										<input class="form-control" type="text" name="basis" id="text-input" placeholder="Nama Band" required value="{{$band->basis}}">
									</div>
								</div>
								<div class="col-md-6">  
									<div class="form-group required"> 
										<label class="control-label">Deskripsi</label>
										<input class="form-control" type="text" name="deskripsi" id="text-input" placeholder="Deskripsi Band" required value="{{$band->deskripsi}}">
									</div>
								</div>
								<div class="col-md-6"> 
									<div class="form-group required">
										<label class="control-label">Wilayah</label>
										<input class="form-control" type="text" name="Wilayah" id="text-input" placeholder="Wilayah" required value="{{$band->Wilayah}}">
									</div>
								</div>
								<div class="col-md-6"> 
									<div class="form-group required">
										<label class="control-label">Harga Sewa / Jam</label>
										<input class="form-control" type="text" name="harga" id="text-input" placeholder="Harga Sewa / Jam" required value="{{$band->harga}}">
									</div>
								</div>
								<div class="col-md-6"> 
									<div class="form-group">
										<label>Url Video</label>
										<input class="form-control" type="text" name="youtube" id="text-input" placeholder="Link Youtube" value="{{$band->youtube_video}}">
									</div>
								</div>
								<div class="col-md-6"> 
									<div class="form-group">
										<label>Link Website</label>
										<input class="form-control" type="text" name="web" id="text-input" placeholder="Link Website" value="{{$band->url_website}}">
									</div>
								</div>
								<div class="col-md-6"> 
									<div class="form-group">
										<label>Username Soundcloud</label>
										<input class="form-control" type="text" name="soundcloud" id="text-input" placeholder="Username SoundCloud" value="{{$band->username_soundcloud}}">
									</div>
								</div>
								<div class="col-md-6"> 
									<div class="form-group">
										<label>Username Reverbnation</label>
										<input class="form-control" type="text" name="reverb" id="text-input" placeholder="Username Reverbnation" value="{{$band->username_reverbnation}}">
									</div>
								</div>
								<div class="col-md-12"> 
									<div class="form-group">
										<div class="row">
			                                <?php $genre = App\Genre::all(); $cek ='';
			                                $genreband = App\GenreBand::join('genres', 'genre_bands.genre_id' ,'=', 'genres.id')->where('band_id', $band->id)->get();
			                                ?>

			                                @foreach($genre as $genres)
			                                    <div class="col-lg-2 col-md-12">
			                                        <div class="checkbox">
			                                        @if(!$genreband->isEmpty()) 
			                                            @foreach($genreband as $genrem)
			                                                <?php $genrems[] = $genrem->genre_id?>                                         
			                                            @endforeach

			                                            @if(in_array($genres->id, $genrems))
			                                                <?php $cek = ' checked'?>
			                                            @else
			                                                <?php $cek = ' unchecked'?>
			                                            @endif                                          
			                                            <label><input type="checkbox" name="checkbox[]" value="{{$genres->id}}" <?php echo $cek; ?>
			                                            >{{$genres->genre_name}}</label>
			                                        @else
			                                            <label><input type="checkbox" name="checkbox[]" value="{{$genres->id}}"<
			                                            >{{$genres->genre_name}}</label>
			                                        @endif
			                                        </div>
			                                    </div>
			                                    
			                                @endforeach
			                            </div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Add Photo :</label>
										<img name="cover" src={!! Cloudder::show($band->photo, array("crop" => "scale", "width" => 100, "height" => '')) !!}>
										<input name="photo" id="photo" type="file" class="btn">
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Add Cover :</label>
										<img name="cover" src={!! Cloudder::show($band->cover, array("crop" => "scale", "width" => 100, "height" => '')) !!}>
										<input name="cover" id="cover" type="file" class="btn">
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