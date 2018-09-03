<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Hashing\BcryptHasher;

use App\Http\Requests;
use Mail;
use Session;
use App\User;
use App\Musician;
use App\Musicianpassreset;

class EmailController extends Controller
{

	public function showLinkRequestForm(){
		 return view('musician.auth.passwords.email');
	}
    public function sendResetLinkEmail(Request $request){
    	$email = $request->input('email');
    	$cek = Musician::where('email', $email)->first();

    	if($cek == null){
    		Session::flash('status', 'Email not found');
			return redirect()->back();
		}
    	else
    		$unik=str_random(54);
    		$t=time();
    		$token = $unik.$t;
    		//$content = "Click here to reset your password: <a href='/password/reset/$token'>Click Here</a>";

    		$reset = New Musicianpassreset;
	        $reset->email = $email;
	        $reset->token = $token;
	        $reset->save();

	        Mail::send('musician.auth.send', ['title' => 'Your Password Reset Link', 'token' => $token], function ($message) use ($email)
	        {
	        	$message->subject('Your Password Reset Link');
	            $message->from('tianboyand97@gmail.com', 'Admin Gighub');
	            $message->to($email);
	        });

	        Session::flash('status2', 'We have e-mailed your password reset link!');
	        return redirect()->back();   
	}

	public function showResetForm(Request $request, $token){
		$cek = Musicianpassreset::where('token', $token)->first();
		if($cek == null)
			return redirect('login');
		else
			return view('musician.auth.passwords.reset')->with('token', $token);
	}

	public function reset(Request $request){
		$email = $request->email;
		$token = $request->token;
		$pass = $request->password;
		$passconfirm = $request->password_confirmation;

		$cek = Musicianpassreset::where('token', $token)->first();

		if($cek->email !== $email){
			Session::flash('status2', 'Wrong email address!');
	        return redirect()->back(); 
		}
		else{
			if($pass !== $passconfirm){
				Session::flash('status2', 'Password confirmation did not match!');
		        return redirect()->back(); 
			}
			else{
				$ubah = Musician::where('email', $email)->first();
				$ubah->password = bcrypt($pass);
				$ubah->save();
				Session::flash('status', 'Password has been change! Please login');
				return redirect('login');
			}
		}
	}
}
