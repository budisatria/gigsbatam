@extends('layouts.app')

@section('content')
<div class="container">
@if (Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">
                @if($sewas->type_sewa == 'hireband')
                    Berikan Review Kepada - <a href={{url('band/'.$sewas->first()->slug)}}>{{$sewas->first()->nama_grupband}}</a>
                @else
                    Berikan Review Kepada - <a href={{url('musician/'.$sewas->first()->slug)}}>{{$sewas->first()->name}}</a>
                @endif
                </div>

                <div class="panel-body">
                    <p>Dalam partisipasinya pada Gig : {{$sewas->gig->nama_gig}}</p>
                    <br/> 
                    {{ Form::open(['route'=>['review.new',$sewas->id],'role'=> 'form','class' => 'clearfix']) }}                           
                    <div class="form-group required">
                        <label class="col-md-4 control-label">Pesan Review</label>                          
                        {!! Form::text('pesan',null,['required' => 'required','class' => 'form-control']) !!}
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Nilai Review</label>
                        <br/>
                        <div id="rateYo"></div>
                        <input type="hidden" name="rate" id="rate" class="form-control">
                        
                    </div>

                    <div class="form-group">
                        <button class="btn btn-primary" type="submit">Kirim Review</button>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection