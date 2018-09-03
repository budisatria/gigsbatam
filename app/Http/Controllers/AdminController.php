<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\User;
use App\Musician;
use App\GenreMusisi;
use App\GenreBand;
use App\Grupband;
use App\Gig;
use App\Sewa;
use App\Genre;
use App\Saldo;
use App\Notif;
use App\SaldoDetail;
use App\Position;
use App\Bankadmin;
use App\Withdraw;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct(){
    	$this->middleware('admin');
    }

    public function index(){
    	// return Auth::guard('admin')->user();
    	return view('admin.dashboard');
    }

    public function listUser(){
    	$user = User::all();
    	return view('admin.listUser')->with('users',$user);
    }

    public function editUser($id){
        $users = User::findOrFail($id);
        return view('admin.edituser')->with('user',$users);
    }

    public function inputEditUser(Request $req, $id){
        $users = User::findOrFail($id);
        $users->update($req->all());
        return redirect()->back();
    }

    public function listMusisi(){
    	$musisi = Musician::all();
    	return view('admin.listMusician')->with('musisis',$musisi);
    }

    public function editMusisi($id){
        $musisis = Musician::findOrFail($id);
        $genremusisi = GenreMusisi::join('genres', 'genre_musisi.genre_id' ,'=', 'genres.id')->where('musician_id', $id)->get();
        $musisis->genre = $genremusisi;
        return view('admin.editmusisi')->with('musisi',$musisis);
    }

    public function inputEditMusisi(Request $req, $id){
        $musisis = Musician::findOrFail($id);
        $musisis->update($req->all());
        return redirect()->back();
    }

    public function listBand(){
    	$band = Grupband::all();
    	foreach ($band as $_band) {
    		$admin = Musician::where('id', $_band->admin_id)->get();
    		$_band->admin = $admin;
    	}

    	return view('admin.listBand')->with('bands',$band);
    }

    public function editBand($id){
        $bands = Grupband::findOrFail($id);
        $genreband = GenreBand::join('genres', 'genre_bands.genre_id' ,'=', 'genres.id')->where('band_id', $id)->get();
        $admin = Musician::where('id', $bands->admin_id)->first();
        $bands->genre = $genreband;
        $bands->admin = $admin;
        return view('admin.editband')->with('band',$bands);
    }

    public function inputEditBand(Request $req, $id){
        $bands = Grupband::findOrFail($id);
        $bands->update($req->all());
        return redirect()->back();
    }

    public function listGig(){
    	$gig = Gig::all();
    	foreach ($gig as $_gig) {
    		$user = User::where('id', $_gig->user_id)->get();
    		$_gig->organizer = $user;
    	}

    	return view('admin.listGig')->with('gigs',$gig);
    }

    public function editGig($id){
        $gigs = Gig::findOrFail($id);
        $owner = User::where('id', $gigs->user_id)->first();
        $gigs->owner = $owner;
        return view('admin.editgig')->with('gig',$gigs);
    }

    public function inputEditGig(Request $req, $id){
        $gigs = Gig::findOrFail($id);
        $gigs->update($req->all());
        return redirect()->back();
    }

    public function listOrder(){
    	$order = Sewa::where('status_request', '!=', '2')->get();
    	foreach ($order as $_order) {
    		$gig = Gig::where('id', $_order->gig_id)->get();
    		if($_order->type_sewa == 'hireband')
    		{
    			$obj = Grupband::where('id', $_order->object_id)->get();
    			$sbj = User::where('id', $_order->subject_id)->get();
    		}
    		elseif($_order->type_sewa == 'hiremusisi')
    		{
    			$obj = Musician::where('id', $_order->object_id)->get();
    			$sbj = User::where('id', $_order->subject_id)->get();
    		}
    		elseif($_order->type_sewa == 'bandhire')
    		{
    			$sbj = Grupband::where('id', $_order->subject_id)->get();
    			$obj = User::where('id', $_order->object_id)->get();
    		}
    		elseif($_order->type_sewa == 'musisihire')
    		{
    			$sbj = Musician::where('id', $_order->subject_id)->get();
    			$obj = User::where('id', $_order->object_id)->get();
    		}

    		$_order->gig = $gig;
    		$_order->obj = $obj;
    		$_order->sbj = $sbj;
    	}

    	//echo $order;
    	return view('admin.listOrder')->with('orders',$order);
    }

    public function createGenre(){
    	$genre = Genre::all();
    	return view('admin.create-genre')->with('genres',$genre);
    }

    public function inputGenre(Request $request){
    	$input = $request->all();
    	
    	$input['genre_name'] = $request->name;
    	Genre::create($input);

    	return redirect()->back();
    }

    public function createPosisi(){
    	$posisi = Position::all();
    	return view('admin.create-posisi')->with('posisis',$posisi);
    }

    public function inputPosisi(Request $request){
    	$input = $request->all();
    	
    	$input['position_name'] = $request->name;
    	Position::create($input);

    	return redirect()->back();
    }

    public function editOrder($id){
    	$order = Sewa::where('id',$id)->first();

    		$gig = Gig::where('id', $order->gig_id)->first();
    		if($order->type_sewa == 'hireband')
    		{
    			$obj = Grupband::where('id', $order->object_id)->first();
    			$sbj = User::where('id', $order->subject_id)->first();
    		}
    		elseif($order->type_sewa == 'hiremusisi')
    		{
    			$obj = Musician::where('id', $order->object_id)->first();
    			$sbj = User::where('id', $order->subject_id)->first();
    		}
    		elseif($order->type_sewa == 'bandhire')
    		{
    			$sbj = Grupband::where('id', $order->subject_id)->first();
    			$obj = User::where('id', $order->object_id)->first();
    		}
    		elseif($order->type_sewa == 'musisihire')
    		{
    			$sbj = Musician::where('id', $order->subject_id)->first();
    			$obj = User::where('id', $order->object_id)->first();
    		}

    		$order->gig = $gig;
    		$order->obj = $obj;
    		$order->sbj = $sbj;

    	return view('admin.edit-order')->with('orders',$order);
    }

    public function inputEditOrder(Request $request, $id){
    	$order = Sewa::find($id);
        $input = $request->all();
        //kalau ubah status saldo sudah ditransfer
        if($request->status == '4'){
            $sewa = Sewa::where('id', $id)->first();
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
            
        }
        elseif($request->status == '2'){
            $sewa = Sewa::where('id', $id)->first();
            if($sewa->type_sewa == 'hireband' || $sewa->type_sewa == 'hiremusisi'){
                $notif = New Notif;
                $notif->object_id = $sewa->gig_id;
                $notif->subject_id = 0;
                $notif->user_id = $sewa->subject_id;
                $notif->type_subject = 'admin';
                $notif->type_user = 'organizer'; 
                $notif->type_notif = 'lunas';
                $notif->save();

                if($sewa->type_sewa == 'hireband'){
                    $notif = New Notif;
                    $notif->object_id = $sewa->gig_id;
                    $notif->subject_id = $sewa->subject_id;
                    $notif->user_id = $sewa->object_id;
                    $notif->type_subject = 'organizer';
                    $notif->type_user = 'band'; 
                    $notif->type_notif = 'lunas';
                    $notif->save();
                }
                elseif($sewa->type_sewa == 'hiremusisi'){
                    $notif = New Notif;
                    $notif->object_id = $sewa->gig_id;
                    $notif->subject_id = $sewa->subject_id;
                    $notif->user_id = $sewa->object_id;
                    $notif->type_subject = 'organizer';
                    $notif->type_user = 'musisi'; 
                    $notif->type_notif = 'lunas';
                    $notif->save();
                }
            }
            elseif($sewa->type_sewa == 'bandhire' || $sewa->type_sewa == 'musisihire'){
                $notif = New Notif;
                $notif->object_id = $sewa->gig_id;
                $notif->subject_id = 0;
                $notif->user_id = $sewa->object_id;
                $notif->type_subject = 'admin';
                $notif->type_user = 'organizer'; 
                $notif->type_notif = 'lunas';
                $notif->save();

                if($sewa->type_sewa == 'bandhire'){
                    $notif = New Notif;
                    $notif->object_id = $sewa->gig_id;
                    $notif->subject_id = $sewa->object_id;
                    $notif->user_id = $sewa->subject_id;
                    $notif->type_subject = 'organizer';
                    $notif->type_user = 'band'; 
                    $notif->type_notif = 'lunas';
                    $notif->save();
                }
                elseif($sewa->type_sewa == 'musisihire'){
                    $notif = New Notif;
                    $notif->object_id = $sewa->gig_id;
                    $notif->subject_id = $sewa->object_id;
                    $notif->user_id = $sewa->subject_id;
                    $notif->type_subject = 'organizer';
                    $notif->type_user = 'musisi'; 
                    $notif->type_notif = 'lunas';
                    $notif->save();
                }
            }

            
            // $notif->object_id = $sewa->gig_id;
            // $notif->subject_id = 0;
            // $notif->user_id = $band->id;
            // $notif->type_subject = 'admin';
            // $notif->type_user = 'organizer';
            // $notif->type_notif = 'lunas';
            // $notif->save();
        }
    	$order->update($input);
    	return redirect()->back();
    }

    public function createBank(){
        $bank = Bankadmin::all();
        return view('admin.create-bank')->with('banks',$bank);
    }

    public function inputBank(Request $request){
        $input = $request->all();
        
        $input['nama_bank'] = $request->name;
        $input['no_rek'] = $request->norek;
        $input['atas_nama'] = $request->namaakun;
        $input['cabang'] = $request->cabang;
        Bankadmin::create($input);

        return redirect()->back();
    }

    public function listWithdraw(){
        $wd = Withdraw::all();
        foreach ($wd as $withdraw) {
           $saldo = Saldo::where('id', $withdraw->saldo_id)->first();

            if($saldo->type_pemilik == 'musisi')
                $owner = Musician::where('id', $saldo->subject_id)->first();
            else
                $owner = Grupband::where('id', $saldo->subject_id)->first();
 
           $withdraw->saldo = $saldo;
           $withdraw->owner = $owner;
        }


        return view('admin.listWithdraw')->with('withdraw',$wd);
    }

    public function editWithdraw($id){
        $withdraw = Withdraw::where('id',$id)->first();
        $saldo = Saldo::where('id', $withdraw->saldo_id)->first();
        if($saldo->type_pemilik == 'musisi')
            $owner = Musician::where('id', $saldo->subject_id)->first();
        else{
            $owner = Grupband::where('id', $saldo->subject_id)->first();
            $ownerband = Musician::where('id', $owner->admin_id)->first();

            $owner->band = $ownerband;
        }

        //return $withdraw->saldo_akhir;

        return view('admin.editwithdraw')->with('wd',$withdraw)->with('owners',$owner)->with('saldos',$saldo);
    }

    public function inputEditWithdraw(Request $req, $id){
        $withdraw = Withdraw::findOrFail($id);
        $saldo = Saldo::where('id', $withdraw->saldo_id)->first();
        
        Withdraw::where('id', $id)->update(['status' => $req->status]);

        if($saldo->type_pemilik == 'band'){
            $notif = New Notif;
            $notif->object_id = 0;
            $notif->subject_id = 0;
            $notif->user_id = $saldo->subject_id;
            $notif->type_subject = 'admin';
            $notif->type_user = 'band'; 
            $notif->type_notif = 'withdrawselesai';
            $notif->save();
        }else{
            $notif = New Notif;
            $notif->object_id = 0;
            $notif->subject_id = 0;
            $notif->user_id = $saldo->subject_id;
            $notif->type_subject = 'admin';
            $notif->type_user = 'musisi'; 
            $notif->type_notif = 'withdrawselesai';
            $notif->save();
        }

        return redirect()->back();
    }
}
