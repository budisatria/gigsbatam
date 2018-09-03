@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Detail Gig - <a href={{url('gig/'.$gig->slug)}}>{{$gig->nama_gig}}</a></div>
                <div class="panel-body">
                    <div class="col-md-8">
                        <p>Title : {{$gig->nama_gig}}</p>
                        <p>Deskripsi : {{$gig->deskripsi}}</p>
                        <p>Lokasi : {{$gig->lokasi}}</p>
                        <p>Detail Lokasi : {{$gig->detail_lokasi}}</p>
                        <p>Tanggal Mulai : {{$gig->tanggal_mulai}}</p>
                        <p>Tanggal Selesai : {{$gig->tanggal_selesai}}</p>
                        <p>Type Gig : {{$gig->type_gig}}</p>
                        <p>Owner Gig : {{$gig->owner->first_name}}</p>

                        {{ Form::open(['route'=>['edit.gig',$gig->id],'role'=> 'form', 'class' => 'ui reply form', 'enctype' => 'multipart/form-data']) }}
                            <div class="form-group"></div>
                            <label>Aktif</label>

                            <select class="form-control" name="aktif" id="aktif">
                                <option hidden value="{{$gig->aktif}}" selected>{{$gig->aktif}}</option>
                                <option value="Y" >Y</option>
                                <option value="N" >N</option>
                            </select>
                            <div class="form-group"></div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        {{ Form::close() }}
                    </div>
                    <div class="col-md-4">
                        <img src={!! Cloudder::show($gig->photo , ['format' => 'jpg', 'crop' => 'fit', 'width' => '200', 'height'=> '200']) !!} alt="Logo" class="widthfull">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection