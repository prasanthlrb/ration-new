<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\Strict\StrictProductResource;
use App\Http\Resources\Strict\StrictProductReviewResource;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductReview;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\SubCategory;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class ProductController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $shop_id = $request->shop_id;
        return ProductResource::collection(Product::with(['shop', 'productImages', 'productOptions', 'addons', 'category', 'subCategory'])
            ->where('shop_id', '=', $shop_id)->get());
    }

    public function store(Request $request)
    {

        if(!$request->user()->shop->canAddProduct()){
            return CResponse::shopPlanNeedUpgrade();
        }
        $validated_data = $this->validate($request, [...Product::rules(), ...ProductOption::rules(-1)]);

        $shop = Shop::findOrFail($validated_data['shop_id']);

        $product_variant_id = null;
        if (isset($validated_data['product_variant_id'])) {
            $product_variant_id = $validated_data['product_variant_id'];
        }

        $sub_category = SubCategory::findOrFail($validated_data['sub_category_id']);
        $product = DB::transaction(function () use ($shop, $product_variant_id, $sub_category, $validated_data, $request,) {

            if ($product_variant_id == null) {
                $product_variant = new ProductVariant();
                $product_variant->save();
                $product_variant_id = $product_variant->id;
            }

            $product_validated_data = Product::extractFromData($validated_data);
            $product_validated_data['product_variant_id'] = $product_variant_id;
            $product_validated_data['category_id'] = $sub_category->category_id;
            $product = new Product($product_validated_data);
            $product->module_id = $shop->module_id;
            $product->save();

            $addon_ids = $request->get('addons');
            if (isset($addon_ids)) {
                $product->updateAddons($addon_ids);
            }

            $option_data = ProductOption::extractFromData($validated_data);
            $option_data['product_id'] = $product->id;
            $product_option = new ProductOption($option_data);
            $product_option->save();

            $product->saveImages($request);
            $product->loadAll();
            return $product;

        });
        return new StrictProductResource($product);

    }

    public function show(Request $request, $id): ProductResource
    {
        $shop_id = $request->shop_id;

        return new ProductResource(Product::withAll()
            ->where('shop_id', '=', $shop_id)->findOrFail($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Application|ResponseFactory|Response|StrictProductResource
    {
        if (env('DEMO', false) && env('DEMO_MAX_PRODUCT_ID',0)>=$id) {
            return CResponse::demoCreateNewError('product');
        }
        $shop_id = $request->shop_id;
        $product = Product::where('shop_id', '=', $shop_id)->findOrFail($id);
        $validated_data = $this->validate($request, Product::rules($id), Product::ruleMessages());
        $product->update($validated_data);

        $addons_ids = $request->get('addons');
        if (isset($addons_ids)) {
            $product->updateAddons($addons_ids);
        }
        $product->saveImages($request);
        $product->save();
        $product->loadAll();
        return new StrictProductResource($product);
    }

    public function remove_availability(Request $request, $id): StrictProductResource
    {
        $shop_id = $request->shop_id;
        $product = Product::where('shop_id', '=', $shop_id)->findOrFail($id);
        $product->available_from = null;
        $product->available_to = null;
        $product->save();
        $product->loadAll();
        return new StrictProductResource($product);
    }


    public function reviews(Request $request, $id): AnonymousResourceCollection
    {
        $product = Product::where('shop_id', '=', $request->shop_id)->findOrFail($id);
        return StrictProductReviewResource::collection(ProductReview::with(['product', 'customer'])->where('product_id', $id)->get());
    }



}

