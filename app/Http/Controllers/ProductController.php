<?php

namespace App\Http\Controllers;

use App\Http\Helper\ResponseHelper;
use App\Models\Product;
use App\Models\ProductColorType;
use App\Models\ProductImage;
use App\Models\ProductSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /// @route   GET product
    /// @desc    Get all product
    /// @access  Public
    public function index()
    {
        try {
            //* Get all Products with relation of brand, product_images, product_color_types and product_sizes
            $products = Product::with(['brand', 'product_images', 'product_color_types', 'product_sizes'])
                ->get();

            //* Return all Products
            return ResponseHelper::respond('Success Get Products', $products);

            //* Catch all error and return it
        } catch (\Throwable $e) {
            return ResponseHelper::failServerError($e->getMessage());
        }
    }

    /// @route   POST product
    /// @desc    Store product with multiple images, colors and sizes
    /// @access  Private
    public function store(Request $request)
    {
        //* Validate all request
        $validator = Validator::make($request->all(), [
            'brand_id' => ['required', 'integer', 'exists:brands,id'],
            //* Check images parameter
            'images' => ['required', 'array'],
            //* Check values of images parameter
            'images.*' => ['required', 'image', 'mimes:png,jpg', 'max:5120'],
            'name' => ['required', 'string', 'unique:products,name', 'max:255'],
            'rating' => ['required', 'numeric', 'gt:0', 'lte:5'],
            'reviews_total' => ['required', 'integer', 'gte:0'],
            'solds_total' => ['required', 'integer', 'gte:0'],
            'description' => ['required', 'string'],
        ]);

        //* Check if request is not valid
        if ($validator->fails()) {
            return ResponseHelper::failValidationError($validator->errors()->first());
        }

        //* Store product id without initial value
        $productId = 0;

        try {
            //* Creating Product with data from request
            $product = new Product();

            $product->brand_id = $request->brand_id;
            $product->name = $request->name;
            $product->rating = $request->rating;
            $product->reviews_total = $request->reviews_total;
            $product->solds_total = $request->solds_total;
            $product->description = $request->description;

            //* Run logic when product is save create images, colors and sizes
            if ($product->save()) {
                //* Update value of variable productId with new created product id
                $productId = $product->id;

                //* Loop request with parameter images
                foreach ($request->file('images') as $file) {
                    //* get file original name
                    $productImageName = $file->getClientOriginalName();
                    //* store file publicly
                    $file->storePubliclyAs('public/product/image', $productImageName);

                    //* Creating Product with data from request and productId
                    $productImage = new ProductImage();

                    $productImage->product_id = $productId;
                    $productImage->image = 'storage/product/image/' . $productImageName;

                    $productImage->save();
                }

                //* Loop request with parameter color_images
                foreach ($request->file('color_images') as $file) {
                    //* get file original name
                    $productImageName = $file->getClientOriginalName();
                    //* store file publicly
                    $file->storePubliclyAs('public/product/color', $productImageName);

                    //* Creating ProductColorType with data from request and productId
                    $productImage = new ProductColorType();

                    $productImage->product_id = $productId;
                    $productImage->image = 'storage/product/color/' . $productImageName;

                    $productImage->save();
                }

                //* Loop request with parameter sizes
                foreach ($request->sizes as $size) {
                    //* Creating ProductSize with data from request and productId
                    $productSize = new ProductSize();

                    $productSize->product_id = $productId;
                    $productSize->size = $size;
                    $productSize->size = $size;

                    $productSize->save();
                }

                //* Find a Product with relation of brand, product_images, product_color_types and product_sizes
                $newProduct = Product::with(['brand', 'product_images', 'product_color_types', 'product_sizes'])
                    ->find($productId);

                //* Return response created
                return ResponseHelper::respondCreated('Success Create Product', $newProduct);
            }

            //* Catch all error and return it
        } catch (\Exception $e) {
            //* Run logic when variable productId no null
            if ($productId != 0) {
                //* Find specific Product and delete
                Product::find($productId)->delete();
            }

            return ResponseHelper::failServerError($e->getMessage());
        }
    }
}
