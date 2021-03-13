<?php

namespace App;

use App\Constants\OrderStatuses;
use App\Constants\TransactionReasons;
use App\Constants\TransactionTypes;
use App\Traits\Authorizable;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int id
 * @property mixed name
 * @property mixed email
 * @property string password
 * @property mixed mobile
 * @property mixed balance
 */
class User extends Authenticatable
{
    use Notifiable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'mobile'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /*
     * Relations
     */
    public function orders()
    {
        return $this->hasMany('App\Order');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }


    /**
     * Methods
     */
    public function isActive()
    {
        return $this->active == 1 ? true : false;
    }

    public function isAdmin()
    {
        return $this->hasRole('admin');
    }


    public function increase($amount)
    {
        $this->balance += $amount;
        $this->save();

        return $this;
    }

    public function decrease($amount)
    {
        $this->balance -= $amount;
        $this->save();

        return $this;
    }

    /**
     * Scopes
     */
    public function scopeWhereEmail($query, $input)
    {
        return empty($input)
            ? $query
            : $query->where('email', 'LIKE', $input);
    }

    public function scopeWhereRoleIdIn($query, $input = [])
    {
        return empty($input) || !is_array($input)
            ? $query
            : $query->whereHas('roles', function($q) use ($input) {
                $q->whereIn('id', $input);
            });
    }

    public function scopeWhereActive($query, $input)
    {
        return $query->where('active', $input);
    }

    public function totalCollateral() {
        /*
         * In Transaction model we set withTrashed() for order relation
         * so in all cases all orders returned.
         * In this case we do'nt want deleted orders
         * So the conditions must be like this:
         *      1 - The order must be matched or unmatched.
         *      2 - The order should not be deleted.
         * */
        $userTransactions = $this->transactions()
            ->where('reason', TransactionReasons::Collateral)
            ->whereHas('order', function ($query) {
                $query->where('deleted_at', null)
                    ->where(function ($q) {
                        $q->where('status', OrderStatuses::Unmatched)
                            ->orWhere('status', OrderStatuses::Matched);
                    });
        })->get();

        $totalCollateral = 0;
        foreach ($userTransactions as $transaction) {
            $totalCollateral += $transaction->amount;
        }

        return $totalCollateral;
    }

    public function totalCommission() {
        /*
         * In Transaction model we set withTrashed() for order relation
         * so in all cases all orders returned.
         * In this case we do'nt want deleted orders
         * So the conditions must be like this:
         *      1 - The order should not be deleted.
         * */
        $userTransactions = $this->transactions()
            ->where('reason', TransactionReasons::Commission)
            ->whereHas('order', function ($query) {
                $query->where('deleted_at', null)
                    ->where('status', OrderStatuses::Unmatched);
            })->get();

        $totalCommission = 0;
        foreach ($userTransactions as $transaction) {
            $totalCommission += $transaction->amount;
        }

        return $totalCommission;
    }
}
