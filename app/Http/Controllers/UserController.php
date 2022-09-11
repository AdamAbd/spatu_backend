<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseHelper;
use Illuminate\Http\Request;

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
}
