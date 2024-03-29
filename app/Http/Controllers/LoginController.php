<?php

namespace App\Http\Controllers;

use App\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Session;
use Cookie;
use Socialite;
class LoginController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function redirectToGoogle(Request $request)
    {
        
        $intended = $request->get('page');

        $url = $request->get('url');    
        
        $payment_page =  url()->previous();

        if ($payment_page) {
            Session::put('pay', $payment_page);
        }else{
            if ($intended) {
                Session::put('page_url', $intended);
            }
            if($url){
                Session::put('url', $url);
                $request->session()->put('url', 'hello world');

            }
        } 
        Session::put('url.intended', $intended);
        Session::put('url.crawl', $url);
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        
        try {

             $redirect = Session::get('url.intended', url('/'));
             $url = Session::get('url.crawl', url('/'));

            $user = Socialite::driver('google')->user();

            $finduser = User::where('google_id', $user->id)->first();
            
            if ($finduser){

                Auth::login($finduser);
                if(Session::get('pay') !=null){
                    //dd('working');
                    return redirect(Session::get('pay'));
                }else{

                    if($redirect != null && $url != null){
                        return redirect($redirect.'?url='.urlencode($url));
                    }
                    elseif ($redirect != null && $url == null) {
                        return redirect($redirect);
                    }elseif ($redirect == null && $url == null) {
                        return redirect($redirect);
                    } else {
                        return redirect()->intended('/home');
                    }
                }
            } else {
                if ($user->user['verified_email'] !== false) {
                  
                    $newUser = $newUser = User::create([
                        'name' => $user->name,
                        'email' => $user->email,
                        'google_id' => $user->id,
                        'picture' => $user->avatar,
                        'password' => encrypt('123456dummy'),
                    ]);

                    Auth::login($newUser);
                    if ($redirect != null && $url != null) {
                        return redirect($redirect . '?url=' . $url);
                    } elseif ($redirect != null && $url == null) {
                        return redirect($redirect);
                    } else {
                        return redirect()->intended('/home');

                    }

                } else {
                    $err = "Email is not verified";
                    dd($err);
                }
            }
            
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        Session::flush();
        return redirect('/home');
    }
}
