@extends('layouts.app')

@section('content')
@if(Auth::guard('musician')->user())
@if(Session::has('message'))
    <div class="alert alert-info">{{ Session::get('message') }}</div>
@endif
    <div class="container">
    @if(Auth::guard('musician')->user()->deskripsi == null || Auth::guard('musician')->user()->no_telp == null || Auth::guard('musician')->user()->Wilayah == null || Auth::guard('musician')->user()->harga_sewa == null)
        <div class="alert alert-danger">Lengkapi data akun kamu, untuk membuka fitur yang terkunci</div>
    @endif
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="media">
                    <div class="media-left">
                        <form class="bootstrap-form-with-validation" action="musician/photo/update" id="proc-upload" method="POST" enctype="multipart/form-data">
                            <img src={!! Cloudder::show(Auth::guard('musician')->user()->photo , ['format' => 'jpg', 'crop' => 'fit', 'width' => '350', 'height'=> '350']) !!} alt="Logo" class="widthfull">
                            <div class="row">
                                <div class="col-lg-10 col-lg-offset-2 col-md-12">
                                <label class="uploading btn btn-black">
                                    <span>Upload  Photo Musisi</span>
                                    <input type="file" id="photo" name="photo" required>
                                </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="media-body">
                     <?php $bankakun = App\Musician::join('bank_musisi', 'musicians.id','=','bank_musisi.musician_id')
                                                    ->join('banks', 'bank_musisi.bank_id','=','banks.id')
                                                    ->where('musicians.id', Auth::guard('musician')->user()->id)->first();
                        ?>
                        <ul class="list-unstyled fa-ul">
                            <li></li>
                            <li></li>
                        </ul>
                        <div class="row">
                            <div class="col-lg-11 col-lg-offset-1 col-sm-12">
                                <h1>{{ Auth::guard('musician')->user()->name }}</h1></div>
                            <div class="col-lg-11 col-lg-offset-1 col-md-12">
                                <p>Harga Sewa / Jam : Rp. {{ Auth::guard('musician')->user()->harga_sewa }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-11 col-lg-offset-1 col-md-12">
                                <p>Tipe : {{ Auth::guard('musician')->user()->tipe }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-11 col-lg-offset-1 col-md-12">
                                <p>Basis : {{ Auth::guard('musician')->user()->basis }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-11 col-lg-offset-1 col-md-12">
                                <p>Wilayah : {{ Auth::guard('musician')->user()->Wilayah }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <?php $genremusisi = App\GenreMusisi::join('genres', 'genre_musisi.genre_id' ,'=', 'genres.id')->where('musician_id', Auth::guard('musician')->user()->id)->get();?>
                            <div class="col-lg-11 col-lg-offset-1 col-sm-12">
                                <p>Genre : 
                                @foreach($genremusisi as $genredetail)
                                    {{$genredetail->genre_name}} |
                                @endforeach
                                </p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-11 col-lg-offset-1 col-sm-12">
                                <p>Deskripsi : {{ Auth::guard('musician')->user()->deskripsi }}</p>
                            </div>
                        </div>                                                                    
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-lg-6 col-lg-offset-3 col-sm-12">
                        <form class="bootstrap-form-with-validation " action="musician/profile/update" method="POST">
                            <h2 class="text-center">Lengkapi Profil Musisi</h2>                        
                            <div class="form-group required">
                            <label class="control-label">Nama Musisi</label>
                                <input class="form-control" type="text" name="name" id="text-input" value="{{ Auth::guard('musician')->user()->name }}" required>
                            </div>
                            <div class="form-group required">
                            <label class="control-label">Wilayah </label>
                            <select class="form-control" name="Wilayah" id="Wilayah" required>
                                <option value="" hidden>Pilih Wilayah</option>
                                @if(Auth::guard('musician')->user()->Wilayah == null)
                                   <option hidden>{{ Auth::guard('musician')->user()->Wilayah}}</option>
                                @else
                                    <option hidden selected>{{ Auth::guard('musician')->user()->Wilayah}}</option>
                                @endif
                                <option value="Batu Aji">Batu Aji</option>
                                <option value="Nagoya">Nagoya</option>
                                <option value="Batam Center">Batam Center</option>
                                <option value="Tanjung Piayu">Tanjung Piayu</option>
                                <option value="Tiban">Tiban</option>
                                <option value="Bengkong">Bengkong</option>
                                <option value="Sei Panas">Sei Panas</option>
                                <option value="Sekupang">Sekupang</option>
                                <option value="Jodoh">Jodoh</option>
                                <option value="Pinuin">Pinuin</option>
                                <option value="Nongsa">Nongsa</option>
                                <option value="Botani">Botani</option>
                            </select>
                            </div>

                            <div class="form-group required">
                            <label class="control-label">Genre Musisi</label>
                            <div class="row">
                                <?php $genre = App\Genre::all(); $cek ='';?>
                                @foreach($genre as $genres)
                                    <div class="col-lg-2 col-md-12">
                                        <div class="checkbox">
                                        @if(!$genremusisi->isEmpty()) 
                                            @foreach($genremusisi as $genrem)
                                                <?php $genrems[] = $genrem->genre_id?>                                         
                                            @endforeach

                                            @if(in_array($genres->id, $genrems))
                                                <?php $cek = ' checked'?>
                                            @else
                                                <?php $cek = ' unchecked'?>
                                            @endif                                          
                                            <label><input type="checkbox" name="checkbox[]" value="{{$genres->id}}" <?php echo $cek; ?>
                                            >{{$genres->genre_name}}</label>
                                        @else
                                            <label><input type="checkbox" name="checkbox[]" value="{{$genres->id}}"<
                                            >{{$genres->genre_name}}</label>
                                        @endif
                                        </div>
                                    </div>
                                    
                                @endforeach
                            </div>
                            </div>
                            <div class="form-group required">
                            <label class="control-label">Basis</label>
                            <div class="form-group">
                                <input class="form-control" type="text" name="basis" id="text-input" value="{{ Auth::guard('musician')->user()->basis }}" required placeholder="Ex : Vokalis">
                            </div>
                            </div>
                            <div class="form-group required">
                            <label class="control-label">No.Telepon </label>
                            <input class="form-control" type="text" name='no_telp' required placeholder="No.Telepon" value="{{ Auth::guard('musician')->user()->no_telp }}">                            
                            </div>
                            <div class="form-group required">
                            <label class="control-label">Deskripsi </label>
                            <textarea class="form-control" rows="5" name='deskripsi' placeholder="Jelaskan informasi tentang dirimu" required>{{ Auth::guard('musician')->user()->deskripsi }}</textarea>
                            </div>
                            <div class="form-group required">
                            <label class="control-label">Harga Sewa / Jam</label>
                            <input class="form-control" type="number" id="myNumber" name="harga_sewa" value="{{ Auth::guard('musician')->user()->harga_sewa }}" required>
                            </div>
                            <div class="form-group"></div>
                            <label>Url Video </label>
                            <input class="form-control" type="text" name="youtube_video" value="{{ Auth::guard('musician')->user()->youtube_video }}" placeholder="http://">

                            <div class="form-group required">
                            <label class="control-label">No.Rekening </label>
                            <input class="form-control" type="text" name='norek' placeholder="No.Rekening" @if($bankakun != null) value="{{ $bankakun->no_rek }}" @endif required>
                            </div>
                            <div class="form-group required">
                            <label class="control-label">Nama Pemilik Rekening </label>
                            <input class="form-control" type="text" name='namapemilik' placeholder="Nama yang tercantum di Rekening" @if($bankakun != null) value="{{ $bankakun->atas_nama }}" @endif required>
                            </div>
                            <div class="form-group required">
                            <label class="control-label">Nama Bank </label>
                            <input class="form-control" type="text" name='bank' placeholder="eg: BRI" @if($bankakun != null) value="{{ $bankakun->nama_bank }}" @endif required>
                            </div>
                            
                         
                            <div class="form-group"></div>
                            <label>Soundcloud Username (Optional)</label>
                            <input class="form-control" type="text" name="username_soundcloud" value="{{ Auth::guard('musician')->user()->username_soundcloud }}" placeholder="http://">
                            <div class="form-group"></div>
                            <label>Revebnation Username (Optional)</label>
                            <input class="form-control" type="text" name="username_reverbnation" value="{{ Auth::guard('musician')->user()->username_reverbnation }}" placeholder="http://">
                            <div class="form-group"></div>
                            <label>Website (Optional)</label>
                            <input class="form-control" type="text" name="url_website" value="{{ Auth::guard('musician')->user()->url_website }}" placeholder="http://">
                            <div class="form-group"></div>
                            <div class="form-group">
                                <button class="btn btn-black btn-block" type="submit">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="panel-footer"></div>
        </div>
    </div>
    
@else
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div>
                @if(Auth::guard('user')->user())
                    <div class="jumbotron">
                    <h1 class="text-center">Temukan Musisi di Wilayahmu</h1>   
                    <p class="text-center">Sesuaikan Kritera</p>                 
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4">
                            <form class="bootstrap-form-with-validation" action="{{ url('/search')}}" method="POST">                                
                                <div class="form-group">
                                    <label class="control-label" for="text-input">Tanggal Pelaksanaan</label>
                                    <input class="form-control" type="text" name="tanggal" placeholder="Tanggal Pelaksanaan" id="mulai">                            
                                </div>
                                <div class="form-group">
                                    <?php
                                        $Wilayah = App\Musician::groupBy('Wilayah')->get(['Wilayah']);
                                    ?>
                                    <label class="control-label" for="email-input">Wilayah</label>
                                    <select class="form-control" name="Wilayah">
                                        <option value="">Pilih Wilayah</option>
                                    @foreach($Wilayah as $_Wilayah)
                                        <option value="{{$_Wilayah->Wilayah}}">{{$_Wilayah->Wilayah}}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="form-group required">
                                    <label class="control-label" for="email-input">Tipe Musisi</label>
                                    <select class="form-control" name="tipe" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="0">Grup Band</option>
                                        <option value="1">Solo</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                <?php $genre = App\Genre::all(); $cek ='';?>
                                    <label class="control-label" for="textarea-input">Genre Musisi</label>
                                    <div class="row">
                                    @foreach($genre as $genres)
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="checkbox">
                                                <label class="control-label">
                                                    <input type="checkbox" name="checkbox[]" value="{{$genres->id}}" >{{$genres->genre_name}}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-black btn-block" type="submit">Temukan </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>    


                @elseif(Auth::guard('musician')->user())
                    <div class="panel-heading">Welcome {{Auth::guard('musician')->user()->name}}</div>

                    <div class="panel-body">
                        Your Application's Landing Page.
                    </div>
                @elseif(Auth::guard('admin')->user())
                    <div class="panel-heading">Welcome Admin {{Auth::guard('admin')->user()->first_name}}</div>

                    <div class="panel-body">
                        Your Application's Landing Page.
                    </div>
                @else

                <div class="jumbotron">
                    <h1 class="text-center">Temukan Musisi di Wilayahmu</h1>   
                    <p class="text-center">Sesuaikan Kritera</p>                 
                    <div class="row">
                        <div class="col-md-4 col-md-offset-4">
                            <form class="bootstrap-form-with-validation" action="{{ url('/search')}}" method="POST">                                
                                <div class="form-group">
                                    <label class="control-label" for="text-input">Tanggal Pelaksanaan</label>
                                    <input class="form-control" type="text" name="tanggal" placeholder="Tanggal Pelaksanaan" id="mulai">                            
                                </div>
                                <div class="form-group">
                                    <?php
                                        $Wilayah = App\Musician::groupBy('Wilayah')->get(['Wilayah']);
                                    ?>
                                    <label class="control-label" for="email-input">Wilayah</label>
                                    <select class="form-control" name="Wilayah">
                                        <option value="">Pilih Wilayah</option>
                                    @foreach($Wilayah as $_Wilayah)
                                        <option value="{{$_Wilayah->Wilayah}}">{{$_Wilayah->Wilayah}}</option>
                                    @endforeach
                                    </select>
                                </div>
                                <div class="form-group required">
                                    <label class="control-label" for="email-input">Tipe Musisi</label>
                                    <select class="form-control" name="tipe" required>
                                        <option value="">Pilih Tipe</option>
                                        <option value="0">Grup Band</option>
                                        <option value="1">Solo</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                <?php $genre = App\Genre::all(); $cek ='';?>
                                    <label class="control-label" for="textarea-input">Genre Musisi</label>
                                    <div class="row">
                                    @foreach($genre as $genres)
                                        <div class="col-lg-4 col-sm-6">
                                            <div class="checkbox">
                                                <label class="control-label">
                                                    <input type="checkbox" name="checkbox[]" value="{{$genres->id}}" >{{$genres->genre_name}}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                    </div>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-black btn-block" type="submit">Temukan </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>    
                @endif
                
                </div>
            </div>
        </div>
    </div>
@endif



@endsection
