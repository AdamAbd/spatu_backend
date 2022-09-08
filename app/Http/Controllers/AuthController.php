<?php

namespace App\Http\Controllers;

use App\Mail\SendCodeMail;
use App\Mail\SendVerificationCode;
use App\Mail\VerifyCodeMail;
use App\Models\User;
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
            return response()->json([
                'message' => $validator->errors()->first()
            ], 422);
        }

        try {
            $user = new User();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);

            if ($user->save()) {
                Mail::to($request->email)->send(new VerifyCodeMail(143567));

                return response()->json([
                    'message' => 'Success',
                ], 201);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'user' => null
            ]);
        }
    }
}
