<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\MusicianAuth;
use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Musician;
use App\GenreMusisi;
use App\GenreBand;
use App\Grupband;
use App\GrupbandMusisi;
use App\Sewa;
use App\User;
use App\Gig;
use App\Saldo;
use App\Review;
use App\Withdraw;
use App\SaldoDetail;
use App\BankMusisi;
use App\Bank;
use App\Notif;
use DB;
use Session;
use Cloudder;
use Hash;

class MusicianController extends Controller
{
    public function __construct(){
        if(Auth::guard('user')->user())
            $this->middleware('auth');
        elseif(Auth::guard('musician')->user())
            $this->middleware('musician');       
    }

    public function index(){
        if(Auth::guard('musician')->user()){
        	$gig = Gig::where('aktif', 'Y')->where('type_gig','=','post')->where('status', '0')->get();
        	return view('musician.dashboard')->with('gigs', $gig);
        }else{
            return redirect()->back();
        }
    }

    public function profile($slug){
    	$musisi = Musician::whereSlug($slug)->firstOrFail()->id;
        $musisis = Musician::findOrFail($musisi);

        $genremusisi = GenreMusisi::join('genres', 'genre_musisi.genre_id' ,'=', 'genres.id')->where('musician_id', $musisi)->get();
        $musisis->genre = $genremusisi;

        $query = "SELECT sum(review.nilai) AS nilai, count(sewas.id) AS bagi FROM review INNER JOIN sewas ON review.sewa_id = sewas.id WHERE sewas.type_sewa = 'musisihire' AND sewas.subject_id = $musisis->id"; 

        $query2 = "SELECT sum(review.nilai) AS nilai, count(sewas.id) AS bagi FROM review INNER JOIN sewas ON review.sewa_id = sewas.id WHERE sewas.type_sewa = 'hiremusisi' AND sewas.object_id = $musisis->id"; 

         $join = Sewa::join('review', 'sewas.id', '=', 'review.sewa_id')
                        ->join('gigs', 'sewas.gig_id', '=', 'gigs.id')
                        ->where('sewas.type_sewa', '=', 'musisihire')
                        ->where('sewas.subject_id', $musisis->id)
                        ->orWhere('sewas.type_sewa', '=', 'hiremusisi')
                        ->where('sewas.object_id', $musisis->id)
                        ->get(['review.id', 'review.pesan', 'review.nilai']);

        $review = DB::select($query);
        $review2 = DB::select($query2);
       
        $pembagi = count($join);
        $plus = $review[0]->nilai + $review2[0]->nilai;
        if($pembagi == 0 && $plus == 0)
            $hasil = 0;
        else
            $hasil = round($plus / $pembagi);

    	return view('musician.profile')->with('musisi', $musisis)->with('review', $hasil)->with('totalrev', $pembagi);
    }

    protected function create(array $data)
    {
        return Musician::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
    }
    
    public function doUpdateProfile(Request $req){
        if(Auth::guard('musician')->user()){
            if($req->checkbox != null){
                $authId = Auth::guard('musician')->user()->id;
                $musicianAuth = Musician::find($authId);
                $input = $req->all();
                $input['aktif'] = 'Y';
                $musicianAuth->update($input);

                $bank = BankMusisi::where('musician_id', $authId)->first();

                if($bank != null){
                    Bank::where('id', $bank->bank_id)
                              ->update(['no_rek' => $req->norek, 'atas_nama' => $req->namapemilik, 'nama_bank' => $req->bank]);
                }else{
                    $input['no_rek'] = $req->norek;
                    $input['atas_nama'] = $req->namapemilik;
                    $input['nama_bank'] = $req->bank;
                    $bankid = Bank::create($input)->id;

                    $bankmusisi = new BankMusisi;
                    $bankmusisi->musician_id = $authId;
                    $bankmusisi->bank_id = $bankid;
                    $bankmusisi->save();
                }

                $genre_musisi = GenreMusisi::where('musician_id', $authId)->get(['genre_id']);
                if(!$genre_musisi->isEmpty()){
                    foreach ($genre_musisi as $genrem) {
                        $genres[] = $genrem->genre_id;                  
                    }
                    $result = array_merge(array_diff($genres,$req->checkbox),array_diff($req->checkbox,$genres));

                    $sama = array_intersect($result,$genres); 
                    $beda = array_diff($result,$genres); 

                    if($beda != null){
                        foreach ($beda as $genrebeda) {
                            $genremu = new GenreMusisi;
                            $genremu->genre_id = $genrebeda;
                            $genremu->musician_id = $authId;
                            $genremu->save();
                        }
                    }

                    if($sama != null){
                        foreach ($sama as $genresama) {
                            GenreMusisi::where('musician_id', $authId)->where('genre_id', $genresama)->delete();
                        }
                    }            
                }
                else{
                    if($req->checkbox != null) {          
                        foreach ($req->checkbox as $value) {
                            $genremu = new GenreMusisi;
                            $genremu->genre_id = $value;
                            $genremu->musician_id = $authId;
                            $genremu->save();
                        }   
                    }      
                }

                return redirect('/');
            }else{
                Session::flash('message', 'PILIH GENRE MUSISI KAMU!!!');
                return redirect()->back();
            }
        }
        else{
            return redirect()->back();
        }
    }

    public function doUpdatePhoto(Request $req){
        $input = $req->all();
        $musisi = Musician::findOrFail(Auth::guard('musician')->user()->id);
        $file = array('photo' => $req->file('photo'));
        Cloudder::upload($req->file('photo')->getPathName());
        $input['photo'] = Cloudder::getPublicId();
        $musisi->update($input); 
        return redirect()->back();
    }

    public function doUpdateAkun(Request $req){
        if(Auth::guard('musician')->user()){
            $authId = Auth::guard('musician')->user()->id;
            $musicianAuth = Musician::find($authId);
            $input = $req->all();
            $musicianAuth->update($input);

            $bank = BankMusisi::where('musician_id', $authId)->first();

            if($bank != null){
                Bank::where('id', $bank->bank_id)
                          ->update(['no_rek' => $req->no_rek, 'atas_nama' => $req->atas_nama, 'nama_bank' => $req->nama_bank]);
            }else{
                $input['no_rek'] = $req->no_rek;
                $input['atas_nama'] = $req->atas_nama;
                $input['nama_bank'] = $req->nama_bank;
                $bankid = Bank::create($input)->id;

                $bankmusisi = new BankMusisi;
                $bankmusisi->musician_id = $authId;
                $bankmusisi->bank_id = $bankid;
                $bankmusisi->save();
            }
            Session::flash('message', 'Data akun berhasil di ubah!');
            return redirect()->back();
        }
        else{
            return redirect()->back();
        }
    }

    public function doUpdatePassword(Request $req){
        if(Auth::guard('musician')->user()){
            $authId = Auth::guard('musician')->user()->id;
            $musicianAuth = Musician::find($authId);
            if (Hash::check($req->pass, $musicianAuth->password)){
                if($req->newpass != $req->confirmpass){
                    Session::flash('message', 'Password baru tidak cocok');
                    return redirect()->back();
                }else{
                    $musicianAuth->password = bcrypt($req->newpass);
                    $musicianAuth->save();
                    Session::flash('message', 'Password berhasil di ubah!');
                    return redirect()->back();
                }
            }else{
                Session::flash('message', 'Password Salah');
                return redirect()->back();
            }
        }
        else{
            return redirect()->back();
        }
    }

    public function addBand(Request $request){
        $authId = Auth::guard('musician')->user()->id;
        $input = $request->all();
        if($request->checkbox != null){
            $input['nama_grupband'] = $request->name;
            $input['admin_id'] = $authId;
            $input['youtube_video'] = $request->youtube;
            $input['url_website'] = $request->web;
            $input['username_soundcloud'] = $request->soundcloud;
            $input['username_reverbnation'] = $request->reverb;
            $input['cover'] = 'sample';
            if ($request->hasFile('photo'))
            {
                $file = array('photo' => $request->file('photo'));
                Cloudder::upload($request->file('photo')->getPathName());
                $input['photo'] = Cloudder::getPublicId();
            }
            else{
                $input['photo'] = 'sample';
            }

            GrupBand::create($input);
            $piladmin = Grupband::where('admin_id', $authId)->orderBy('created_at', 'desc')->first();
            $bandmusisi = new GrupbandMusisi;
            $bandmusisi->position_id = $request->posisi;
            $bandmusisi->musician_id = $authId;
            $bandmusisi->grupband_id = $piladmin->id;
            $bandmusisi->save();

            $genre_band = GenreBand::where('band_id', $piladmin->id)->get(['genre_id']);
                if(!$genre_band->isEmpty()){
                    foreach ($genre_band as $genrem) {
                        $genres[] = $genrem->genre_id;                  
                    }
                    $result = array_merge(array_diff($genres,$request->checkbox),array_diff($request->checkbox,$genres));

                    $sama = array_intersect($result,$genres); 
                    $beda = array_diff($result,$genres); 

                    if($beda != null){
                        foreach ($beda as $genrebeda) {
                            $genremu = new GenreBand;
                            $genremu->genre_id = $genrebeda;
                            $genremu->band_id = $piladmin->id;
                            $genremu->save();
                        }
                    }

                    if($sama != null){
                        foreach ($sama as $genresama) {
                            GenreBand::where('band_id', $piladmin->id)->where('genre_id', $genresama)->delete();
                        }
                    }            
                }
                else{
                    if($request->checkbox != null) {          
                        foreach ($request->checkbox as $value) {
                            $genremu = new GenreBand;
                            $genremu->genre_id = $value;
                            $genremu->band_id = $piladmin->id;
                            $genremu->save();
                        }   
                    }      
                }

            Session::flash('message', 'Band'.$request->name.'Berhasil dibuat.');
            return redirect()->action('MusicianController@bandProfile', [$piladmin->slug]);
        }
        else{
            Session::flash('message', 'HARAP ISI GENRE BAND!!!');
            return redirect()->back();
        }
    }

    public function bandProfile($slug){
        $bands = Grupband::whereSlug($slug)->firstOrFail()->id;
        $band = Grupband::findOrFail($bands);
        $genreband = GenreBand::join('genres', 'genre_bands.genre_id' ,'=', 'genres.id')->where('band_id', $bands)->get();
        
        $band->genre = $genreband;

        $anggotaband = GrupbandMusisi::join('musicians', 'grupband_musisi.musician_id', '=', 'musicians.id')
                                    ->join('positions', 'grupband_musisi.position_id', '=', 'positions.id')
                                    ->where('grupband_id', $band->id)->get(['musicians.id','musicians.name','musicians.photo','musicians.slug','positions.position_name']);

        //hitung review type bandhire
        $query = "SELECT sum(review.nilai) AS nilai FROM review INNER JOIN sewas ON review.sewa_id = sewas.id WHERE sewas.type_sewa = 'bandhire' AND sewas.subject_id = $band->id"; 

        //hitung review type hireband
        $query2 = "SELECT sum(review.nilai) AS nilai FROM review INNER JOIN sewas ON review.sewa_id = sewas.id WHERE sewas.type_sewa = 'hireband' AND sewas.object_id = $band->id"; 

        $join = Sewa::join('review', 'sewas.id', '=', 'review.sewa_id')
                        ->join('gigs', 'sewas.gig_id', '=', 'gigs.id')
                        ->where('sewas.type_sewa', '=', 'bandhire')
                        ->where('sewas.subject_id', $band->id)
                        ->orWhere('sewas.type_sewa', '=', 'hireband')
                        ->where('sewas.object_id', $band->id)
                        ->get(['review.id', 'review.pesan', 'review.nilai']);


        $review = DB::select($query);
        $review2 = DB::select($query2);

        $pembagi = count($join);
        $plus = $review[0]->nilai + $review2[0]->nilai;
        if($pembagi == 0 && $plus == 0)
            $hasil = 0;
        else
            $hasil = round($plus / $pembagi);

        $query = "SELECT * FROM  musicians WHERE id NOT IN (SELECT musician_id FROM grupband_musisi WHERE grupband_id = ".$band->id.")";

        $ceknotanggota = DB::select($query);
        
        if($ceknotanggota == null)
            $data = null;
        else
            $data = $ceknotanggota;
      
        return view('musician.bandprofile')->with('band', $band)->with('anggota', $anggotaband)->with('compare', $data)->with('review', $hasil)->with('totalrev',$pembagi);
        

    }

    public function listBand(){
        if(!Auth::guard('musician')->user())
            return redirect()->back();
        else{
            $anggota = GrupbandMusisi::join('grupbands', 'grupband_musisi.grupband_id', '=', 'grupbands.id')
                                    ->where('grupband_musisi.musician_id', Auth::guard('musician')->user()->id)
                                    ->get();

            return view('musician.listband')->with('listband', $anggota);
        }
    }

    public function editBand($slug){
        if(!Auth::guard('musician')->user())
            return redirect()->back();
        else{
            $bands = Grupband::whereSlug($slug)->firstOrFail()->id;
            $band = Grupband::findOrFail($bands);

            if($band->admin_id != Auth::guard('musician')->user()->id)
                return redirect()->back();
            else
                return view('musician.editband')->with('band', $band);
        }
    }

    public function updateBand(Request $request, $slug){
        $authId = Auth::guard('musician')->user()->id;
        $bands = Grupband::whereSlug($slug)->firstOrFail()->id;
        $band = Grupband::findOrFail($bands);

        $input = $request->all();
        $input['nama_grupband'] = $request->name;
        $input['youtube_video'] = $request->youtube;
        $input['url_website'] = $request->web;
        $input['username_soundcloud'] = $request->soundcloud;
        $input['username_reverbnation'] = $request->reverb;
        if ($request->hasFile('photo'))
        {
            $file = array('photo' => $request->file('photo'));
            Cloudder::upload($request->file('photo')->getPathName());
            $input['photo'] = Cloudder::getPublicId();
        }

        if ($request->hasFile('cover'))
        {
            $file = array('cover' => $request->file('cover'));
            Cloudder::upload($request->file('cover')->getPathName());
            $input['cover'] = Cloudder::getPublicId();
        }

        //dd($input);
        $band->update($input);
        // print "<pre>";
        // print_r($input);
        // print "</pre>";
        $genre_band = GenreBand::where('band_id', $bands)->get(['genre_id']);
            if(!$genre_band->isEmpty()){
                foreach ($genre_band as $genrem) {
                    $genres[] = $genrem->genre_id;                  
                }
                $result = array_merge(array_diff($genres,$request->checkbox),array_diff($request->checkbox,$genres));

                $sama = array_intersect($result,$genres); 
                $beda = array_diff($result,$genres); 

                if($beda != null){
                    foreach ($beda as $genrebeda) {
                        $genremu = new GenreBand;
                        $genremu->genre_id = $genrebeda;
                        $genremu->band_id = $bands;
                        $genremu->save();
                    }
                }

                if($sama != null){
                    foreach ($sama as $genresama) {
                        GenreBand::where('band_id', $bands)->where('genre_id', $genresama)->delete();
                    }
                }            
            }
            else{
                if($request->checkbox != null) {          
                    foreach ($request->checkbox as $value) {
                        $genremu = new GenreBand;
                        $genremu->genre_id = $value;
                        $genremu->band_id = $bands;
                        $genremu->save();
                    }   
                }      
            }
             
        return redirect()->action('MusicianController@bandProfile', [$slug]);
    }

    public function listMusisi(){
        $all = Musician::all();
        return view('musician.allmusician')->with('allmusisi', $all)->renderSections()['content'];
    }


    public function addAnggota(Request $request, $slug, $id){
        $bands = Grupband::whereSlug($slug)->firstOrFail()->id;
        $band = Grupband::findOrFail($bands);
        $input = $request->all();
        $input['grupband_id'] = $band->id;
        $input['musician_id'] = $id;
        $input['position_id'] = $request->posisi;

        GrupbandMusisi::create($input);
        return redirect()->action('MusicianController@bandProfile', [$slug]);
    }

    public function deleteAnggota($slug, $id){
        $bands = Grupband::whereSlug($slug)->firstOrFail()->id;
        $band = Grupband::findOrFail($bands);

        if(Auth::guard('musician')->user()){
            if(Auth::guard('musician')->user()->id == $band->admin_id || $id == Auth::guard('musician')->user()->id)
            {       
                $musisi = Musician::findOrFail($id);    
                GrupbandMusisi::where('musician_id', $id)->delete();
                Session::flash('message', 'Anggota '.$musisi->name.' Berhasil di hapus.');
            }
            else
                Session::flash('message', 'Anda Tidak Punya Otoritas!');
        }
        else
            Session::flash('message', 'Anda Tidak Punya Otoritas!');


        return redirect()->back();
    }


    public function listSewaMusisi(){
        
            $reqmusisi = Sewa::where('object_id', Auth::guard('musician')->user()->id)
                            ->where('type_sewa', '=', 'hiremusisi')
                            ->where('status_request', '0')
                            ->where('status', '0')
                            ->get();

            foreach ($reqmusisi as $sewam) {
                $organizer = User::where('id', $sewam->subject_id)->first();
                $gig = Gig::where('id', $sewam->gig_id)->first();
                $sewam->organizer = $organizer;
                $sewam->gig = $gig;
            }

            return view('musician.bookinglist-musisi')->with('sewamusisi', $reqmusisi);
       
    }

    public function listSewaMusisiApprove(){
        
            $reqmusisi = Sewa::where('object_id', Auth::guard('musician')->user()->id)
                            ->where('type_sewa', '=', 'hiremusisi')
                            ->where('status_request', '1')
                            ->where('status', '!=', '3')
                            ->where('status', '!=', '4')
                            ->get();

            foreach ($reqmusisi as $sewam) {
                $organizer = User::where('id', $sewam->subject_id)->first();
                $gig = Gig::where('id', $sewam->gig_id)->first();
                $sewam->organizer = $organizer;
                $sewam->gig = $gig;
            }

            return view('musician.bookinglist-musisi-approve')->with('sewamusisi', $reqmusisi);
        
    }

    public function listSewaMusisiSelesai(){
       
            $reqmusisi = Sewa::where('object_id', Auth::guard('musician')->user()->id)
                            ->where('type_sewa', '=', 'hiremusisi')
                            ->where('status_request', '1')
                            ->where('status', '3')
                            ->orWhere('object_id', Auth::guard('musician')->user()->id)
                            ->where('type_sewa', '=', 'hiremusisi')
                            ->where('status_request', '1')
                            ->where('status', '=', '4')
                            ->get();

            foreach ($reqmusisi as $sewam) {
                $organizer = User::where('id', $sewam->subject_id)->first();
                $gig = Gig::where('id', $sewam->gig_id)->first();
                $cek = Review::where('sewa_id', $sewam->id)->get();
                $sewam->organizer = $organizer;
                $sewam->gig = $gig;
                $sewam->review = $cek;
            }

            return view('musician.bookinglist-musisi-selesai')->with('sewamusisi', $reqmusisi);
       
    }


    public function confirmSewa($id){
        $sewa = Sewa::findOrFail($id);

        if($sewa->object_id != Auth::guard('musician')->user()->id)
            return redirect()->back();
        else{
            Sewa::where('id', $id)->where('object_id', Auth::guard('musician')->user()->id)
                  ->update(['status_request' => 1]);

            Gig::where('id', $sewa->gig_id)->update(['status' => 1]);

            $notif = New Notif;
            $notif->object_id = $sewa->gig_id;
            $notif->subject_id = Auth::guard('musician')->user()->id;
            $notif->user_id = $sewa->subject_id;
            $notif->type_subject = 'musisi';
            $notif->type_user = 'organizer';
            $notif->type_notif = 'terimasewa';
            $notif->save();

            return redirect()->action('MusicianController@listSewaMusisiApprove');
        }
    }

    public function cancelSewa($id){
        $sewa = Sewa::findOrFail($id);

        if($sewa->object_id != Auth::guard('musician')->user()->id)
            return redirect()->back();
        else{
            Sewa::where('id', $id)
                  ->where('object_id', Auth::guard('musician')->user()->id)
                  ->update(['status_request' => 2]);

            Gig::where('id', $sewa->gig_id)->update(['status' => 0, 'type_gig' => 'post']);

            $notif = New Notif;
            $notif->object_id = $sewa->gig_id;
            $notif->subject_id = Auth::guard('musician')->user()->id;
            $notif->user_id = $sewa->subject_id;
            $notif->type_subject = 'musisi';
            $notif->type_user = 'organizer';
            $notif->type_notif = 'tolaksewa';
            $notif->save();

            return redirect()->back();
        }
    }


    public function listSewaBands(){
        
            $listbands = Grupband::where('admin_id', Auth::guard('musician')->user()->id)->get();
            foreach ($listbands as $list) {
                $sewaband = Sewa::where('object_id', $list->id)->where('type_sewa', '=', 'hireband')
                                ->where('status_request', '!=', '2')->count();
                $list->sewa = $sewaband;
            }
                
            //dd($listbands);
            return view('musician.listband-book')->with('listband', $listbands);
        
    }


