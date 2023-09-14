<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static where(string $string, mixed $user_id)
 * @method static findOrFail($id)
 * @method static create(array $delivery_boy_review)
 */
class DeliveryBoyReview extends Model
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'customer_id' => ['required'],
            'order_id' => ['required'],
            'delivery_boy_id' => ['required'],
            'rating' => ['required', 'in:1,2,3,4,5'],
            'review' => [],
        ];
    }

    //===================== RelationShips  ====================================//

    public function deliveryBoy(): BelongsTo
    {
        return $this->belongsTo(DeliveryBoy::class,);
    }


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class,);
    }

}

