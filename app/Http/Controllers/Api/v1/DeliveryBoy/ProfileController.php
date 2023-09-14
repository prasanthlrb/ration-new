<?php

namespace App\Http\Controllers\Api\v1\DeliveryBoy;

use App\Helpers\CResponse;
use App\Helpers\CValidator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictDeliveryBoyResource;
use App\Models\DeliveryBoy;
use App\Models\Order;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function show(Request $request): StrictDeliveryBoyResource
    {
        $user = $request->user();
        return new StrictDeliveryBoyResource($user);
    }

    public function update(Request $request): Response|StrictDeliveryBoyResource|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_DELIVERY_ID',0)>=$request->user_id) {
            return CResponse::demoCreateNewError('delivery boy');
        }
        $delivery_boy = $request->user();

        $data = [...$delivery_boy->toArray(), ...$request->all(),];

        $validator = Validator::make($data, DeliveryBoy::rules($delivery_boy->id));
        if ($validator->fails()) {
            return CResponse::validation_error($validator->errors());
        }
        $delivery_boy->fill($validator->validated());
        $delivery_boy->saveAvatarImage($request, 'avatar', false);
        if ($request->has('password')) {
            $delivery_boy->password = Hash::make($request->get('password'));
        }

        $delivery_boy->save();
        return new StrictDeliveryBoyResource($delivery_boy);

    }

    public function update_status(Request $request): Application|ResponseFactory|Response|StrictDeliveryBoyResource
    {
        $validated_data = $this->validate($request, [
            'status' => ['required', 'boolean',],
        ]);

        $user = $request->user();

        if ($validated_data['status']) {
            $user->active_for_delivery = true;
        } else {
            $any_order = Order::where(function($q) use ($user) {
                return $q->where('delivery_boy_id', $user->id)->where('complete', false);
            })->orWhere(function($q) use ($user) {
                return $q->where('assign_delivery_boy_id', $user->id)->where('complete', false);
            })->exists();
            if ($any_order) {
                return CResponse::error('Please deliver active order first');
            }
            $user->active_for_delivery = false;
        }

        $user->save();
        return new StrictDeliveryBoyResource($user);
    }

    public function update_location(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, [
            'latitude' => ['required', 'numeric',],
            'longitude' => ['required', 'numeric',]
        ]);

        $latitude = $validated_data['latitude'];
        $longitude = $validated_data['longitude'];

        $user = $request->user();
        $user->latitude = $latitude;
        $user->longitude = $longitude;

        $user->save();
        return CResponse::success();
    }

    /**
     * TODO: ----------- You need to setup your own business logic to delete, which data
     */
    public function delete_account(Request $request): Application|ResponseFactory|Response
    {
        $customer = $request->user();
        $validated_data = CValidator::validate($request->all(), [
            'password' => 'required'
        ]);

        if (!Hash::check($validated_data['password'], $customer->password)) {
            return CResponse::validation_error(['password' => 'Password is wrong']);
        }

        //TODO: Here need to add -------------------
        return CResponse::success('account_deleted');
    }


}

