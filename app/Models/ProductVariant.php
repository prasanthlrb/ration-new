<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * @method static create(int[] $products_variant)
 * @method static where(string $string, string $string1, $shop_id)
 * @property mixed $id
 */
class ProductVariant extends Model
{
    //===================== Defaults  ====================================//

    protected $guarded = [];


    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'name' => ['required'],
            'unit_id' => [Rule::exists('units', 'id')],
            'unit_title' => [],
        ];
    }


    //======================= Relationships ===========================================//


    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }


}
