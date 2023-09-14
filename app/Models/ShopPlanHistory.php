<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static create(int[] $shop_plan_history)
 * @method static where(string $string, mixed $id)
 */
class ShopPlanHistory extends Model
{
    //===================== Defaults  ====================================//

    protected $guarded = [];


    //===================== RelationShips  ====================================//

    public function shopPlan(): BelongsTo
    {
        return $this->belongsTo(ShopPlan::class);
    }


    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }


}
