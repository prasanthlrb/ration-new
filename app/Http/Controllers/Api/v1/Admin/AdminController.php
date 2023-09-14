<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictAdminResource;
use App\Models\Admin;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictAdminResource::collection(Admin::withAll()->get());
    }

    public function store(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, Admin::rules());
        $validated_data['password'] = Hash::make($validated_data['password']);
        $admin = new Admin($validated_data);
        $admin->saveAvatarImage($request);
        $admin->save();
        return Response('Admin is created', 200);
    }


    public function show(Request $request, $id): StrictAdminResource
    {
        return new StrictAdminResource(Admin::with(Admin::getAll())->findOrFail($id));
    }


    public function update(Request $request, $id): Response|Application|ResponseFactory
    {
        if (env('DEMO', false) && env('DEMO_MAX_ADMIN_ID', 0) >= $id) {
            return CResponse::demoCreateNewError('admin');
        }
        $admin = Admin::findOrFail($id);
        $validated_data = $this->validate($request, Admin::rules($id));
        $admin->fill($validated_data);
        if ($request['password'] != null) {
            $admin->password = Hash::make($validated_data['password']);
        }
        $admin->saveAvatarImage($request);
        $admin->save();
        return CResponse::success('Admin is edited');
    }


    public function remove_avatar(Request $request, $id): Response|Application|ResponseFactory
    {
        $admin = Admin::findOrFail($id);
        $admin->removeAvatar();
        $admin->save();
        return CResponse::success('Avatar removed');
    }


}

