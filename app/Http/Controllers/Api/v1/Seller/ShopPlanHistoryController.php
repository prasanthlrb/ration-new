<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictShopPlanHistoryResource;
use App\Models\ShopPlanHistory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;


class ShopPlanHistoryController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        $shop_id = $request->shop_id;
        $plan_histories = ShopPlanHistory::with(['shop', 'shopPlan'])->where('shop_id', $shop_id)->latest()->get();
        return StrictShopPlanHistoryResource::collection($plan_histories);
    }


}

