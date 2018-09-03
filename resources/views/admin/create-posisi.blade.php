@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">BUAT Posisi</div>
                <div class="panel-body">
                @if(!$posisis->isEmpty())
                     <?php $no=1;?>
                    @foreach($posisis as $posisi)                       
                        <p>{{$no++ }}. {{$posisi->position_name}}</p>
                    @endforeach
                @endif
                    {{ Form::open(['route'=>['add.posisi'],'role'=> 'form', 'class' => 'ui reply form', 'enctype' => 'multipart/form-data']) }}
                        <div class="form-group"></div>
                        <label>Nama Posisi</label>
                            <input class="form-control" type="text" name="name" id="text-input" required>
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
@endsection