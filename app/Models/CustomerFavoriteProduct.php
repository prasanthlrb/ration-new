<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(int[] $customer_favorite_product)
 * @method static $this where(string $string, mixed $product_id)
 * @method  $this first()
 */
class CustomerFavoriteProduct extends Model
{
    //===================== Defaults  ====================================//

    protected $guarded = [];
}
