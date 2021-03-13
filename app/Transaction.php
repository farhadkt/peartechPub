<?php

namespace App;

use App\Constants\TransactionReasons;
use App\Constants\TransactionTypes;
use App\Helpers\Number;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int reason
 * @property int type
 * @property int order_id
 * @property number amount
 * @property  int user_id
 */
class Transaction extends Model
{
    protected $appends = ['type_label', 'reason_label', 'created_at_string'];

    /*
     * Relations
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function order()
    {
        return $this->belongsTo('App\Order')->withTrashed();
    }

    /*
     * Accessors
     */
    public function getCurrencyAmountAttribute($value)
    {
        return number_format($this->amount, 4) . ' CAD';
    }

    /*
     * Scopes
     */
    public function scopeWhereTypeIn($query, $input = [])
    {
        return empty($input) || !is_array($input)
            ? $query
            : $query->whereIn('type', $input);
    }

    public function scopeWhereReasonIn($query, $input = [])
    {
        return empty($input) || !is_array($input)
            ? $query
            : $query->whereIn('reason', $input);
    }

    public function scopeWhereMinAmount($query, $input)
    {
        return is_numeric($input)
            ? $query->where('amount', '>=', $input)
            : $query;
    }

    public function scopeWhereMaxAmount($query, $input)
    {
        return !empty($input) && is_numeric($input)
            ? $query->where('amount', '<=', $input)
            : $query;
    }

    /*
     * Accessors
     */
    public function getTypeLabelAttribute($value)
    {
        return \TransactionTypes::listReverse()[$this->type];
    }

    public function getCreatedAtStringAttribute($value)
    {
        return (new Carbon($this->created_at))->format('Y-m-d H:i');
    }

    public function getReasonLabelAttribute($value)
    {
        return \TransactionReasons::listReverse()[$this->reason];
    }

    /*
     * Methods
     */
    public function increase($amount, $reason, $orderId, $userId)
    {
        $this->user_id = $userId;
        $this->amount = $amount;
        $this->reason = $reason;
        $this->order_id = $orderId;
        $this->type = TransactionTypes::Inc;
        $this->save();

        return $this;
    }

    public function decrease($amount, $reason, $orderId, $userId)
    {
        $this->user_id = $userId;
        $this->amount = $amount;
        $this->reason = $reason;
        $this->order_id = $orderId;
        $this->type = TransactionTypes::Dec;
        $this->save();

        return $this;
    }
}
