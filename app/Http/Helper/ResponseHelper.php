<?php

namespace App\Http\Helper;

use App\Http\Helper\ResponseCode;
use App\Models\User;
use Carbon\Carbon;

class ResponseHelper
{
    public static function respond($message = "Success", $data = null, $statusCode = ResponseCode::success)
    {
        return response()->json([
            "code" => $statusCode,
            "status" => "Success",
            "message" => $message,
            "data" => $data
        ], $statusCode);
    }

    public static function fail($errors, $status, $statusCode = ResponseCode::invalid_request)
    {
        return response()->json([
            "code" => $statusCode,
            "status" => $status,
            "errors" => $errors
        ], $statusCode);
    }

    public static function respondCreated($message, $data = null)
    {
        return ResponseHelper::respond($message, $data, ResponseCode::created);
    }

    public static function responDeleted($message, $data = null)
    {
        return ResponseHelper::respond($message, $data, ResponseCode::deleted);
    }

    public static function respondUpdated($message, $data = null)
    {
        return ResponseHelper::respond($message, $data, ResponseCode::updated);
    }

    public static function responNoContent($message, $data = null)
    {
        return ResponseHelper::respond($message, $data, ResponseCode::no_content);
    }

    public static function failUnauthorized($errors = null, $status = "Unauthorized")
    {
        return ResponseHelper::fail($errors, $status, ResponseCode::unauthorized);
    }

    public static function failForbidden($errors = null, $status = "Forbidden")
    {
        return ResponseHelper::fail($errors, $status, ResponseCode::forbidden);
    }

    public static function failNotFound($errors = null, $status = "Not Found")
    {
        return ResponseHelper::fail($errors, $status, ResponseCode::resource_not_found);
    }

    public static function failValidationError($errors = null, $status = "Bad Request")
    {
        return ResponseHelper::fail($errors, $status, ResponseCode::invalid_data);
    }

    public static function failResourceExists($errors = null, $status = "Conflict")
    {
        return ResponseHelper::fail($errors, $status, ResponseCode::resource_exists);
    }

    public static function failResourceGone($errors = null, $status = "Gone")
    {
        return ResponseHelper::fail($errors, $status, ResponseCode::resource_gone);
    }

    public static function failTooManyRequests($errors = null, $status = "Too Many Requests")
    {
        return ResponseHelper::fail($errors, $status, ResponseCode::too_many_requests);
    }

    public static function failServerError($errors = null, $status = "Internal Server Error")
    {
        return ResponseHelper::fail($errors, $status, ResponseCode::server_error);
    }

    public static function respondWithToken(User $user, $message = 'Success Login.')
    {
        try {
            //* Creating two types of token
            //* Access Token used for accesing user only API routes with limited time (30 minute)
            //* Refresh Token used for refresh Access Token after 30 minute 
            $accessToken = $user->createToken("access-token", ["user|accessToken"], Carbon::now()->addMinute(30))->plainTextToken;
            $refreshToken = $user->createToken("refresh-token", ["user|refreshToken"], Carbon::now()->addDay(30))->plainTextToken;

            //* Creating cookie with Refresh Token and live only 30 day
            $cookie = cookie("refresh_token", $refreshToken, 60 * 24 * 30);

            //* Return success with data of user and Access Token while sending the cookie
            return ResponseHelper::respond($message, [
                'user' => $user,
                'access_token' => $accessToken,
            ])->withCookie($cookie);

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
