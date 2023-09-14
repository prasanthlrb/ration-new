<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictProductResource;
use App\Http\Resources\Strict\StrictShopResource;
use App\Models\Product;
use App\Models\Shop;
use Illuminate\Http\Request;


class CustomerFavoriteController extends Controller
{

    public function index(Request $request): array
    {
        $module_id = $request->query('module_id');

        $productQuery = Product::withAll();
        $shopQuery = Shop::withAll();

        if (isset($module_id)) {
            $productQuery = $productQuery->where('module_id', $module_id);
            $shopQuery = $shopQuery->where('module_id', $module_id);
        }
        $productQuery->with(Product::getFavoriteRelation($request->user_id))
            ->whereHas('customerFavoriteProducts', function ($q) use ($request) {
                $q->where('customer_id', '=', $request->user_id);
            });

        $shopQuery->with(Shop::getFavoriteRelation($request->user_id))
            ->whereHas('customerFavoriteShops', function ($q) use ($request) {
                $q->where('customer_id', '=', $request->user_id);
            });

        return [
            'products' => StrictProductResource::collection($productQuery->get()),
            'shops' => StrictShopResource::collection($shopQuery->get()),
        ];
    }
}

