<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RefreshTokenController extends Controller
{
    /// @route   POST refresh-token
    /// @desc    Delete old Access Token and create new Access Token with Refresh Token attach
    /// @access  Public
    public function refreshToken(Request $request)
    {
        try {
            //* Loop all user token where name is access-token and delete it
            foreach ($request->user()->tokens as $token) {
                if ($token->name == "access-token") {
                    $request->user()->tokens()->where('id', $token->id)->delete();
                }
            }

            //* Create new Access Token
            $accessToken = $request->user()->createToken('access-token', ['user|accessToken'], Carbon::now()->addMinute(30))
                ->plainTextToken;

            //* Return success with new Access Token
            return ResponseHelper::respondCreated('Success Creating Token', $accessToken);

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
