@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">nama organizer: {{ $organizer->first_name }}
                @if(Auth::guard('user')->user()) 
                    @if(Auth::guard('user')->user()->id == $organizer->id)
                        <a href={{ url('user/edit/'.$organizer->slug) }}>edit</a>
                    @endif
                @endif
                </div>

                <div class="panel-body">
                <img name="aboutme" class="img-circle" src={!! Cloudder::show($organizer->photo) !!}>
                    @if(!Auth::guard('user')->user())
                       This is {{$organizer->first_name}} Profile pages
                    @else
                        @if(Auth::guard('user')->user()->id == $organizer->id)
                            Your Profile Organizer's Page
                        @else
                            This is {{$organizer->first_name}} Profile page
                        @endif
                    @endif
                     
                </div>
            </div>

            @foreach($gig as $_gig)
                <div class="panel panel-default">
                    <div class="panel-heading"><a href={{url('gig/'.$_gig->slug)}}>{{$_gig->nama_gig}}</a></div>
                    <div class="panel-body">
                        <p>{{$_gig->deskripsi}}</p>
                    </div>
                </div>
            @endforeach


        </div>
    </div>
</div>
@endsection