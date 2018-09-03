@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Detail Order</div>
                <div class="panel-body">
                    <div class="col-md-6">
                        <?php
                            if($orders->status == '0')
                                $status = 'Belum Bayar';
                            if($orders->status == '1')
                                $status = 'Menunggu Verifikasi Pembayaran';
                            if($orders->status == '2')
                                $status = 'Lunas';
                            if($orders->status == '3')
                                $status = 'Selesai';
                            if($orders->status == '4')
                                $status = 'Selesai! Saldo telah ditambahkan';
                            if($orders->status == '5')
                                $status = 'Batal';
                        ?>
                        <p>Gig : {{$orders->gig->nama_gig}}</p>
                    @if($orders->status_request == '0')
                        <p>Status Request : Menunngu Konfirmasi</p>
                    @elseif($orders->status_request == '1')                  
                        <p>Status Request : Diterima</p>
                    @else
                        <p>Status Request : Ditolak</p>
                    @endif
                        <p>Mulai : {{$orders->gig->tanggal_mulai}}</p>
                        <p>Selesai : {{$orders->gig->tanggal_selesai}}</p>
                        <p>Type Sewa : {{$orders->type_sewa}}</p>
                    @if($orders->type_sewa == 'hireband' || $orders->type_sewa == 'hiremusisi')
                        <p>Penyewa : {{$orders->sbj->first_name}}</p>
                    @endif
                    @if($orders->type_sewa == 'hireband')
                        <p>Disewa : {{$orders->obj->nama_grupband}}</p>
                    @elseif($orders->type_sewa == 'hiremusisi')
                        <p>Disewa : {{$orders->obj->name}}</p>
                    @endif
                        <p>Total Bayar : Rp. {{$orders->total_biaya}}</p>

                    @if($orders->status == '6')
                        <h3>Penarikan </h3>
                        <p>Nomor Rekening : {{$banks->no_rek}}</p>
                        <p>Nama Rekening : {{$banks->nama_rek}} </p>
                        <p>Nama Bank : {{$banks->nama_bank}} </p>
                        <p>Bank Tujuan : {{$bankadmin->nama_bank}} </p>
                    @endif

                        {{ Form::open(['route'=>['edit.order',$orders->id],'role'=> 'form', 'class' => 'ui reply form', 'enctype' => 'multipart/form-data']) }}
                            <div class="form-group"></div>
                            <label>Status Sewa</label>
                            @if($orders->status == '4')
                                <select class="form-control" name="status" id="status" readonly>
                                    <option hidden value="{{$orders->status}}" selected>{{$status}}</option>
                                </select>
                            @elseif($orders->status == '1')
                                <select class="form-control" name="status" id="status">
                                    <option hidden value="{{$orders->status}}" selected>{{$status}}</option>
                                    <option value="0" >Belum Bayar</option>
                                    <option value="1" >Menunggu Verifikasi Pembayaran</option>
                                    <option value="2" >Lunas</option>
                                </select>
                                <div class="form-group"></div>
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            @else
                                <select class="form-control" name="status" id="status">
                                    <option hidden value="{{$orders->status}}" selected>{{$status}}</option>
                                    <option value="0" >Belum Bayar</option>
                                    <option value="1" >Menunggu Verifikasi Pembayaran</option>
                                    <option value="2" >Lunas</option>
                                    <option value="3" >Selesai</option>
                                    <option value="4" >Selesai! Saldo telah ditambahkan</option>
                                    <option value="5" >batal</option>
                                </select>
                                <div class="form-group"></div>
                                <div class="form-group">
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            @endif
 
                    {{ Form::close() }}
                    </div>
                    <div class="col-md-6">
                        <?php 
                            $banks = App\KonfirmasiPembayaran::where('sewa_id', $orders->id)->first();
                        ?>
                        @if($banks != null)
                        <?php
                            $bankadmin = App\Bankadmin::where('id', $banks->bank_admin_id)->first();
                        ?>
                            <h3>Konfirmasi Pembayaran</h3>
                            <p>Nomor Rekening : {{$banks->no_rek}}</p>
                            <p>Nama Rekening : {{$banks->nama_rek}} </p>
                            <p>Nama Bank : {{$banks->nama_bank}} </p>
                            <p>Bank Tujuan : {{$bankadmin->nama_bank}} </p>
                            <h3>Bukti Pembayaran</h3>
                            <img name="buktipembayaran" src={!! Cloudder::show($banks->photo, array("crop" => "scale", "width" => 350, "height" => 350)) !!}>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection