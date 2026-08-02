<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MasterProduct;
use Illuminate\Http\Request;

class MasterProductController extends Controller
{
    /**
     * Search for master products (for Merchants and Customers)
     */
    public function index(Request $request)
    {
        $query = MasterProduct::with('category');

        if ($request->has('q')) {
            $q = $request->q;
            $query->where(function($sub) use ($q) {
                $sub->where('name', 'like', "%$q%")
                    ->orWhere('brand', 'like', "%$q%")
                    ->orWhereJsonContains('search_tags', $q);
            });
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $this->paginateQuery($query, $request, 50, 200);

        return $this->paginatedResponse($products, 'Master products retrieved successfully');
    }

    public function show($id)
    {
        $product = MasterProduct::with(['category', 'listings.merchant'])->findOrFail($id);
        return $this->successResponse($product, 'Master product details retrieved');
    }
}
