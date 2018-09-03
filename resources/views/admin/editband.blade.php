@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Detail Band - <a href={{url('band/'.$band->slug)}}>{{$band->nama_grupband}}</a></div>
                <div class="panel-body">
                    <div class="col-md-8">
                        <p>Name : {{$band->nama_grupband}}</p>
                        <p>Wilayah : {{$band->Wilayah}}</p>
                        <p>Tipe : {{$band->tipe}}</p>
                        <p>Basis : {{$band->basis}}</p>
                        <p>Deskripsi : {{$band->deskripsi}}</p>
                        <p>Harga Sewa /Jam : Rp. {{$band->harga}}</p>
                        <p>Genre : 
                        @foreach($band->genre as $genre)
                            {{$genre->genre_name}} | 
                        @endforeach
                        </p>
                        <p>Admin Band : <a href={{url('musician/'.$band->admin->slug)}}>{{$band->admin->name}}</a></p>
                        {{ Form::open(['route'=>['edit.band',$band->id],'role'=> 'form', 'class' => 'ui reply form', 'enctype' => 'multipart/form-data']) }}
                            <div class="form-group"></div>
                            <label>Aktif</label>

                            <select class="form-control" name="aktif" id="aktif">
                                <option hidden value="{{$band->aktif}}" selected>{{$band->aktif}}</option>
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
                        <img src={!! Cloudder::show($band->photo , ['format' => 'jpg', 'crop' => 'fit', 'width' => '200', 'height'=> '200']) !!} alt="Logo" class="widthfull">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection