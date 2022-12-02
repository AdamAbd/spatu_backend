<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseHelper;
use App\Models\Product;
use App\Models\ProductColorType;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\Size;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $products = Product::with(['brand', 'product_images', 'product_color_types', 'product_sizes'])->get();

            //* Return user detail
            return ResponseHelper::respond('Success Get Products', $products);

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
    public function storeProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            'title' => ['required', 'string', 'unique:products,title', 'max:255'],
            'rating' => ['required', 'numeric', 'gt:0'],
            'reviews_total' => ['required', 'integer', 'gt:0'],
            'solds_total' => ['required', 'integer', 'gt:0'],
            'description' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $product = new Product();

            $product->brand_id = $request->brand_id;
            $product->title = $request->title;
            $product->rating = $request->rating;
            $product->reviews_total = $request->reviews_total;
            $product->solds_total = $request->solds_total;
            $product->description = $request->description;

            if ($product->save()) {
                return ResponseHelper::respondCreated('Success', $product);
            }
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeProductImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'image' => ['required', 'image', 'mimes:png,jpg'],
        ]);

        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $productImageName = $request->file('image')->getClientOriginalName();
            $request->file('image')->storePubliclyAs('public/product/image', $productImageName);

            $productImage = new ProductImage();

            $productImage->product_id = $request->product_id;
            $productImage->image = 'storage/product/image/' . $productImageName;

            if ($productImage->save()) {
                return ResponseHelper::respondCreated('Success', $productImage);
            }
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeProductColorType(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'image' => ['required', 'image', 'mimes:png,jpg'],
        ]);

        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $productColorTypeName = $request->file('image')->getClientOriginalName();
            $request->file('image')->storePubliclyAs('public/product/color', $productColorTypeName);

            $productColorType = new ProductColorType();

            $productColorType->product_id = $request->product_id;
            $productColorType->image = 'storage/product/color/' . $productColorTypeName;

            if ($productColorType->save()) {
                return ResponseHelper::respondCreated('Success', $productColorType);
            }
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeSize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'size' => ['required', 'numeric', 'unique:sizes,size'],
        ]);

        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $size = new Size();

            $size->size = $request->size;

            if ($size->save()) {
                return ResponseHelper::respondCreated('Success', $size);
            }
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeProductSize(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'size_id' => ['required', 'numeric', 'exists:sizes,id'],
        ]);

        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        try {
            $productSize = new ProductSize();

            $productSize->product_id = $request->product_id;
            $productSize->size_id = $request->size_id;

            if ($productSize->save()) {
                return ResponseHelper::respondCreated('Success', $productSize);
            }
        } catch (\Exception $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
