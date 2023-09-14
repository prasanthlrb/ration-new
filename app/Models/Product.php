<?php

namespace App\Models;

use App\Helpers\Util\StringUtil;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;

/**
 * @method static $this create(array $product)
 * @method static $this findOrFail($id)
 * @method static $this where(string $string, string $string1, $shop_id = '')
 * @method static $this find(mixed $product_id)
 * @method static $this whereIn(string $string, mixed $ids)
 * @method $this orderBy(string $string, string $string1)
 * @property mixed $id
 * @property mixed $discount
 * @property mixed $discount_type
 * @property mixed $price
 * @property integer $selling_count
 * @property mixed $product_variant_id
 * @property mixed|null $available_to
 * @property mixed|null $available_from
 * @property Module $module
 */
class Product extends Model
{
    //===================== Defaults  ====================================//
    protected $guarded = [];

    protected $casts = ['need_prescription' => 'boolean', 'active' => 'boolean',];

    //Food Types
    public static string $veg_type = 'veg';
    public static string $non_veg_type = 'non_veg';
    public static string $vegan_type = 'vegan';

    public static function withAll(): Builder
    {
        return static::with(['unit', 'productImages', 'productOptions', 'shop', 'shop.module', 'addons', 'shop.timings', 'subCategory', 'category', 'productVariant', 'productVariant.products', 'productVariant.products.productImages', 'productVariant.products.productOptions', 'productVariant.products.addons', 'productVariant.products.subCategory', 'productVariant.products.category',]);
    }

    public function loadAll(): Product
    {
        return $this->load(['unit', 'productImages', 'productOptions', 'shop', 'shop.module', 'addons', 'shop.timings', 'subCategory', 'category', 'productVariant', 'productVariant.products', 'productVariant.products.productImages', 'productVariant.products.productOptions', 'productVariant.products.addons', 'productVariant.products.subCategory', 'productVariant.products.category',]);
    }

    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return ['name' => ['required'],
            'unit_id' => ['required_with:unit_title', Rule::exists('units', 'id')],
            'unit_title' => [], 'product_variant_id' => [],
            'shop_id' => ['required'],
            'description' => [],
            'sub_category_id' => ['required', Rule::exists('sub_categories', 'id')],
            'active' => ['boolean'], 'need_prescription' => ['boolean'],
            'food_type' => [],
            'available_from' => ['date_format:H:i',],
            'available_to' => ['required_with:available_from', 'date_format:H:i', 'after:available_from'],];
    }

    public static function ruleMessages(): array
    {

        return ['available_to.required_with' => 'Set available to for the product also',];
    }

    //======================= Getters ===========================================//

    public static function extractFromData(array $validated_data): array
    {
        return ['shop_id' => $validated_data['shop_id'], 'description' => $validated_data['description'] ?? null, 'sub_category_id' => $validated_data['sub_category_id'], 'name' => $validated_data['name'], 'unit_id' => Arr::get($validated_data, 'unit_id'), 'unit_title' => Arr::get($validated_data, 'unit_title'), 'food_type' => $validated_data['food_type'] ?? null, 'available_from' => $validated_data['available_from'] ?? null, 'available_to' => $validated_data['available_to'] ?? null,];
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }


    //===================== Functionalities  ====================================//

    public function saveImages(Request $request, $key = 'images'): bool
    {
        if ($request->has($key)) {
            $imageList = $request->get($key);
            if (is_array($imageList)) {
                foreach ($imageList as $image) {
                    ProductImage::saveProductImage($this, $image);
                }
            } else {
                ProductImage::saveProductImage($this, $imageList);
            }
        }

        return false;
    }


    public function updateAddons(array $ids)
    {
        $this->addons()->sync($ids);
    }


    //===================== RelationShips  ====================================//

    public function addons(): BelongsToMany
    {
        return $this->belongsToMany(Addon::class, 'product_addons', 'product_id', 'addon_id');

    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function customerFavoriteProducts(): HasMany
    {
        return $this->hasMany(CustomerFavoriteProduct::class, 'product_id', 'id');
    }


    public function productOptions(): HasMany
    {
        return $this->hasMany(ProductOption::class);
    }

    public function productVariant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public static function getFavoriteRelation($customer_id = null): array
    {
        if ($customer_id == null) return [];
        return ['customerFavoriteProducts' => function ($hasMany) use ($customer_id) {
            $hasMany->where('customer_id', $customer_id);
        }];
    }


    //=====================  Boot Events  ====================================//

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->slug = StringUtil::getSlugFromText($model->name);
        });
    }
}
