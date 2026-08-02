<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SearchLog;
use App\Models\Waitlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey = 'products:' . md5($request->fullUrl());

        $result = $this->remember($cacheKey, function () use ($request) {
            // ... (rest of the code)
        }, 1); // Reduced to 1 second for easier testing/simulation

        return $this->successResponse($result, 'Products retrieved successfully');
    }

    public function show(Request $request, $id)
    {
        $cacheKey = "product:{$id}";

        $product = $this->remember($cacheKey, function () use ($request, $id) {
            $query = Product::with(['merchant:id,name,logo,address,phone,rating', 'category:id,name']);

            // Selective field loading for list views
            if ($request->has('fields')) {
                $fields = array_map('trim', explode(',', $request->get('fields')));
                $query->select(array_merge(['id', 'merchant_id', 'category_id'], $fields));
            }

            return $query->findOrFail($id);
        }, 60); // 60-second cache for individual product

        return $this->successResponse($product, 'Product retrieved successfully');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'master_product_id' => 'nullable|exists:master_products,id',
            'category_id' => 'required_without:master_product_id|exists:categories,id',
            'name' => 'required_without:master_product_id|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock_count' => 'required|integer|min:0',
            'images' => 'nullable|array',
            'is_available' => 'boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        $merchant = $request->user()->merchant;

        $data = [
            'merchant_id' => $merchant->id,
            'price' => $request->price,
            'stock_count' => $request->stock_count,
            'is_available' => $request->is_available ?? true,
        ];

        if ($request->has('master_product_id')) {
            $master = \App\Models\MasterProduct::findOrFail($request->master_product_id);
            $data['master_product_id'] = $master->id;
            $data['category_id'] = $master->category_id;
        } else {
            $data['name'] = $request->name;
            $data['category_id'] = $request->category_id;
            $data['images'] = $request->images;
            $data['description'] = $request->description;
        }

        $product = Product::create($data);

        // Clear product listing cache
        $this->globalCacheFlush();

        return $this->successResponse($product->load(['masterProduct', 'category']), 'Product listed successfully', 201);
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($product->merchant_id !== $request->user()->merchant->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $validator = Validator::make($request->all(), [
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'price' => 'sometimes|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'stock_count' => 'sometimes|integer|min:0',
            'is_available' => 'boolean',
            'is_featured' => 'boolean',
            'attributes' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation failed', 422, $validator->errors()->toArray());
        }

        $product->update($request->only([
            'category_id', 'name', 'description', 'images', 'price',
            'compare_price', 'stock_count', 'is_available', 'is_featured', 'attributes'
        ]));

        // Clear caches for this product
        $this->globalCacheFlush();

        return $this->successResponse($product, 'Product updated successfully');
    }

    public function destroy(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($product->merchant_id !== $request->user()->merchant->id) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $product->delete();

        // Clear caches
        $this->globalCacheFlush();

        return $this->successResponse(null, 'Product deleted successfully');
    }

    public function merchantProducts(Request $request)
    {
        $merchant = $request->user()->merchant;
        $cacheKey = "merchant_products:{$merchant->id}:" . md5($request->fullUrl());

        $products = $this->remember($cacheKey, function () use ($request, $merchant) {
            $query = Product::where('merchant_id', $merchant->id)
                ->with('category:id,name')
                ->orderBy('created_at', 'desc');

            return $this->paginateQuery($query, $request, 20, 100);
        }, 30);

        return $this->paginatedResponse($products, 'Merchant products retrieved successfully');
    }
}
