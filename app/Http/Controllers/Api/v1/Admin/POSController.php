<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictOrderResource;
use App\Models\Addon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemAddon;
use App\Models\OrderPayment;
use App\Models\OrderStatus;
use App\Models\POS;
use App\Models\ProductOption;
use App\Models\Shop;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;


class POSController extends Controller
{

    public function store(Request $request): Application|ResponseFactory|Response|StrictOrderResource
    {
        return POS::createOrder($request);
    }

}

