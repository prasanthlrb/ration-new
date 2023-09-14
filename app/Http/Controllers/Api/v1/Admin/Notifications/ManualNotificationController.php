<?php

namespace App\Http\Controllers\Api\v1\Admin\Notifications;

use App\Helpers\CResponse;
use App\Helpers\CValidator;
use App\Http\Controllers\Controller;
use App\Http\Resources\AddonResource;
use App\Http\Resources\Strict\StrictManualNotificationResource;
use App\Models\Addon;
use App\Models\ManualNotification;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;


class ManualNotificationController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        return StrictManualNotificationResource::collection(ManualNotification::query()->latest()->get());
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request)
    {

        $validated_data = CValidator::validate($request->all(), [
            'title' => ['required'],
            'body' => ['required'],
            'all_customers' => ['boolean'],
            'all_sellers' => ['boolean'],
            'all_delivery_boys' => ['boolean'],
        ]);

        $all_customers = $request->get('all_customers');
        $all_sellers = $request->get('all_sellers');
        $all_delivery_boys = $request->get('all_delivery_boys');
        $valid = $all_customers || $all_sellers || $all_delivery_boys;

        if (!$valid) {
            return CResponse::error("Please check any users then send");
        }

        $notification = new ManualNotification($validated_data);
        $notification['all_customers'] = $all_customers??false;
        $notification['all_sellers'] = $all_sellers??false;
        $notification['all_delivery_boys'] = $all_delivery_boys??false;
        $notification->save();

        $notification->send_notification();

        return CResponse::success('Notification Sent');
    }

}

