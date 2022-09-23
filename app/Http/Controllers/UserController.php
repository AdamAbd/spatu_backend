<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseHelper;
use App\Http\Helper\SendMailHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /// @route   GET user/detail
    /// @desc    Get user detail
    /// @access  Public
    public function detail(Request $request)
    {
        try {
            //* Return user detail
            return ResponseHelper::respond('Success', ['user' => $request->user()]);

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    public function update(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'username' => ['string', 'max:16'],
            'email' => ['string', 'unique:users,email', 'email'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $user = $request->user();
            $user->username = $request->username ?: $user->username; // Short hand ternery operator in PHP

            if ($request->email != null) {
                //TODO: Check if user google id is not empty
                //TODO: If user is login with goggle they can't change their email
                $user->email = $request->email;
                $user->email_verified_at = null;

                $user->save();
                return SendMailHelper::sendVerifyCode($user->id, $request->email);
            }

            $user->save();

            return ResponseHelper::respondUpdated('Success Update User', ['user' => $user]);

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
