<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseHelper;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WishlistController extends Controller
{
    public function store(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'integer', 'exists:products,id'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $wishlistExist = Wishlist::where('user_id', $request->user()->id)
                ->where('product_id', $request->product_id)
                ->first();

            if ($wishlistExist) {
                $wishlistExist->delete();

                return ResponseHelper::responDeleted('Success');
            }

            $wishlist = new Wishlist();

            $wishlist->user_id = $request->user()->id;
            $wishlist->product_id = $request->product_id;

            if ($wishlist->save()) {
                return ResponseHelper::respondCreated('Success', $wishlist);
            }
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
