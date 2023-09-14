<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @method static $this find($id)
 * @method static $this findOrFail($id)
 * @method static create(string[] $category)
 * @property bool|mixed|string $image
 * @property mixed $active
 */
class Category extends Model
{
    use HasFactory;

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
            'description' => [],
            'active' => ['boolean'],
            'module_id' => ['required']
        ];
    }

    // ------------------------- Functions ---------------------------------------------------//


    public function saveImage(Request $request, $key = 'image'): bool
    {
        $base = 'category_images/';
        if ($request->has($key)) {
            $image = $request->get($key);
            $old_url = $this->image;
            try {
                $url = $base . Str::random();
                $data = base64_decode($image);
                if (Storage::disk('public')->put($url, $data)) {
                    $this->image = $url;
                    if ($old_url != null) {
                        Storage::disk('public')->delete($old_url);
                    }
                    return true;
                }
            } catch (Exception $e) {
            }
        }
        return false;
    }

    public function removeImage(): bool
    {
        if ($this->image != null && Storage::disk('public')->delete($this->image)) {
            $this->image = null;
            return true;
        }
        return false;
    }



    // ------------------------- Relationships ---------------------------------------------------//
    public function subCategories(): HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    //TODO ------------------------- Life Cycle ---------------------------------------------------//

//    public function update(array $attributes = [], array $options = []): bool
//    {
//        $active = $attributes['active'];
//        if (isset($active) && !$active && $this->active) {
//            $this->subCategories()->update(['active' => false]);
//            $this->products()->update(['active' => false]);
//        }
//        return parent::update($attributes, $options);
//    }


}
