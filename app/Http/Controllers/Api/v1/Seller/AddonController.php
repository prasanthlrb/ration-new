<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Helpers\CResponse;
use App\Helpers\CValidator;
use App\Http\Controllers\Controller;
use App\Http\Resources\AddonResource;
use App\Models\Addon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class AddonController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $shop_id = $request->shop_id;
        return AddonResource::collection(Addon::where('shop_id', '=', $shop_id)->get());
    }

    public function store(Request $request): AddonResource
    {
        $validated_data = $this->validate($request, Addon::rules());
        $addon = new Addon($validated_data);
        $addon->save();
        $addon->saveImage($request);
        return new AddonResource($addon);
    }

    public function show(Request $request, $id): AddonResource
    {
        $shop_id = $request->shop_id;
        return new AddonResource(Addon::where('shop_id', '=', $shop_id)->findOrFail($id));
    }

    /**
     * @throws ValidationException
     */
    public function update(Request $request, $id): Application|ResponseFactory|Response|AddonResource
    {
        if (env('DEMO', false) && env('DEMO_MAX_ADDON_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('addon');
        }
        $shop_id = $request->shop_id;
        $addon = Addon::where('shop_id', '=', $shop_id)->findOrFail($id);
        $data = [...$addon->toArray(), ...$request->all(),];

        $validated_data = CValidator::validate($data, Addon::rules($id));
        $addon->fill($validated_data);
        $addon->saveImage($request);
        $addon->save();
        return new AddonResource($addon);
    }

    /**
     * @param Request $request
     * @param $id
     * @return Response|Application|ResponseFactory
     */
    public function removeImage(Request $request, $id): Response|Application|ResponseFactory
    {
        $shop_id = $request->shop_id;
        $addon = Addon::where('shop_id', '=', $shop_id)->findOrFail($id);
        $addon->removeImage();
        $addon->save();
        return CResponse::success('Image is deleted');
    }

}

