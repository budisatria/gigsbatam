<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notif;
use App\Gig;
use App\Grupband;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        if(Auth::guard('user')->user()!=null){
            $this->middleware('auth');
        }else if(Auth::guard('musician')->user()!=null){
            $this->middleware('musician');
        }else if(Auth::guard('admin')->user()!=null){
            $this->middleware('admin');
        }else{
            $this->middleware('auth');
        }
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        return view('home');
    }

    public function detailNotif($id){
        $notif = Notif::findOrFail($id);
        Notif::where('id', $id)->update(['baca' => 'Y']);

        if($notif->type_notif == 'reqsewa' && $notif->type_user == 'musisi'){
            return redirect('listsewa/musisi');
        }
        elseif($notif->type_notif == 'reqsewa' && $notif->type_user == 'band'){
            return redirect('listsewa/band');
        }
        else if($notif->type_notif == 'terimasewa' && $notif->type_subject == 'musisi'){
            return redirect('listsewa-musisi');
        }
        else if($notif->type_notif == 'terimasewa' && $notif->type_subject == 'band'){
            return redirect('listsewa-band');
        }
        else if($notif->type_notif == 'tolaksewa' && $notif->type_subject == 'musisi'){
            return redirect('listsewa-musisi');
        }
        else if($notif->type_notif == 'tolaksewa' && $notif->type_subject == 'band'){
            return redirect('listsewa-band');
        }
        else if($notif->type_notif == 'reqoffer' && $notif->type_subject == 'band' || $notif->type_notif == 'reqoffer' && $notif->type_subject == 'musisi'){
            return redirect('listoffer-pending');
        }
        else if($notif->type_notif == 'lunas'){
            $gig = Gig::where('id', $notif->object_id)->first();
            return redirect()->action('GigController@detailGig', [$gig->slug]);
        }
        else if($notif->type_notif == 'terimaoffer'){
            $gig = Gig::where('id', $notif->object_id)->first();
            return redirect()->action('GigController@detailGig', [$gig->slug]);
        }
        else if($notif->type_notif == 'tolakoffer'){
            $gig = Gig::where('id', $notif->object_id)->first();
            return redirect()->action('GigController@detailGig', [$gig->slug]);
        }
        else if($notif->type_notif == 'tambahsaldo'){
            return redirect()->action('MusicianController@saldo', [Auth::guard('musician')->user()->slug]);
        }
        else if($notif->type_notif == 'review' && $notif->type_user == 'musisi'){
            return redirect()->action('MusicianController@profile', [Auth::guard('musician')->user()->slug]);
        }
        else if($notif->type_notif == 'review' && $notif->type_user == 'band'){
            $band = Grupband::where('id', $notif->user_id)->first();
            return redirect()->action('MusicianController@bandProfile', [$band->slug]);
        }
        else if($notif->type_notif == 'withdraw'){
            return redirect('admin/listwithdraw');
        }
        else if($notif->type_notif == 'withdrawselesai'){
            if($notif->type_user == 'musisi')
                return redirect()->action('MusicianController@saldo', [Auth::guard('musician')->user()->slug]);
            else{
                $band = Grupband::where('id', $notif->user_id)->first();
                return redirect()->action('MusicianController@saldoBand', $band->slug);
            }
        }
        else if($notif->type_notif == 'konfirmasipembayaran'){
            return redirect('admin/listorder');
        }
    }


}
