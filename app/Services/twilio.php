<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class twilio
{
    public function send($merchant)
    {
        $sid = env("TWILIO_SID");
        $token = env("TWILIO_TOKEN");
        $client = new Client($sid, $token);

        try{
            // Use the Client to make requests to the Twilio REST API
            $client->messages->create(
            // The number you'd like to send the message to
                $merchant->phone,
                [
                    // A Twilio phone number you purchased at https://console.twilio.com
//                    'from' => '+15017250604',
                    // The body of the text message you'd like to send
                    'body' => "sending otp via twilio!"
                ]
            );
        }catch(TwilioException $e){
            log::alert($e->getMessage());
        }

    }
}
