<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;

class EmailVerificaionTokenController extends Controller
{
    public function notice(Request $request)
    {

        return $request->user('merchant')->hasVerifiedEmail()
            ? redirect()->route('merchant.index')
            : view('merchant.auth.verify-email');
    }

    public function verify(Request $request)
    {
        $merchant = Merchant::where('verification_token' , $request->token)->firstOrFail();


        if($merchant->verification_token_expires_at > now()){
            $merchant->verify_verification_token() ;
            return redirect()->intended(route('merchant.index'));
        }
        abort(401) ;

    }

    public function store(Request $request)
    {
        if ($request->user('merchant')->hasVerifiedEmail()) {
            return to_route('merchant.index');
        }

        $request->user('merchant')->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
