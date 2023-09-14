<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\HomeBannerResource;
use App\Models\HomeBanner;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;


class HomeBannerController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        return HomeBannerResource::collection(HomeBanner::with(['shop', 'product'])->get());
    }


    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, HomeBanner::rules());
        $banner = new HomeBanner($validated_data);
        $banner->saveImage($request);
        $banner->save();
        return CResponse::success('Banner is created');
    }

    public function show(Request $request, $id): HomeBannerResource
    {
        return new HomeBannerResource(HomeBanner::findOrFail($id));
    }


    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_HOME_BANNER_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('home banner');
        }
        $banner = HomeBanner::findOrFail($id);
        $validated_data = $this->validate($request, HomeBanner::rules());
        $banner->saveImage($request);
        if (isset($validated_data['product_id'])) {
            $validated_data['shop_id'] = null;
        }
        if (isset($validated_data['shop_id'])) {
            $validated_data['product_id'] = null;
        }
        $banner->update($validated_data);
        $banner->save();
        return CResponse::success('Banner is updated');
    }


}

