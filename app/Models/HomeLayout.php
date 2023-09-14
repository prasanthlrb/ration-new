<?php

namespace App\Models;


use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @method static create(array $home_layout)
 * @method static array<$this> all()
 * @method static where(string $string, string $other_type)
 * @property int|mixed|string $priority
 * @property mixed $enable
 * @property mixed $item_ids
 * @property mixed $type
 * @property array|mixed $images
 * @property mixed $active
 */
class HomeLayout extends Model
{
    //===================== Defaults  ====================================//

    public static string $featured_shops_type = 'featured_shops';
    public static string $featured_products_type = 'featured_products';
    public static string $popular_products_type = 'popular_products';
    public static string $latest_products_type = 'latest_products';
    public static string $banner_type = 'home_banner';
    public static string $other_type = 'other';

    protected $casts = [
        'active' => 'boolean',
    ];


    //======================= Getters ===========================================//


    public static function isShopType(string $type): bool
    {
        return $type == HomeLayout::$featured_shops_type;
    }

    public static function isBannerType(string $type): bool
    {
        return $type == HomeLayout::$banner_type;
    }

    public static function isOtherType(string $type): bool
    {
        return $type == HomeLayout::$other_type;
    }

    public static function isProductType(string $type): bool
    {
        return $type == HomeLayout::$featured_products_type
            || $type == HomeLayout::$latest_products_type
            || $type == HomeLayout::$popular_products_type;
    }

    public static function isFeaturedProductType(string $type): bool
    {
        return $type == HomeLayout::$featured_products_type;
    }

    public static function isLatestProductType(string $type): bool
    {
        return $type == HomeLayout::$latest_products_type;
    }

    public static function isPopularProductType(string $type): bool
    {
        return $type == HomeLayout::$popular_products_type;
    }


    //===================== Functionalities  ====================================//

    public static function saveLayoutImage($image_data): ?string
    {
        try {
            $url = "home_layout_images/" . Str::random();
            $data = base64_decode($image_data);
            if (Storage::disk('public')->put($url, $data)) {
                return $url;
            }
        } catch (Exception $e) {
        }
        return null;
    }
// In: dev
//    public static function deleteLayoutImage($image_url): bool
//    {
//        try {
//            return Storage::disk('public')->delete($image_url);
//        } catch (Exception $e) {
//        }
//        return false;
//    }

}
