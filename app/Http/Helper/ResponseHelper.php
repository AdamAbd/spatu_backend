<?php

namespace App\Http\Helper;

use App\Http\Helper\ResponseCode;
use App\Models\User;
use Carbon\Carbon;

class ResponseHelper
{
    public static function respond($message = 'Success', $data = null, $status = ResponseCode::success)
    {
        return response()->json([
            "status" => $status,
            "message" => $message,
            "data" => $data
        ], $status);
    }

    public static function fail($message, $status = 400,)
    {
        return ResponseHelper::respond($message, null, $status);
    }

    public static function respondCreated($message, $data)
    {
        return ResponseHelper::respond($message, $data, ResponseCode::created);
    }

    public static function responDeleted($message, $data)
    {
        return ResponseHelper::respond($message, $data, ResponseCode::deleted);
    }

    public static function respondUpdated($message, $data)
    {
        return ResponseHelper::respond($message, $data, ResponseCode::updated);
    }

    public static function responNoContent($message, $data)
    {
        return ResponseHelper::respond($message, $data, ResponseCode::no_content);
    }

    public static function failUnauthorized($message = 'Unauthorized')
    {
        return ResponseHelper::fail($message, ResponseCode::unauthorized);
    }

    public static function failForbidden($message = 'Forbidden')
    {
        return ResponseHelper::fail($message, ResponseCode::forbidden);
    }

    public static function failNotFound($message = 'Not Found')
    {
        return ResponseHelper::fail($message, ResponseCode::resource_not_found);
    }

    public static function failValidationError($message = 'Bad Request')
    {
        return ResponseHelper::fail($message, ResponseCode::invalid_data);
    }

    public static function failResourceExists($message = 'Conflict')
    {
        return ResponseHelper::fail($message, ResponseCode::resource_exists);
    }

    public static function failResourceGone($message = 'Gone')
    {
        return ResponseHelper::fail($message, ResponseCode::resource_gone);
    }

    public static function failTooManyRequests($message = 'Too Many Requests')
    {
        return ResponseHelper::fail($message, ResponseCode::too_many_requests);
    }

    public static function failServerError($message = 'Internal Server Error')
    {
        return ResponseHelper::fail($message, ResponseCode::server_error);
    }

    public static function respondWithToken(User $user)
    {
        try {
            //* Creating two types of token
            //* Access Token used for accesing user only API routes with limited time (30 minute)
            //* Refresh Token used for refresh Access Token after 30 minute 
            $accessToken = $user->createToken('access-token', ['user|accessToken'], Carbon::now()->addMinute(30))->plainTextToken;
            $refreshToken = $user->createToken('refresh-token', ['user|refreshToken'], Carbon::now()->addDay(30))->plainTextToken;

            //* Creating cookie with Refresh Token and live only 30 day
            $cookie = cookie('token', $refreshToken, 60 * 24 * 30);

            //* Return success with data of user and Access Token while sending the cookie
            return ResponseHelper::respond('Success Login', [
                'user' => $user,
                'access_token' => $accessToken,
            ])->withCookie($cookie);

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
