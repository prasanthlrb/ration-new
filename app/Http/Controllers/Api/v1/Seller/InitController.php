<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictBusinessSettingResource;
use App\Http\Resources\Strict\StrictModuleResource;
use App\Http\Resources\Strict\StrictSellerResource;
use App\Http\Resources\Strict\StrictShopResource;
use App\Models\BusinessSetting;
use App\Models\Shop;
use Illuminate\Http\Request;


class InitController extends Controller
{

    public function index(Request $request): array
    {

        $data = [];
        if (isset($request->user_id)) {
            $shop = Shop::with('module')->find($request->user()->shop_id);
            $data['shop'] = new StrictShopResource($shop);
            $data['module'] = new StrictModuleResource($shop->module);
            $request->user()->load('role');
            $data['seller'] = new StrictSellerResource($request->user());
        }

        $business_setup = BusinessSetting::getInstance();
        $data['business_setting'] = new StrictBusinessSettingResource($business_setup);

        return $data;

    }
}

