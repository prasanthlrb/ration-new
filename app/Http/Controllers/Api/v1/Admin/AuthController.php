<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Helpers\FirebaseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictAdminResource;
use App\Http\Resources\Strict\StrictSellerResource;
use App\Models\Admin;
use App\Models\Customer;
use App\Models\Seller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;


class AuthController extends Controller
{
    public function register(Request $request): int
    {
        return 0;

    }


    public function login(Request $request): Response|Application|ResponseFactory
    {
        $this->validate($request, Admin::loginRules());
        $admin = Admin::getFromMobileOrEmail($request->get('mobile_number'), $request->get('email'));
        if ($admin) {
            $admin->load('role');
            if (Hash::check($request->get('password'), $admin->password)) {
                $token = $admin->createToken($request->get('fcm_token'));
                return CResponse::success(['admin' => new StrictAdminResource($admin), 'token' => $token->access_token]);
            } else {
                return CResponse::validation_error(['password' => 'Password is not correct']);
            }
        } else {
            return CResponse::validation_error(['email' => 'This email is not found']);
        }
    }


    public function google_login(Request $request): Response|Application|ResponseFactory
    {
        $validated_data = $this->validate($request, Admin::googleLoginRules());

        $google_user = FirebaseHelper::getUserFromUID($validated_data['uid']);
        if ($google_user == null) {
            return CResponse::error();
        }
        $email = $google_user->email;

        $user = Admin::where('email', $email)->first();
        if ($user == null) {
            return CResponse::error('This email is not found');
        }

        $token = $user->createToken($request->get('fcm_token'));

        return CResponse::success(['admin' => new StrictAdminResource($user), 'token' => $token->access_token]);
    }


    /**
     * @param Request $request
     * @return Response|Application|ResponseFactory
     */
    public function logout(Request $request): Response|Application|ResponseFactory
    {
        if ($request->has('fcm_token')) {
            $request->user()->deleteToken($request->get('fcm_token'));
        }
        return CResponse::success();
    }


}

