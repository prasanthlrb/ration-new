<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * @method static create(array $seller_role)
 * @method static $this where(string $string, mixed $shop_id)
 * @method $this findOrFail($id)
 * @property false|mixed|string $permissions
 */
class SellerRole extends Model
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
            'title' => ['required'],
            'active' => ['boolean'],
            'permissions' => ['required', 'array'],
            'shop_id' => ['required', Rule::exists('shops', 'id')],
        ];
    }

    public static function ruleMessages($id = null): array
    {
        return [
            'permissions.required' => 'Select at least one permission',
        ];
    }

    //===================== Functionalities  ====================================//


    public function isPermission($permission): bool
    {
        try {
            $permissions = json_decode($this->permissions);
            return in_array($permission, $permissions);
        } catch (Exception $e) {
        }
        return false;
    }
}
