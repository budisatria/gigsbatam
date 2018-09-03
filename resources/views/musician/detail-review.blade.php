@extends('layouts.app')

@section('content')
<div class="container">
<h1 class="text-center">Reviews</h1>
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    @foreach($review as $_review)
        <?php
            $user = App\User::where('id', $_review->user_id)->first();
        ?>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="panel panel-default">
                    <div class="panel-heading">Review dari: <a href={{ url('musician/'.$user->slug) }}>{{ $user->first_name }}</a>
                    <p><a href={{ url('gig/'.$_review->slug) }}>{{$_review->nama_gig}}</a></p>
                    </div>

                    <div class="panel-body">                   
                        <p>{{$_review->pesan}}</p>
                        <p>
                            <?php 
                                for($i=0;$i<$_review->nilai;$i++)
                                    echo "<i class='fa fa-star'></i>";
                            ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection