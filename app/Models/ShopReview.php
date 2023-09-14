<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static $this where(string $string, $id)
 * @method static $this findOrFail($id)
 * @method static create(array $shop_review)
 */
class ShopReview extends Model
{
    //===================== Defaults  ====================================//

    protected $guarded = [];


    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'customer_id' => ['required'],
            'shop_id' => ['required'],
            'rating' => ['required', 'in:1,2,3,4,5'],
            'review' => [],
        ];
    }

    //===================== RelationShips  ====================================//

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class,);
    }


    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class,);
    }

}
