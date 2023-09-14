<?php

namespace App\Http\Controllers\Api\v1\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class TestController extends Controller
{
    public function test(Request $request): Response|Application|ResponseFactory
    {
        return response($request->user());
    }


}

