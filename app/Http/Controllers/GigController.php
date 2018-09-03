<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Auth;
use App\Musician;
use App\GenreMusisi;
use App\Grupband;
use App\Review;
use App\GrupbandMusisi;
use App\Sewa;
use App\Gig;
use App\Notif;
use App\User;
use App\SaldoDetail;
use App\Saldo;
use App\KonfirmasiPembayaran;
use DB;
use Input;
use Session;
use Cloudder;

class GigController extends Controller
{
    public function __construct()
    {
    	if(Auth::guard('user')->user() != null)
            $this->middleware('auth');
        elseif(Auth::guard('musician')->user() != null)
            $this->middleware('musician');
	}


	public function detailGig($slug){
		$gigs = Gig::whereSlug($slug)->firstOrFail()->id;
        $gig = Gig::where('id', $gigs)
        			->where('aktif','=','Y')->first();

        $sewa = Sewa::where('gig_id', $gigs)->where('status_request','!=','2')->get();
         foreach ($sewa as $sewas) {
        	if($sewas->type_sewa === 'bandhire')
        		$penawar = Grupband::where('id',$sewas->subject_id)->get();
        	elseif($sewas->type_sewa === 'musisihire')
        		$penawar = Musician::where('id',$sewas->subject_id)->get();
        	else
        		$penawar = "";

        	$sewas->penawar = $penawar;

        	//$sewas->penawar2 = $penawar2;
        	//$sewas->penawarband = $penawarband;
         	//echo $sewas->type_sewa;
         }

        

        return view('organizer.detail-gig')->with('gigs',$gig)->with('offer',$sewa);
        //return view('organizer.detail-gig')->with('gigs',$gig);
	}

	public function offerGigMusisi($id){
		$gig = Gig::findOrFail($id);
		$sewa = new Sewa;
		$interval = round((strtotime($gig->tanggal_selesai) - strtotime($gig->tanggal_mulai))/3600);
        $sewa->total_biaya = $interval * Auth::guard('musician')->user()->harga_sewa;
        $sewa->gig_id = $id;
        $sewa->object_id = $gig->user_id;
        $sewa->subject_id = Auth::guard('musician')->user()->id;
        $sewa->type_sewa = 'musisihire';
        $sewa->save();

        $notif = New Notif;
        $notif->object_id = $id;
        $notif->subject_id = Auth::guard('musician')->user()->id;
        $notif->user_id = $sewa->object_id;
        $notif->type_subject = 'musisi';
        $notif->type_user = 'organizer';
        $notif->type_notif = 'reqoffer';
        $notif->save();

		Session::flash('message', 'Berhasil mengajukan penawaran!');
		return redirect()->back();
	}

	public function offerGigBand(Request $req, $id){
		$gig = Gig::findOrFail($id);
		$band = Grupband::findOrFail($req->band);
		$sewa = new Sewa;
		$interval = round((strtotime($gig->tanggal_selesai) - strtotime($gig->tanggal_mulai))/3600);
        $sewa->total_biaya = $interval * $band->harga;
        $sewa->gig_id = $id;
        $sewa->object_id = $gig->user_id;
        $sewa->subject_id = $req->band;
        $sewa->type_sewa = 'bandhire';
        $sewa->save();

        $notif = New Notif;
        $notif->object_id = $id;
        $notif->subject_id = $band->id;
        $notif->user_id = $sewa->object_id;
        $notif->type_subject = 'band';
        $notif->type_user = 'organizer';
        $notif->type_notif = 'reqoffer';
        $notif->save();

		Session::flash('message', 'Berhasil mengajukan penawaran!');
		return redirect()->back();
	}

	public function addReview($id){
		$sewa = Sewa::findOrFail($id);
		$cek = Review::where('sewa_id', $sewa->id)->get();

		if($cek->isEmpty()){
			if($sewa->type_sewa == 'hireband')
				$reviewto = Grupband::where('id', $sewa->subject_id)->first();
			elseif($sewa->type_sewa == 'hiremusisi')
				$reviewto = Musician::where('id', $sewa->subject_id)->first();
			elseif($sewa->type_sewa == 'musisihire')
				$reviewto = Musician::where('id', $sewa->object_id)->first();
			else
				$reviewto = Grupband::where('id', $sewa->object_id)->first();

			$gigs = Gig::findOrFail($sewa->gig_id);

			$sewa->gig = $gigs;
			$sewa->objreview = $reviewto;

			return view('organizer.add-review')->with('sewas',$sewa);
		}
		else{
			return redirect()->back();
		}
	}

