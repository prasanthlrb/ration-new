<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, mixed $day)
 * @method static create(array $shop_time)
 */
class ShopTime extends Model
{
    //===================== Defaults  ====================================//

    protected $guarded = [];

    public static array $days = array('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday');


    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'shop_id' => ['required'],
            'day' => ['required', 'in:monday,tuesday,wednesday,thursday,friday,saturday,sunday'],
            'open_at' => ['required', 'date_format:H:i'],
            'close_at' => ['required', 'date_format:H:i', 'after:open_at'],
        ];
    }

}
