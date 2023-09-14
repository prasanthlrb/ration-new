<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictOrderResource;
use App\Models\POS;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;


class POSController extends Controller
{

    /**
     * @throws ValidationException
     */
    public function store(Request $request): Application|ResponseFactory|Response|StrictOrderResource
    {
        return POS::createOrder($request);
    }


}