	public function inputReview(Request $req, $id){
		$sewa = Sewa::findOrFail($id);
		$cek = Review::where('sewa_id', $id)->get();

		if($cek->isEmpty()){
			//cek sudah 24 jam belum kasih review ny sejak order
			$today = date('Y-m-d H:i:s');
			$getgig = Gig::where('id', $sewa->gig_id)->first();
			$finishdate = $getgig->tanggal_selesai;
			$hours = round((strtotime($today) - strtotime($finishdate)) / 3600);

			if($hours <= 24){
				$review = new Review;
		        $review->sewa_id = $id;
		        $review->user_id = Auth::guard('user')->user()->id;
		        $review->pesan = $req->pesan;
		        $review->nilai = $req->rate;
		        $review->save();

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
	            }
	            $sewa->update(['status' => 4]);
		    }else{
		    	$review = new Review;
		        $review->sewa_id = $id;
		        $review->user_id = Auth::guard('user')->user()->id;
		        $review->pesan = $req->pesan;
		        $review->nilai = $req->rate;
		        $review->save();
		    }

	        //kasih notifikasi
	        if($sewa->type_sewa == 'hireband'){
		        $notif = New Notif;
			    $notif->object_id = $sewa->gig_id;
			    $notif->subject_id = $sewa->subject_id;
			    $notif->user_id = $sewa->object_id;
			    $notif->type_subject = 'organizer';
			    $notif->type_user = 'band';
			    $notif->type_notif = 'review';
			    $notif->save();
			}else if($sewa->type_sewa == 'hiremusisi'){
				$notif = New Notif;
			    $notif->object_id = $sewa->gig_id;
			    $notif->subject_id = $sewa->subject_id;
			    $notif->user_id = $sewa->object_id;
			    $notif->type_subject = 'organizer';
			    $notif->type_user = 'musisi';
			    $notif->type_notif = 'review';
			    $notif->save();
			}else if($sewa->type_sewa == 'bandhire'){
				$notif = New Notif;
			    $notif->object_id = $sewa->gig_id;
			    $notif->subject_id = $sewa->object_id;
			    $notif->user_id = $sewa->subject_id;
			    $notif->type_subject = 'organizer';
			    $notif->type_user = 'band';
			    $notif->type_notif = 'review';
			    $notif->save();
			}else if($sewa->type_sewa == 'musisihire'){
				$notif = New Notif;
			    $notif->object_id = $sewa->gig_id;
			    $notif->subject_id = $sewa->object_id;
			    $notif->user_id = $sewa->subject_id;
			    $notif->type_subject = 'organizer';
			    $notif->type_user = 'musisi';
			    $notif->type_notif = 'review';
			    $notif->save();
			}

			return redirect('/');
		}else{
			return redirect()->back();
		}
	}

	public function editGig($slug){
		$gig = Gig::whereSlug($slug)->firstOrFail()->id;
        $gigs = Gig::findOrFail($gig);

        if(Auth::guard('user')->user()->id == $gigs->user_id){
        	return view('organizer.editgig')->with('gig',$gigs);
        }
        else
        	return redirect()->back();
	}

	public function inputEditGig(Request $req, $slug){
		$gig = Gig::whereSlug($slug)->firstOrFail()->id;
        $gigs = Gig::findOrFail($gig);

        $input = $req->all();
        if ($req->hasFile('photo'))
        {
            $file = array('photo' => $req->file('photo'));
            Cloudder::upload($req->file('photo')->getPathName());
            $input['photo_gig'] = Cloudder::getPublicId();
        }
        $gigs->update($input);
        return redirect()->action('GigController@detailGig', [$slug]);
	}
}
