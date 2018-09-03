<?php

namespace App\Classes;

use App\Notif;
use App\Musician;
use App\User;
use App\Grupband;

class Firebase{
	public static function sendPushNotification($data, $notif){
		// 'id','object_id','subject_id','user_id','type_user','type_notif','type_subject','baca'
		// udin mengirim permintaan sewa ke anda
		// subject_id mengirim object_id ke user_id (type_user) 

		// object_id    => id data (notif apa) yang dikirim misal: gig_id
        // subject_id   => id user pengirim
        // user_id      => id user penerima
        // type_user    => type user penerima 


		$auth = null;
		if($data['type_user']=='musisi'){
			$auth = Musician::find($data['user_id']);
		}
		elseif ($data['type_user']=='organizer') {
			$auth = User::find($data['user_id']);
		}
		elseif ($data['type_user']=='band') {
			$grupband = Grupband::find($data['user_id']); 
			$auth = Musician::find($grupband->admin_id);
		}
		$_data = array(
			'to'=> $auth->firebase,
			'data'=> $notif
		);

		$key_firebase = 'AAAAPtKtpOI:APA91bGEVuE4FgWs__X8yhfwKU2zlzWxTFELjGFmLOcE9KtFTQ2YBiTWM_6hQNuVXC1aYx3SmTfU9RJORQuBjL2FvQ-g_sw3g2UmjRyH9b5UeRHp405L3yeySnmMhrn6mOHy7nqzGyIy';

		$url = 'https://fcm.googleapis.com/fcm/send';
		$headers = array(
			'Authorization: key='.$key_firebase,
			'Content-Type: application/json'
		);

		$ch = curl_init();
		//Setting the curl url
        curl_setopt($ch, CURLOPT_URL, $url);

        //setting the method as post
        curl_setopt($ch, CURLOPT_POST, true);

        //adding headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //disabling ssl support
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        // adding the data in json format
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($_data));

        $result =  curl_exec($ch);
        if($result == false){
            $result = 'Curl failed: '.curl_error($ch);
        }
        curl_close($ch); 

		Notif::Create($data);
        return $result;
	}
}