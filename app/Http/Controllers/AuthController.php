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
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            //* Check user where email
            $userExist = User::where('email', $request->email)->first();

            //* If user exist and email verified at not null return validation error
            if ($userExist && $userExist->email_verified_at != null) {
                return ResponseHelper::failValidationError('The email has already been taken.');

                //* If user exist update user with data from request
            } else if ($userExist) {
                $userExist->username = $request->username;
                $userExist->password = bcrypt($request->password);

                //* Run logic when user is save send verify code to user email
                if ($userExist->save()) {
                    return SendMailHelper::sendVerifyCode($userExist->id, $userExist->email);
                }

                //* Else creating user with data from request
            } else {
                $user = new User();
                $user->username = $request->username;
                $user->email = $request->email;
                $user->password = bcrypt($request->password);

                //* Run logic when user is save send verify code to user email
                if ($user->save()) {
                    return SendMailHelper::sendVerifyCode($user->id, $user->email);
                }
            }


            //* Catch all error and return it
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /// @route   POST auth/verify
    /// @desc    Verify user email and reset password
    /// @access  Public
    public function verify(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'integer', 'min:100000', 'max:999999'],
            'type' => ['required', 'string', 'in:email,reset'],
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
                $userExist = User::where('id', $verifyCodesExist->user_id)->first();

                if (!$userExist) {
                    return ResponseHelper::failUnauthorized();
                }

                $userExist->update(['email_verified_at' => Carbon::now()]);

                //* Delete column verify code
                $verifyCodesExist->delete();

                //* Return success with user data and token 
                return ResponseHelper::respondWithToken($userExist, 'Your email verification success');
            } else {
                $userExist = User::where('id', $verifyCodesExist->user_id)->whereNotNull('email_verified_at')->first();

                if (!$userExist) {
                    return ResponseHelper::failUnauthorized();
                }

                //* Return success response
                return ResponseHelper::respond('Your email verification success');
            }

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

            if ($request->type == 'email' && !empty($userExist->email_verified_at)) {
                return ResponseHelper::failUnauthorized();
            }

            if ($request->type == 'reset' && empty($userExist->email_verified_at)) {
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
            if (empty($userExist->email_verified_at)) {
                return ResponseHelper::failUnauthorized('Email not verified');
            }

            //* Return success with user data and token 
            return ResponseHelper::respondWithToken($userExist);

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /// @route   POST auth/google
    /// @desc    Creating or login in user with their google id
    /// @access  Public
    public function google(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email'],
            'avatar' => ['required', 'url', 'max:255'],
            'google_id' => ['required', 'string', 'max:255'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            //* Check user where email and password
            $userExist = User::where('email', $request->email)->first();

            //* Check if user is exist or not
            if ($userExist) {
                $isGoogleIdValid = Hash::check($request->google_id, $userExist->google_id);

                //* Return unauthorized when user is exist but the google id is different with google id in database
                if (!empty($userExist->google_id) && !$isGoogleIdValid) {
                    return ResponseHelper::failUnauthorized();

                    //* Check if user exist and google id is same as database
                } elseif ($isGoogleIdValid) {
                    $userExist->avatar = $request->avatar;
                    $userExist->save();

                    //* Check if user exist and google id is empty
                } elseif (empty($userExist->google_id)) {
                    $userExist->google_id = bcrypt($request->google_id);
                    $userExist->avatar = $request->avatar;
                    $userExist->email_verified_at = Carbon::now();
                    $userExist->save();
                }
                //* If user not exist yet
            } else {
                $rand = rand(100000, 999999);

                $user = new User();
                $user->username = $request->username;
                $user->email = $request->email;
                $user->password = bcrypt($rand);
                $user->google_id = bcrypt($request->google_id);
                $user->avatar = $request->avatar;
                $user->email_verified_at = Carbon::now();

                //* Run logic when user is save return success with user data and token 
                if ($user->save()) {
                    return ResponseHelper::respondWithToken($user);
                }
            }

            //* Return success with user data and token 
            return ResponseHelper::respondWithToken($userExist);

            //* Catch all error and return it
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
            return ResponseHelper::responDeleted('Success Logout', null)->withCookie(Cookie::forget('refresh_token'));

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /// @route   POST auth/reset
    /// @desc    Send reset verify code
    /// @access  Public
    public function sendReset(Request $request)
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
            $userExist = User::where('email', $request->email)->first();

            //* Check user where email or email already verified or not
            if (!$userExist || empty($userExist->email_verified_at)) {
                //* Return email not found
                return ResponseHelper::failNotFound('Email not found');
            }


            //* Run logic when user is save send verify code to user email
            return SendMailHelper::sendVerifyCode($userExist->id, $userExist->email, 'reset');

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /// @route   POST auth/reset_password
    /// @desc    Reset user password
    /// @access  Public
    public function resetPassword(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'code' => ['required', 'integer', 'min:100000', 'max:999999'],
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

            $userExist = User::where('id', $verifyCodesExist->user_id)->whereNotNull('email_verified_at')->first();

            if (!$userExist) {
                return ResponseHelper::failUnauthorized();
            }

            $userExist->password = bcrypt($request->password);
            $userExist->save();

            //* Delete column verify code
            $verifyCodesExist->delete();

            //* Return success response
            return ResponseHelper::respond('Your password has been changed');

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
