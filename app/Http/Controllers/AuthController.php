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
    /// @route   POST auth/register
    /// @desc    Register new user and send the verification code to their email
    /// @access  Public
    public function register(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:16'],
            'email' => ['required', 'string', 'unique:users,email', 'email'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            //* Creating user with data from request
            $user = new User();
            $user->username = $request->username;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);

            //* Run logic when user is save send verify code to user email
            if ($user->save()) {
                return $this->sendVerifyCode($user->id, $user->email);
            }

            //* Catch all error and return it
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /// @route   POST auth/verify
    /// @desc    Verify all user email
    /// @access  Public
    public function verify(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'integer', 'min:100000', 'max:999999'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $verifyCodesExist = VerifyCodes::where('code', $request->code)->whereDate('expired_at', '>=', Carbon::now())->first();
            if (!$verifyCodesExist) {
                return ResponseHelper::failUnauthorized();
            }

            User::where('id', $verifyCodesExist->user_id)->update(['email_verified_at' => Carbon::now()]);

            $verifyCodesExist->delete();

            return ResponseHelper::respond('Your email verification success');
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /// @route   
    /// @desc    Create verification code and send to user email
    /// @access  Private
    public function sendVerifyCode($userId, $email)
    {
        try {
            //* Create random verification code with length of 6
            $randomCode = rand(100000, 999999);

            //* Save all data to database
            $verifyCodes = new VerifyCodes();
            $verifyCodes->user_id = $userId;
            $verifyCodes->code = $randomCode;
            $verifyCodes->expired_at = Carbon::now()->addMinute(10);
            $verifyCodes->save();

            //* Mail verification code to user
            Mail::to($email)->send(new VerifyCodeMail($randomCode));

            //* Return success response
            return ResponseHelper::respondCreated("Please check your email", null);

            //* Catch all error and return it
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
