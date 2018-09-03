@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-md-offset-3">
                <div class="row">
                    <div class="col-md-8 col-md-offset-2">
                        <div>
                        <div class="panel panel-default">
                            <ul class="nav nav-tabs panel-body">
                                <li class="active"><a style="padding-left: 50px; padding-right: 50px;" href="#tab-2" role="tab" data-toggle="tab">Organizer</a></li>
                                <li ><a style="padding-left: 50px; padding-right: 50px;" href="#tab-1" role="tab" data-toggle="tab">Musisi </a></li>
                                
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane" role="tabpanel" id="tab-1">
                                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/musician/register') }}">
                                    {{ csrf_field() }}                                        
                                         <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} required" id='register-name' >
                                         <br>
                                            <label class="col-md-4 control-label">Name</label>

                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Full Name" required>

                                                @if ($errors->has('name'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} required">
                                            <label class="col-md-4 control-label">E-Mail Address</label>

                                            <div class="col-md-6">
                                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email Address" required>

                                                @if ($errors->has('email'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} required">
                                            <label class="col-md-4 control-label">Password</label>

                                            <div class="col-md-6">
                                                <input type="password" class="form-control" name="password" placeholder="Password" required>

                                                @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }} required">
                                            <label class="col-md-4 control-label">Confirm Password</label>

                                            <div class="col-md-6">
                                                <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>

                                                @if ($errors->has('password_confirmation'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-md-offset-4">
                                                <button class="btn btn-black btn-block" type="submit">Register Musisi </button>
                                            </div>
                                        </div>

                                        <div class="col-sm-12">
                                           @if (count($errors) > 0)
                                                <div class="alert alert-danger">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>

                                    </form>
                                </div> 
                                <div class="tab-pane  active" role="tabpanel" id="tab-2">
                                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/register') }}">
                                    {{ csrf_field() }}                                        
                                        <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }} required" id='register-first-name'>
                                        <br>
                                            <label class="col-md-4 control-label">First Name</label>

                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="first_name" value="{{ old('first_name') }}" placeholder="First Name" required>

                                                @if ($errors->has('first_name'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('first_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }} required" id='register-last-name'>
                                            <label class="col-md-4 control-label">Last Name</label>

                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" placeholder="Last Name" required>

                                                @if ($errors->has('last_name'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('last_name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                         <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }} required" id='register-name' style="display:none">
                                            <label class="col-md-4 control-label">Name</label>

                                            <div class="col-md-6">
                                                <input type="text" class="form-control" name="name" value="{{ old('name') }}">

                                                @if ($errors->has('name'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('name') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} required">
                                            <label class="col-md-4 control-label">E-Mail Address</label>

                                            <div class="col-md-6">
                                                <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email Address" required>

                                                @if ($errors->has('email'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} required">
                                            <label class="col-md-4 control-label">Password</label>

                                            <div class="col-md-6">
                                                <input type="password" class="form-control" name="password" placeholder="Password" required>

                                                @if ($errors->has('password'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }} required">                                        
                                            <label class="col-md-4 control-label">Confirm Password</label>

                                            <div class="col-md-6">
                                                <input type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password" required>

                                                @if ($errors->has('password_confirmation'))
                                                    <span class="help-block">
                                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-6 col-md-offset-4">
                                                <button class="btn btn-black btn-block" type="submit">Register Organizer </button>
                                            </div>
                                        </div>


                                        <div class="col-sm-12">
                                           @if (count($errors) > 0)
                                                <div class="alert alert-danger">
                                                    <ul>
                                                        @foreach ($errors->all() as $error)
                                                            <li>{{ $error }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                        
                                    </form>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
