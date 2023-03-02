<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseHelper;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductSizeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            //* Get all Products Size
            $productsSize = ProductSize::get();

            //* Return all Products Size
            return ResponseHelper::respond('Success Get Products Size', $productsSize);

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
            'size' => ['required', 'numeric', 'gte:0'],
            'price' => ['required', 'integer'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            //* Creating Product Size with data from request
            $productSize = new ProductSize();

            $productSize->product_id = $request->product_id;
            $productSize->size = $request->size;
            $productSize->price = $request->price;

            $productSize->save();

            //* Return response created
            return ResponseHelper::respondCreated('Success Create Product Size', $productSize);
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
            'size' => ['numeric', 'gte:0'],
            'price' => ['integer'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $productSizeExist = ProductSize::find($id);

            if (!$productSizeExist) {
                return ResponseHelper::failNotFound('Product Size Not Found');
            }

            $productSizeExist->update($request->all());

            return ResponseHelper::respondUpdated('Success Update Product Size', ProductSize::find($id));
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
            $productSizeExist = ProductSize::destroy($id);

            if (!$productSizeExist) {
                return ResponseHelper::failNotFound('Product Size Not Found');
            }

            return ResponseHelper::responDeleted('Success Delete Product Size');
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