    public function listSewaBand($slug){
        
            $bands = Grupband::whereSlug($slug)->firstOrFail()->id;
            $band = Grupband::findOrFail($bands);

            if($band->admin_id == Auth::guard('musician')->user()->id){
                $reqband = Sewa::where('object_id', $band->id)
                                ->where('type_sewa', '=', 'hireband')
                                ->where('status_request', '0')
                                ->where('status', '0')
                                ->get();

                foreach ($reqband as $sewaband) {
                    $organizer = User::where('id', $sewaband->subject_id)->first();
                    $gig = Gig::where('id', $sewaband->gig_id)->first();
                    $sewaband->organizer = $organizer;
                    $sewaband->gig = $gig;
                }

                return view('musician.bookinglist-band')->with('sewaband', $reqband)->with('bands',$band);
            }else{
                return redirect()->back();
            }
    }

    public function listSewaBandApprove($slug){

            $bands = Grupband::whereSlug($slug)->firstOrFail()->id;
            $band = Grupband::findOrFail($bands);

            if($band->admin_id == Auth::guard('musician')->user()->id){
                $reqband = Sewa::where('object_id', $band->id)
                                ->where('type_sewa', '=', 'hireband')
                                ->where('status_request', '1')
                                ->where('status', '!=', '3')
                                ->where('status', '!=', '4')
                                ->get();

                foreach ($reqband as $sewaband) {
                    $organizer = User::where('id', $sewaband->subject_id)->first();
                    $gig = Gig::where('id', $sewaband->gig_id)->first();
                    $sewaband->organizer = $organizer;
                    $sewaband->gig = $gig;
                }

                return view('musician.bookinglist-band-approve')->with('sewaband', $reqband)->with('bands',$band);
            }else{
                return redirect()->back();
            }
    }


    public function listSewaBandSelesai($slug){

            $bands = Grupband::whereSlug($slug)->firstOrFail()->id;
            $band = Grupband::findOrFail($bands);

            if($band->admin_id == Auth::guard('musician')->user()->id){
                $reqband = Sewa::where('object_id', $band->id)
                                ->where('type_sewa', '=', 'hireband')
                                ->where('status_request', '1')
                                ->where('status', '=', '3')
                                ->orWhere('object_id', $band->id)
                                ->where('type_sewa', '=', 'hireband')
                                ->where('status_request', '1')
                                ->where('status', '=', '4')
                                ->get();

                foreach ($reqband as $sewaband) {
                    $organizer = User::where('id', $sewaband->subject_id)->first();
                    $gig = Gig::where('id', $sewaband->gig_id)->first();
                    $cek = Review::where('sewa_id', $sewaband->id)->get();

                    $sewaband->organizer = $organizer;
                    $sewaband->gig = $gig;      
                    $sewaband->review = $cek;      
                }

               return view('musician.bookinglist-band-selesai')->with('sewaband', $reqband)->with('bands',$band);
            }else{
                return redirect()->back();
            }
    }


    public function confirmBand($id){
        $sewa = Sewa::where('id',$id)->first();
        $band = Grupband::findOrFail($sewa->object_id);

        if($band->admin_id != Auth::guard('musician')->user()->id)
            return redirect()->back();
        else{
            Sewa::where('id', $id)
                  ->where('object_id', $band->id)
                  ->where('type_sewa', '=', 'hireband')
                  ->update(['status_request' => 1]);
            Gig::where('id', $sewa->gig_id)->update(['status' => 1]);

            $notif = New Notif;
            $notif->object_id = $sewa->gig_id;
            $notif->subject_id = $band->id;
            $notif->user_id = $sewa->subject_id;
            $notif->type_subject = 'band';
            $notif->type_user = 'organizer';
            $notif->type_notif = 'terimasewa';
            $notif->save();

            return redirect()->action('MusicianController@listSewaBandApprove', [$band->slug]);
        }
    }

    public function cancelBand($id){
        $sewa = Sewa::findOrFail($id);
        $band = Grupband::findOrFail($sewa->object_id);

        if($band->admin_id != Auth::guard('musician')->user()->id)
            return redirect()->back();
        else{
            Sewa::where('id', $id)
                  ->where('object_id', $band->id)
                  ->where('type_sewa', '=', 'hireband')
                  ->update(['status_request' => 2]);

            Gig::where('id', $sewa->gig_id)->update(['status' => 0, 'type_gig' => 'post']);

            $notif = New Notif;
            $notif->object_id = $sewa->gig_id;
            $notif->subject_id = $band->id;
            $notif->user_id = $sewa->subject_id;
            $notif->type_subject = 'band';
            $notif->type_user = 'organizer';
            $notif->type_notif = 'tolaksewa';
            $notif->save();

            return redirect()->back();
        }
    }

