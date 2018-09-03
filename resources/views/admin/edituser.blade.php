@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">Detail User - <a href={{url('user/'.$user->slug)}}>{{$user->first_name}} {{$user->last_name}}</a></div>
                <div class="panel-body">
                    <div class="col-md-8">
                        <p>First Name : {{$user->first_name}}</p>
                        <p>Last Name : {{$user->last_name}}</p>
                        <p>Email: {{$user->email}}</p>
                        {{ Form::open(['route'=>['edit.user',$user->id],'role'=> 'form', 'class' => 'ui reply form', 'enctype' => 'multipart/form-data']) }}
                            <div class="form-group"></div>
                            <label>Aktif</label>

                            <select class="form-control" name="aktif" id="aktif">
                                <option hidden value="{{$user->aktif}}" selected>{{$user->aktif}}</option>
                                <option value="Y" >Y</option>
                                <option value="N" >N</option>
                            </select>
                            <div class="form-group"></div>
                            <div class="form-group">
                                <button class="btn btn-primary" type="submit">Submit</button>
                            </div>
                        {{ Form::close() }}
                    </div>
                    <div class="col-md-4">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection