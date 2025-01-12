<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class OtpAuthenticationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
        ]);

        $merchant = Merchant::where('email' , $request->email)->first();

        if (! $merchant)
        {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $merchant->generate_otp();

        return view('merchant.auth.otp-authenticated' , ['email' =>$merchant->email]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => 'required',
            'email' => 'required|email|max:255',
        ]);

        $merchant = Merchant::where('email' , $request->email)->first();

        if (!$merchant)
        {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        if($merchant && $merchant->otp == $request->get('otp'))
        {
            if($merchant->otp_expires_at > now())
            {
                $merchant->reset_otp();
                Auth::guard('merchant')->login($merchant);
                return to_route('merchant.index');
            }
            throw ValidationException::withMessages([
                'email' => 'the otp has expired',
            ]);
        }

    }
}
