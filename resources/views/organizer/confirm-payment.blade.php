@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Konfirmasi Pembayaran - <a href={{url('gig/'.$konfirm->gig->slug)}}>{{$konfirm->gig->nama_gig}}</a></div>

                <div class="panel-body">
                    <p>Sudah ditransfer sebesar <b>Rp. {{$konfirm->total_biaya}}</b></p>
                    {{ Form::open(['route'=>['add.confirmpayment',$konfirm->id],'role'=> 'form', 'class' => 'ui reply form', 'enctype' => 'multipart/form-data']) }}
                    <?php $bank = App\Bankadmin::all();?>
                            <p><b>Ke Bank: </b></p>
                            <select class="form-control" name="bank" id="bank" required>
                                @foreach($bank as $_bank)                               
                                    <option value="{{$_bank->id}}">{{$_bank->nama_bank}} - {{$_bank->no_rek}} - {{$_bank->atas_nama}}</option>
                                @endforeach
                            </select>
                            
                            <div class="form-group required">
                            <br/>
                            <p><b>Dari Bank: </b></p>
                            <label class="control-label">Nomor Rekening Pengirim</label>
                                <input class="form-control" type="text" name="no_rek" id="no_rek" placeholder="Nomor rekening pengirim" required>
                                </div>
                            <div class="form-group required">
                            <label class="control-label">Nama Pengirim</label>
                                <input class="form-control" type="text" name="nama_rek" id="nama_rek" placeholder="Nama rekening pengirim" required>
                                </div>
                            <div class="form-group required">
                            <label class="control-label">Nama Bank Pengirim </label>
                                <input class="form-control" type="text" name="nama_bank" id="nama_bank" placeholder="eg. BCA" required>
                                </div>
                            <div class="form-group">
                            <label class="control-label">Photo Bukti Pembayaran:</label>
                                <input name="photo" id="photo" type="file" class="btn">
                            </div>
                            <div class="form-group"></div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Konfirmasi</button>
                            </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection