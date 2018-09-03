@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Detail Withdraw</div>
                <div class="panel-body">
                    <div class="col-md-8">
                        <?php
                            if($wd->status == '0')
                                $status = 'Withdraw';
                            if($wd->status == '1')
                                $status = 'Dana telah ditransfer';
                        ?>
                        <p>Jumlah withdraw : {{$wd->jumlah}}</p>
                        <p>Saldo Akhir : {{$wd->saldo_akhir}}</p>
                    @if($saldos->type_pemilik == 'musisi')
                        <p>Dari Akun : {{$owners->name}}</p>
                    @else
                        <p>Dari Akun : {{$owners->nama_grupband}}</p>
                        <p>Admin Band : {{$owners->band->name }}</p>
                    @endif
                        <p>Type akun : {{$saldos->type_pemilik}}


                        {{ Form::open(['route'=>['edit.withdraw',$wd->id],'role'=> 'form', 'class' => 'ui reply form', 'enctype' => 'multipart/form-data']) }}
                            <div class="form-group"></div>
                            <label>Status Withdraw</label>
                            @if($wd->status == '0')
                                <select class="form-control" name="status" id="status">
                                    <option hidden value="{{$wd->status}}" selected>{{$status}}</option>
                                    <option value="0" >Withdraw</option>
                                    <option value="1" >Dana telah ditransfer</option>
                                </select>
                                <div class="form-group"></div>
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            @else
                                <select class="form-control" name="status" id="status" disabled="">
                                    <option hidden value="{{$wd->status}}" selected>{{$status}}</option>
                                    <option value="1" >Dana telah ditransfer</option>
                                </select>
                            @endif
                        {{ Form::close() }}
                    </div>

                    <div class="col-md-4">
                        @if($saldos->type_pemilik == 'musisi')
                        <?php 
                            $banks = App\BankMusisi::where('musician_id', $owners->id)->first();
                        ?>
                        @else
                            <?php
                                $banks = App\BankMusisi::where('musician_id', $owners->band->id)->first();
                            ?>
                        @endif


                        @if($banks != null)
                        <?php
                            $bank = App\Bank::where('id', $banks->bank_id)->first();
                        ?>
                            <h3>Informasi Rekening</h3>
                            <p>Nama Bank : {{$bank->nama_bank}} </p>
                            <p>No Rekening : {{$bank->no_rek}} </p>
                            <p>Atas Nama : {{$bank->atas_nama}} </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection