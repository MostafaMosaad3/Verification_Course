<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MerchantEnsureEmailsVerifiedMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (config('verification.way') == 'email' || config('verification.way') == 'cvt')
        {
            if (!$request->user('merchant') ||
                ($request->user('merchant') instanceof MustVerifyEmail &&
                    !$request->user('merchant')->hasVerifiedEmail()))
            {
                return to_route('merchant.verification.notice');
            }

        }

        return $next($request);
    }
}
