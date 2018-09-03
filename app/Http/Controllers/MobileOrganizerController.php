<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\User;
use App\Gig;
use App\Sewa;
use App\Grupband;
use App\Musician;
use App\KonfirmasiPembayaran;
use App\Review;
use DB;
use Hash;
use App\Classes\Firebase;

class MobileOrganizerController extends Controller
{
    public function register(Request $request){
        User::create([
            'first_name'=>$request['first_name'],
            'last_name'=>$request['last_name'],
            'email'=>$request['email'],
            'firebase' => $request['firebase'],
            'password'=>bcrypt($request['password']),
        ]);

        $user = User::where('email',$request['email'])->first();

        return response()->json(["message"=>"Register success","error"=>0,"user"=>$user],200);
    }

    public function tesnotif(){
        // public static function sendPushNotification($data, $notif){
        // // 'id','object_id','subject_id','user_id','type_user','type_notif','type_subject','baca'
        // // udin mengirim permintaan sewa ke anda
        // // subject_id mengirim object_id ke user_id (type_user) 
        // Notif::Create($data);

        Firebase::sendPushNotification(
            array(
                    'object_id'=>'',
                    'subject_id'=>'',
                    'user_id'=>'',
                    'type_user'=>'',
                    'type_notif'=>'',
                    'type_subject'=>'',
                    'baca'=>''
                ),
            array(
                    'title'=>'GigHub',
                    'body'=>'isi pesan'
                )
            );
        return '';

    }

    public function login(Request $request){
        $listUser = DB::table('users')->where('email',$request['email'])->first();
        $user = User::where('email',$request['email'])->first();

        if($user==null){
            $Response['message']="Email not registered";
            $Response['error']=1;
            $Response['user'] = $user;
        }
        else {
            if(Hash::check($request['password'], $user->password)){
                $Response['message'] = "Login Success, Welcome ".$user->first_name;
                $Response['error'] = 0;
                
                $user->firebase = $request['firebase'];
                $user->save();
                $obj = $user;
                $Response['user'] = $obj;
            }
            else{
                $Response['message'] = "Login Error, Wrong Password";
                $Response['error'] = 2;
                $Response['user'] = null;
            }
        }
        return $Response;
    }

    public function createGig(Request $request){
        $req = $request->all();
        $organizerAuth = User::find($request['user_id']);

        $req['user_id'] = $organizerAuth->id;
        $req['nama_gig'] = $request->nama_gig;
        if($req['photo_gig'] != ""){
            $req['photo_gig'] = $request->photo_gig;
        }
        $req['deskripsi'] = $request->deskripsi;
        $req['lokasi'] = $request->lokasi;
        $req['detail_lokasi'] = $request->detail_lokasi;
        $req['tanggal_mulai'] = $request->tanggal_mulai;
        $req['tanggal_selesai'] = $request->tanggal_selesai;
        $req['lat'] = $request->lat;
        $req['lng'] = $request->lng;
        $req['type_gig'] = "post";

        // $gigBaru = new Gig;
        // dd($req);

        Gig::create($req);
        return array("message"=>"Success, Gig has been created","error"=>0);
    }

    public function yourGigs(Request $request){
        $req = $request->all();
        $organizerAuth = User::find($request['user_id']);        
        $yourGigs = Gig::where('user_id', $organizerAuth->id)->get();
        // dd($yourGigs);
        return response()->json(['status'=>'ok', 'error'=>0, 'yourgigs'=>$yourGigs],200);

    }

    public function allGigs(){
        $gigs = Gig::where([['type_gig', "post"],['status','0'],])->get();
        // $gigs = DB::table('gigs')->where('type_gig','=', "post",'AND','status','=','0')->get();
        return response()->json(['status'=>'ok','error'=>0, 'gigs'=>$gigs],200);
    }

    public function bookMusician(Request $request){
        $req = $request->all();
        $organizerAuth = User::find($request['user_id']);
        
        if($organizerAuth!=null){
            $req['user_id'] = $organizerAuth->id;
            $req['nama_gig'] = $request->nama_gig;
            $req['lokasi'] = $request->lokasi;
            $req['detail_lokasi'] = $request->detail_lokasi;
            $req['tanggal_mulai'] = $request->tanggal_mulai;
            $req['tanggal_selesai'] = $request->tanggal_selesai;
            if($req['photo_gig'] ==""){
                $req['photo_gig'] = $request->photo_gig;
            }
            $req['lat'] = $request->lat;
            $req['lng'] = $request->lng;

            $gigBaru = new Gig;
            $gigBaru->user_id = $req['user_id'];
            $gigBaru->nama_gig = $req['nama_gig'];
            $gigBaru->photo_gig = $req['photo_gig'];
            $gigBaru->lokasi = $req['lokasi'];
            $gigBaru->detail_lokasi = $req['detail_lokasi'];
            $gigBaru->lat = $req['lat'];
            $gigBaru->lng = $req['lng'];
            $gigBaru->tanggal_mulai = $req['tanggal_mulai'];
            $gigBaru->tanggal_selesai = $req['tanggal_selesai'];

            $gigBaru->save();

            $gig = Gig::where('user_id',$organizerAuth->id)
                            ->where('status',0)
                            ->orderBy('created_at', 'desc')->first();

            $interval = round((strtotime($gig->tanggal_selesai) - strtotime($gig->tanggal_mulai))/3600);

            $book = new Sewa;

            if($request['tipe']=="Group"){
                $object = Grupband::find($request['object_id']);
                $book->total_biaya = $interval*$object->harga;
                $book->gig_id = $gig->id;
                $book->object_id = $object->id;
                $book->subject_id = $organizerAuth->id;
                $book->type_sewa='hireband';
                $book->save();
            }
            else{
                $object = Musician::find($request['object_id']);
                $book->total_biaya = $interval*$object->harga_sewa;
                $book->gig_id = $gig->id;
                $book->object_id = $object->id;
                $book->subject_id = $organizerAuth->id;
                $book->type_sewa='hiremusisi';
                $book->save();


                Firebase::sendPushNotification(
            array(
                    'object_id'=>$book->gig_id,
                    'subject_id'=>$request['user_id'],
                    'user_id'=>$request['object_id'],
                    'type_user'=>'musisi',
                    'type_notif'=>'reqsewa',
                    'type_subject'=>'organizer',
                    'baca'=>'N'
                ),
            array(
                    'title'=>'GigHub',
                    'body'=>''.$organizerAuth->first_name.' mengirimkan permintaan sewa kepada anda',
                    'type'=>'booking'
                )
            );
            }


            // dd($book);

            return array("message"=>"Success, Book Request has been sent","error"=>0);
        }
        
        return array("message"=>"Failed, Book Request has failed to sent","error"=>1);
    }

    public function onRequestBooking(Request $request){

        $req = $request->all();

        if($request['tipe_user']=="org"){
            $user = User::find($req['user_id']);

            $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id 
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.subject_id = m.id
                WHERE s.type_sewa = 'musisihire' AND s.object_id = ".$user->id."
                AND (s.status = '0' AND s.status_request = '0')

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.object_id = m.id
                WHERE s.type_sewa = 'hiremusisi' AND s.subject_id = ".$user->id."
                AND (s.status = '0' AND s.status_request = '0')

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.subject_id = gb.id
                WHERE s.type_sewa = 'bandhire' AND s.object_id = ".$user->id."
                AND (s.status = '0' AND s.status_request = '0')

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.object_id = gb.id
                WHERE s.type_sewa = 'hireband' AND s.subject_id = ".$user->id."
                AND (s.status = '0' AND s.status_request = '0')");
        }
        else{
            $user = Musician::find($req['user_id']);
            $band = DB::table('musicians')
                ->join('grupband_musisi','musicians.id','=','grupband_musisi.musician_id')
                ->join('grupbands','grupband_musisi.grupband_id','=','grupbands.id')
                ->select('grupbands.*')
                ->where('musicians.id',$req['user_id'])->first();

         
            if($band!=null){    
            $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id 
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.subject_id = m.id
                WHERE s.type_sewa = 'musisihire' AND s.subject_id = ".$user->id."
                AND (s.status = '0' AND s.status_request = '0')

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.object_id = m.id
                WHERE s.type_sewa = 'hiremusisi' AND s.object_id = ".$user->id."
                AND (s.status = '0' AND s.status_request = '0')

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,gb.id,gb.admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.subject_id = gb.id
                WHERE s.type_sewa = 'bandhire' AND s.subject_id = ".$band->id."
                AND (s.status = '0' AND s.status_request = '0')

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,gb.id,gb.admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.object_id = gb.id
                WHERE s.type_sewa = 'hireband' AND s.object_id = ".$band->id."
                AND (s.status = '0' AND s.status_request = '0')");
            }
            else{
                $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id 
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.subject_id = m.id
                WHERE s.type_sewa = 'musisihire' AND s.subject_id = ".$user->id."
                AND (s.status = '0' AND s.status_request = '0')

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.object_id = m.id
                WHERE s.type_sewa = 'hiremusisi' AND s.object_id = ".$user->id."
                AND (s.status = '0' AND s.status_request = '0')");                
            }
        }


        return array("message"=>"OK","error"=>0,"penyewaanList"=>$_result);
    }




