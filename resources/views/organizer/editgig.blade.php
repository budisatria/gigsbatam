@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
			<div class="panel panel-default">
				<div class="panel-heading">Edit - <a href={{ url('/gig/'.$gig->slug) }}>{{$gig->nama_gig}}</a>
				</div>
				<div class="panel-body">
                    <form class="bootstrap-form-with-validation" action="save/{{$gig->slug}}" method="POST" enctype="multipart/form-data">
                            <div id="map"></div>
                            <h2 class="text-center"></h2>
                            <div class="form-group required">
                            <label class="control-label">Nama Acara</label>
                                <input class="form-control" type="text" name="nama_gig" id="text-input" value="{{$gig->nama_gig}}" required>
                            </div>
                            <div class="form-group required">
                            <label class="control-label">Lokasi Acara </label>
                                <input class="form-control" type="text" name="lokasi" id="lokasi" value="{{$gig->lokasi}}" required>
                            </div>
                            <div class="form-group required">
                            <label class="control-label">Detail Lokasi</label>
                                <input class="form-control" type="text" name="detail_lokasi" id="text-input" value="{{$gig->detail_lokasi}}" required>
                            </div>
                            <div class="form-group required">
                            <label>Deskripsi Acara</label>
                                <input class="form-control" type="text" name="deskripsi" id="text-input" value="{{$gig->deskripsi}}" required>                   
                            </div>
                            <div class="form-group required">
                            <label>Waktu Mulai </label>
                                <input class="form-control" type="text" name="tanggal_mulai" id="mulai" value="{{$gig->tanggal_mulai}}" required>
                            </div>
                            <div class="form-group">
                            <label class="control-label">Waktu Selesai </label>
                                <input class="form-control" type="text" name="tanggal_selesai" id="selesai" value="{{$gig->tanggal_selesai}}" required>
                            </div>
                            <div class="form-group"></div>
                            <label>Add Photo :</label>
                            	<img name="photo" src={!! Cloudder::show($gig->photo_gig, array("crop" => "scale", "width" => 100, "height" => '')) !!}>
                                <input name="photo" id="photo" type="file" class="btn">
                            <div class="form-group"></div>
                                <input class="form-control" type="hidden" name="lat" id="lat">
                            <div class="form-group"></div>
                            
                            <input class="form-control" type="hidden" name="lng" id="lng">
                            <div class="form-group"></div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                    </form>
                </div>
			</div>
        </div>
    </div>
</div>

    <script>
    var latx = document.getElementById("lat");
    var lngx = document.getElementById("lng");

    </script>

     <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBsYEIyuLzbigTP2q9K9ONkVfqxJHNy4cE=places&callback=initAutocomplete"
 async defer></script>
<script type="text/javascript">
function initAutocomplete() {
    var places = new google.maps.places.Autocomplete(document.getElementById('lokasi'));
    google.maps.event.addListener(places, 'place_changed', function () {
        var place = places.getPlace();
        var address = place.formatted_address;
        var latitude = place.geometry.location.lat();
        var longitude = place.geometry.location.lng();
        latx.value = latitude;
        lngx.value = longitude;
    });
}
</script>
@endsection