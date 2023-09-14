<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(string[] $unit)
 * @method static $this findOrFail($id)
 */
class Unit extends Model
{
    //===================== Defaults  ====================================//
    protected $guarded = [];

    protected $casts = [
        'active' => 'boolean'
    ];

    //===================== Rules  ====================================//

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'type' => ['required'],
            'title' => ['required', 'unique:units,title'.$extra_rule],
            'description' => [],
            'active' => ['boolean']
        ];
    }
}
