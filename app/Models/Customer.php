<?php

namespace App\Models;

use App\Helpers\Auth\HasTokenLog;
use App\Helpers\Util\StringUtil;
use App\Rules\MobileNumberRule;
use Exception;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * @property mixed fcm_token
 * @property mixed|string password
 * @property mixed email
 * @property mixed name
 * @property string $avatar
 * @property Carbon|mixed|null $mobile_number_verified_at
 * @property Carbon|mixed|null $email_verified_at
 * @property mixed $id
 * @method static $this where(string $string, mixed $compare, ?$id = '')
 * @method static $this when(string $string, mixed $email)
 * @method static create(array $user)
 * @method static $this findOrFail($id)
 * @method static int count()
 * @method bool exists()
 * @method first()
 */
class Customer extends Authenticatable
{
    use HasTokenLog;

    //===================== Defaults  ====================================//

    protected $guarded = ['password'];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'bool'
    ];

    //===================== Rules  ====================================//

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


    public static function googleLoginRules(): array
    {
        return [
            'uid' => ['required'],
        ];
    }


    public static function registerRules($id = null): array
    {

        return [
            'first_name' => ['required'],
            'last_name' => ['required'],
            'mobile_number' => ['required', 'numeric', 'unique:customers,mobile_number', new MobileNumberRule],
            'email' => ['nullable', 'email', 'unique:customers,email'],
            'password' => ['required'],
            'from_referral' => ['nullable', 'exists:customers,referral'],
            'fcm_token' => []
        ];
    }

    public static function registerRuleMessage(): array
    {
        return [
            'from_referral.exists' => 'This referral is not valid',
        ];
    }

    public static function rules($id = null): array
    {
        $extra_rule = $id != null ? "," . $id : "";

        $rules = [
            'active' => ['boolean'],
            'first_name' => ['required'],
            'last_name' => ['required'],
            'mobile_number' => ['unique:customers,mobile_number' . $extra_rule],
            'email' => ['unique:customers,email' . $extra_rule],
        ];

        if ($id == null) {
            $rules['password'] = ['required'];
        }
        return $rules;
    }


    //======================= Getters ===========================================//


    public static function getFromMobileOrEmail($mobile_number, $email): Customer|null
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
        $base = 'customer_images/';
        if ($request->get($key)) {
            $image = $request->get($key);
            $old_url = $this->avatar;

            try {
                $url = $base . Str::random(16);
                $data = base64_decode($image);
                if (Storage::disk('public')->put($url, $data)) {
                    $this->avatar = $url;
                    if ($old_url) {
                        Storage::disk('public')->delete($old_url);
                    }
                }
            } catch (Exception $e) {
            }
            return false;
        }

        return false;
    }

    public function deleteAvatar(): bool
    {
        if ($this->avatar != null && Storage::disk('public')->delete($this->avatar)) {
            $this->avatar = null;
            return true;
        }
        return false;
    }

    //===================== RelationShips  ====================================//

    public function wallet(): HasOne
    {
        return $this->hasOne(CustomerWallet::class);
    }

    //===================== Boot Events  ====================================//

    protected static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->referral = StringUtil::generateReferral();
        });

        self::created(function ($model) {
            $customer_wallet = new CustomerWallet();
            $customer_wallet->customer_id = $model->id;
            $customer_loyalty_wallet = new CustomerLoyaltyWallet();
            $customer_loyalty_wallet->customer_id = $model->id;
            $customer_wallet->save();
            $customer_loyalty_wallet->save();
        });
    }

}
