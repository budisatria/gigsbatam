@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Data Add</div>

                <div class="panel-body">
                    <P><a href="{{url('admin/create-genre')}}">Tambah Genre</a></P>
                    <br/>
                    <P><a href="{{url('admin/create-posisi')}}">Tambah Posisi</a></P>
                    <br/>
                    <P><a href="{{url('admin/create-bank')}}">Tambah Bank</a></P>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection