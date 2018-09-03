@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Detail Musisi - <a href={{url('musician/'.$musisi->slug)}}>{{$musisi->name}}</a></div>
                <div class="panel-body">
                    <div class="col-md-8">
                        <p>Name : {{$musisi->name}}</p>
                        <p>Email: {{$musisi->email}}</p>
                        <p>No.Hp : {{$musisi->no_telp}}</p>
                        <p>Wilayah : {{$musisi->Wilayah}}</p>
                        <p>Tipe : {{$musisi->tipe}}</p>
                        <p>Basis : {{$musisi->basis}}</p>
                        <p>Deskripsi : {{$musisi->deskripsi}}</p>
                        <p>Harga Sewa /Jam : Rp. {{$musisi->harga_sewa}}</p>
                        <p>Genre : 
                        @foreach($musisi->genre as $genre)
                            {{$genre->genre_name}} | 
                        @endforeach
                        </p>
                        {{ Form::open(['route'=>['edit.musisi',$musisi->id],'role'=> 'form', 'class' => 'ui reply form', 'enctype' => 'multipart/form-data']) }}
                            <div class="form-group"></div>
                            <label>Aktif</label>

                            <select class="form-control" name="aktif" id="aktif">
                                <option hidden value="{{$musisi->aktif}}" selected>{{$musisi->aktif}}</option>
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
                        <img src={!! Cloudder::show($musisi->photo , ['format' => 'jpg', 'crop' => 'fit', 'width' => '200', 'height'=> '200']) !!} alt="Logo" class="widthfull">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection