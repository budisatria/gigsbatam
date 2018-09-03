<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Musician;
use App\GenreMusisi;
use App\Grupband;
use App\GrupbandMusisi;
use App\Sewa;
use App\Gig;
use App\User;
use App\Review;
use App\Notif;
use App\SaldoDetail;
use App\Saldo;
use App\KonfirmasiPembayaran;
use DB;
use Session;
use Cloudder;

class OrganizerController extends Controller
{
	public function __construct()
    {
    	if(Auth::guard('user')->user())
            $this->middleware('auth');
        elseif(Auth::guard('musician')->user())
            $this->middleware('musician');
        else
            $this->middleware('auth');

        //SET Orderan Selesai
        $setupdate = DB::Select("UPDATE sewas inner join gigs on gigs.id = sewas.gig_id SET sewas.status = '3' WHERE gigs.tanggal_selesai < NOW() AND sewas.status = '2'");

        //SET Orderan Batal jika tidak melunasi pembayaran dalam 24 JAM
        $setupdate = DB::Select("UPDATE sewas SET status = '5' WHERE HOUR(TIMEDIFF(NOW(), updated_at)) >= 24 AND status = '0' AND status_request = '1'");

        //Isi saldo otomatis
        $query = "SELECT sewas.* FROM sewas INNER JOIN gigs ON gigs.id = sewas.gig_id WHERE HOUR(TIMEDIFF(NOW(), gigs.tanggal_selesai)) >= 25 AND sewas.status = '3' AND sewas.status_request = '1'";
            $select = DB::Select($query);

            if($select != null){
                foreach ($select as $sewa) {
                    if($sewa->type_sewa == 'hireband'){
                        $ceksaldo = Saldo::where('subject_id', $sewa->object_id)->where('type_pemilik','=','band')->first();
                        if($ceksaldo == null){
                            $saldo = New Saldo;
                            $saldo->saldo = $sewa->total_biaya;
                            $saldo->subject_id = $sewa->object_id;
                            $saldo->type_pemilik = 'band';
                            $saldo->save();

                            $saldodetail = New SaldoDetail;
                            $saldodetail->saldo_id = $saldo->id;
                            $saldodetail->sewa_id = $sewa->id;
                            $saldodetail->save();
                        }else{
                            $ceksaldo->saldo = $sewa->total_biaya + $ceksaldo->saldo;
                            $ceksaldo->save();

                            $saldodetail = New SaldoDetail;
                            $saldodetail->saldo_id = $ceksaldo->id;
                            $saldodetail->sewa_id = $sewa->id;
                            $saldodetail->save();
                        }

                        $notif = New Notif;
                        $notif->object_id = $sewa->gig_id;
                        $notif->subject_id = 0;
                        $notif->user_id = $sewa->object_id;
                        $notif->type_subject = 'admin';
                        $notif->type_user = 'band'; 
                        $notif->type_notif = 'tambahsaldo';
                        $notif->save();

                    }
                    elseif($sewa->type_sewa == 'hiremusisi'){
                        $ceksaldo = Saldo::where('subject_id', $sewa->object_id)->where('type_pemilik','=','musisi')->first();
                        if($ceksaldo == null){
                            $saldo = New Saldo;
                            $saldo->saldo = $sewa->total_biaya;
                            $saldo->subject_id = $sewa->object_id;
                            $saldo->type_pemilik = 'musisi';
                            $saldo->save();

                            $saldodetail = New SaldoDetail;
                            $saldodetail->saldo_id = $saldo->id;
                            $saldodetail->sewa_id = $sewa->id;
                            $saldodetail->save();
                        }else{
                            $ceksaldo->saldo = $sewa->total_biaya + $ceksaldo->saldo;
                            $ceksaldo->save();

                            $saldodetail = New SaldoDetail;
                            $saldodetail->saldo_id = $ceksaldo->id;
                            $saldodetail->sewa_id = $sewa->id;
                            $saldodetail->save();
                        }

                        $notif = New Notif;
                        $notif->object_id = $sewa->gig_id;
                        $notif->subject_id = 0;
                        $notif->user_id = $sewa->object_id;
                        $notif->type_subject = 'admin';
                        $notif->type_user = 'musisi'; 
                        $notif->type_notif = 'tambahsaldo';
                        $notif->save();
                    }
                    elseif($sewa->type_sewa == 'bandhire'){
                        $ceksaldo = Saldo::where('subject_id', $sewa->subject_id)->where('type_pemilik','=','band')->first();
                        if($ceksaldo == null){
                            $saldo = New Saldo;
                            $saldo->saldo = $sewa->total_biaya;
                            $saldo->subject_id = $sewa->subject_id;
                            $saldo->type_pemilik = 'band';
                            $saldo->save();

                            $saldodetail = New SaldoDetail;
                            $saldodetail->saldo_id = $saldo->id;
                            $saldodetail->sewa_id = $sewa->id;
                            $saldodetail->save();
                        }else{
                            $ceksaldo->saldo = $sewa->total_biaya + $ceksaldo->saldo;
                            $ceksaldo->save();

                            $saldodetail = New SaldoDetail;
                            $saldodetail->saldo_id = $ceksaldo->id;
                            $saldodetail->sewa_id = $sewa->id;
                            $saldodetail->save();
                        }

                        $notif = New Notif;
                        $notif->object_id = $sewa->gig_id;
                        $notif->subject_id = 0;
                        $notif->user_id = $sewa->subject_id;
                        $notif->type_subject = 'admin';
                        $notif->type_user = 'band'; 
                        $notif->type_notif = 'tambahsaldo';
                        $notif->save();
                    }
                    elseif($sewa->type_sewa == 'musisihire'){
                        $ceksaldo = Saldo::where('subject_id', $sewa->subject_id)->where('type_pemilik','=','musisi')->first();
                        if($ceksaldo == null){
                            $saldo = New Saldo;
                            $saldo->saldo = $sewa->total_biaya;
                            $saldo->subject_id = $sewa->subject_id;
                            $saldo->type_pemilik = 'musisi';
                            $saldo->save();

                            $saldodetail = New SaldoDetail;
                            $saldodetail->saldo_id = $saldo->id;
                            $saldodetail->sewa_id = $sewa->id;
                            $saldodetail->save();
                        }else{
                            $ceksaldo->saldo = $sewa->total_biaya + $ceksaldo->saldo;
                            $ceksaldo->save();

                            $saldodetail = New SaldoDetail;
                            $saldodetail->saldo_id = $ceksaldo->id;
                            $saldodetail->sewa_id = $sewa->id;
                            $saldodetail->save();
                        }

                        $notif = New Notif;
                        $notif->object_id = $sewa->gig_id;
                        $notif->subject_id = 0;
                        $notif->user_id = $sewa->subject_id;
                        $notif->type_subject = 'admin';
                        $notif->type_user = 'musisi'; 
                        $notif->type_notif = 'tambahsaldo';
                        $notif->save();
                    }
                    $setupdate = DB::Select("UPDATE sewas SET status = '4' WHERE id = $sewa->id");
                }
            }

        //SET Orderan Batal jika tidak di konfirmasi organizer & musisi / band dalam 24 jam
        // $setdelete = DB::Select("DELETE gigs inner join sewas WHERE HOUR(TIMEDIFF(NOW(), sewas.created_at)) >= 24 AND sewas.status_request = '0'");
        // $setdelete = DB::Select("DELETE sewas WHERE HOUR(TIMEDIFF(NOW(), created_at)) >= 24 AND status_request = '0'");
	}

    public function index(){
    	// if(!Auth::guard('user')->user())
    	// 	return view('/');
    	// else{
    		$bands = Grupband::where('aktif', 'Y')->get();
            $musisis = Musician::where('aktif', 'Y')->get();
    		return view('organizer.discover')->with('musisi', $musisis)->with('band', $bands);
    	//}
    	//return view('musician.dashboard');
    }

    public function profile($slug){
        $organizer = User::whereSlug($slug)->firstOrFail()->id;
        $organizers = User::findOrFail($organizer);

        if(Auth::guard('user')->user() == null)
            $gigs = Gig::where('user_id', $organizers)->get();
        else
            $gigs = Gig::where('user_id', Auth::guard('user')->user()->id)->get();

        return view('organizer.profile')->with('organizer', $organizers)->with('gig', $gigs);
    }

    public function editProfile($slug){
        $organizer = User::whereSlug($slug)->firstOrFail()->id;
        $organizers = User::findOrFail($organizer);

        if(Auth::guard('user')->user()->id == $organizers->id)
            return view('organizer.editprofile')->with('organizer', $organizers);
        else
            return redirect()->back();
    }

    public function inputEditProfile(Request $req, $slug){
        $organizer = User::whereSlug($slug)->firstOrFail()->id;
        $organizers = User::findOrFail($organizer);

        $input = $req->all();
        if ($req->hasFile('photo'))
        {
            $file = array('photo' => $req->file('photo'));
            Cloudder::upload($req->file('photo')->getPathName());
            $input['photo'] = Cloudder::getPublicId();
        }

        $organizers->update($input);

        return redirect()->back();

    }

    public function sewaBand($slug){
        $bands = Grupband::whereSlug($slug)->firstOrFail()->id;
        $band = Grupband::findOrFail($bands);
        //echo Auth::guard('user')->user()->id
        return view('organizer.sewa-band')->with('bands', $band);
    }

    public function sewamusisi($slug){
        $musisis = Musician::whereSlug($slug)->firstOrFail()->id;
        $musisi = Musician::findOrFail($musisis);
        //echo Auth::guard('user')->user()->id
        return view('organizer.sewa-musisi')->with('musisis', $musisi);
    }

    public function inputSewaBand(Request $request, $slug){
    	$bands = Grupband::whereSlug($slug)->firstOrFail()->id;
        $band = Grupband::findOrFail($bands);

        $input = $request->all();
        $input['nama_gig'] = $request->name;
        $input['type_gig'] = 'sewa';
        $input['tanggal_mulai'] = $request->mulai;
        $input['tanggal_selesai'] = $request->selesai;
        $input['user_id'] = Auth::guard('user')->user()->id;
        if ($request->hasFile('photo'))
        {
            $file = array('photo' => $request->file('photo'));
            Cloudder::upload($request->file('photo')->getPathName());
            $input['photo_gig'] = Cloudder::getPublicId();
        }

        $gig = Gig::create($input);

        //$gig=Gig::where('user_id', Auth::guard('user')->user()->id)
        			//->where('status', 0)
        			//->orderBy('created_at', 'desc')->first();

        $interval = round((strtotime($gig->tanggal_selesai) - strtotime($gig->tanggal_mulai))/3600);

       	$sewa = new Sewa;
        $sewa->total_biaya = $interval * $band->harga;
        $sewa->gig_id = $gig->id;
        $sewa->object_id = $band->id;
        $sewa->subject_id = Auth::guard('user')->user()->id;
        $sewa->save();

        $notif = New Notif;
        $notif->object_id = $gig->id;
        $notif->subject_id = Auth::guard('user')->user()->id;
        $notif->user_id = $band->id;
        $notif->type_subject = 'organizer';
        $notif->type_user = 'band';
        $notif->type_notif = 'reqsewa';
        $notif->save();

        return redirect()->action('OrganizerController@listSewaBand');
    }

    public function inputSewaMusisi(Request $request, $slug){
    	$musisis = Musician::whereSlug($slug)->firstOrFail()->id;
        $musisi = Musician::findOrFail($musisis);
        $input = $request->all();


        $input['tanggal_mulai'] = $request->mulai;
        $input['tanggal_selesai'] = $request->selesai;        

        if(strtotime($input['tanggal_selesai'])< strtotime($input['tanggal_mulai'])){
            Session::flash('message','Tanggal Mulai dan Selesai tidak benar');
            return redirect()->back();
        }
        else{
             $input['nama_gig'] = $request->name;
             $input['user_id'] = Auth::guard('user')->user()->id;
            if ($request->hasFile('photo'))
            {
                $file = array('photo' => $request->file('photo'));
                Cloudder::upload($request->file('photo')->getPathName());
                $input['photo_gig'] = Cloudder::getPublicId();
            }
            //dd($input);
            $gig = Gig::create($input);

            // $gig=Gig::where('user_id', Auth::guard('user')->user()->id)
            //          ->where('status', 0)
            //          ->orderBy('created_at', 'desc')->first();

            $interval = round((strtotime($gig->tanggal_selesai) - strtotime($gig->tanggal_mulai))/3600);

           	$sewa = new Sewa;
            $sewa->total_biaya = $interval * $musisi->harga_sewa;
            $sewa->gig_id = $gig->id;
            $sewa->object_id = $musisi->id;
            $sewa->subject_id = Auth::guard('user')->user()->id;
            $sewa->type_sewa = 'hiremusisi';
            $sewa->save();

            $notif = New Notif;
            $notif->object_id = $gig->id;
            $notif->subject_id = Auth::guard('user')->user()->id;
            $notif->user_id = $musisi->id;
            $notif->type_subject = 'organizer';
            $notif->type_user = 'musisi';
            $notif->type_notif = 'reqsewa';
            $notif->save();

            return redirect()->action('OrganizerController@listSewaMusisi');
        }

    }


    public function listSewa(){
    	if(Auth::guard('user')->user()){
	    	$reqband = Sewa::where('subject_id', Auth::guard('user')->user()->id)
	    					->where('type_sewa', '=', 'hireband')
	    					->get();

	    	$reqmusisi = Sewa::where('subject_id', Auth::guard('user')->user()->id)
	    					->where('type_sewa', '=', 'hiremusisi')
	    					->get();

	    	foreach ($reqband as $sewa) {
	    		$band = Grupband::where('id', $sewa->object_id)->first();
	    		$gig = Gig::where('id', $sewa->gig_id)->first();
                $cek = Review::where('sewa_id', $sewa->id)->get();

	    		$sewa->band = $band;
	    		$sewa->gig = $gig;
                $sewa->review = $cek;
	    	}

	    	foreach ($reqmusisi as $sewam) {
	    		$musisi = Musician::where('id', $sewam->object_id)->first();
	    		$gig = Gig::where('id', $sewam->gig_id)->first();
                $cek = Review::where('sewa_id', $sewam->id)->get();

	    		$sewam->musisi = $musisi;
	    		$sewam->gig = $gig;
                $sewam->review = $cek;
	    	}

	    	return view('organizer.listsewa')->with('sewa', $reqband)->with('sewamusisi', $reqmusisi);
	    }else{
	    	return redirect()->back();
	    }
    }

    public function listSewaBand(){
    	if(Auth::guard('user')->user()){
	    	$reqband = Sewa::where('subject_id', Auth::guard('user')->user()->id)
	    					->where('type_sewa', '=', 'hireband')
                            ->orderBy('updated_at', 'desc')
	    					->get();

	    	foreach ($reqband as $sewa) {
	    		$band = Grupband::where('id', $sewa->object_id)->first();
	    		$gig = Gig::where('id', $sewa->gig_id)->first();
                $cek = Review::where('sewa_id', $sewa->id)->get();

	    		$sewa->band = $band;
	    		$sewa->gig = $gig;
                $sewa->review = $cek;
	    	}

	    	return view('organizer.listsewa-band')->with('sewa', $reqband);
            
    	}else{
	    	return redirect()->back();
	    }

    }

    public function listSewaMusisi(){
    	if(Auth::guard('user')->user()){
	    	$reqmusisi = Sewa::where('subject_id', Auth::guard('user')->user()->id)
	    					->where('type_sewa', '=', 'hiremusisi')
                            ->orderBy('updated_at', 'desc')
	    					->get();

	    	foreach ($reqmusisi as $sewam) {
	    		$musisi = Musician::where('id', $sewam->object_id)->first();
	    		$gig = Gig::where('id', $sewam->gig_id)->first();
                $cek = Review::where('sewa_id', $sewam->id)->get();

	    		$sewam->musisi = $musisi;
	    		$sewam->gig = $gig;
                $sewam->review = $cek;
	    	}

	    	return view('organizer.listsewa-musisi')->with('sewamusisi', $reqmusisi);
    	}else{
	    	return redirect()->back();
	    }
    }


    public function confirmPayment($id){
    	$sewa = Sewa::where('id', $id)->first();
    	if($sewa == null)
    		return redirect()->back();
    	else{
    		if($sewa->status == '0'){
		    	$gig = Gig::where('id', $sewa->gig_id)->first();
		    	$sewa->gig = $gig;

		    	return view('organizer.confirm-payment')->with('konfirm', $sewa);
		    }
		    else
		    	return redirect()->back();	
    	}
    }


    public function inputKonfirmasiPembayaran(Request $request, $id){
        $sewa = Sewa::where('id', $id)->first();

        Sewa::where('id', $id)->update(['status' => 1]);

        $input = $request->all();
        $input['sewa_id'] = $id;
        $input['bank_admin_id'] = $request->bank;
        if ($request->hasFile('photo'))
        {
            $file = array('photo' => $request->file('photo'));
            Cloudder::upload($request->file('photo')->getPathName());
            $input['photo'] = Cloudder::getPublicId();
        }
        KonfirmasiPembayaran::create($input);


        if($sewa->type_sewa == 'hireband' || $sewa->type_sewa == 'hiremusisi'){
            $notif = New Notif;
            $notif->object_id = $sewa->gig_id;
            $notif->subject_id = $sewa->subject_id;
            $notif->user_id = 0;
            $notif->type_subject = 'organizer';
            $notif->type_user = 'admin';
            $notif->type_notif = 'konfirmasipembayaran';
            $notif->save();
        }else{
            $notif = New Notif;
            $notif->object_id = $sewa->gig_id;
            $notif->subject_id = $sewa->object_id;
            $notif->user_id = 0;
            $notif->type_subject = 'organizer';
            $notif->type_user = 'admin';
            $notif->type_notif = 'konfirmasipembayaran';
            $notif->save();
        }

        return redirect()->action('OrganizerController@listSewa');
        
    }

    public function createGig(){
        
        return view('organizer.create-gig');
    }

    public function inputGig(Request $request){
        $input = $request->all();
        $input['nama_gig'] = $request->name;
        $input['type_gig'] = 'post';
        $input['tanggal_mulai'] = $request->mulai;
        $input['tanggal_selesai'] = $request->selesai;
        $input['user_id'] = Auth::guard('user')->user()->id;
        if ($request->hasFile('photo'))
        {
            $file = array('photo' => $request->file('photo'));
            Cloudder::upload($request->file('photo')->getPathName());
            $input['photo_gig'] = Cloudder::getPublicId();
        }

        Gig::create($input);
        return redirect('home');
    }


    public function listOffer(){
        if(Auth::guard('user')->user()){
            // $offerband = Sewa::where('object_id', Auth::guard('user')->user()->id)
            //                 ->where('type_sewa', '=', 'bandhire')
            //                 ->get();

            // $offermusisi = Sewa::where('object_id', Auth::guard('user')->user()->id)
            //                 ->where('type_sewa', '=', 'musisihire')
            //                 ->get();

            // foreach ($offerband as $sewa) {
            //     $band = Grupband::where('id', $sewa->subject_id)->first();
            //     $gig = Gig::where('id', $sewa->gig_id)->first();

            //     $sewa->band = $band;
            //     $sewa->gig = $gig;
            // }

            // foreach ($offermusisi as $sewam) {
            //     $musisi = Musician::where('id', $sewam->subject_id)->first();
            //     $gig = Gig::where('id', $sewam->gig_id)->first();

            //     $sewam->musisi = $musisi;
            //     $sewam->gig = $gig;
            // }

            // return view('organizer.listoffer')->with('sewa', $offerband)->with('sewamusisi', $offermusisi);

            //$query = "SELECT * FROM  sewas WHERE status_request IN (SELECT id FROM sewas WHERE grupband_id = ".$band->id

            $sewa = Sewa::where('type_sewa','!=','hireband')->where('type_sewa','!=','hiremusisi')
                        ->where('status_request','!=','2')
                        ->where('object_id',Auth::guard('user')->user()->id)
                        ->groupBy('gig_id')
                        ->get();

            

            foreach ($sewa as $sewas) {
                $gig = Gig::where('id',$sewas->gig_id)->get();
                $offer = Sewa::where('gig_id', $sewas->gig_id)->get();
                $cek = Review::where('sewa_id', $sewas->id)->get();

                // foreach ($offer as $penawars) {
                //     if($penawars->type_sewa === 'bandhire')
                //         $penawar = Grupband::where('id',$sewas->subject_id)->get();
                //     elseif($penawars->type_sewa === 'musisihire')
                //         $penawar = Musician::where('id',$sewas->subject_id)->get();
                //     else
                //         $penawar = "";

                //     $penawars->penawaran = $penawar;
                // }

                $sewas->gig = $gig;
                $sewas->listpenawar = $offer;
                $sewas->review = $cek;
            }

            return view('organizer.listoffer')->with('sewas', $sewa);
            //return $sewa;
        }
        else{
            return redirect()->back();
        }
    }

    public function listOfferApprove(){
        if(Auth::guard('user')->user()){
            
            $sewa = Sewa::where('type_sewa','!=','hireband')->where('type_sewa','!=','hiremusisi')
                        ->where('status_request','=','1')
                        ->where('status','!=','3')
                        ->where('status','!=','4')
                        ->where('object_id',Auth::guard('user')->user()->id)
                        ->groupBy('gig_id')->get();

            

            foreach ($sewa as $sewas) {
                $gig = Gig::where('id',$sewas->gig_id)->get();
                $offer = Sewa::where('gig_id', $sewas->gig_id)->where('status_request','1')->get();
                $cek = Review::where('sewa_id', $sewas->id)->get();

                $sewas->gig = $gig;
                $sewas->listpenawar = $offer;
                $sewas->review = $cek;
            }

            return view('organizer.listoffer-approve')->with('sewas', $sewa);
        }
        else{
            return redirect()->back();
        }
    }

    public function listOfferFinish(){
        if(Auth::guard('user')->user()){
            
            $sewa = Sewa::where('type_sewa','!=','hireband')->where('type_sewa','!=','hiremusisi')
                        ->where('status_request','=','1')
                        ->where('status','=','3')
                        ->orWhere('status','=','4')
                        ->where('object_id',Auth::guard('user')->user()->id)
                        ->groupBy('gig_id')->get();

            

            foreach ($sewa as $sewas) {
                $gig = Gig::where('id',$sewas->gig_id)->get();
                $offer = Sewa::where('gig_id', $sewas->gig_id)->get();
                $cek = Review::where('sewa_id', $sewas->id)->get();

                $sewas->gig = $gig;
                $sewas->listpenawar = $offer;
                $sewas->review = $cek;
            }

            return view('organizer.listoffer-finish')->with('sewas', $sewa);
        }
        else{
            return redirect()->back();
        }

    }

    public function listOfferPending(){
        if(Auth::guard('user')->user()){
            
            $sewa = Sewa::where('type_sewa','!=','hireband')->where('type_sewa','!=','hiremusisi')
                        ->where('status_request','=','0')
                        ->where('object_id',Auth::guard('user')->user()->id)
                        ->groupBy('gig_id')->get();

            foreach ($sewa as $sewas) {
                $gig = Gig::where('id',$sewas->gig_id)->get();
                $offer = Sewa::where('gig_id', $sewas->gig_id)->where('status_request','!=','2')->get();

                $sewas->gig = $gig;
                $sewas->listpenawar = $offer;
            }

            return view('organizer.listoffer-pending')->with('sewas', $sewa);
        }
        else{
            return redirect()->back();
        }

    }



}
