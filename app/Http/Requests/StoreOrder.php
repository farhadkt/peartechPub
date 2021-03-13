<?php

namespace App\Http\Requests;

use App\Constants\CommissionOptions;
use App\Constants\OrderTypes;
use App\Rules\BalanceSufficiencyFromOrder;
use App\Setting;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * @property mixed collateral
 * @property mixed amount
 * @property mixed product_id
 * @property mixed delivery_date
 * @property mixed type
 */
class StoreOrder extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $currentMonth = date('M Y');
        $currentDate = date('Y-m-d');
        $firstDayThisMonth = date('Y-m-01');
        $lastDayThisMonth  = date('Y-m-t');

        // Consider the locked amount for the order, within the limits of the user account balance in edit order
        $lockedAmount = 0;
        if ($request->method == 'PATCH') {
            $collateralAmount = ($request->oldAmount * $request->oldCollateral) / 100;
            $commission = $request->oldAmount * Setting::key('commission');
            $lockedAmount = $collateralAmount + $commission;
        }

        return [
            'product_id' => ['required', 'numeric', 'exists:products,id'],
            'amount' => ['required', 'numeric', 'min:1'],
            'delivery_date' => ['required', 'date_format:Y-m', 'after:' . $currentMonth],
            'validity_date' => [
                'required', 'date_format:Y-m-d',
                'after_or_equal:' . $firstDayThisMonth,
                'before_or_equal:' . $lastDayThisMonth
            ],
            'collateral' => ['required', 'numeric', 'between:0.01,100', new BalanceSufficiencyFromOrder($request->amount, $lockedAmount)],
            'type' => ['required', Rule::in(OrderTypes::values())]
        ];
    }

    public function messages()
    {
        return [
            'delivery_date.after_or_equal' => 'The delivery date must be after or equal current month.'
        ];
    }
}
