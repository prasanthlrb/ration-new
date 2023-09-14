<?php

namespace App\Models;

use App\Jobs\ProcessManualNotification;
use Illuminate\Database\Eloquent\Model;


/**
 * @property mixed $data
 * @property mixed $id
 * @property mixed $type
 * @property mixed $body
 * @property mixed $title
 * @method static where(string $string, string $string1, mixed $user_id)
 * @method static $this find(int $manual_notification_id)
 */
class ManualNotification extends Model
{
    //===================== Defaults  ====================================//

    protected $guarded = [];


    protected $casts = [
        'data' => 'array',
        'schedule_at' => 'datetime',
        'all_customers' => 'boolean',
        'all_sellers' => 'boolean',
        'all_delivery_boys' => 'boolean',

    ];


    public static string $promotional = 'promotional';
    public static string $other_type = 'other';


    //===================== Functionalities  ====================================//

    public function send_notification()
    {
        ProcessManualNotification::dispatch($this->id);
    }


    public function getAllTokens(): array
    {
        $ar = [];
        if ($this->all_customers) {
            array_push($ar, ...TokenLog::getAllToken(Customer::class));
        }
        if ($this->all_sellers) {
            array_push($ar, ...TokenLog::getAllToken(Seller::class));
        }
        if ($this->all_delivery_boys) {
            array_push($ar, ...TokenLog::getAllToken(DeliveryBoy::class));
        }
        return $ar;

    }

}


