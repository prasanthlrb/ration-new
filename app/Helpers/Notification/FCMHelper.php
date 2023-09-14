<?php

namespace App\Helpers\Notification;


use App\Models\BusinessSetting;

class FCMHelper
{
    public static function sendNotification(FCMOption $fcm_option): bool|string
    {
        error_log("1");
        if (!$fcm_option->is_valid()) return false;

        error_log("2");
        $server_key = BusinessSetting::_get(BusinessSetting::$fcm_server_key);
        error_log("3");
        if (!$server_key) return false;
        error_log("4");
        $data = array_merge([], $fcm_option->data??[
            'type'=>'other'
        ]);

        error_log("5");


        $json_data = [
            "registration_ids" => $fcm_option->getRegistrationIds(),
            "priority" => "high",
            "notification" => [
                "title" => $fcm_option->title,
                "body" => $fcm_option->body,
                "click_action" => "FLUTTER_NOTIFICATION_CLICK"
            ],
            "data" => $data
        ];
        $body = json_encode($json_data);

        $url = 'https://fcm.googleapis.com/fcm/send';
        $headers = array(
            'Content-Type:application/json',
            'Authorization:key=' . $server_key
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        $result = curl_exec($ch);
        error_log($result);
        curl_close($ch);
        if ($result === FALSE) {
            return false;
        } else {
            return true;
        }
    }

}

