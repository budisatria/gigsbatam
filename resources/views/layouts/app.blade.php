<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gighub - Discover Musician in Town</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700">

    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}

    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">    
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,80">
    <link rel="stylesheet" href="{{ asset('/css/JLX-Simple-Footer-with-Icon.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/bootstrap-datetimepicker.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.2.0/jquery.rateyo.min.css">
    <style>
        body {
            font-family: 'Lato';
        }
        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body id="app-layout">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    Gighub
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    @if(Auth::guard('musician')->user())
                        @if(Auth::guard('musician')->user()->deskripsi != null && Auth::guard('musician')->user()->no_telp != null && Auth::guard('musician')->user()->Wilayah != null && Auth::guard('musician')->user()->harga_sewa != null)
                            <li role="presentation"><a href="{{url('discover')}}">Discover </a></li>
                            <li role="presentation"><a href={{ url('/musician/'.Auth::guard('musician')->user()->slug) }}>Profil </a></li>
                            <li role="presentation"><a href="{{ url('/list-band') }}">Band </a></li>
                            <li role="presentation"><a href="{{url('listsewa/musisi')}}">Booking Musisi</a></li>        
                            <li role="presentation"><a href="{{url('listsewa/band')}}">Booking Band</a></li>                            
                        @else
                            <li class="disabled"><a>Discover</a></li>
                            <li class="disabled"><a>Band</a></li>
                            <li class="disabled"><a>Booking Musisi</a></li>
                            <li class="disabled"><a>Booking Band</a></li>
                            <li class="disabled"><a>Penawaran </a></li>
                        @endif       
                    @elseif(Auth::guard('user')->user())
                        <li><a href="{{ url('/discover-organizer') }}">Discover</a></li>
                        <li><a href={{ url('user/'.Auth::guard('user')->user()->slug) }}>Gig</a></li>
                        <li><a href="{{ url('/listsewa') }}">Sewa </a></li>
                        <li><a href="{{ url('/listoffer') }}">Penawaran </a></li>
                    @elseif(Auth::guard('admin')->user())
                        <li><a href="{{ url('/admin') }}">Dashboard</a></li>
                        <li><a href="{{ url('admin/listuser') }}">Data Organizer</a></li>
                        <li><a href="{{ url('admin/listmusisi') }}">Data Musisi</a></li>
                        <li><a href="{{ url('admin/listband') }}">Data Band</a></li>
                        <li><a href="{{ url('admin/listgig') }}">Data Gig</a></li>
                        <li><a href="{{ url('admin/listorder') }}">Data Order</a></li>
                        <li><a href="{{ url('admin/listwithdraw') }}">Data Withdraw</a></li>
                    @endif
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if(Auth::guard('admin')->user())
                        <?php
                            $cek = App\Notif::where('user_id', 0)
                                        ->where('type_user', 'admin')->where('baca', 'N')->get();
                         ?>
                        <li class="dropdown" style="color: white;">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bell"></i> @if(!$cek->isEmpty()){{count($cek)}} @endif</a>
                            <ul class="dropdown-menu" role="menu">
                                @if($cek != null)
                                    @foreach ($cek as $notif)
                                        @if($notif->type_notif == 'withdraw')
                                            <?php
                                                if($notif->type_subject == 'musisi')
                                                    $org = App\Musician::where('id', $notif->subject_id)->first();
                                                elseif($notif->type_subject == 'band')
                                                    $org = App\Grupband::where('id', $notif->subject_id)->first();
                                            ?>
                                            <li><a href={{url('notif/'.$notif->id)}}>@if($notif->type_subject == 'musisi'){{$org->name}}@elseif($notif->type_subject == 'band'){{$org->nama_grupband}}@endif melakukan withdraw</a>
                                            <li>
                                        @elseif($notif->type_notif == 'konfirmasipembayaran')
                                            <?php
                                                $org = App\User::where('id', $notif->subject_id)->first();
                                                $gig = App\Gig::where('id', $notif->object_id)->first();
                                            ?>
                                            <li><a href={{url('notif/'.$notif->id)}}>{{$org->first_name}} telah melakukan konfirmasi pembayaran Gig {{$gig->nama_gig}}</a>
                                            <li>
                                        @endif
                                    @endforeach
                                @endif
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::guard('admin')->user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/admin/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
                    @elseif(Auth::guard('user')->user())
                    <?php
                        $cek = App\Notif::where('user_id', Auth::guard('user')->user()->id)
                                        ->where('type_user', 'organizer')->where('baca', 'N')->get();
                    ?>
                        <li class="dropdown" style="color: white;">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bell"></i> @if(!$cek->isEmpty()){{count($cek)}} @endif</a>
                            <ul class="dropdown-menu" role="menu">
                                @if($cek != null)
                                    @foreach ($cek as $notif)
                                        @if($notif->type_notif == 'terimasewa')
                                            <?php
                                                $gig = App\Gig::where('id', $notif->object_id)->first();
                                                if($notif->type_subject == 'musisi')
                                                    $org = App\Musician::where('id', $notif->subject_id)->first();
                                                elseif($notif->type_subject == 'band')
                                                    $org = App\Grupband::where('id', $notif->subject_id)->first();
                                            ?>
                                            <li><a href={{url('notif/'.$notif->id)}}>@if($notif->type_subject == 'musisi'){{$org->name}}@elseif($notif->type_subject == 'band'){{$org->nama_grupband}}@endif menerima permintaan anda di Gig {{$gig->nama_gig}}</a><li>
                                        @elseif($notif->type_notif == 'tolaksewa')
                                            <?php
                                                $gig = App\Gig::where('id', $notif->object_id)->first();
                                                if($notif->type_subject == 'musisi')
                                                    $org = App\Musician::where('id', $notif->subject_id)->first();
                                                elseif($notif->type_subject == 'band')
                                                    $org = App\Grupband::where('id', $notif->subject_id)->first();
                                            ?>
                                            <li><a href={{url('notif/'.$notif->id)}}>@if($notif->type_subject == 'musisi'){{$org->name}}@elseif($notif->type_subject == 'band'){{$org->nama_grupband}}@endif menolak permintaan anda di Gig {{$gig->nama_gig}}</a>
                                            <li>
                                        @elseif($notif->type_notif == 'reqoffer')
                                            <?php
                                                $gig = App\Gig::where('id', $notif->object_id)->first();
                                                if($notif->type_subject == 'musisi')
                                                    $org = App\Musician::where('id', $notif->subject_id)->first();
                                                elseif($notif->type_subject == 'band')
                                                    $org = App\Grupband::where('id', $notif->subject_id)->first();
                                            ?>
                                            <li><a href={{url('notif/'.$notif->id)}}>@if($notif->type_subject == 'musisi'){{$org->name}}@elseif($notif->type_subject == 'band'){{$org->nama_grupband}}@endif mengajukan penawaran di Gig {{$gig->nama_gig}}</a>
                                            <li>
                                        @elseif($notif->type_notif == 'lunas')
                                            <?php
                                                $gig = App\Gig::where('id', $notif->object_id)->first();
                                            ?>
                                            <li><a href={{url('notif/'.$notif->id)}}>Pembayaran Gig {{$gig->nama_gig}} telah diverikasi Admin (LUNAS)</a>
                                            <li>
                                        @endif
                                    @endforeach
                                @endif
                            </ul>
                        </li>
                        <a class="btn btn-danger navbar-btn navbar-left" type="button" href="{{url('create-gig')}}">+ Buat Gig</a>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::guard('user')->user()->first_name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>
                        </li>
                    @elseif(Auth::guard('musician')->user())
                        @if(Auth::guard('musician')->user()->deskripsi != null && Auth::guard('musician')->user()->no_telp != null && Auth::guard('musician')->user()->Wilayah != null && Auth::guard('musician')->user()->harga_sewa != null)
                        <?php
                            $musisiid = Auth::guard('musician')->user()->id;
                            $query = "SELECT * FROM notif WHERE  user_id = $musisiid AND type_user = 'musisi' AND baca = 'N' OR user_id IN (SELECT id FROM grupbands WHERE admin_id = $musisiid AND type_user = 'band' AND baca = 'N')";

                            $cek = DB::select($query);
                        ?>
                            <li class="dropdown" style="color: white;">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bell"></i> @if($cek != null){{count($cek)}} @endif</a>
                                <ul class="dropdown-menu" role="menu">
                                    @if($cek != null)
                                        @foreach($cek as $notif)
                                            @if($notif->type_user == 'band')
                                            <?php
                                                $band = App\Grupband::where('id', $notif->user_id)->first();
                                                $org = App\User::where('id', $notif->subject_id)->first();
                                                $gig = App\Gig::where('id', $notif->object_id)->first();
                                            ?>
                                                @if($notif->type_notif == 'reqsewa')
                                                    <li><a href={{url('notif/'.$notif->id)}}>{{$org->first_name}} mengirimi {{$band->nama_grupband}} permintaan di Gig {{$gig->nama_gig}}</a>
                                                    </li>
                                                @elseif($notif->type_notif == 'terimaoffer')
                                                    <li><a href={{url('notif/'.$notif->id)}}>{{$org->first_name}} menerima tawaran {{$band->nama_grupband}} di Gig {{$gig->nama_gig}}</a>
                                                    </li>
                                                @elseif($notif->type_notif == 'tolakoffer')
                                                    <li><a href={{url('notif/'.$notif->id)}}>{{$org->first_name}} menolak tawaran {{$band->nama_grupband}} di Gig {{$gig->nama_gig}}</a>
                                                @elseif($notif->type_notif == 'lunas')
                                                    <li><a href={{url('notif/'.$notif->id)}}>{{$org->first_name}} telah melunasi pembayaran Gig {{$gig->nama_gig}}</a>
                                                @elseif($notif->type_notif == 'tambahsaldo')
                                                    <li><a href={{url('notif/'.$notif->id)}}>Saldo {{$band->nama_grupband}} telah ditambahkan dari Gig {{$gig->nama_gig}}</a>
                                                @elseif($notif->type_notif == 'review')
                                                    <li><a href={{url('notif/'.$notif->id)}}>{{$org->first_name}} telah memberikan review ke {{$band->nama_grupband}} dalam Gig {{$gig->nama_gig}}</a>
                                                @elseif($notif->type_notif == 'withdrawselesai')
                                                    <li><a href={{url('notif/'.$notif->id)}}>Withdraw dari band {{$band->nama_grupband}} telah ditransfer Admin</a>
                                                @endif

                                            @elseif($notif->type_user == 'musisi')
                                            <?php                                                
                                                $org = App\User::where('id', $notif->subject_id)->first();
                                                $gig = App\Gig::where('id', $notif->object_id)->first();
                                            ?>
                                                @if($notif->type_notif == 'reqsewa')
                                                    <li><a href={{url('notif/'.$notif->id)}}>{{$org->first_name}} mengirimi Anda permintaan di Gig {{$gig->nama_gig}}</a>
                                                    </li>
                                                @elseif($notif->type_notif == 'terimaoffer')
                                                    <li><a href={{url('notif/'.$notif->id)}}>{{$org->first_name}} menerima tawaran Anda di Gig {{$gig->nama_gig}}</a>
                                                    </li>
                                                @elseif($notif->type_notif == 'tolakoffer')
                                                    <li><a href={{url('notif/'.$notif->id)}}>{{$org->first_name}} menolak tawaran Anda di Gig {{$gig->nama_gig}}</a>
                                                    </li>
                                                @elseif($notif->type_notif == 'lunas')
                                                    <li><a href={{url('notif/'.$notif->id)}}>{{$org->first_name}} telah melunasi pembayaran Gig {{$gig->nama_gig}}</a>
                                                @elseif($notif->type_notif == 'tambahsaldo')
                                                    <li><a href={{url('notif/'.$notif->id)}}>Saldo kamu telah ditambahkan dari Gig {{$gig->nama_gig}}</a>
                                                @elseif($notif->type_notif == 'review')
                                                    <li><a href={{url('notif/'.$notif->id)}}>{{$org->first_name}} telah memberikan review ke Kamu dalam Gig {{$gig->nama_gig}}</a>
                                                @elseif($notif->type_notif == 'withdrawselesai')
                                                    <li><a href={{url('notif/'.$notif->id)}}>Withdraw dari saldo kamu telah ditransfer Admin</a>
                                                @endif
                                            @endif
                                        @endforeach
                                    @endif
                                </ul>
                            </li>
                            <button class="btn btn-danger navbar-btn navbar-left" type="button" data-toggle="modal" data-target="#addband">+ Add Band</button>
                        @else
                            <li class="dropdown" style="color: white;">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><i class="fa fa-bell"></i></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li>test</li>
                                </ul>
                            </li>
                            <button class="btn btn-disabled navbar-btn navbar-left" type="button">+ Add Band</button>
                        @endif
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::guard('musician')->user()->name }} <span class="caret"></span>
                            </a>
                            <ul class="dropdown-menu" role="menu">
                                <li><a href={{ url('/my-account') }}><i class="fa fa-btn fa-user"></i>My Account</a></li>
                                <li><a href={{ url('/musician/saldo/'.Auth::guard('musician')->user()->slug) }}><i class="fa fa-btn fa-money"></i>Saldo</a></li>
                                <li><a href="{{ url('musician-logout') }}"><i class="fa fa-btn fa-sign-out"></i>Logout</a></li>
                            </ul>

                        </li>
                    @else
                        <li><a href="{{ url('/login') }}">Login </a></li>
                        <!-- <li><a href="{{ url('/admin/login') }}">Login Admin</a></li> -->
                        <li><a href="{{ url('/register') }}">Register</a></li>
                        <!-- <li><a href="{{ url('/admin/register') }}">Register Admin</a></li> -->
                        <!-- <li><a href="{{ url('/musician/register') }}">Register Musician</a></li> -->
                        <!-- <li><a href="{{ url('/musician/login') }}">Login Musician</a></li> -->
                    @endif
                </ul>
            </div>
        </div>
    </nav><!-- 
                    @if(Auth::guard('admin')->user())
                        <p> Admin : {{Auth::guard('admin')->user()->first_name}}</p>
                    @elseif(Auth::guard('user')->user())
                        <p> User : {{ Auth::guard('user')->user()->first_name }} </p>
                    @elseif(Auth::guard('musician')->user())
                        <p>Musician :{{ Auth::guard('musician')->user()->name }} </p>
                    @else
                        <p>Guest</p>
                    @endif -->
    @yield('content')

    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="{{ url('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.1.1/jquery.rateyo.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}

