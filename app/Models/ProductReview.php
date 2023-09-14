<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @method static Builder where(string $string, string $string1, mixed $user_id = '=')
 * @method static $this create(array $product_review)
 * @method static $this findOrFail($id)
 */
class ProductReview extends Model
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
            'product_option_id' => ['required'],
            'rating' => ['required', 'in:1,2,3,4,5'],
            'review' => [],
        ];
    }


    //===================== RelationShips  ====================================//

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class,);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,);
    }


}
