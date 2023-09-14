<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * @property mixed $user_id
 * @property mixed $customer_id
 * @property mixed $id
 * @property mixed $balance
 * @method static $this where(string $string, mixed $userId)
 * @method static $this first()
 * @method static $this findOrFail($id)
 * @method firstOrFail()
 */
class CustomerWallet extends Model
{

    //===================== Functionalities  ====================================//

    public static function addMoney(int $customerId, float $amount, string $payment_method, string $payment_id = null)
    {
        $customer_wallet = CustomerWallet::with(['customer', 'transactions'])->findOrFail($customerId);

        $transaction = new CustomerWalletTransaction();
        $transaction->amount = $amount;
        $transaction->payment_method = $payment_method;
        $transaction->added = true;
        $transaction->payment_id = $payment_id;
        $transaction->customer_wallet_id = $customer_wallet->id;

        $customer_wallet->balance += $amount;
        DB::transaction(function () use ($transaction, $customer_wallet) {
            $customer_wallet->save();
            $transaction->save();
        });
    }

    //===================== RelationShips  ====================================//

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CustomerWalletTransaction::class)->latest();
    }


}
