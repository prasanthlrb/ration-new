<?php

namespace App\Models;

use App\Helpers\Auth\HasTokenLog;
use App\Rules\MobileNumberRule;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @method static create(array $admin)
 * @method static $this where(string $string, string $string1, ?mixed $email = '')
 * @method static $this first()
 * @method static $this findOrFail($id)
 * @method static count()
 * @property mixed $fcm_token
 * @property mixed|string $password
 * @property mixed $email
 * @property mixed $name
 * @property mixed $id
 * @property mixed $avatar
 */
class Admin extends Authenticatable
{
    use HasTokenLog;

    //===================== Defaults  ====================================//

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_owner' => 'boolean',

    ];

    public static function withAll(): Builder
    {
        return static::with(['role']);
    }

    //===================== Rules  ====================================//

    public static function loginRules(): array
    {
        return [
            'email' => ['nullable', 'required_without:mobile_number', 'email'],
            'mobile_number' => ['nullable', 'required_without:email', new MobileNumberRule],
            'password' => ['required'],
        ];
    }


    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'active' => ['boolean'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:admins,email' . $extra_rule],
            'mobile_number' => ['required', 'unique:admins,mobile_number' . $extra_rule, new MobileNumberRule],
            'password' => $id == null ? ['required'] : [],
            'is_super' => ['boolean'],
            'role_id' => ['required_if:is_super,false'],

        ];
    }


    public static function googleLoginRules(): array
    {
        return [
            'uid' => ['required'],
        ];
    }



    //======================= Getters ===========================================//

    public static  function getFromMobileOrEmail($mobile_number, $email): Admin|null
    {
        return self::when($mobile_number != null, function ($q) use ($mobile_number) {
            return $q->where('mobile_number', $mobile_number);
        })->when($email != null, function ($q) use ($email) {
            return $q->where('email', $email);
        })->first();
    }


    //===================== Functionalities  ====================================//


    //Image
    public function saveAvatarImage(Request $request, $key = 'avatar'): bool
    {
        $base = 'admin_images/';
        if ($request->has($key)) {
            $image = $request->get($key);
            $old_url = $this->avatar;
            try {
                $url = $base . Str::random();
                $data = base64_decode($image);
                if (Storage::disk('public')->put($url, $data)) {
                    $this->avatar = $url;
                    if ($old_url != null) {
                        Storage::disk('public')->delete($old_url);
                    }
                    return true;
                }
            } catch (Exception) {
            }
        }
        return false;
    }

    public function removeAvatar(): bool
    {
        if ($this->avatar != null && Storage::disk('public')->delete($this->avatar)) {
            $this->avatar = null;
            return true;
        }
        return false;
    }


    //===================== RelationShips  ====================================//

    public function role(): BelongsTo
    {
        return $this->belongsTo(AdminRole::class,);
    }

}
