<?php

namespace App\Http\Controllers\Api\v1\Seller;

use App\Helpers\CResponse;
use App\Http\Controllers\Controller;
use App\Http\Resources\Strict\StrictDeliveryBoyResource;
use App\Http\Resources\Strict\StrictOrderResource;
use App\Models\BusinessSetting;
use App\Models\DeliveryBoy;
use App\Models\Order;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use function response;


class OrderController extends Controller
{

    public function index(Request $request): AnonymousResourceCollection
    {

        $orders = Order::withAll()->where('shop_id', '=', $request->shop_id)->orderByDesc('updated_at')->get();
        return StrictOrderResource::collection($orders);
    }

    public function show(Request $request, $id): StrictOrderResource
    {

        $order = Order::withAll()->where('shop_id', '=', $request->shop_id)->findOrFail($id);
//        return $order;
        return new StrictOrderResource($order);
    }

    public function update(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        return CResponse::success('Order is updated');
    }


    ///======================= Order Action Controller ==========================================//

    public function cancel(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::withAll()->where('shop_id', $request->shop_id)->findOrFail($id);
        if ($order->canCancel()) {
            $order->cancelByShop();
        } else
            return CResponse::error('You can\'t cancel order');
        $order->loadAll();
        return new StrictOrderResource($order);
    }

    public function reject(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::withAll()->where('shop_id', $request->shop_id)->findOrFail($id);
        if ($order->canReject()) {
            $order->rejectOrder($request->get('description'));
        } else
            return CResponse::error('You can\'t reject order');
        $order->loadAll();
        return new StrictOrderResource($order);
    }

    public function accept(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::withAll()->where('shop_id', $request->shop_id)->findOrFail($id);
        if ($order->canAccept()) {
            if($order->hasEnoughStock()){
                $order->accept();
            }else{
                return CResponse::error('Shop haven\'t enough quantity to fulfill this order');
            }
        } else
            return CResponse::error('You can\'t accept this order');
        $order->loadAll();
        return new StrictOrderResource($order);
    }

    public function set_ready_at(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $ready_at = $request->get('ready_at');
        if (isset($ready_at)) {
            $date = Carbon::parse($ready_at);
            if ($date->gte(Carbon::now())) {
                $order = Order::withAll()->where('shop_id', $request->shop_id)->findOrFail($id);
                $order->setReadyAt($date);
                return new StrictOrderResource($order);
            } else {
                return CResponse::validation_error(['est_time' => ['Please set it any future date']]);
            }
        }
        return CResponse::validation_error(['est_time' => ['Please set estimation time']]);
    }


    public function ready(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::withAll()->where('shop_id', $request->shop_id)->findOrFail($id);
        if ($order->canReady()) {
            $order->ready();
        } else
            return CResponse::error('Order can\'t be ready');
        $order->loadAll();
        return new StrictOrderResource($order);
    }

    public function assign_delivery_boy(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $validated_data = $request->validate(['delivery_boy_id' => ['required']]);
        $delivery_boy_id = $validated_data['delivery_boy_id'];
        $shop = Shop::findOrFail($request->user()->shop_id);
        $order = Order::where('shop_id', $request->shop_id)->findOrFail($id);

        if ($shop->self_delivery) {
            $delivery_boy = DeliveryBoy::where('shop_id', $shop->id)->findOrFail($delivery_boy_id);
        } else {
            $delivery_boy = DeliveryBoy::where('id', '!=', $order->assign_delivery_boy_id)->where('shop_id', null)->findOrFail($delivery_boy_id);
        }

        if (!$delivery_boy->active_for_delivery) {
            return CResponse::error('Delivery boy is not active');
        }

        $max_orders = BusinessSetting::maxOrderAssignToDeliveryBoy();
        $delivery_boy_orders = Order::where('assign_delivery_boy_id', $delivery_boy_id)->where('complete', false)->count();

        if ($delivery_boy_orders >= $max_orders) {
            return CResponse::error('Delivery boy has a already maximum numbers of order');
        }

        $order->setAssignDeliveryBoy($delivery_boy_id);

        $order->loadAll();
        return new StrictOrderResource($order);
    }


    public function deliver(Request $request, $id): Application|ResponseFactory|Response|StrictOrderResource
    {
        $order = Order::withAll()->where('shop_id', $request->shop_id)->findOrFail($id);
        if ($order->canDeliver()) {
            $order->deliverByShop();
        } else {
            return CResponse::error('Order can\'t be deliver');
        }
        $order->loadAll();
        return new StrictOrderResource($order);
    }


    ///=================== Order Functionalities ==========================//


    public function set_as_paid(Request $request, $id): Response|AnonymousResourceCollection|Application|ResponseFactory
    {
        $order = Order::where('shop_id', $request->shop_id)->findOrFail($id);
        $order->setAsPaid();
        return CResponse::success();
    }


    public function get_order_delivery_boy(Request $request, $id): Response|AnonymousResourceCollection|Application|ResponseFactory
    {
        $order = Order::where('shop_id', $request->shop_id)->findOrFail($id);
        if ($order->delivery_boy_id != null) {
            return CResponse::error('Delivery boy already accept your order');
        }
        $shop = Shop::findOrFail($request->user()->shop_id);

        if ($shop->self_delivery) {
            $delivery_boys = DeliveryBoy::where('active_for_delivery', true)->where('shop_id', $shop->id)->where('id', '!=', $order->assign_delivery_boy_id)->get();
        } else {
            $delivery_boys = DeliveryBoy::where('active_for_delivery', true)->where('id', '!=', $order->assign_delivery_boy_id)->where('shop_id', null)->get();
        }


        return StrictDeliveryBoyResource::collection($delivery_boys);
    }


}

