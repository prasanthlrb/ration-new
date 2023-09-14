<?php

namespace App\Models;

use App\Helpers\Auth\HasTokenLog;
use App\Rules\MobileNumberRule;
use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @method static create(array $seller)
 * @method static $this findMany(mixed $seller_ids)
 * @method static $this whereIn(string $string, array $string1)
 * @method static $this findOrFail($id)
 * @method static $this where(Closure|string $param, ?string $param2 = '')
 * @method $this first()
 * @property mixed $fcm_token
 * @property mixed|string $password
 * @property mixed $email
 * @property mixed $name
 * @property string $avatar
 * @property mixed $is_owner
 * @property mixed $id
 */
class Seller extends Authenticatable
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
        'active' => 'boolean',

    ];

    public static function withAll(): Builder
    {
        return self::with(['role']);
    }

    //===================== Rules  ====================================//

    public static function googleLoginRules(): array
    {
        return [
            'uid' => ['required'],
        ];
    }

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        return [
            'active' => ['boolean'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'email' => ['required', 'email', 'unique:sellers,email' . $extra_rule],
            'mobile_number' => ['required', 'unique:sellers,mobile_number' . $extra_rule],
            'password' => $id == null ? ['required'] : [],
            'is_owner' => ['boolean'],
            'bank_name' => [],
            'account_number' => [],
            'role_id' => ['required_if:is_owner,false'],
        ];
    }


    public static function loginRules(): array
    {
        return [
            'email' => ['nullable', 'required_without:mobile_number', 'email'],
            'mobile_number' => ['nullable', 'required_without:email', new MobileNumberRule],
            'password' => ['required'],
        ];
    }


    public static function sendOTPRule(): array
    {
        return [
            'email' => ['nullable', 'required_without:mobile_number', 'email'],
            'mobile_number' => ['nullable', 'required_without:email', new MobileNumberRule],
        ];
    }


    public static function verifyOTPRule(): array
    {
        return [
            'email' => ['nullable', 'required_without:mobile_number', 'email'],
            'mobile_number' => ['nullable', 'required_without:email', new MobileNumberRule],
            'otp' => ['required'],
        ];
    }

    public static function loginRuleMessage(): array
    {
        return [
            'email.exists' => 'This email is not exists',
            'email.required_without' => 'Enter a email address',
            'mobile_number.required_without' => 'Enter a mobile number',
        ];
    }


    //======================= Getters ===========================================//


    public static function getFromMobileOrEmail($mobile_number, $email): Seller|null
    {
        return self::when($mobile_number != null, function ($q) use ($mobile_number) {
            return $q->where('mobile_number', $mobile_number);
        })->when($email != null, function ($q) use ($email) {
            return $q->where('email', $email);
        })->first();
    }


    //===================== Functionalities  ====================================//


    public function saveAvatarImage(Request $request, $key = 'avatar'): bool
    {
        $base = 'seller_images/';
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
                }
            } catch (Exception) {
            }
            return false;
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
        return $this->belongsTo(SellerRole::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