    public function onProccessBooking(Request $request){
        $req = $request->all();

        if($request['tipe_user']=="org"){
            $user = User::find($req['user_id']);

            $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id 
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.subject_id = m.id
                WHERE s.type_sewa = 'musisihire' AND s.object_id = ".$user->id."
                AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.object_id = m.id
                WHERE s.type_sewa = 'hiremusisi' AND s.subject_id = ".$user->id."
                AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.subject_id = gb.id
                WHERE s.type_sewa = 'bandhire' AND s.object_id = ".$user->id."
                AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.object_id = gb.id
                WHERE s.type_sewa = 'hireband' AND s.subject_id = ".$user->id."
                AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'");

        }
        else{
            $user = Musician::find($req['user_id']);
            // $band = DB::table('grupbands')->where('admin_id',$req['user_id'])->first();
            $band = DB::table('musicians')
                ->join('grupband_musisi','musicians.id','=','grupband_musisi.musician_id')
                ->join('grupbands','grupband_musisi.grupband_id','=','grupbands.id')
                ->select('grupbands.*')
                ->where('musicians.id',$req['user_id'])->first();

        // $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
        //         , m.name,m.harga_sewa,m.photo,u.first_name,u.last_name
        //         FROM sewas as s
        //         JOIN users as u ON s.object_id = u.id 
        //         JOIN gigs as g ON s.gig_id = g.id
        //         JOIN musicians as m ON s.subject_id = m.id
        //         WHERE s.type_sewa = 'musisihire' AND s.subject_id = ".$user->id."
        //         AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'

        //         UNION

        //         SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
        //         , m.name,m.harga_sewa,m.photo,u.first_name,u.last_name
        //         FROM sewas as s
        //         JOIN users as u ON s.subject_id = u.id
        //         JOIN gigs as g ON s.gig_id = g.id
        //         JOIN musicians as m ON s.object_id = m.id
        //         WHERE s.type_sewa = 'hiremusisi' AND s.object_id = ".$user->id."
        //         AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'

        //         UNION

        //         SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
        //         FROM sewas as s
        //         JOIN users as u ON s.object_id = u.id
        //         JOIN gigs as g ON s.gig_id = g.id
        //         JOIN grupbands as gb ON s.subject_id = gb.id
        //         WHERE s.type_sewa = 'bandhire' AND s.subject_id = ".$band->id."
        //         AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'

        //         UNION

        //         SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
        //         FROM sewas as s
        //         JOIN users as u ON s.subject_id = u.id
        //         JOIN gigs as g ON s.gig_id = g.id
        //         JOIN grupbands as gb ON s.object_id = gb.id
        //         WHERE s.type_sewa = 'hireband' AND s.object_id = ".$band->id."
        //         AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'");

            if($band!=null){

            $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id 
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.subject_id = m.id
                WHERE s.type_sewa = 'musisihire' AND s.subject_id = ".$user->id."
                AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.object_id = m.id
                WHERE s.type_sewa = 'hiremusisi' AND s.object_id = ".$user->id."
                AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,gb.id,gb.admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.subject_id = gb.id
                WHERE s.type_sewa = 'bandhire' AND s.subject_id = ".$band->id."
                AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,gb.id,gb.admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.object_id = gb.id
                WHERE s.type_sewa = 'hireband' AND s.object_id = ".$band->id."
                AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'");
            }
            else{
                $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id 
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.subject_id = m.id
                WHERE s.type_sewa = 'musisihire' AND s.subject_id = ".$user->id."
                AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.object_id = m.id
                WHERE s.type_sewa = 'hiremusisi' AND s.object_id = ".$user->id."
                AND (s.status = '0' OR s.status = '1' OR s.status = '2') AND s.status_request = '1'");
            }
        }
        

        return array("message"=>"OK","error"=>0,"penyewaanList"=>$_result);
    }


    public function completedBooking(Request $request){
        $req = $request->all();

        if($request['tipe_user']=="org"){
            $user = User::find($req['user_id']);

            $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id 
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.subject_id = m.id
                WHERE s.type_sewa = 'musisihire' AND s.object_id = ".$user->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.object_id = m.id
                WHERE s.type_sewa = 'hiremusisi' AND s.subject_id = ".$user->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.subject_id = gb.id
                WHERE s.type_sewa = 'bandhire' AND s.object_id = ".$user->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.object_id = gb.id
                WHERE s.type_sewa = 'hireband' AND s.subject_id = ".$user->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'");

        }
        else{
            $user = Musician::find($req['user_id']);
            // $band = DB::table('grupbands')->where('admin_id',$req['user_id'])->first();
            $band = DB::table('musicians')
                ->join('grupband_musisi','musicians.id','=','grupband_musisi.musician_id')
                ->join('grupbands','grupband_musisi.grupband_id','=','grupbands.id')
                ->select('grupbands.*')
                ->where('musicians.id',$req['user_id'])->first();

            // $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
            //     , m.name,m.harga_sewa,m.photo,u.first_name,u.last_name
            //     FROM sewas as s
            //     JOIN users as u ON s.object_id = u.id 
            //     JOIN gigs as g ON s.gig_id = g.id
            //     JOIN musicians as m ON s.subject_id = m.id
            //     WHERE s.type_sewa = 'musisihire' AND s.subject_id = ".$user->id."
            //     AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

            //     UNION

            //     SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
            //     , m.name,m.harga_sewa,m.photo,u.first_name,u.last_name
            //     FROM sewas as s
            //     JOIN users as u ON s.subject_id = u.id
            //     JOIN gigs as g ON s.gig_id = g.id
            //     JOIN musicians as m ON s.object_id = m.id
            //     WHERE s.type_sewa = 'hiremusisi' AND s.object_id = ".$user->id."
            //     AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

            //     UNION

            //     SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
            //     FROM sewas as s
            //     JOIN users as u ON s.object_id = u.id
            //     JOIN gigs as g ON s.gig_id = g.id
            //     JOIN grupbands as gb ON s.subject_id = gb.id
            //     WHERE s.type_sewa = 'bandhire' AND s.subject_id = ".$band->id."
            //     AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

            //     UNION

            //     SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
            //     FROM sewas as s
            //     JOIN users as u ON s.subject_id = u.id
            //     JOIN gigs as g ON s.gig_id = g.id
            //     JOIN grupbands as gb ON s.object_id = gb.id
            //     WHERE s.type_sewa = 'hireband' AND s.object_id = ".$band->id."
            //     AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

            //     ");

            if($band!=null){    
            $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id 
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.subject_id = m.id
                WHERE s.type_sewa = 'musisihire' AND s.subject_id = ".$user->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.object_id = m.id
                WHERE s.type_sewa = 'hiremusisi' AND s.object_id = ".$user->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,gb.id,gb.admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.subject_id = gb.id
                WHERE s.type_sewa = 'bandhire' AND s.subject_id = ".$band->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,gb.id,gb.admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.object_id = gb.id
                WHERE s.type_sewa = 'hireband' AND s.object_id = ".$band->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'");
            }
            else{
                $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id 
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.subject_id = m.id
                WHERE s.type_sewa = 'musisihire' AND s.subject_id = ".$user->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.object_id = m.id
                WHERE s.type_sewa = 'hiremusisi' AND s.object_id = ".$user->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'");
            }

        }
        

        return array("message"=>"OK","error"=>0,"penyewaanList"=>$_result);
    }


    public function konfirmasiPembayaran(Request $request){
        
            $konpem = new KonfirmasiPembayaran;
            $konpem->sewa_id = $request['sewa_id'];
            $konpem->nama_rek = $request['nama_rek'];
            $konpem->no_rek = $request['no_rek'];
            $konpem->nama_bank = $request['nama_bank'];
            // $konpem->photo = $request['photo'];
            if($request['photo'] != ""){
                $req['photo'] = $konpem->photo;
            }
            $konpem->bank_admin_id = $request['bank_admin_id'];

            // dd($konpem);
            $konpem->save();

            DB::table('sewas')->where('id',$request['sewa_id'])
            ->update(array(
                'status'=>'1'
                ));

            // Firebase::sendPushNotification(
            //         array(
            //                 'object_id'=>$request['object_id'],
            //                 'subject_id'=>$request['subject_id'],
            //                 'user_id'=>$request['user_id'],
            //                 'type_user'=>'organizer',
            //                 'type_notif'=>'reqoffer',
            //                 'type_subject'=>'musisi',
            //                 'baca'=>'N'
            //             ),
            //         array(
            //                 'title'=>'GigHub',
            //                 'body'=>''.$object->name.' melakukan konfirmasi pembayaran\nsilahkan tunggu konfirmasi\ndari admin',
            //                 'type'=>'booking'
            //             )
            //     );

            return array("message"=>"Success, Confirmation has been sent","error"=>0);

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

    public function uploadGigPhoto(Request $request){
        DB::table('gigs')->where('id',$request['gig_id'])
            ->update(array(
                'photo_gig' => $request['photo_gig'],
                ));

        return array("message"=>"Gig Photo has been uploaded","error"=>0);
    }

    public function declineGigRequest(Request $request){
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

    public function memberGroup(Request $request){
        $_result = DB::select("SELECT GROUP_CONCAT(p.position_name separator ', ')posisi, GROUP_CONCAT(m.name separator ', ')anggota FROM grupbands AS gb JOIN grupband_musisi as gbm ON gb.id = gbm.grupband_id JOIN musicians as m ON gbm.musician_id = m.id JOIN positions AS p ON gbm.position_id = p.id WHERE gb.id = ".$request['id']." GROUP BY gb.id");
        return response()->json(["message"=>"Call Member","error"=>0,"member"=>$_result[0]],200);
    }

    public function sendReview(Request $request){
        if ($request!=null) {
            $review = new Review;
            $review->sewa_id = $request['sewa_id'];
            $review->user_id = $request['user_id'];
            $review->nilai = $request['nilai'];
            $review->pesan = $request["pesan"];
            $review->save();

            DB::table('sewas')->where('id',$request['sewa_id'])
            ->update(array(
                'status'=>'4'
                ));

            // // subject_id mengirim object_id ke user_id (type_user) 

            $review = Review::find($request['review_id']);

            $pengirim = User::find($request['organizer_id']);
            $penerima = Musician::find($request['musician_id']);

            Firebase::sendPushNotification(
            array(
                    'object_id'=>$review->id,
                    'subject_id'=>$pengirim->id,
                    'user_id'=>$penerima->id,
                    'type_user'=>'musisi',
                    'type_notif'=>'review',
                    'type_subject'=>'organizer',
                    'baca'=>'N'
                ),
            array(
                    'title'=>'GigHub',
                    'body'=>"".$pengirim->first_name." mengirimkan review kepada anda",
                    'type'=>'booking'
                )
            );

            return array("message"=>"Success, Confirmation has been sent","error"=>0);
        }
        else{
            return array("message"=>"Failed to review","error"=>1);
        }
    }

    public function yourReview(Request $request){
        if($request!=null){
            if($request['type_sewa']=="hireband" || $request['type_sewa']=="bandhire"){   
                $_result = DB::select("SELECT r.*, u.first_name, u.last_name, u.email, u.photo,u.aktif, r.created_at FROM review as r JOIN sewas as s ON r.sewa_id = s.id JOIN grupbands as gpb ON s.object_id = gpb.id JOIN users AS u ON r.user_id = u.id WHERE r.sewa_id = ".$request['sewa_id']);

                return response()->json(["message"=>"ok","error"=>0,"yourReviews"=>$_result],200);
            }
            else if($request['type_sewa']=="hiremusisi" || $request['type_sewa']=="musisihire"){
                $_result = DB::select("SELECT r.*, u.first_name, u.last_name, u.email, u.photo,u.aktif, r.created_at FROM review as r JOIN sewas as s ON r.sewa_id = s.id JOIN musicians as m ON s.object_id = m.id JOIN users AS u ON r.user_id = u.id WHERE r.sewa_id = ".$request['sewa_id']);

                return response()->json(["message"=>"ok","error"=>0,"yourReviews"=>$_result],200);
            } 
        }
        else{
            return response()->json(["message"=>"error","error"=>1,"yourReviews"=>$_result]);
        }
    }

    public function totalGigs(Request $request){
        $req = $request->all();

       
        if ($req['tipe_user']=="msc" && $req['tipe']=="Group") {
            $band = DB::table('grupbands')
                // ->join('grupband_musisi','musicians.id','=','grupband_musisi.musician_id')
                // ->join('grupbands','grupband_musisi.grupband_id','=','grupbands.id')
                // ->select('grupbands.*')->get();
                ->where('id',$req['user_id'])->first();

            $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id 
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.subject_id = gb.id
                WHERE (s.status='4' AND s.type_sewa='bandhire' AND s.subject_id=".$req['user_id'].")|| (s.status='4' AND s.type_sewa='hireband' AND s.object_id=".$req['user_id'].")");
        }
        else{
            $user = Musician::find($req['user_id']);
            // $band = DB::table('grupbands')->where('admin_id',$req['user_id'])->first();
            $band = DB::table('musicians')
                ->join('grupband_musisi','musicians.id','=','grupband_musisi.musician_id')
                ->join('grupbands','grupband_musisi.grupband_id','=','grupbands.id')
                ->select('grupbands.*')
                ->where('musicians.id',$req['user_id'])->first();

            // $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
            //     , m.name,m.harga_sewa,m.photo,u.first_name,u.last_name
            //     FROM sewas as s
            //     JOIN users as u ON s.object_id = u.id 
            //     JOIN gigs as g ON s.gig_id = g.id
            //     JOIN musicians as m ON s.subject_id = m.id
            //     WHERE s.type_sewa = 'musisihire' AND s.subject_id = ".$user->id."
            //     AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

            //     UNION

            //     SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
            //     , m.name,m.harga_sewa,m.photo,u.first_name,u.last_name
            //     FROM sewas as s
            //     JOIN users as u ON s.subject_id = u.id
            //     JOIN gigs as g ON s.gig_id = g.id
            //     JOIN musicians as m ON s.object_id = m.id
            //     WHERE s.type_sewa = 'hiremusisi' AND s.object_id = ".$user->id."
            //     AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

            //     UNION

            //     SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
            //     FROM sewas as s
            //     JOIN users as u ON s.object_id = u.id
            //     JOIN gigs as g ON s.gig_id = g.id
            //     JOIN grupbands as gb ON s.subject_id = gb.id
            //     WHERE s.type_sewa = 'bandhire' AND s.subject_id = ".$band->id."
            //     AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

            //     UNION

            //     SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,u.first_name,u.last_name
            //     FROM sewas as s
            //     JOIN users as u ON s.subject_id = u.id
            //     JOIN gigs as g ON s.gig_id = g.id
            //     JOIN grupbands as gb ON s.object_id = gb.id
            //     WHERE s.type_sewa = 'hireband' AND s.object_id = ".$band->id."
            //     AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

            //     ");

            if($band!=null){    
            $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id 
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.subject_id = m.id
                WHERE s.type_sewa = 'musisihire' AND s.subject_id = ".$user->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.object_id = m.id
                WHERE s.type_sewa = 'hiremusisi' AND s.object_id = ".$user->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,gb.id,gb.admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.subject_id = gb.id
                WHERE s.type_sewa = 'bandhire' AND s.subject_id = ".$band->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug, gb.nama_grupband, gb.harga, gb.photo,gb.id,gb.admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN grupbands as gb ON s.object_id = gb.id
                WHERE s.type_sewa = 'hireband' AND s.object_id = ".$band->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'");
            }
            else{
                $_result = DB::select("SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.object_id = u.id 
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.subject_id = m.id
                WHERE s.type_sewa = 'musisihire' AND s.subject_id = ".$user->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'

                UNION

                SELECT s.*,g.nama_gig,g.deskripsi,g.photo_gig,g.lokasi,g.detail_lokasi,g.lat,g.lng,g.tanggal_mulai,g.tanggal_selesai,g.type_gig,g.user_id,g.aktif,g.slug
                , m.name,m.harga_sewa,m.photo,(m.id-m.id) AS grupband_id,(m.id-m.id) AS admin_id,u.first_name,u.last_name
                FROM sewas as s
                JOIN users as u ON s.subject_id = u.id
                JOIN gigs as g ON s.gig_id = g.id
                JOIN musicians as m ON s.object_id = m.id
                WHERE s.type_sewa = 'hiremusisi' AND s.object_id = ".$user->id."
                AND (s.status = '3' OR s.status = '4') AND s.status_request = '1'");
            }

        }
        

        return array("message"=>"OK","error"=>0,"penyewaanList"=>$_result);
    
    }
}