    public function confirmOfferBand($id, $slug){
        $sewa = Sewa::where('id',$id)->first();
        $bands = Grupband::whereSlug($slug)->firstOrFail()->id;
        $band = Grupband::findOrFail($bands);

        Sewa::where('gig_id', $sewa->gig_id)->update(['status_request' => 2]);
        Sewa::where('id', $id)->update(['status_request' => 1]);
        Gig::where('id', $sewa->gig_id)->update(['status' => 1]);

        $notif = New Notif;
        $notif->object_id = $sewa->gig_id;
        $notif->subject_id = $sewa->object_id;
        $notif->user_id = $band->id;
        $notif->type_subject = 'organizer';
        $notif->type_user = 'band';
        $notif->type_notif = 'terimaoffer';
        $notif->save();

        return redirect()->back();
    }

    public function confirmOfferSewa($id, $slug){
        $sewa = Sewa::where('id',$id)->first();
        $musicians = Musician::whereSlug($slug)->firstOrFail()->id;
        $musician = Musician::findOrFail($musicians);

        Sewa::where('gig_id', $sewa->gig_id)->update(['status_request' => 2]);
        Sewa::where('id', $id)->update(['status_request' => 1]);
        Gig::where('id', $sewa->gig_id)->update(['status' => 1]);

        $notif = New Notif;
        $notif->object_id = $sewa->gig_id;
        $notif->subject_id = $sewa->object_id;
        $notif->user_id = $musician->id;
        $notif->type_subject = 'organizer';
        $notif->type_user = 'musisi';
        $notif->type_notif = 'terimaoffer';
        $notif->save();

        return redirect()->back();
    }

    public function cancelOfferSewa($id, $slug){
        $sewa = Sewa::where('id',$id)->first();
        $bands = Musician::whereSlug($slug)->firstOrFail()->id;
        $band = Musician::findOrFail($bands);

        //Sewa::where('gig_id', $sewa->gig_id)->update(['status_request' => 2]);
        Sewa::where('id', $id)->update(['status_request' => 2]);

        $notif = New Notif;
        $notif->object_id = $sewa->gig_id;
        $notif->subject_id = $sewa->object_id;
        $notif->user_id = $band->id;
        $notif->type_subject = 'organizer';
        $notif->type_user = 'musisi';
        $notif->type_notif = 'tolakoffer';
        $notif->save();

        return redirect()->back();
    }

    public function cancelOfferBand($id, $slug){
        $sewa = Sewa::where('id',$id)->first();
        $bands = Grupband::whereSlug($slug)->firstOrFail()->id;
        $band = Grupband::findOrFail($bands);

        //Sewa::where('gig_id', $sewa->gig_id)->update(['status_request' => 2]);
        Sewa::where('id', $id)->update(['status_request' => 2]);

        $notif = New Notif;
        $notif->object_id = $sewa->gig_id;
        $notif->subject_id = $sewa->object_id;
        $notif->user_id = $band->id;
        $notif->type_subject = 'organizer';
        $notif->type_user = 'band';
        $notif->type_notif = 'tolakoffer';
        $notif->save();

        return redirect()->back();
    }

    public function saldo($slug){
        $musisis = Musician::whereSlug($slug)->firstOrFail()->id;
        $musisi = Musician::findOrFail($musisis);

        if($musisi->id == Auth::guard('musician')->user()->id){
            // $bands = GrupbandMusisi::where('musician_id', Auth::guard('musician')->user()->id)->get();
            // foreach ($bands as $listband){
            //     $band = Grupband::where('id', $listband->grupband_id)->get();
            //     $saldo = Saldo::where('subject_id', $musisi->id)->where('type_pemilik','=','band')->get();
            //     $listband->band = $band;
            //     $listband->saldoband = $saldo;
            // }

            $saldom = Saldo::where('subject_id', $musisi->id)->where('type_pemilik','=','musisi')->first();
            if($saldom == null){
                $trx = '';
            }else{
                $trx = SaldoDetail::where('saldo_id',$saldom->id)->get();
                foreach ($trx as $sewa) {
                    $sewax = Sewa::where('id', $sewa->sewa_id)->get();
                    
                    foreach ($sewax as $sewas) {
                        $gigs = Gig::where('id', $sewas->gig_id)->get();
                        $sewas->gig = $gigs;
                    }

                    //$sewa->gig = $sewax;
                    $sewa->listsewa = $sewax;

                }
            }

            //return $trx;
            return view('musician.saldo')->with('saldo', $saldom)->with('detail',$trx);
        }
        else
            return redirect()->back();
    }

