<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property mixed|string $image

 * @property mixed $product_id
 * @method static $this findOrFail($id)
 * @method static $this create(array $product)
 */
class ProductImage extends Model
{

    //===================== Functionalities  ====================================//

    public static function saveProductImage(Product $product, $image): bool
    {
        try {
            $url = "product_images/" . Str::random();
            $data = base64_decode($image);
            if (Storage::disk('public')->put($url, $data)) {
                $productImage = new ProductImage();
                $productImage->product_id = $product->id;
                $productImage->image = $url;
                return $productImage->save();
            }
        } catch (Exception $e) {
        }
        return false;
    }

    public function removeImage(): bool
    {
        return !$this->image || Storage::disk('public')->delete($this->image);
    }

    //===================== RelationShips  ====================================//

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
