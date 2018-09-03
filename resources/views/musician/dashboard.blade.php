@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-center">Discover Gig</h1><br>
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
        @if(!$gigs->isEmpty())  
            @foreach($gigs as $gig)
                <div class="panel panel-default">
                    <div class="panel-heading"><a href={{ url('/gig/'.$gig->slug) }}>{{$gig->nama_gig}}</a> - <span>Lokasi : {{$gig->detail_lokasi}}</span>
                    </div>
                    <div class="panel-body">
                        <p>Deskripsi :{{$gig->deskripsi}}</p>
                        <p>Tanggal Main :{{$gig->tanggal_mulai}} Sampai {{$gig->tanggal_selesai}}</p>                                            
                    </div>
                </div>
            @endforeach
        @else
            <p class="center">Tidak ada Gig</p>
        @endif
        </div>
    </div>
</div>
@endsection