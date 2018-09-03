@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">SEWA MUSISI - <a href={{url('musician/'.$musisis->slug)}}>{{$musisis->name}}</a></div>

                <div class="panel-body">
                    {{ Form::open(['route'=>['add.sewamusisi',$musisis->slug],'role'=> 'form', 'class' => 'ui reply form', 'enctype' => 'multipart/form-data']) }}
                            <div id="map"></div>
                            <h2 class="text-center"></h2>
                            <div class="form-group required">
                            <label class="control-label">Nama Acara</label>
                                <input class="form-control" type="text" name="name" id="text-input" required>
                                </div>
                            <div class="form-group required">
                            <label class="control-label">Lokasi Acara </label>
                                <input class="form-control" type="text" name="lokasi" id="lokasi" required>
                                </div>
                            <div class="form-group required">
                            <label class="control-label">Detail Lokasi</label>
                                <input class="form-control" type="text" name="detail_lokasi" id="text-input" required>
                                </div>
                            <div class="form-group required">
                            <label class="control-label">Deskripsi Acara</label>
                                <input class="form-control" type="text" name="deskripsi" id="text-input" required>
                                </div>
                            <div class="form-group required">
                            <label class="control-label">Waktu Mulai </label>
                                <input class="form-control" type="text" name="mulai" id="mulai" required>
                                </div>
                            <div class="form-group required">
                            <label class="control-label">Waktu Selesai </label>
                                <input class="form-control" type="text" name="selesai" id="selesai" required>
                                </div>
                            <div class="form-group"></div>
                            <label>Add Photo :</label>
                                <input name="photo" id="photo" type="file" class="btn">
                            <div class="form-group"></div>
                                <input class="form-control" type="hidden" name="lat" id="lat">
                            <div class="form-group"></div>
                            
                                <input class="form-control" type="hidden" name="lng" id="lng">
                            <div class="form-group"></div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>

    <script>
    var latx = document.getElementById("lat");
    var lngx = document.getElementById("lng");

    </script>

     <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBpCtQnxHIl0odalU4P2Ss2epKWEDz80P8&libraries=places&callback=initAutocomplete"
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
<!-- <script type="text/javascript">
    var wmulai = document.getElementById('mulai');
    var wselesai = document.getElementById('selesai');

    if (wmulai) {}
</script> -->
@endsection