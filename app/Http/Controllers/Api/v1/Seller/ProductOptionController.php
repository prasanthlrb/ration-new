<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductOptionResource;
use App\Http\Resources\ProductVariantResource;
use App\Models\ProductOption;
use App\Models\ProductVariant;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class ProductOptionController extends Controller
{

    public function store(Request $request): ProductOptionResource
    {
        $validated_data = $this->validate($request, ProductOption::rules());
        $product_option = new ProductOption($validated_data);
        $product_option->save();
        return new ProductOptionResource($product_option);

    }

    public function show(Request $request, $id): ProductVariantResource
    {
        $shop_id = $request->shop_id;

        return new ProductVariantResource(ProductVariant::with(['shop', 'productImages', 'product', 'productOptions'])
            ->where('shop_id', '=', $shop_id)->findOrFail($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Response|Application|ResponseFactory|ProductOptionResource
    {
        $shop_id = $request->shop_id;
        $product_option = ProductOption::findOrFail($id);
        $validated_data = $this->validate($request, ProductOption::rules($id));
        if (ProductOption::where('product_id', $product_option->product_id)
            ->where('option_value', $validated_data['option_value'])->where('id', '!=', $id)->exists()) {
            return CResponse::validation_error(['option_value' => "This option is already used. Use other"]);
        }
        $product_option->update($validated_data);
        $product_option->save();

        return new ProductOptionResource($product_option);
    }
}

