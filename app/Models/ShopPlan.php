<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $shop_plan)
 * @method static $this findOrFail($shop_plan_id)
 * @property mixed $price
 * @property mixed $admin_commission
 * @property mixed $admin_commission_type
 */
class ShopPlan extends Model
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
            'active' => ['boolean'],
            'title' => ['required', 'unique:shop_plans,title' . $extra_rule],
            'sub_title' => [],
            'price' => ['required', 'numeric', 'min:0'],
            'products' => ['required', 'numeric', 'min:0'],
            'admin_commission' => ['numeric', 'min:0', function ($attribute, $value, $fail) {
                if (request()->get('admin_commission_type') == 'percent' && $value > 100)
                    $fail('Percentage discount is not more than 100');
            },],
            'admin_commission_type' => ['required_with:admin_commission', 'in:percent,amount',],
        ];
    }


}
