<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Musician;
use App\User;
use App\Genre;
use App\GenreBand;
use App\GenreMusisi;
use App\Grupband;
use App\GrupbandMusisi;
use App\Position;
use App\Sewa;
use App\Gig;
use App\Bank;
use App\BankMusisi;
use App\Saldo;
use App\Withdraw;
use DB;
use Hash;
use Inout;
use Illuminate\Support\Facades\Input;
use App\Classes\Firebase;

class MobileMusicianController extends Controller
{
    public function all(){
        $musicians = Musician::all();

        // $musicians = DB::select('SELECT m.name, g.genre_name FROM musicians as m inner join genre_musisi as gm inner join genres as g on m.id = gm.musician_id AND g.id = gm.genre_id');

        return response()->json(['status'=>'ok', 'error'=>0, 'musicians'=>$musicians],200);
    }

    public function register(Request $request){
        Musician::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => bcrypt($request['password']),
            'firebase' => $request['firebase'],
        ]);

        $musician = Musician::where('email',$request['email'])->first();

        return response()->json(["message"=>"Register success","error"=>0,"musician"=>$musician],200);


    }

    public function login(Request $request){
        $musician = Musician::where('email',$request['email'])->first();
        if($musician==null){
            $Response['message']="Email not registered";
            $Response['error']=1;
            $Response['musician'] = new \stdClass();
        }
        else {
            if(Hash::check($request['password'], $musician->password)){
                $Response['message'] = "Login Success, Welcome ".$musician->name;
                $Response['error'] = 0;
                $musician->firebase = $request['firebase'];
                $musician->save();
                $obj = $musician;
                $Response['musician'] = $obj;
            }
            else{
                $Response['message'] = "Login Error, Wrong Password";
                $Response['error'] = 2;
                $Response['musician'] = null;
            }
        }
        return $Response;
    }

    public function tesnotif(Request $request){
        // public static function sendPushNotification($data, $notif){
        // // 'id','object_id','subject_id','user_id','type_user','type_notif','type_subject','baca'
        // // udin mengirim permintaan sewa ke anda
        // // subject_id mengirim object_id ke user_id (type_user) 
        // Notif::Create($data);
        // object_id    => id data (notif apa) yang dikirim misal: gig_id
        // subject_id   => id user pengirim
        // user_id      => id user penerima
        // type_user    => type user penerima 

        return Firebase::sendPushNotification(
            array(
                    'object_id'=>$request['object_id'],
                    'subject_id'=>$request['pengirim_id'],
                    'user_id'=>$request['penerima_id'],
                    'type_user'=>$request['type_penerima'],
                    'type_notif'=>'reqsewa',
                    'type_subject'=>$request['type_pengirim'],
                    'baca'=>'N'
                ),
            array(
                    'title'=>'GigHub',
                    'body'=>"isi pesan panjang panjang sekali sampai turun kebawah",
                    'type'=>'booking'
                )
            );
    }

    public function genres(){
        $genres = Genre::all();
        return response()->json(['message'=>'ok','error'=>0, 'genreList'=>$genres],200);
    }

    public function genreMusician(){
        // $musicians = DB::select('SELECT g.genre_name FROM genres as g INNER JOIN genre_musisi as gm on g.id = gm.genre_id WHERE gm.musician_id = 1');
        $musicians = DB::select('SELECT g.genre_name FROM genres as g');
        return response()->json(['message'=>'ok', 'error'=>0, 'genremusisi'=>$musicians],200);        
    }

    public function musicianGenres(Request $request){

        $musicianAuth = Musician::find($request['user_id']);

        if($musicianAuth!=null){
            $musicianGenres = DB::select("SELECT g.* FROM genres as g JOIN genre_musisi as gm ON g.id = gm.genre_id JOIN musicians as m ON gm.musician_id = m.id WHERE m.id = ".$musicianAuth->id);

            return array("message"=>"OK","error"=>0,"musicianGenres"=>$musicianGenres);
        }
        else{
            return array("message"=>"OK","error"=>0,"musicianGenres"=>$musicianGenres);

        }
    }

    public function search(){
        $isSearchWilayah = false;
        $isSearchGenre = false;
        $isSearchRole = false;
        $katakunci = 0;
        if(!empty(Input::get("Wilayah"))){
            // return Input::get("Wilayah");
            $isSearchWilayah = true;
            $katakunci+=1;
        }
        if(!empty(Input::get("genre_name"))){
            // return Input::get("genres");
            $isSearchGenre = true;
            $katakunci+=1;
        }
        if(!empty(Input::get("tipe"))){
            // return Input::get("role");
            $isSearchRole = true;
            $katakunci+=1;
        }
        // if(!empty(Input::get("datetime"))){
        //     return Input::get("datetime");
        // }
        $query1 = " WHERE ";
        $query2 = " WHERE ";
        if($isSearchWilayah){
            $query1.="m.Wilayah LIKE '%".Input::get("Wilayah")."%'";
            $query2.="gpb.Wilayah LIKE '%".Input::get("Wilayah")."%'";
        }
        if($isSearchGenre){

            if($katakunci>1){
                $query1.=" AND ";
                $query2.=" AND ";
            }

            if(strpos(Input::get('genre_name'), ',')){
                $genrebanyak = explode(',', Input::get('genre_name'));
                $or = 0;
                $query1.= " ( ";
                $query2.= " ( ";
                foreach ($genrebanyak as $gen) {
                    $or +=1;
                    if ($or>1) {
                        $query1.= " OR ";
                        $query2.= " OR ";
                    }
                    $query1.= " g.genre_name LIKE '%".$gen."%'";
                    $query2.= " g.genre_name LIKE '%".$gen."%'";   
                }
                // $query1.= " g.genre_name LIKE '%".$gen."%'";
                // $query2.= " g.genre_name LIKE '%".$gen."%'";
                $query1.= " ) ";
                $query2.= " ) ";
                // $query1.= " OR ";
                // $query2.= " OR ";

            }
            else {
            //     $query1.= " OR ";
            //     $query2.= " OR ";
            $query1.="g.genre_name LIKE '%".Input::get('genre_name')."%'";
            $query2.="g.genre_name LIKE '%".Input::get('genre_name')."%'";
            }

        }
        if($isSearchRole){
            if($katakunci>1){
                $query1.=" AND ";
                $query2.=" AND ";
            }
            $query1.="m.tipe LIKE '%".Input::get('tipe')."%' ";
            $query2.="gpb.tipe LIKE '%".Input::get('tipe')."%' ";
        }

        // $_result = DB::select("SELECT * FROM musicians as m INNER JOIN genre_musisi as gm INNER JOIN genres as g ON m.id = gm.musician_id AND g.id = gm.genre_id".$query2);
        
        if ($isSearchWilayah){
            $_result = DB::select("SELECT m.id,name, Wilayah, harga_sewa,photo,deskripsi,m.tipe,m.youtube_video,m.url_website,m.username_soundcloud, m.username_reverbnation, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM musicians as m JOIN genre_musisi as gm ON m.id = gm.musician_id JOIN genres as g ON g.id= gm.genre_id".$query1."GROUP BY m.id, m.name UNION SELECT gpb.id, nama_grupband,Wilayah,harga,photo,deskripsi,gpb.tipe,gpb.youtube_video,gpb.url_website,gpb.username_soundcloud, gpb.username_reverbnation, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM grupbands as gpb JOIN genre_bands as gb ON gpb.id = gb.band_id JOIN genres as g ON g.id= gb.genre_id".$query2."GROUP BY gpb.id, gpb.nama_grupband");
        }
        else if($isSearchWilayah && $isSearchRole){
            $_result = DB::select("SELECT m.id,name, Wilayah, harga_sewa,photo,deskripsi,m.tipe,m.youtube_video,m.url_website,m.username_soundcloud, m.username_reverbnation, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM musicians as m JOIN genre_musisi as gm ON m.id = gm.musician_id JOIN genres as g ON g.id= gm.genre_id".$query1."GROUP BY m.id, m.name UNION SELECT gpb.id, nama_grupband,Wilayah,harga,photo,deskripsi,gpb.tipe,gpb.youtube_video,gpb.url_website,gpb.username_soundcloud, gpb.username_reverbnation, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM grupbands as gpb JOIN genre_bands as gb ON gpb.id = gb.band_id JOIN genres as g ON g.id= gb.genre_id".$query2."GROUP BY gpb.id, gpb.nama_grupband");
        }
        else if($isSearchWilayah && $isSearchGenre){
            $_result = DB::select("SELECT m.id,name, Wilayah, harga_sewa,photo,deskripsi,m.tipe,m.youtube_video,m.url_website,m.username_soundcloud, m.username_reverbnation, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM musicians as m JOIN genre_musisi as gm ON m.id = gm.musician_id JOIN genres as g ON g.id= gm.genre_id".$query1."GROUP BY m.id, m.name UNION SELECT gpb.id, nama_grupband,Wilayah,harga,photo,deskripsi,gpb.tipe,gpb.youtube_video,gpb.url_website,gpb.username_soundcloud, gpb.username_reverbnation, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM grupbands as gpb JOIN genre_bands as gb ON gpb.id = gb.band_id JOIN genres as g ON g.id= gb.genre_id".$query2."GROUP BY gpb.id, gpb.nama_grupband");
        }
        else if($isSearchRole ){
            $_result = DB::select("SELECT m.id,name, Wilayah, harga_sewa,photo,deskripsi,m.tipe,m.youtube_video,m.url_website,m.username_soundcloud, m.username_reverbnation, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM musicians as m JOIN genre_musisi as gm ON m.id = gm.musician_id JOIN genres as g ON g.id= gm.genre_id".$query1."GROUP BY m.id, m.name UNION SELECT gpb.id, nama_grupband,Wilayah,harga,photo,deskripsi,gpb.tipe,gpb.youtube_video,gpb.url_website,gpb.username_soundcloud, gpb.username_reverbnation, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM grupbands as gpb JOIN genre_bands as gb ON gpb.id = gb.band_id JOIN genres as g ON g.id= gb.genre_id".$query2."GROUP BY gpb.id, gpb.nama_grupband");
        }
        else if($isSearchGenre){
            $_result = DB::select("SELECT m.id,name, Wilayah, harga_sewa,photo,deskripsi,m.tipe,m.youtube_video,m.url_website,m.username_soundcloud, m.username_reverbnation, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM musicians as m JOIN genre_musisi as gm ON m.id = gm.musician_id JOIN genres as g ON g.id= gm.genre_id".$query1."GROUP BY m.id, m.name UNION SELECT gpb.id, nama_grupband,Wilayah,harga,photo,deskripsi,gpb.tipe,gpb.youtube_video,gpb.url_website,gpb.username_soundcloud, gpb.username_reverbnation, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM grupbands as gpb JOIN genre_bands as gb ON gpb.id = gb.band_id JOIN genres as g ON g.id= gb.genre_id".$query2."GROUP BY gpb.id, gpb.nama_grupband");
        }
        else {
            // $_result = DB::select("SELECT *, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM musicians as m JOIN genre_musisi as gm ON m.id = gm.musician_id JOIN genres as g ON g.id= gm.genre_id GROUP BY m.id, m.name");
            $_result = DB::select("SELECT m.id,name, Wilayah, harga_sewa,photo,deskripsi,m.tipe,m.youtube_video,m.url_website,m.username_soundcloud, m.username_reverbnation, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM musicians as m JOIN genre_musisi as gm ON m.id = gm.musician_id JOIN genres as g ON g.id= gm.genre_id GROUP BY m.id, m.name UNION SELECT gpb.id, nama_grupband,Wilayah,harga,photo,deskripsi,gpb.tipe,gpb.youtube_video,gpb.url_website,gpb.username_soundcloud, gpb.username_reverbnation, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM grupbands as gpb JOIN genre_bands as gb ON gpb.id = gb.band_id JOIN genres as g ON g.id= gb.genre_id GROUP BY gpb.id, gpb.nama_grupband");
        }
        // else{
        //     $_result = DB::select("SELECT m.id,name, Wilayah, harga_sewa,photo,deskripsi,m.tipe, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM musicians as m JOIN genre_musisi as gm ON m.id = gm.musician_id JOIN genres as g ON g.id= gm.genre_id GROUP BY m.id, m.name UNION SELECT gpb.id, nama_grupband,Wilayah,harga,photo,deskripsi,gpb.tipe, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM grupbands as gpb JOIN genre_bands as gb ON gpb.id = gb.band_id JOIN genres as g ON g.id= gb.genre_id GROUP BY gpb.id, gpb.nama_grupband");
        // }

        // $_result = array();
        // foreach ($result as $r) {
        //     array_push($_result, $result);
        // }
        // $_result=array();
        // foreach($result as $r){
        //     array_push($_result, Musician::find($r->id));
        // }
        // $_result = DB::select("SELECT * m.name,m.harga,m.photo,g.genre_name FROM musicians as m INNER JOIN genre_name as gm InputNNER JOIN genres as g ON m.id = gm.musician_id AND g.id = gm.genre_id WHERE m.Wilayah LIKE '%Bandung%' AND g.genre_name LIKE '%Jazz%'");
        return array("message"=>"OK","error"=>0,"musicians"=>$_result);
    }

    public function createBand(Request $request){

        $req = $request->all();
        $musicianAuth = Musician::find($request['admin_id']);

        if($musicianAuth!=null){

            $req['admin_id'] = $musicianAuth->id;
            $req['nama_grupband'] = $request->nama_grupband;
            $req['deskripsi'] = $request->deskripsi;
            $req['harga'] = $request->harga;
            $req['Wilayah']=$request->Wilayah;
            $req['photo']= $request->photo;
            $req['cover']= $request->cover;
            $req['youtube_video']=$request->youtube_video;
            $req['url_website']=$request->url_website;
            $req['username_soundcloud']=$request->username_soundcloud;
            $req['username_reverbnation']=$request->username_reverbnation;

            // // $req['cover'];
            // $req['slug']=$request->slug;

            // dd($req['g'][0]);


            $grupBandBaru = new Grupband;
            $grupBandBaru->admin_id = (int)$req['admin_id'];
            $grupBandBaru->nama_grupband = $req['nama_grupband'];
            // $grupBandBaru->slug = $req['slug'];
            $grupBandBaru->Wilayah = $req['Wilayah'];
            $grupBandBaru->harga = $req['harga'];
            // $grupBandBaru->nama_grupband = ;
            $grupBandBaru->deskripsi = $req['deskripsi'];
            
            // $grupBandBaru->Wilayah= $request['Wilayah'];
            $grupBandBaru->photo=$request['photo'];
            // $grupBandBaru->cover=$request['cover'];
            if($request['youtube_video']!=""){
                $grupBandBaru->youtube_video=$request['youtube_video'];
            }
            if($request['username_soundcloud']!=""){
                $grupBandBaru->username_soundcloud=$request['username_soundcloud'];
            }
            if($request['username_reverbnation']!=""){
                $grupBandBaru->username_reverbnation=$request['username_reverbnation'];
            }

            // dd($req);
            // return array($musicianAuth);
            $grupBandBaru->save();



            $admin_band = Grupband::where('admin_id',$musicianAuth->id)->orderBy('created_at', 'desc')->first();

            $bandmusisi = new GrupbandMusisi;
            $bandmusisi->position_id = $req['posisi'];
            $bandmusisi->musician_id = $musicianAuth->id;
            $bandmusisi->grupband_id = $admin_band->id;
            
            $bandmusisi->save();

            $genre = array();
            for($i=0;$i<$req['genre_count'];$i++){
                $genre = explode(' ',$req['genre_id']);
                $genreBand = new GenreBand;
                $genreBand->band_id = $admin_band->id;
                $genreBand->genre_id = $genre[$i];
                $genreBand->save();
            }



            return array("message"=>"Success, Band has been created","error"=>0, "genre"=>$genre);
        }

        return array("message"=>"Failed to create band","error"=>1,"genre"=>$genre);
    }


    public function gigOffer(Request $request){

        $userAuth = Musician::find($request['user_id']);
        
        if($userAuth!=null){
            $gig = Gig::find($request['gig_id']);
            // $gig->tanggal_mulai = $request['tanggal_mulai'];
            // $gig->tanggal_selesai = $request['tanggal_selesai'];

            $interval = round((strtotime($request['tanggal_selesai']) - strtotime($request['tanggal_mulai']))/3600);

            $book = new Sewa;
            $book->gig_id = $request['gig_id'];
            $book->object_id = $request['object_id'];
            if($request['tipe']=="Solo"){
                $object = Musician::find($request['subject_id']);
                $book->total_biaya = $interval*$object->harga_sewa;
                $book->type_sewa = 'musisihire';
                $book->subject_id = $object->id;

               
            }
            else{
                $object = Grupband::find($request['subject_id']);
                $book->total_biaya = $interval*$object->harga;
                $book->type_sewa = 'bandhire';
                $book->subject_id = $object->id;

                // Firebase::sendPushNotification(
                //     array(
                //             'object_id'=>$request['object_id'],
                //             'subject_id'=>$request['user_id'],
                //             'user_id'=>$request['object_id'],
                //             'type_user'=>'musisi',
                //             'type_notif'=>'reqsewa',
                //             'type_subject'=>'organizer',
                //             'baca'=>'N'
                //         ),
                //     array(
                //             'title'=>'GigHub',
                //             'body'=>''.$object->nama_grupband.' mengirimkan permintaan penawaran gig kepada anda',
                //             'type'=>'booking'
                //         )
                // );
            }
            $book->save();

             Firebase::sendPushNotification(
                    array(
                            'object_id'=>$request['object_id'],
                            'subject_id'=>$request['subject_id'],
                            'user_id'=>$request['user_id'],
                            'type_user'=>'organizer',
                            'type_notif'=>'reqoffer',
                            'type_subject'=>'musisi',
                            'baca'=>'N'
                        ),
                    array(
                            'title'=>'GigHub',
                            'body'=>''.$object->name.' mengirimkan permintaan penawaran gig kepada anda',
                            'type'=>'booking'
                        )
                );
            


            return array("message"=>"Success, Gig Offer has been sent","error"=>0);
        }
        return array("message"=>"Failed, Gig Offer Fail to sent","error"=>1);
    }



    public function listGigOffer(Request $request){

        $musician = Musician::find($request['user_id']);

        $_result = DB::select("SELECT m.id,name, Wilayah, harga_sewa,photo,deskripsi,m.tipe, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM musicians as m JOIN genre_musisi as gm ON m.id = gm.musician_id JOIN genres as g ON g.id= gm.genre_id WHERE m.id =".$musician->id." GROUP BY m.id, m.name UNION SELECT gpb.id, nama_grupband,Wilayah,harga,photo,deskripsi,gpb.tipe, GROUP_CONCAT(g.genre_name separator ', ') genrenya FROM grupbands as gpb JOIN genre_bands as gb ON gpb.id = gb.band_id JOIN genres as g ON g.id= gb.genre_id JOIN grupband_musisi as gpbm ON gpbm.grupband_id = gpb.id WHERE gpbm.musician_id=".$musician->id." GROUP BY gpb.id, gpb.nama_grupband 
");

        return array("message"=>"Success","error"=>0, "gigOfferMusicianList"=>$_result);
    }


    public function yourBands(Request $request){
        $musicianAuth = Musician::find($request['user_id']);
        $grupBandMusisi = DB::table('grupband_musisi')->where('musician_id',$musicianAuth->id)->first();
        // $groupbands = Grupband::where('admin_id',$musicianAuth->id)->get();

        // $_result = DB::select("SELECT gpbm.*,gb.nama_grupband, gb.tipe, gb.basis, gb.deskripsi, gb.Wilayah, gb.photo, gb.cover,gb.harga, gb.youtube_video, gb.url_website, gb.username_soundcloud, gb.username_reverbnation, gb.aktif, gb.admin_id, gb.slug, musicians.photo as photo_musisi, GROUP_CONCAT(positions.position_name separator ', ')posisi, GROUP_CONCAT(musicians.name separator ', ')anggota FROM grupband_musisi AS gpbm JOIN grupbands AS gb ON gpbm.grupband_id = gb.id JOIN musicians ON gpbm.musician_id = musicians.id JOIN positions ON gpbm.position_id = positions.id WHERE gpbm.musician_id = ".$request['user_id']);

        $_result = DB::select("SELECT gbm.*,gb.nama_grupband, gb.tipe, gb.basis, gb.deskripsi, gb.Wilayah, gb.photo, gb.cover,gb.harga, gb.youtube_video, gb.url_website, gb.username_soundcloud, gb.username_reverbnation, gb.aktif, gb.admin_id, gb.slug, m.photo as photo_musisi, GROUP_CONCAT(p.position_name separator ', ')posisi, GROUP_CONCAT(m.name separator ', ')anggota FROM grupbands AS gb JOIN grupband_musisi as gbm ON gb.id = gbm.grupband_id JOIN musicians as m ON gbm.musician_id = m.id JOIN positions AS p ON gbm.position_id = p.id WHERE gb.admin_id = ".$request['user_id']." GROUP BY gb.id");

        return response()->json(['status'=>'ok', 'error'=>0, 'groupBands'=>$_result],200);
    }

    // public function yourBands(){
    //     $yourbands = Grupband::
    // }

    public function updatePhotoProfile(Request $request){
        $user='-';
        if($request['tipe_user']=="msc"){

            $user = 'musician';

            DB::table('musicians')->where('id',$request['id'])
            ->update(array(
                'photo' => $request['photo'],
                ));

            $musicianTable = DB::table('musicians')->where('id',$request['id'])->first();

            $obj = $musicianTable;
            unset($obj->password);
            unset($obj->remember_token); 
            unset($obj->created_at); 
            unset($obj->updated_at);


        }
        else{
            $user = 'user';
            DB::table('users')->where('id',$request['id'])
            ->update(array(
                'photo'=>$request['photo'],
                ));
            
            $organizerTable = DB::table('users')->where('id',$request['id'])->first();
            
            $obj = $organizerTable;
            unset($obj->password);
            unset($obj->remember_token); 
            unset($obj->created_at); 
            unset($obj->updated_at);
        }

        return response()->json(["message"=>"Photo has been Updated","error"=>0,$user=>$obj],200);
    }

    public function updateProfile(Request $request){
        $req = $request->all();
        // $req['name'] = $request->name;
        // $req['email'] = $request->email;
        // $req['deskripsi'] = $request->deskripsi;
        $input = $request->all();

        $user='-';
        if($request['tipe_user']=="msc"){

            $user = 'musician';

            DB::table('musicians')->where('id',$request['id'])
            ->update(array(
                'name'=>$req['name'],
                'email'=>$req['email'],
                'deskripsi'=>$req['deskripsi'],
                'no_telp'=>$req['no_telp'],
                'Wilayah'=>$req['Wilayah'],
                'harga_sewa'=>$req['harga_sewa'],
                // 'photo' => $req['photo'],
                'youtube_video'=>$req['youtube_video'],
                'url_website'=>$req['url_website'],
                'username_soundcloud'=>$req['username_soundcloud'],
                'username_reverbnation'=>$req['username_reverbnation']
                ));
            
            $musicianAuth = DB::table('musicians')->where('id',$request['id'])->first();

            // $genreMusician = DB::table('genre_musisi')->where('musician_id',$request['id']);

            // $musicianAuth = Musician::find($request['id']);

            // $musicianAuth->decrement($musicianAuth->name);

            $obj = $musicianAuth;
            unset($obj->password);
            unset($obj->remember_token); 
            unset($obj->created_at); 
            unset($obj->updated_at);



            $genre = array();
            $genres = array();
            $genreMusisi =  GenreMusisi::where('musician_id', $req['id'])->get();
            
            // if(!$genreMusisi->isEmpty()){
                foreach ($genreMusisi as $genrem) {
                        $genres[] = $genrem->genre_id;                  
                    }

                for($i=0;$i<$req['genre_count'];$i++){
                    $genre = explode(' ',$req['genre_id']);
                }
                $result = array_merge(array_diff($genre, $genres),array_diff($genres, $genre));
                $sama = array_intersect($result, $genres);
                $beda = array_diff($result, $genres);

                if($sama!=null){
                    foreach ($sama as $genresama) {
                        GenreMusisi::where('musician_id', $req['id'])->where('genre_id', $genresama)->delete();
                    }
                }
                if($beda!=null){
                    foreach ($beda as $genrebeda) {
                        $genremu = new GenreMusisi;
                        $genremu->genre_id = $genrebeda;
                        $genremu->musician_id = $req['id'];
                        $genremu->save();
                    }
                }
            // }

                
        }
        
        else{

            $user = 'user';
            DB::table('users')->where('id',$request['id'])
            ->update(array(
                'first_name'=>$req['first_name'],
                'last_name'=>$req['last_name'],
                'email'=>$req['email']
                ));
            
            $organizerAuth = DB::table('users')->where('id',$request['id'])->first();
            // $musicianAuth = Musician::find($request['id']);

            // $musicianAuth->decrement($musicianAuth->name);

            $obj = $organizerAuth;
            unset($obj->password);
            unset($obj->remember_token); 
            unset($obj->created_at); 
            unset($obj->updated_at);   
        }


        return response()->json(["message"=>"Profile has been Updated","error"=>0,$user=>$obj],200);

    }

    public function updateMusicianBank(Request $request){

        $input = $request->all();

        $bank = Bank::join('bank_musisi', 'bank_musisi.bank_id','=','banks.id')
                            ->where('bank_musisi.musician_id', $request['id'])->first();

                
                if ($bank!=null) {
                    DB::table('banks')->where('id',$request['id'])
                        ->update(array(
                            'no_rek' => $request['no_rek'],
                            'atas_nama' => $request['atas_nama'],
                            'nama_bank' => $request['nama_bank'],
                            ));
                }
                else{
                //     return Bank::create([
                //     'nama_bank' => $request['nama_bank'],
                //     'no_rek' => $request['no_rek'],
                //     'atas_nama' => $request['atas_nama'],
                // ]);
                    $input['no_rek'] = $request->no_rek;
                    $input['atas_nama'] = $request->atas_nama;
                    $input['nama_bank'] = $request->nama_bank;
                    $bankid = Bank::create($input)->id;

                    $bankmusisi = new BankMusisi;
                    $bankmusisi->musician_id = $request['id'];
                    $bankmusisi->bank_id = $bankid;
                    $bankmusisi->save();
                }
                // return response()->json(["message"=>"Bank has been Updated","error"=>0,$bank],200);
                return array("message"=>"Bank has been Updated","error"=>0);
    }


    public function bankMusisi(Request $request){
        $bankmusisi = DB::select("SELECT b.* FROM banks as b JOIN bank_musisi as bm ON b.id = bm.bank_id JOIN musicians as m ON bm.musician_id = m.id WHERE m.id = ".$request['id']);

        return response()->json(['status'=>'ok', 'error'=>0, 'bank'=>$bankmusisi],200);
    }

    public function confirmRequest(Request $request){
        $req = $request->all();


        $band = DB::table('grupbands')->where('admin_id',$req['user_id'])->first();
        // $sewa = DB::table('sewas')->where('object_id',$band->id)->update(['status_request' =>1]);
        $sewa = Sewa::find($req['sewa_id']);
        $sewa->update(['status_request'=>'1']);
        $object = Musician::find($request['user_id']);

        // $sewa->update(['status_request' =>1]);
 // // subject_id mengirim object_id ke user_id (type_user) 
        if ($req['tipe']=="org") {
            
            Firebase::sendPushNotification(
                        array(
                            'object_id'=>$sewa->id,
                            'subject_id'=>$req['user_id'],
                            'user_id'=>$sewa->object_id,
                            'type_user'=>'musisi',
                            'type_notif'=>'terimasewa',
                            'type_subject'=>'organizer',
                            'baca'=>'N'
                        ),
                    array(
                            'title'=>'GigHub',
                            'body'=>''.$object->name.' menerima permintaan sewa anda',
                            'type'=>'booking'
                        )
                );
        }
        else{
            Firebase::sendPushNotification(
                        array(
                            'object_id'=>$sewa->id,
                            'subject_id'=>$req['user_id'],
                            'user_id'=>$sewa->object_id,
                            'type_user'=>'organizer',
                            'type_notif'=>'terimasewa',
                            'type_subject'=>'musisi',
                            'baca'=>'N'
                        ),
                    array(
                            'title'=>'GigHub',
                            'body'=>''.$object->name.' menerima permintaan sewa anda',
                            'type'=>'booking'
                        )
                );
        }

        return array("message"=>"Success, Confirm Request has been sent","error"=>0);
    }

    public function viewAddAnggota(Request $request){
        $req = $request->all();
        // $band = DB::table('grupbands')->where('admin_id',$req['user_id'])->first();

        // $calonAnggota = DB::select("SELECT m.* FROM musicians AS m JOIN grupband_musisi as gbm ON m.id = gbm.musician_id WHERE gbm.musician_id <> ".$req['user_id']." AND gbm.grupband_id <> ".$req['grupband_id']);

        $calonAnggota = DB::select("SELECT m.* FROM musicians AS m JOIN grupband_musisi as gbm ON m.id = gbm.musician_id WHERE gbm.musician_id <> ".$req["user_id"]." AND gbm.grupband_id <> ".$req["grupband_id"]." UNION SELECT m.* FROM musicians AS m LEFT JOIN grupband_musisi AS gbm ON m.id = gbm.musician_id WHERE gbm.musician_id IS NULL AND gbm.grupband_id is null");


        return response()->json(['status'=>'ok', 'error'=>0, 'musicianans'=>$calonAnggota],200);

    }

    public function addAnggota(Request $request){
        $req = $request->all();

        $calonAnggota = Musician::find($req['calon_id']);
        $band = DB::table('grupbands')->where('admin_id',$req['user_id'])->first();

        $grupBandMusisi = new GrupbandMusisi;
        $grupBandMusisi->position_id = $req['position_id'];
        $grupBandMusisi->musician_id = $req['calon_id'];
        $grupBandMusisi->grupband_id = $band->id;
        $grupBandMusisi->save();

        return array("message"=>$calonAnggota->name." has been Added","error"=>0);
    }

    public function viewRemoveAnggota(Request $request){
        $req = $request->all();

        // $calonMantanAnggota = DB::select("SELECT m.*, p.position_name FROM musicians AS m JOIN grupband_musisi AS gbm ON gbm.musician_id = m.id JOIN positions AS p ON gbm.position_id = p.id JOIN grupbands AS gb ON gbm.grupband_id = gb.id WHERE gb.admin_id = 4 AND gbm.musician_id <> ".$req['user_id']." AND gb.id = ".$req['grupband_id']);

        $calonMantanAnggota = DB::select("SELECT m.*, p.position_name FROM musicians AS m JOIN grupband_musisi AS gbm ON gbm.musician_id = m.id JOIN positions AS p ON gbm.position_id = p.id WHERE gbm.grupband_id = ".$req['grupband_id']." AND m.id <> ".$req['user_id']);

        return response()->json(['status'=>'ok', 'error'=>0, 'musicianans'=>$calonMantanAnggota],200);

    }

    public function removeAnggota(Request $request){
        $req = $request->all();

        $calonMantanAnggota = Musician::find($req['user_id']);
        $GrupbandMusisi = DB::table('grupband_musisi')
            ->where('musician_id',$req['user_id'])
            ->where('grupband_id',$req['grupband_id'])
            ->delete();
            // ->update(array(
            //     'position_id'=>$req['position_id'],
            //     'musician_id'=>$req['user_id'],
            //     'grupband_id'=>$req['grupband_id']
            //     ));
        // $band = Grupband::find($req['user_id']);

        // $GrupbandMusisi->delete();

        // $grupBandMusisi = new GrupbandMusisi;
        // $grupBandMusisi->position_id = $req['position_id'];
        // $grupBandMusisi->musician_id = $req['calon_id'];
        // $grupBandMusisi->grupband_id = $band->id;
        // $grupBandMusisi->save();

        return array("message"=>$calonMantanAnggota->name." has been Removed","error"=>0);
    }


    public function position(){
        $positions = Position::all();
        return response()->json(['message'=>'ok','error'=>0, 'positions'=>$positions],200);
    }

    public function musicianPosition(Request $request){
        $req = $request->all();
        $musicianAuth = Musician::find($request['user_id']);
        $band = DB::table('grupbands')->where('admin_id',$musicianAuth->id)->first();

        if($musicianAuth!=null){
            $musicianPosition = DB::select("SELECT p.* FROM positions as p JOIN grupband_musisi as gpbm ON p.id = gpbm.position_id WHERE gpbm.grupband_id = ".$band->id);

            return array("message"=>"OK","error"=>0,"musicianPosition"=>$musicianPosition);
        }
        else{
            return array("message"=>"OK","error"=>0,"musicianPosition"=>$musicianPosition);

        }
    }

    public function viewSaldo(Request $request){
        $req = $request->all();

        $viewSaldo = DB::select("SELECT s.*, m.name FROM saldo AS s JOIN musicians AS m ON s.subject_id = m.id WHERE m.id = ".$req['user_id']." UNION SELECT s.*, gb.nama_grupband FROM saldo AS s JOIN grupband_musisi AS gbm ON s.subject_id = gbm.grupband_id JOIN grupbands AS gb ON gbm.grupband_id = gb.id WHERE gb.admin_id = ".$req['user_id']);

        return array("message"=>"OK","error"=>0,"musicianSaldos"=>$viewSaldo);
    }

    public function viewMember(Request $request){
        $req = $request->all();

        $groupMembers = DB::select("SELECT GROUP_CONCAT(m.name separator ', ') member FROM musicians AS m JOIN grupband_musisi AS gbm ON m.id = gbm.musician_id WHERE gbm.grupband_id = ".$req['grupband_id']);


        return array("message"=>"OK","error"=>0,"groupMembers"=>$groupMembers);
    }

    public function withdraw(Request $request){
        $saldo = Saldo::find($request['saldo_id']);
        $saldoAkhir = $saldo->saldo - $request['jumlah'];

        $withdraw = New Withdraw;
        $withdraw->jumlah = $request['jumlah'];
        $withdraw->saldo_id = $request['saldo_id'];
        $withdraw->saldo_akhir = $saldoAkhir;
        $withdraw->save();

        Saldo::where('id', $request['saldo_id'])->update(['saldo' => $saldoAkhir]);

        return array("message"=>"Withdrawal request has been sent","error"=>0);
    }

    public function logout(Request $request){

        if($request['tipe_user']=="msc"){
            Musician::where('id',$request['user_id'])->update(['firebase'=>'']);
        }
        else if($request['tipe_user']=="org"){
            User::where('id',$request['user_id'])->update(['firebase'=>'']);
        }

        return array("message"=>"Logout Success","error"=>0);
    }

    public function organizerProfile(Request $request){

        // $OrganizerProfile = DB::select("SELECT * FROM users WHERE id=".$request['user_id']);
        $OrganizerProfile = DB::table('users')->where('id',$request['user_id'])->first();

         return response()->json(["message"=>"Photo has been Updated","error"=>0,"user"=>$OrganizerProfile],200);
    }


    public function declineBookRequest(Request $request){
        // $Book = sewa::find($request['sewa_id']);

        if($request['sewa_id']!=null || $request['sewa_id']!=""){
            DB::table('sewas')->where('id',$request['sewa_id'])
                ->update(array(
                    'status_request' => '2',
                    ));
            
            return array("message"=>"Decline success","error"=>0);
        }
        elseif ($request['sewa_id']==null || $request['sewa_id']=="") {
            return array("message"=>"Failed to Decline","error"=>1);
        }


    }

    public function musicianReviewer(Request $request){
        if($request!=null){
            if($request['tipe']=="Solo"){   
                $_result = DB::select("SELECT r.*, u.first_name, u.last_name, u.email, u.photo,u.aktif, r.created_at FROM review as r JOIN sewas as s ON r.sewa_id = s.id JOIN musicians as m ON s.object_id = m.id JOIN users AS u ON r.user_id = u.id WHERE m.id = ".$request['musician_id']);
                return response()->json(["message"=>"ok","error"=>0,"yourReviews"=>$_result],200);
            }
            else if($request['tipe']=="Group"){
                $_result = DB::select("SELECT r.*, u.first_name, u.last_name, u.email, u.photo,u.aktif, r.created_at FROM review as r JOIN sewas as s ON r.sewa_id = s.id JOIN grupbands as gpb ON s.object_id = gpb.id JOIN users AS u ON r.user_id = u.id WHERE gpb.id = ".$request['musician_id']);
                return response()->json(["message"=>"ok","error"=>0,"yourReviews"=>$_result],200);
            }
        }
        else{
            return response()->json(["message"=>"error","error"=>1,"yourReviews"=>$_result]);
        }
    }

    public function updateProfileBand(Request $request){
         $req = $request->all();
        // $req['name'] = $request->name;
        // $req['email'] = $request->email;
        // $req['deskripsi'] = $request->deskripsi;

            DB::table('grupbands')->where('id',$request['id'])
            ->update(array(
                'nama_grupband'=>$req['nama_grupband'],
                'deskripsi'=>$req['deskripsi'],
                'basis'=>$req['basis'],
                'Wilayah'=>$req['Wilayah'],
                'harga'=>$req['harga'],
                // 'photo' => $req['photo'],
                'youtube_video'=>$req['youtube_video'],
                'url_website'=>$req['url_website'],
                'username_soundcloud'=>$req['username_soundcloud'],
                'username_reverbnation'=>$req['username_reverbnation']
                ));
            
            $grupband = DB::table('grupbands')->where('id',$request['id'])->first();

            return response()->json(["message"=>"Profile has been Updated","error"=>0,"groupBand"=>$grupband],200);
    }
}
