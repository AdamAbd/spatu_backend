<?php

namespace App\Http\Helper;

use App\Mail\VerifyCodeMail;
use App\Models\VerifyCodes;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class SendMailHelper
{
    /// Create verification code and send to user email
    public static function sendVerifyCode($userId, $email, $type = 'email')
    {
        try {
            //* Create random verification code with length of 6
            //TODO: Refactor $randomCode so it will be unique
            $randomCode = rand(100000, 999999);

            //* Save all data to database
            $verifyCodes = new VerifyCodes();
            $verifyCodes->user_id = $userId;
            $verifyCodes->code = $randomCode;
            //* Set the expiry time to 10 more minute
            $verifyCodes->expired_at = Carbon::now()->addMinute(10);
            $verifyCodes->type = $type;
            $verifyCodes->save();

            //* Mail verification code to user
            //TODO: Make subject of mail dynamic
            Mail::to($email)->send(new VerifyCodeMail($randomCode));

            //* Return success response
            return ResponseHelper::respondCreated("Please check your email", null);

            //* Catch all error and return it
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