    public function saldoBandList($slug){
        if($slug != Auth::guard('musician')->user()->slug){
            return redirect()->back();
        }else{
            $anggota = GrupbandMusisi::join('grupbands', 'grupband_musisi.grupband_id', '=', 'grupbands.id')
                                    ->where('grupband_musisi.musician_id', Auth::guard('musician')->user()->id)
                                    ->get();

            return view('musician.listBandSaldo')->with('listband', $anggota);
        }
    }

    public function saldoBand($slug){
        $bands = Grupband::whereSlug($slug)->firstOrFail()->id;
        $band = Grupband::findOrFail($bands);

        $saldo = Saldo::where('subject_id', $band->id)->where('type_pemilik','=','band')->first();
        if($saldo == null){
            $trx = '';
        }else{
            $trx = SaldoDetail::where('saldo_id',$saldo->id)->get();
            foreach ($trx as $sewa) {
                $sewax = Sewa::where('id', $sewa->sewa_id)->get();
                
                foreach ($sewax as $sewas) {
                    $gigs = Gig::where('id', $sewas->gig_id)->get();
                    $sewas->gig = $gigs;
                }

                //$sewa->gig = $sewax;
                $sewa->listsewa = $sewax;

            }
        }

            //return $trx;
            return view('musician.saldoBand')->with('saldoband', $saldo)->with('detail',$trx)->with('bands',$band);
    }


    public function withdraw(Request $req, $id){
        $saldo = Saldo::findOrFail($id);
        $nilai = $saldo->saldo - $req->jumlah;

        $withdraw = New Withdraw;
        $withdraw->jumlah = $req->jumlah;
        $withdraw->saldo_id = $id;
        $withdraw->saldo_akhir = $nilai;
        $withdraw->save();

        //update field saldo       
        Saldo::where('id', $id)->update(['saldo' => $nilai]);

        $notif = New Notif;
        $notif->object_id = 0;
        $notif->subject_id = $saldo->subject_id;
        $notif->user_id = 0;
        $notif->type_subject = $saldo->type_pemilik;
        $notif->type_user = 'admin';
        $notif->type_notif = 'withdraw';
        $notif->save();

        return redirect()->back();
    }

    public function detailReview($slug){
        $musisi = Musician::whereSlug($slug)->firstOrFail()->id;
        $musisis = Musician::findOrFail($musisi);

        $join = Sewa::join('review', 'sewas.id', '=', 'review.sewa_id')
                        ->join('gigs', 'sewas.gig_id', '=', 'gigs.id')
                        ->where('sewas.type_sewa', '=', 'musisihire')
                        ->where('sewas.subject_id', $musisis->id)
                        ->orWhere('sewas.type_sewa', '=', 'hiremusisi')
                        ->where('sewas.object_id', $musisis->id)
                        ->get();


        //return $join;

        return view('musician.detail-review')->with('review',$join)->with('musisi',$musisis);
    }

    public function detailReviewBand($slug){
        $band = Grupband::whereSlug($slug)->firstOrFail()->id;
        $bands = Grupband::findOrFail($band);

        $join = Sewa::join('review', 'sewas.id', '=', 'review.sewa_id')
                        ->join('gigs', 'sewas.gig_id', '=', 'gigs.id')
                        ->where('sewas.type_sewa', '=', 'bandhire')
                        ->where('sewas.subject_id', $bands->id)
                        ->orWhere('sewas.type_sewa', '=', 'hireband')
                        ->where('sewas.object_id', $bands->id)
                        ->get();


        //return $join;

        return view('musician.detail-review-band')->with('review',$join)->with('band',$bands);
    }
    
 
}
