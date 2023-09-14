<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int|mixed $revenue
 * @property mixed $shop_id
 * @property mixed $order_id
 * @property mixed $delivery_boy_id
 * @property float|int|mixed $pay_to_admin
 * @property int|mixed $delivery_charge
 * @property int|mixed $tax
 * @property float|int|mixed $order_amount
 * @property mixed $packaging_charge
 * @property float|int|mixed $take_from_admin
 * @property DeliveryBoyRevenue|mixed $pay_to_delivery_boy
 * @property float|int|mixed $take_from_delivery_boy
 * @property mixed $collected_payment_from_customer
 * @method static $this where(string $string, string $string)
 * @method static create(array $shop_revenue)
 * @method sum(string $string)
 * @method whereDate(string $string, mixed $dt)
 * @method whereMonth(string $string, $format)
 * @method get()
 * @method whereBetween(string $string, array $array)
 */
class ShopRevenue extends Model
{
    //===================== Defaults  ====================================//


    //===================== RelationShips  ====================================//


    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function shopPayout(): BelongsTo
    {
        return $this->belongsTo(ShopPayout::class);
    }

    public function deliveryBoy(): BelongsTo
    {
        return $this->belongsTo(DeliveryBoy::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
