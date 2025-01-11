<?php

namespace App\Http\Controllers\MerchantAuth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\MerchantEmailVerificationRequest;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;

class VerifyEmailController extends Controller
{
    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(MerchantEmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user('merchant')->hasVerifiedEmail()) {
            return redirect()->intended(route('merchant.index'));
        }

        if ($request->user('merchant')->markEmailAsVerified()) {
            event(new Verified($request->user('merchant')));
        }

        return redirect()->intended(route('merchant.index'));
    }
}
