<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Helpers\CValidator;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictCustomerResource;
use App\Models\Customer;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class CustomerController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictCustomerResource::collection(Customer::all());
    }

    public function store(Request $request): Application|ResponseFactory|Response
    {
        $validated_data = CValidator::validate($request->all(), Customer::rules());
        $customer = new Customer($validated_data);
        $customer->saveAvatarImage($request);
        if ($request->get('password') !== null) {
            $customer->password = Hash::make($request->get('password'));
        }
        if ($request->get('set_email_as_verified') !== null) {
            $customer->email_verified_at = $request->get('set_email_as_verified') ? now() : null;
        }
        if ($request->get('set_mobile_number_as_verified') !== null) {
            $customer->mobile_number_verified_at = $request->get('set_mobile_number_as_verified') ? now() : null;
        }

        $customer->save();
        return CResponse::success();
    }


    public function show(Request $request, $id): StrictCustomerResource
    {
        return new StrictCustomerResource(Customer::findOrFail($id));
    }


    public function update(Request $request, $id): Application|ResponseFactory|Response
    {
        if (env('DEMO', false) && env('DEMO_MAX_CUSTOMER_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('customer');
        }
        $customer = Customer::findOrFail($id);
        $data = [...$customer->toArray(), ...$request->all(),];
        $validated_data = CValidator::validate($data, Customer::rules($id));
        $customer->fill($validated_data);
        $customer->saveAvatarImage($request);
        if ($request->get('password') !== null) {
            $customer->password = Hash::make($request->get('password'));
        }
        if ($request->get('set_as_email_verified') !== null) {
            $customer->email_verified_at = $request->get('set_as_email_verified') ? now() : null;
        }
        if ($request->get('set_as_mobile_number_verified') !== null) {
            $customer->mobile_number_verified_at = $request->get('set_as_mobile_number_verified') ? now() : null;
        }

        $customer->save();
        return CResponse::success();
    }

    public function remove_avatar(Request $request, $id): Application|ResponseFactory|Response
    {
        $customer = Customer::findOrFail($id);
        $customer->deleteAvatar();
        $customer->save();
        return CResponse::success();
    }

}

