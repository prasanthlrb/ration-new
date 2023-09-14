<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @method static create(array $banner)*@method static findOrFail($id)
 * @method static $this findOrFail($id)
 * @property string $image
 */
class HomeBanner extends Model
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean',
    ];

    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'shop_id' => ['required_without:product_id'],
            'product_id' => ['required_without:shop_id'],
            'active' => ['boolean'],
        ];
    }

    //===================== Functionalities  ====================================//

    public function saveImage(Request $request, $key = 'image'): bool
    {
        if ($request->get($key))
            try {
                $url = "home_banner_images/" . Str::random(16);
                $data = base64_decode($request->get($key));
                $old_url = $this->image;
                if (Storage::disk('public')->put($url, $data)) {
                    $this->image = $url;
                    if ($old_url != null) {
                        Storage::disk('public')->delete($old_url);
                    }
                    return true;
                }
            } catch (Exception $e) {
                return false;
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



    //===================== RelationShips  ====================================//

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class,);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class,);
    }


}
