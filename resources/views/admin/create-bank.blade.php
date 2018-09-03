@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">BUAT BANK</div>
                <div class="panel-body">
                    @if(!$banks->isEmpty())
                         <?php $no=1;?>
                        @foreach($banks as $bank)                       
                            <p>{{$no++ }}. {{$bank->nama_bank}} - {{$bank->no_rek}} - {{$bank->atas_nama}}</p>
                        @endforeach
                    @endif
                    {{ Form::open(['route'=>['add.bank'],'role'=> 'form', 'class' => 'ui reply form', 'enctype' => 'multipart/form-data']) }}
                        <div class="form-group"></div>
                        <label>Nama Bank</label>
                            <input class="form-control" type="text" name="name" id="text-input" required>
                        <div class="form-group"></div>
                        <label>No Rekening</label>
                            <input class="form-control" type="text" name="norek" id="text-input" required>
                        <div class="form-group"></div>
                        <label>Atas Nama</label>
                            <input class="form-control" type="text" name="namaakun" id="text-input" required>
                        <div class="form-group"></div>
                        <label>Cabang Bank</label>
                            <input class="form-control" type="text" name="cabang" id="text-input" required>
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