<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\GenreMusisi;
use App\GenreBand;
use App\Grupband;
use App\Genre;
use App\Musician;
use App\Sewa;
use App\Gig;
use DB;

class SearchController extends Controller
{
    public function pencarian(Request $request){
        $input = $request->all();

        //Kalau pilih MUSISI
        if($input['tipe'] == 1){
            
            //KUMPULAN IF
            if($input['Wilayah'] == null && $request->checkbox == null && $input['tanggal'] == null){              
                $join = Musician::where('aktif', 'Y')->get();
                return view('hasilcarimusisi')->with('listmusisi',$join);
               // echo "NULL Wilayah, NULL CEKBOX, NULL TANGGAL";
            }
            elseif($input['Wilayah'] == null && $request->checkbox == null && $input['tanggal'] != null){                            
                $ceksewa = Sewa::join('gigs', 'sewas.gig_id', '=', 'gigs.id')
                                ->where('sewas.type_sewa', '=', 'hiremusisi')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hiremusisi')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hiremusisi')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])->orwhere('sewas.type_sewa', '=', 'musisihire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'musisihire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'musisihire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->get(['sewas.*']);

                if(!$ceksewa->isEmpty()){
                    foreach ($ceksewa as $value) {
                        if ($value->type_sewa == 'hiremusisi')
                            $querycek = DB::select("SELECT * FROM  musicians WHERE id NOT IN ($value->object_id)");
                        elseif($value->type_sewa == 'musisihire')
                            $querycek = DB::select("SELECT * FROM  musicians WHERE id NOT IN ($value->subject_id)");
                        else
                            $querycek = DB::select("SELECT * FROM  musicians");
                    }
                }
                else{
                    $querycek = DB::select("SELECT * FROM  musicians");
                }

                return view('hasilcarimusisi')->with('listmusisi',$querycek);
                //echo "NULL Wilayah, NULL CEKBOX, TERPILIH TANGGAL";
            }
            elseif($input['Wilayah'] == null && $request->checkbox != null && $input['tanggal'] == null){
                $musisi = Musician::where('aktif', 'Y')->get();
                $genreinput = $request->checkbox;
                foreach ($musisi as $_musisi) {
                    $cekgenre = GenreMusisi::where('musician_id', $_musisi->id)->get(['genre_id']);
                    // foreach ($cekgenre as $_cekgenre) {
                    //     $arrgen = (array) $_cekgenre->genre_id; 
                    //     $result = array_intersect($request->checkbox, $arrgen);
                    //     if($result == null)
                    //         $_cekgenre->hasil = null;
                    //     else
                    //         $_cekgenre->hasil = $result;
                    // }
                    //$request->checkbox as $genreinput)
                    $_musisi->genre = $cekgenre;
                    $_musisi->hasil = $genreinput;
                }

                foreach ($musisi as $final) {
                    foreach ($final->genre as $finalgenre) {
                        $genreid[] = $finalgenre->genre_id;
                    }

                    $result = array_intersect($genreid, $final->hasil);
                    if ($result != null){
                        $finalid[] = $final->id;
                    }
                    else{
                        $finalid = [];
                    }                   
                }

                if($finalid != null){
                    foreach ($finalid as $hasilakhir) {
                        $hasilcariband = Musician::where('id', $hasilakhir)->where('aktif', 'Y')->first();
                        $idband[] = $hasilcariband;
                    }
                }else{
                    $idband = [];
                }

                return view('hasilcarimusisi')->with('listmusisi',$idband);
                //echo "NULL Wilayah, TERPILIH CEKBOX, NULL TANGGAL";
            }
            elseif($input['Wilayah'] == null && $request->checkbox != null && $input['tanggal'] != null){
                
                $ceksewa = Sewa::join('gigs', 'sewas.gig_id', '=', 'gigs.id')
                                ->where('sewas.type_sewa', '=', 'hiremusisi')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hiremusisi')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hiremusisi')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])->orwhere('sewas.type_sewa', '=', 'musisihire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'musisihire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'musisihire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->get(['sewas.*']);

                if(!$ceksewa->isEmpty()){
                    foreach ($ceksewa as $value) {
                        if ($value->type_sewa == 'hiremusisi')
                            $querycek = DB::select("SELECT * FROM  musicians WHERE id NOT IN ($value->object_id)");
                        elseif($value->type_sewa == 'musisihire')
                            $querycek = DB::select("SELECT * FROM  musicians WHERE id NOT IN ($value->subject_id)");
                        else
                            $querycek = DB::select("SELECT * FROM  musicians");
                    }
                }
                else{
                    $querycek = DB::select("SELECT * FROM  musicians");
                }

                if($querycek != null){
                    foreach ($querycek as $value) {
                        $id[] = $value->id;
                    }
                    $musisi = Musician::whereIn('id', $id)->where('aktif', 'Y')->get();
                    $genreinput = $request->checkbox;
                    foreach ($musisi as $_musisi) {
                        $cekgenre = GenreMusisi::where('musician_id', $_musisi->id)->get(['genre_id']);
                        // foreach ($cekgenre as $_cekgenre) {
                        //     $arrgen = (array) $_cekgenre->genre_id; 
                        //     $result = array_intersect($request->checkbox, $arrgen);

                        //     $_cekgenre->hasil = $result;
                        // }
                        $_musisi->genre = $cekgenre;
                        $_musisi->hasil = $genreinput;
                    }

                    foreach ($musisi as $final) {
                        foreach ($final->genre as $finalgenre) {
                            $genreid[] = $finalgenre->genre_id;
                        }

                        $result = array_intersect($genreid, $final->hasil);
                        if ($result != null){
                            $finalid[] = $final->id;
                        }
                        else{
                            $finalid = [];
                        }                   
                    }

                    if($finalid != null){
                        foreach ($finalid as $hasilakhir) {
                            $hasilcariband = Musician::where('id', $hasilakhir)->where('aktif', 'Y')->first();
                            $idband[] = $hasilcariband;
                        }
                    }else{
                        $idband = [];
                    }

                }
                else{
                    $musisi = Musician::where('aktif', 'Y')->get();
                    $genreinput = $request->checkbox;
                    foreach ($musisi as $_musisi) {
                        $cekgenre = GenreMusisi::where('musician_id', $_musisi->id)->get(['genre_id']);
                        // foreach ($cekgenre as $_cekgenre) {
                        //     $arrgen = (array) $_cekgenre->genre_id; 
                        //     $result = array_intersect($request->checkbox, $arrgen);

                        //     $_cekgenre->hasil = $result;
                        // }
                        $_musisi->genre = $cekgenre;
                        $_musisi->hasil = $genreinput;
                    }

                    foreach ($musisi as $final) {
                        foreach ($final->genre as $finalgenre) {
                            $genreid[] = $finalgenre->genre_id;
                        }

                        $result = array_intersect($genreid, $final->hasil);
                        if ($result != null){
                            $finalid[] = $final->id;
                        }
                        else{
                            $finalid = [];
                        }                   
                    }

                    if($finalid != null){
                        foreach ($finalid as $hasilakhir) {
                            $hasilcariband = Musician::where('id', $hasilakhir)->where('aktif', 'Y')->first();
                            $idband[] = $hasilcariband;
                        }
                    }else{
                        $idband = [];
                    }
                }

                return view('hasilcarimusisi')->with('listmusisi',$idband);

                //echo "NULL Wilayah, TERPILIH CEKBOX, TERPILIH TANGGAL";
            }
            elseif($input['Wilayah'] != null && $request->checkbox == null && $input['tanggal'] == null){
                $join = Musician::where('aktif', 'Y')->where('Wilayah', $input['Wilayah'])->get();
                return view('hasilcarimusisi')->with('listmusisi',$join);
                //echo "TERPILIH Wilayah, NULL CEKBOX, NULL TANGGAL";
            }
            elseif($input['Wilayah'] != null && $request->checkbox != null && $input['tanggal'] == null){
                $finalid = [];
                $musisi = Musician::where('aktif', 'Y')->where('Wilayah', $input['Wilayah'])->get();
                $genreinput = $request->checkbox;
                foreach ($musisi as $_musisi) {
                    $cekgenre = GenreMusisi::where('musician_id', $_musisi->id)->get(['genre_id']);
                    // foreach ($cekgenre as $_cekgenre) {
                    //     $arrgen = (array) $_cekgenre->genre_id; 
                    //     $result = array_intersect($request->checkbox, $arrgen);
                    //     if($result == null)
                    //         $_cekgenre->hasil = null;
                    //     else
                    //         $_cekgenre->hasil = $result;
                    // }
                    //$request->checkbox as $genreinput)
                    $_musisi->genre = $cekgenre;
                    $_musisi->hasil = $genreinput;
                }

                foreach ($musisi as $final) {
                    foreach ($final->genre as $finalgenre) {
                        $genreid[] = $finalgenre->genre_id;
                    }

                    $result = array_intersect($genreid, $final->hasil);
                    if ($result != null){
                        $finalid[] = $final->id;
                    }
                    else{
                        $finalid = [];
                    }                   
                }

                if($finalid != null){
                    foreach ($finalid as $hasilakhir) {
                        $hasilcariband = Musician::where('id', $hasilakhir)->where('aktif', 'Y')->first();
                        $idband[] = $hasilcariband;
                    }
                }else{
                    $idband = [];
                }

                return view('hasilcarimusisi')->with('listmusisi',$idband);

                //echo "TERPILIH Wilayah, TERPILIH CEKBOX, NULL TANGGAL";
            }
            elseif($input['Wilayah'] != null && $request->checkbox == null && $input['tanggal'] != null){
                $ceksewa = Sewa::join('gigs', 'sewas.gig_id', '=', 'gigs.id')
                                ->where('sewas.type_sewa', '=', 'hiremusisi')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hiremusisi')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hiremusisi')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])->orwhere('sewas.type_sewa', '=', 'musisihire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'musisihire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'musisihire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->get(['sewas.*']);

                if(!$ceksewa->isEmpty()){
                    foreach ($ceksewa as $value) {
                        if ($value->type_sewa == 'hiremusisi')
                            $querycek = DB::select("SELECT id FROM  musicians WHERE id NOT IN ($value->object_id)");
                        elseif($value->type_sewa == 'musisihire')
                            $querycek = DB::select("SELECT id FROM  musicians WHERE id NOT IN ($value->subject_id)");
                        else
                            $querycek = DB::select("SELECT id FROM  musicians");
                    }
                }
                else{
                    $querycek = DB::select("SELECT id FROM  musicians");
                }

                //CEK Wilayah MUSISI
                foreach ($querycek as $value) {
                    $id[] = $value->id;
                }                    
                //ENDCEK

                $musisi = Musician::whereIn('id', $id)->where('Wilayah', $input['Wilayah'])->get();

                return view('hasilcarimusisi')->with('listmusisi',$musisi);
                //echo "TERPILIH Wilayah, NULL CEKBOX, TERPILIH TANGGAL";
            }
            else{
                $finalid = [];
                $ceksewa = Sewa::join('gigs', 'sewas.gig_id', '=', 'gigs.id')
                                ->where('sewas.type_sewa', '=', 'hiremusisi')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hiremusisi')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hiremusisi')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])->orwhere('sewas.type_sewa', '=', 'musisihire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'musisihire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'musisihire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->get(['sewas.*']);

                if(!$ceksewa->isEmpty()){
                    foreach ($ceksewa as $value) {
                        if ($value->type_sewa == 'hiremusisi')
                            $querycek = DB::select("SELECT * FROM  musicians WHERE id NOT IN ($value->object_id)");
                        elseif($value->type_sewa == 'musisihire')
                            $querycek = DB::select("SELECT * FROM  musicians WHERE id NOT IN ($value->subject_id)");
                        else
                            $querycek = DB::select("SELECT * FROM  musicians");
                    }
                }
                else{
                    $querycek = DB::select("SELECT * FROM  musicians");
                }

                if($querycek != null){
                    foreach ($querycek as $value) {
                        $id[] = $value->id;
                    }
                    $musisi = Musician::whereIn('id', $id)->where('Wilayah', $input['Wilayah'])->where('aktif', 'Y')->get();
                    $genreinput = $request->checkbox;
                    foreach ($musisi as $_musisi) {
                        $cekgenre = GenreMusisi::where('musician_id', $_musisi->id)->get(['genre_id']);
                        // foreach ($cekgenre as $_cekgenre) {
                        //     $arrgen = (array) $_cekgenre->genre_id; 
                        //     $result = array_intersect($request->checkbox, $arrgen);

                        //     $_cekgenre->hasil = $result;
                        // }
                        $_musisi->genre = $cekgenre;
                        $_musisi->hasil = $genreinput;

                    }

                    foreach ($musisi as $final) {
                        foreach ($final->genre as $finalgenre) {
                            $genreid[] = $finalgenre->genre_id;
                        }

                        $result = array_intersect($genreid, $final->hasil);
                        if ($result != null){
                            $finalid[] = $final->id;
                        }
                        else{
                            $finalid = [];
                        }                   
                    }

                    if($finalid != null){
                        foreach ($finalid as $hasilakhir) {
                            $hasilcariband = Musician::where('id', $hasilakhir)->where('aktif', 'Y')->first();
                            $idband[] = $hasilcariband;
                        }
                    }else{
                        $idband = [];
                    }

                }
                else{
                    $musisi = Musician::where('Wilayah', $input['Wilayah'])->where('aktif', 'Y')->get();
                    $genreinput = $request->checkbox;
                    foreach ($musisi as $_musisi) {
                        $cekgenre = GenreMusisi::where('musician_id', $_musisi->id)->get(['genre_id']);
                        // foreach ($cekgenre as $_cekgenre) {
                        //     $arrgen = (array) $_cekgenre->genre_id; 
                        //     $result = array_intersect($request->checkbox, $arrgen);

                        //     $_cekgenre->hasil = $result;
                        // }
                        $_musisi->genre = $cekgenre;
                        $_musisi->hasil = $genreinput;
                    }

                    foreach ($musisi as $final) {
                        foreach ($final->genre as $finalgenre) {
                            $genreid[] = $finalgenre->genre_id;
                        }

                        $result = array_intersect($genreid, $final->hasil);
                        if ($result != null){
                            $finalid[] = $final->id;
                        }
                        else{
                            $finalid = [];
                        }                   
                    }

                    if($finalid != null){
                        foreach ($finalid as $hasilakhir) {
                            $hasilcariband = Musician::where('id', $hasilakhir)->where('aktif', 'Y')->first();
                            $idband[] = $hasilcariband;
                        }
                    }else{
                        $idband = [];
                    }
                }

                return view('hasilcarimusisi')->with('listmusisi',$idband);
                //echo "Wilayah, CEKBOX, TANGGAL TERPILIH";
            }
            //END KUMPULAN IF
            
        }
        //END PILIH MUSISI

        //Kalau pilih BAND
        else{

            //KUMPULAN IF
            if($input['Wilayah'] == null && $request->checkbox == null && $input['tanggal'] == null){              
                $join = Grupband::where('aktif', 'Y')->get();
                return view('hasilcari')->with('listband',$join);
               // echo "NULL Wilayah, NULL CEKBOX, NULL TANGGAL";
            }
            elseif($input['Wilayah'] == null && $request->checkbox == null && $input['tanggal'] != null){                            
                $ceksewa = Sewa::join('gigs', 'sewas.gig_id', '=', 'gigs.id')
                                ->where('sewas.type_sewa', '=', 'hireband')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hireband')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hireband')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])->orwhere('sewas.type_sewa', '=', 'bandhire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'bandhire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'bandhire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->get(['sewas.*']);

                if(!$ceksewa->isEmpty()){
                    foreach ($ceksewa as $value) {
                        if ($value->type_sewa == 'hireband')
                            $querycek = DB::select("SELECT * FROM  grupbands WHERE id NOT IN ($value->object_id)");
                        elseif($value->type_sewa == 'bandhire')
                            $querycek = DB::select("SELECT * FROM  grupbands WHERE id NOT IN ($value->subject_id)");
                        else
                            $querycek = DB::select("SELECT * FROM  grupbands");
                    }
                }
                else{
                    $querycek = DB::select("SELECT * FROM  grupbands");
                }

                return view('hasilcari')->with('listband',$querycek);
                //echo "NULL Wilayah, NULL CEKBOX, TERPILIH TANGGAL";
            }
            elseif($input['Wilayah'] == null && $request->checkbox != null && $input['tanggal'] == null){
                $musisi = Grupband::where('aktif', 'Y')->get();
                $genreinput = $request->checkbox;
                foreach ($musisi as $_musisi) {
                    $cekgenre = GenreBand::where('band_id', $_musisi->id)->get(['genre_id']);
                    // foreach ($cekgenre as $_cekgenre) {
                    //     $arrgen = (array) $_cekgenre->genre_id; 
                    //     $result = array_intersect($request->checkbox, $arrgen);

                    //     $_cekgenre->hasil = $result;
                    // }
                    $_musisi->genre = $cekgenre;
                    $_musisi->hasil = $genreinput;
                }


                foreach ($musisi as $final) {
                    foreach ($final->genre as $finalgenre) {
                        $genreid[] = $finalgenre->genre_id;
                    }

                    $result = array_intersect($genreid, $final->hasil);
                    if ($result != null){
                        $finalid[] = $final->id;
                    }
                    else{
                        $finalid = [];
                    }                   
                }

                if($finalid != null){
                    foreach ($finalid as $hasilakhir) {
                        $hasilcariband = Grupband::where('id', $hasilakhir)->where('aktif', 'Y')->first();
                        $idband[] = $hasilcariband;
                    }
                }else{
                    $idband = [];
                }

                return view('hasilcari')->with('listband',$idband);
                //echo "NULL Wilayah, TERPILIH CEKBOX, NULL TANGGAL";
            }
            elseif($input['Wilayah'] == null && $request->checkbox != null && $input['tanggal'] != null){
                
                $ceksewa = Sewa::join('gigs', 'sewas.gig_id', '=', 'gigs.id')
                                ->where('sewas.type_sewa', '=', 'hireband')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hireband')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hireband')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])->orwhere('sewas.type_sewa', '=', 'bandhire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'bandhire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'bandhire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->get(['sewas.*']);

                if(!$ceksewa->isEmpty()){
                    foreach ($ceksewa as $value) {
                        if ($value->type_sewa == 'hireband')
                            $querycek = DB::select("SELECT * FROM  grupbands WHERE id NOT IN ($value->object_id)");
                        elseif($value->type_sewa == 'bandhire')
                            $querycek = DB::select("SELECT * FROM  grupbands WHERE id NOT IN ($value->subject_id)");
                        else
                            $querycek = DB::select("SELECT * FROM  grupbands");
                    }
                }
                else{
                    $querycek = DB::select("SELECT * FROM  grupbands");
                }

                if($querycek != null){
                    foreach ($querycek as $value) {
                        $id[] = $value->id;
                    }
                    $musisi = Grupband::whereIn('id', $id)->where('aktif', 'Y')->get();
                    $genreinput = $request->checkbox;
                    foreach ($musisi as $_musisi) {
                        $cekgenre = GenreBand::where('band_id', $_musisi->id)->get(['genre_id']);
                        // foreach ($cekgenre as $_cekgenre) {
                        //     $arrgen = (array) $_cekgenre->genre_id; 
                        //     $result = array_intersect($request->checkbox, $arrgen);

                        //     $_cekgenre->hasil = $result;
                        // }
                        $_musisi->genre = $cekgenre;
                        $_musisi->hasil = $genreinput;
                    }

                    foreach ($musisi as $final) {
                        foreach ($final->genre as $finalgenre) {
                            $genreid[] = $finalgenre->genre_id;
                        }

                        $result = array_intersect($genreid, $final->hasil);
                        if ($result != null){
                            $finalid[] = $final->id;
                        }
                        else{
                            $finalid = [];
                        }                   
                    }

                    if($finalid != null){
                        foreach ($finalid as $hasilakhir) {
                            $hasilcariband = Grupband::where('id', $hasilakhir)->where('aktif', 'Y')->first();
                            $idband[] = $hasilcariband;
                        }
                    }else{
                        $idband = [];
                    }

                }
                else{
                    $musisi = Grupband::where('aktif', 'Y')->get();
                    $genreinput = $request->checkbox;
                    foreach ($musisi as $_musisi) {
                        $cekgenre = GenreBand::where('band_id', $_musisi->id)->get(['genre_id']);
                        // foreach ($cekgenre as $_cekgenre) {
                        //     $arrgen = (array) $_cekgenre->genre_id; 
                        //     $result = array_intersect($request->checkbox, $arrgen);

                        //     $_cekgenre->hasil = $result;
                        // }
                        $_musisi->genre = $cekgenre;
                        $_musisi->hasil = $genreinput;
                    }

                    foreach ($musisi as $final) {
                        foreach ($final->genre as $finalgenre) {
                            $genreid[] = $finalgenre->genre_id;
                        }

                        $result = array_intersect($genreid, $final->hasil);
                        if ($result != null){
                            $finalid[] = $final->id;
                        }
                        else{
                            $finalid = [];
                        }                   
                    }

                    if($finalid != null){
                        foreach ($finalid as $hasilakhir) {
                            $hasilcariband = Grupband::where('id', $hasilakhir)->where('aktif', 'Y')->first();
                            $idband[] = $hasilcariband;
                        }
                    }else{
                        $idband = [];
                    }

                }

                return view('hasilcari')->with('listband',$idband);

                //echo "NULL Wilayah, TERPILIH CEKBOX, TERPILIH TANGGAL";
            }
            elseif($input['Wilayah'] != null && $request->checkbox == null && $input['tanggal'] == null){
                $join = Grupband::where('aktif', 'Y')->where('Wilayah', $input['Wilayah'])->get();
                return view('hasilcari')->with('listband',$join);
                //echo "TERPILIH Wilayah, NULL CEKBOX, NULL TANGGAL";
            }
            elseif($input['Wilayah'] != null && $request->checkbox != null && $input['tanggal'] == null){
                
                $musisi = Grupband::where('aktif', 'Y')->where('Wilayah', $input['Wilayah'])->get();
                $genreinput = $request->checkbox;
                foreach ($musisi as $_musisi) {
                    $cekgenre = GenreBand::where('band_id', $_musisi->id)->get(['genre_id']);
                    // foreach ($cekgenre as $_cekgenre) {
                    //     $arrgen = (array) $_cekgenre->genre_id; 
                    //     $result = array_intersect($request->checkbox, $arrgen);

                    //     $_cekgenre->hasil = $result;
                    // }
                    $_musisi->genre = $cekgenre;
                    $_musisi->hasil = $genreinput;
                }

                foreach ($musisi as $final) {
                    foreach ($final->genre as $finalgenre) {
                        $genreid[] = $finalgenre->genre_id;
                    }

                    $result = array_intersect($genreid, $final->hasil);
                    if ($result != null){
                        $finalid[] = $final->id;
                    }
                    else{
                        $finalid = [];
                    }                   
                }

                if($finalid != null){
                    foreach ($finalid as $hasilakhir) {
                        $hasilcariband = Grupband::where('id', $hasilakhir)->where('aktif', 'Y')->first();
                        $idband[] = $hasilcariband;
                    }
                }else{
                    $idband = [];
                }

                return view('hasilcari')->with('listband',$idband);

                //echo "TERPILIH Wilayah, TERPILIH CEKBOX, NULL TANGGAL";
            }
            elseif($input['Wilayah'] != null && $request->checkbox == null && $input['tanggal'] != null){
                $ceksewa = Sewa::join('gigs', 'sewas.gig_id', '=', 'gigs.id')
                                ->where('sewas.type_sewa', '=', 'hireband')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hireband')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hireband')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])->orwhere('sewas.type_sewa', '=', 'bandhire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'bandhire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'bandhire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->get(['sewas.*']);

                if(!$ceksewa->isEmpty()){
                    foreach ($ceksewa as $value) {
                        if ($value->type_sewa == 'hireband')
                            $querycek = DB::select("SELECT id FROM  grupbands WHERE id NOT IN ($value->object_id)");
                        elseif($value->type_sewa == 'bandhire')
                            $querycek = DB::select("SELECT id FROM  grupbands WHERE id NOT IN ($value->subject_id)");
                        else
                            $querycek = DB::select("SELECT id FROM  grupbands");
                    }
                }
                else{
                    $querycek = DB::select("SELECT id FROM  grupbands");
                }

                //CEK Wilayah MUSISI
                foreach ($querycek as $value) {
                    $id[] = $value->id;
                }                    
                //ENDCEK

                $musisi = Grupband::whereIn('id', $id)->where('Wilayah', $input['Wilayah'])->get();

                return view('hasilcari')->with('listband',$musisi);
                //echo "TERPILIH Wilayah, NULL CEKBOX, TERPILIH TANGGAL";
            }
            else{
                $ceksewa = Sewa::join('gigs', 'sewas.gig_id', '=', 'gigs.id')
                                ->where('sewas.type_sewa', '=', 'hireband')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hireband')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'hireband')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])->orwhere('sewas.type_sewa', '=', 'bandhire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '0')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'bandhire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '1')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->orwhere('sewas.type_sewa', '=', 'bandhire')
                                ->where('sewas.status_request', '=', '1')
                                ->where('sewas.status', '=', '2')
                                ->whereRaw('? between gigs.tanggal_mulai and gigs.tanggal_selesai', [$input['tanggal']])
                                ->get(['sewas.*']);

                if(!$ceksewa->isEmpty()){
                    foreach ($ceksewa as $value) {
                        if ($value->type_sewa == 'hireband')
                            $querycek = DB::select("SELECT * FROM  grupbands WHERE id NOT IN ($value->object_id)");
                        elseif($value->type_sewa == 'bandhire')
                            $querycek = DB::select("SELECT * FROM  grupbands WHERE id NOT IN ($value->subject_id)");
                        else
                            $querycek = DB::select("SELECT * FROM  grupbands");
                    }
                }
                else{
                    $querycek = DB::select("SELECT * FROM  grupbands");
                }

                if($querycek != null){
                    foreach ($querycek as $value) {
                        $id[] = $value->id;
                    }
                    $musisi = Grupband::whereIn('id', $id)->where('Wilayah', $input['Wilayah'])->where('aktif', 'Y')->get();
                    $genreinput = $request->checkbox;
                    foreach ($musisi as $_musisi) {
                        $cekgenre = GenreBand::where('band_id', $_musisi->id)->get(['genre_id']);
                        // foreach ($cekgenre as $_cekgenre) {
                        //     $arrgen = (array) $_cekgenre->genre_id; 
                        //     $result = array_intersect($request->checkbox, $arrgen);

                        //     $_cekgenre->hasil = $result;
                        // }
                        $_musisi->genre = $cekgenre;
                        $_musisi->hasil = $genreinput;
                    }

                    foreach ($musisi as $final) {
                        foreach ($final->genre as $finalgenre) {
                            $genreid[] = $finalgenre->genre_id;
                        }

                        $result = array_intersect($genreid, $final->hasil);
                        if ($result != null){
                            $finalid[] = $final->id;
                        }
                        else{
                            $finalid = [];
                        }                   
                    }

                    if($finalid != null){
                        foreach ($finalid as $hasilakhir) {
                            $hasilcariband = Grupband::where('id', $hasilakhir)->where('aktif', 'Y')->first();
                            $idband[] = $hasilcariband;
                        }
                    }else{
                        $idband = [];
                    }

                }
                else{
                    $musisi = Grupband::where('Wilayah', $input['Wilayah'])->where('aktif', 'Y')->get();
                    $genreinput = $request->checkbox;
                    foreach ($musisi as $_musisi) {
                        $cekgenre = GenreBand::where('band_id', $_musisi->id)->get(['genre_id']);
                        // foreach ($cekgenre as $_cekgenre) {
                        //     $arrgen = (array) $_cekgenre->genre_id; 
                        //     $result = array_intersect($request->checkbox, $arrgen);

                        //     $_cekgenre->hasil = $result;
                        // }
                        $_musisi->genre = $cekgenre;
                        $_musisi->hasil = $genreinput;
                    }

                    foreach ($musisi as $final) {
                        foreach ($final->genre as $finalgenre) {
                            $genreid[] = $finalgenre->genre_id;
                        }

                        $result = array_intersect($genreid, $final->hasil);
                        if ($result != null){
                            $finalid[] = $final->id;
                        }
                        else{
                            $finalid = [];
                        }                   
                    }

                    if($finalid != null){
                        foreach ($finalid as $hasilakhir) {
                            $hasilcariband = Grupband::where('id', $hasilakhir)->where('aktif', 'Y')->first();
                            $idband[] = $hasilcariband;
                        }
                    }else{
                        $idband = [];
                    }

                }

                return view('hasilcari')->with('listband',$idband);
                //echo "Wilayah, CEKBOX, TANGGAL TERPILIH";
            }
            //END KUMPULAN IF

        }
        //END PILIH BAND
    }
}
