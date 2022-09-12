<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseHelper;
use App\Http\Helper\SendMailHelper;
use App\Models\User;
use App\Models\VerifyCodes;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
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
                return SendMailHelper::sendVerifyCode($user->id, $user->email);
            }

            //* Catch all error and return it
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /// @route   POST auth/verify
    /// @desc    Verify all user email
    /// @desc    Verify user email and reset password
    /// @access  Public
    public function verify(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'integer', 'min:100000', 'max:999999'],
            'type' => ['required', 'string', 'in:email,reset'],
            'password' => ['required_if:type,reset', 'string', 'min:8', 'confirmed'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            //* Find VerifyCode where code = request code and expire_at date is greater than date now in database 
            //* and return unauthorized response if not exist
            $verifyCodesExist = VerifyCodes::where('code', $request->code)
                ->whereDate('expired_at', '>=', Carbon::now())
                ->first();
            if (!$verifyCodesExist) {
                return ResponseHelper::failUnauthorized();
            }

            if ($request->type != $verifyCodesExist->type) {
                return ResponseHelper::failUnauthorized();
            } elseif ($request->type == 'email') {
                //* Update email verified to date now in table users where id
                User::where('id', $verifyCodesExist->user_id)->update(['email_verified_at' => Carbon::now()]);
            } else {
                $userExist = User::where('id', $verifyCodesExist->user_id)->whereNot('email_verified_at', null)->first();

                if (!$userExist) {
                    return ResponseHelper::failUnauthorized();
                }

                $userExist->password = bcrypt($request->password);
                $userExist->save();
            }

            //* Delete column verify code
            $verifyCodesExist->delete();

            //* Return success response
            return ResponseHelper::respond('Your email verification success');

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /// @route   POST auth/resend_code
    /// @desc    Resend verify code
    /// @access  Public
    public function resendCode(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'type' => ['required', 'string', 'in:email,reset'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            //* Find User where email and email verified at is null
            $userExist = User::where('email', $request->email)->first();
            //* If verify code not exist return unauthorized response
            if (!$userExist) {
                return ResponseHelper::failUnauthorized();
            }

            if ($request->type == 'email' && $userExist->email_verified_at != null) {
                return ResponseHelper::failUnauthorized();
            }

            if ($request->type == 'reset' && $userExist->email_verified_at == null) {
                return ResponseHelper::failUnauthorized();
            }

            //* Delete column verify code
            VerifyCodes::where('user_id', $userExist->id)->delete();

            //* Return success response
            return SendMailHelper::sendVerifyCode($userExist->id, $userExist->email, $request->type);

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /// @route   POST auth/login
    /// @desc    Verified email user login with correct email and password
    /// @access  Public
    public function login(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            //* Check user where email and password
            $userExist = User::where('email', $request->email)->first();
            if (!$userExist || !Hash::check($request->password, $userExist->password)) {
                //* Return credential error for security purpose
                return ResponseHelper::failValidationError('Credential Error');
            }

            //* Check user is already verified their emails or not
            if ($userExist->email_verified_at == null) {
                return ResponseHelper::failUnauthorized('Email not verified');
            }

            //* Return success with user data and token 
            return ResponseHelper::respondWithToken($userExist);

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    public function google(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email'],
            'avatar' => ['required', 'string', 'max:255'],
            'google_id' => ['required', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $userExist = User::where('email', $request->email)->first();

            if ($userExist && !empty($userExist->google_id) && $userExist->google_id != $request->google_id) {
                return ResponseHelper::failUnauthorized();

                //* blablabla
            } elseif ($userExist && $userExist->google_id == $request->google_id) {
                $userExist->avatar = $request->avatar;
                $userExist->save();

                //* blablabla
            } elseif ($userExist && $userExist->google_id == null) {
                $userExist->google_id = $request->google_id;
                $userExist->avatar = $request->avatar;
                $userExist->email_verified_at = Carbon::now();
                $userExist->save();

                //* blablablabla
            } else {
                $rand = rand(100000, 999999);

                $user = new User();
                $user->username = $request->username;
                $user->email = $request->email;
                $user->password = bcrypt($rand);
                $user->google_id = $request->google_id;
                $user->avatar = $request->avatar;
                $user->email_verified_at = Carbon::now();

                if ($user->save()) {
                    return ResponseHelper::respondWithToken($user);
                }
            }

            return ResponseHelper::respondWithToken($userExist);
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /// @route   POST auth/logout
    /// @desc    Delete all user token (Access Token and Refresh Token) and delete the cookie
    /// @access  Public
    public function logout(Request $request)
    {
        try {
            //* Delete user token
            $request->user()->tokens()->delete();

            //* Return Success Logout and also delete the cookie
            return ResponseHelper::responDeleted('Success Logout', null)->withCookie(Cookie::forget('token'));

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    public function reset(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            //* Check user where email or email already verified or not
            $userExist = User::where('email', $request->email)->first();
            if (!$userExist || $userExist->email_verified_at == null) {
                //* Return email not found
                return ResponseHelper::failNotFound('Email not found');
            }


            // //* Run logic when user is save send verify code to user email
            return SendMailHelper::sendVerifyCode($userExist->id, $userExist->email, 'reset');

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
