<?php

namespace App\Jobs;

use App\Helpers\Notification\FCMHelper;
use App\Helpers\Notification\FCMOption;
use App\Models\ManualNotification;
use App\Models\Notification;
use App\Models\TokenLog;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessManualNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public int $manual_notification_id;

    public function __construct($manual_notification_id)
    {
        $this->manual_notification_id = $manual_notification_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $notification = ManualNotification::find($this->manual_notification_id);
        if($notification){
            try {

                $fcm = new FCMOption();
                $fcm->title = $notification->title;
                $fcm->body = $notification->body;
                $fcm->tokens = $notification->getAllTokens();
                $fcm->data = $notification->data;
                FCMHelper::sendNotification($fcm);
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
        }else{
        }

    }
}
