<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseHelper;
use App\Mail\VerifyCodeMail;
use App\Models\User;
use App\Models\VerifyCodes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:16'],
            'email' => ['required', 'string', 'unique:users,email', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $user = new User();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);

            if ($user->save()) {
                return $this->sendVerifyCode($user->id, $user->email);
            }
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    public function sendVerifyCode($userId, $email)
    {
        try {
            $randomCode = rand(100000, 999999);

            $verifyCodes = new VerifyCodes();
            $verifyCodes->user_id = $userId;
            $verifyCodes->code = $randomCode;
            $verifyCodes->expired_at = Carbon::now()->addMinute(10);
            $verifyCodes->save();

            Mail::to($email)->send(new VerifyCodeMail($randomCode));

            return ResponseHelper::respondCreated("Please check your email", null);
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
