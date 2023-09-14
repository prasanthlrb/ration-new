<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @method static $this where(string $string, $comp, $shop_id = '')
 * @method static create(array $addon)
 * @method static find(array $ids)
 * @method static $this findOrFail($id)
 * @property mixed $price
 * @property mixed $image
 * @property mixed $shop_id
 */
class Addon extends Model
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
            'shop_id' => ['required'],
            'name' => ['required'],
            'description' => [],
            'price' => ['required', 'numeric', 'min:0'],
            'active' => ['boolean']
        ];
    }

    //===================== Functionalities  ====================================//

    public function saveImage(Request $request, $key = 'image'): bool
    {
        $base = 'addon_images/';

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

    public function removeImage()
    {
        (!$this->image || Storage::disk('public')->delete($this->image));
        $this->image = null;
    }


    //===================== RelationShips  ====================================//

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }


}
