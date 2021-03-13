<?php

namespace App\Rules;

use App\Constants\CommissionOptions;
use App\Setting;
use Illuminate\Contracts\Validation\Rule;

class BalanceSufficiencyFromOrder implements Rule
{
    public $balance = 0;
    public $amount = 0;

    /**
     * Create a new rule instance.
     *
     * @param $amount
     */
    public function __construct($amount, $lockedAmount = 0)
    {
        $this->balance = auth()->user()->balance + $lockedAmount;
        $this->amount = $amount;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $collateralAmount = ($this->amount * $value) / 100;
        $commission = $this->amount * Setting::key('commission');

        return $this->balance >=($collateralAmount + $commission);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Your balance is insufficient.';
    }
}
