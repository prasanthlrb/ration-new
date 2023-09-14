<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Shop;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class ProductImageController extends Controller
{

    public function destroy(Request $request, $id): Response|Application|ResponseFactory
    {
        $shop_id = $request->shop_id;
        $productImage = ProductImage::with(['product' => function ($query) use ($shop_id) {
            $query->where('shop_id', '=', $shop_id);
        }])->findOrFail($id);
        $productImage->removeImage();
        $productImage->delete();
        return CResponse::success('Product image is deleted');
    }


}