<!-- MODAL -->
@if(Auth::guard('musician')->user())
    <div class="modal  fade" id="addband" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" >
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel">Buat Grup Band</h4>
          </div>
          <div class="modal-body" id="modaladd">
           {{ Form::open(['route'=>['add.band'],'role'=> 'form', 'class' => 'ui reply form', 'enctype' => 'multipart/form-data']) }}
                <div class="col-md-12">
                    <div class="col-md-8">  
                        <div class="form-group required">
                            <label class="control-label">Nama Band</label> 
                            <input class="form-control" type="text" name="name" id="text-input" placeholder="Nama Band" required>
                        </div>
                    </div>
                    <div class="col-md-8">  
                        <div class="form-group required"> 
                        <label class="control-label">Deskripsi Band</label>
                            <input class="form-control" type="text" name="deskripsi" id="text-input" placeholder="Deskripsi Band" required>
                        </div>
                    </div>
                    <div class="col-md-12"> 
                        <div class="form-group required"> 
                            <label class="control-label">Wilayah</label>
                            <input class="form-control" type="text" name="Wilayah" id="text-input" placeholder="Wilayah" required>
                        </div>
                    </div>
                    <div class="col-md-12"> 
                        <div class="form-group required">
                        <label class="control-label">Harga Sewa</label> 
                            <input class="form-control" type="text" name="harga" id="text-input" placeholder="Harga Sewa / Jam" required>
                        </div>
                    </div>
                    <div class="col-md-12"> 
                        <div class="form-group"> 
                            <input class="form-control" type="text" name="youtube" id="text-input" placeholder="Link Youtube" >
                        </div>
                    </div>
                    <div class="col-md-12"> 
                        <div class="form-group"> 
                            <input class="form-control" type="text" name="web" id="text-input" placeholder="Link Website" >
                        </div>
                    </div>
                    <div class="col-md-12"> 
                        <div class="form-group"> 
                            <input class="form-control" type="text" name="soundcloud" id="text-input" placeholder="Username SoundCloud" >
                        </div>
                    </div>
                    <div class="col-md-12"> 
                        <div class="form-group"> 
                            <input class="form-control" type="text" name="reverb" id="text-input" placeholder="Username Reverbnation" >
                        </div>
                    </div>

                    <div class="col-md-12"> 
                        <div class="form-group required"> 
                            <label class="control-label">Pilih Posisi</label>
                            <select class="form-control" name="posisi" id="posisi" Required>
                                <?php $posisi = App\Position::all()?>
                                <option value="">- Pilih Posisi -</option>
                                @foreach($posisi as $valposisi)
                                    <option value="{{$valposisi->id}}">{{$valposisi->position_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-12"> 
                        <div class="form-group"> 
                           <?php $genre = App\Genre::all(); $cek ='';?>
                            <label class="control-label" for="textarea-input">Genre Band</label>
                            <div class="row">
                            @foreach($genre as $genres)
                                <div class="col-lg-4 col-sm-6">
                                    <div class="checkbox">
                                        <label class="control-label">
                                            <input type="checkbox" name="checkbox[]" value="{{$genres->id}}">{{$genres->genre_name}}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Add Photo :</label>
                            <input name="photo" id="photo" type="file" class="btn">
                        </div>
                    </div>
                </div>
          </div>
          <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-success">Simpan</button>
            {{Form::close()}}
          </div>
        </div>
      </div>
    </div>
@endif





<!-------------------- JS ------------------>

    <script type="text/javascript">
        // $(document).ready(function(){
        //     $('#mulai').datetimepicker();
        //     $('#selesai').datetimepicker();
        // });
        $('#mulai').datetimepicker({
            startDate: '+0d',
            format: 'yyyy-mm-dd hh:ii'
        });
        $('#selesai').datetimepicker({
            startDate: '+0d',
            format: 'yyyy-mm-dd hh:ii'
        });

    </script>

    <script>
    function myFunction() {
        var x = document.getElementById("myNumber").required;
    }
    </script>

<script type="text/javascript">
    $(document).ready(function() {

        $('#photo').change(function(){
            $('#proc-upload').submit(); 
        });
    });

$(document).ready(
    function () {
      $("#rateYo").rateYo({
        rating: 0,
        numStars: 5,
        fullStar: true
      }).on("rateyo.set", function (e, data) {
            $("#rate").val(data.rating);         
        });
    }
);
    
</script>


<script type="text/javascript">
$(document).on('ajaxComplete ready', function() {
    $('.modalMd').off('click').on('click', function () {
        $('#modalMdContent').load($(this).attr('value'));
    });
});
</script>

</body>
</html>
