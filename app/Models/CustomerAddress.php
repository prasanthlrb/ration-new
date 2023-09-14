<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use MatanYadaev\EloquentSpatial\Objects\Point;

/**
 * @method static $this where(string $string, string $string1, mixed $userId='')
 * @method static create(array $userAddress)
 * @method static $this findOrFail(mixed $user_address_id)
 * @property mixed $latitude
 * @property mixed $longitude
 * @property bool|mixed $selected
 */
class CustomerAddress extends Model
{

    //===================== Defaults  ====================================//

    protected $guarded = [];

    protected $casts = ['selected' => 'boolean',];

    //===================== Rules  ====================================//


    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";
        return ['customer_id' => ['required', Rule::exists('customers', 'id')], 'type' => ['required'], 'address' => ['required'], 'landmark' => [], 'city' => ['required'], 'pincode' => ['required'], 'latitude' => ['required', 'unique:customer_addresses,latitude' . $extra_rule], 'longitude' => ['required', 'unique:customer_addresses,longitude' . $extra_rule],];
    }

    public static function ruleMessages($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return ['latitude.required' => 'Please select location on map', 'latitude.unique' => 'This location is already saved', 'longitude.required' => 'Please select location on map', 'longitude.unique' => 'This location is already saved',];
    }


    //===================== Functionalities  ====================================//
    public function isInZone($zone_id): bool
    {

        return  Zone::query()->whereContains('coordinates',  new Point($this->latitude, $this->longitude))->where('id', $zone_id)->exists();
    }


}
