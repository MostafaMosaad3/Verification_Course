<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PasswordlessVerificationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email|max:255',
        ]);

        $merchant = Merchant::where('email' , $request->email)->first();

        if (!$merchant) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $merchant->sendEmailVerificationNotification();

        return back()->with('status' , 'email sent to your inbox');
    }

    public function verify($id)
    {
        Auth::guard('merchant')->loginUsingId($id);
        return to_route('merchant.index');
    }
}
