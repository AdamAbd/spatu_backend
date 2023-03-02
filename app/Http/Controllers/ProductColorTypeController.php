<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseHelper;
use App\Models\ProductColorType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductColorTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            //* Get all Products Color Type
            $productsColor = ProductColorType::get();

            //* Return all Products Color Type
            return ResponseHelper::respond('Success Get Products Color Type', $productsColor);

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'image' => ['required', 'image', 'mimes:png,jpg', 'max:5120'],
            'price' => ['required', 'integer'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $file = $request->file('image');

            //* get file original name
            $productColorName = $file->getClientOriginalName();
            //* store file publicly
            $file->storePubliclyAs('public/product/color', $productColorName);

            //* Creating Product Color Type with data from request
            $productColor = new ProductColorType();

            $productColor->product_id = $request->product_id;
            $productColor->image = 'storage/product/color/' . $productColorName;
            $productColor->price = $request->price;

            $productColor->save();

            //* Return response created
            return ResponseHelper::respondCreated('Success Create Product Color Type', $productColor);
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'product_id' => ['integer', 'exists:products,id'],
            'image' => ['image', 'mimes:png,jpg', 'max:5120'],
            'price' => ['integer'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $productColorExist = ProductColorType::find($id);

            if (!$productColorExist) {
                return ResponseHelper::failNotFound('Product Color Type Not Found');
            }

            $productColorExist->update($request->all());

            if ($request->hasFile('image')) {
                $file = $request->file('image');

                //* get file original name
                $productColorName = $file->getClientOriginalName();
                //* store file publicly
                $file->storePubliclyAs('public/product/color', $productColorName);

                $productColorExist->image = 'storage/product/color/' . $productColorName;

                $productColorExist->update();
            }

            return ResponseHelper::respondUpdated('Success Update Product Color Type', ProductColorType::find($id));
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $productColorExist = ProductColorType::destroy($id);

            if (!$productColorExist) {
                return ResponseHelper::failNotFound('Product Color Type Not Found');
            }

            return ResponseHelper::responDeleted('Success Delete Product Color Type');
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
