<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @method static find($id)
 * @method static findOrFail($id)
 * @method static create(array $sub_category)
 * @property mixed $active
 */
class SubCategory extends Model
{
    // --------------------------- Defaults ---------------------------------------//

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];

    // --------------------------- Rules ---------------------------------------//


    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'name' => ['required'],
            'active' => ['boolean'],
            'category_id' => ['required', function ($attribute, $value, $fail) {
                if (!Category::findOrFail($value)->active)
                    $fail('This category is not active. Try to active parent category then active sub categories');
            },
            ]
        ];
    }

    //===================== Functionalities  ====================================//

    //===================== RelationShips  ====================================//


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }


    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    // ------------------------- Boot Events  ---------------------------------------------------//


    public function update(array $attributes = [], array $options = []): bool
    {
        $active = $attributes['active'] ?? null;
        if (isset($active) && !$active && $this->active) {
            $this->products()->update(['active' => false]);
        }
        return parent::update($attributes, $options);
    }

}
