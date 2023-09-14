<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static Builder where(string $string, mixed $lat_1)
 * @method static Builder whereIn(string $string, mixed $lat_1)
 * @property mixed $data
 */
class NavigationCache extends Model
{
    //===================== Defaults  ====================================//

    protected $guarded = [];
}
