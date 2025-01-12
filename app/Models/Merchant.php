<?php

namespace App\Models;

use App\Notifications\MerchantEmailVerficationNotification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Merchant extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    protected $guarded = [ ] ;

    public function sendEmailVerificationNotification()
    {
        if (config('verification.way') == 'email')
        {
            $url = URL::temporarySignedRoute(
                'merchant.verification.verify', $date = now()->addMinutes(30),
                ['id' => $this->getKey() , 'hash'=>sha1($this->getEmailForVerification())]
            );
            $this->notify(new MerchantEmailVerficationNotification($url));
        }

        if (config('verification.way') == 'cvt')
        {
            $this->generate_verification_token();
            $url = route('merchant.verification.verify', ['id' => $this->getKey(),'token'=>$this->verification_token]);
            $this->notify(new MerchantEmailVerficationNotification($url));
        }

        if (config('verification.way') == 'passwordless')
        {
            $url = URL::temporarySignedRoute(
                'merchant.login.verify', now()->addMinutes(30),
                ['id' => $this->getKey()]
            );
            $this->notify(new MerchantEmailVerficationNotification($url));
        }
    }

    public function generate_verification_token()
    {
        if (config('verification.way') == 'cvt')
        {
            $this->verification_token = Str::random(40);
            $this->verification_token_expires_at = now()->addMinutes(30);
            $this->save();
        }
    }

    public function verify_verification_token()
    {
        if (config('verification.way') == 'cvt')
        {
            $this->email_verified_at = now() ;
            $this->verification_token = null ;
            $this->verification_token_expires_at = null ;
            $this->save();
        }
    }

    public function generate_otp()
    {
        if (config('verification.way') == 'otp')
        {
            $this->otp = rand(111111,999999);
            $this->otp_expires_at = now()->addMinutes(1);
            $this->save();
        }
    }

    public function reset_otp()
    {
        if (config('verification.way') == 'otp')
        {
            $this->otp = null ;
            $this->otp_expires_at = null ;
            $this->save();
        }
    }

}
