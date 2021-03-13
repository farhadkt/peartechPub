<?php

namespace App;

use App\Helpers\Number;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed product_id
 * @property mixed amount
 * @property string delivery_date
 * @property mixed collateral
 * @property mixed type
 * @property decimal commission
 * @property int status
 * @property decimal product_detail_percent
 * @property string validity_date
 */
class Order extends Model
{
    use SoftDeletes;
    protected $appends = array('casted_collateral', 'profit_loss', 'profit_loss_p_w_s', 'profit_loss_b_o_d_d', 'profit_loss_p_b_o_d_d_w_s');

    /*
     * Relations
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function product()
    {
        return $this->belongsTo('App\Product');
    }

    public function transactions()
    {
        return $this->hasMany('App\Transaction');
    }

    /*
     * Accessors
     */
    public function getCurrencyAmountAttribute($value)
    {
        return number_format($this->amount, 2) . ' CAD';
    }

    public function getCastedCollateralAttribute($value)
    {
        return Number::trimTrailingZeroes($this->collateral) .'%';
    }

    public function getProfitLossPAttribute($value)
    {
        return round($this->profitLossPercent(), 2);
    }

    public function getProfitLossPWSAttribute($value)
    {
        return round($this->profitLossPercentWithSings(), 2);
    }

    public function getProfitLossAttribute($value)
    {
        return round($this->profitLoss(), 2);
    }

    public function getProfitLossBODDAttribute($value)
    {
        return round($this->profitLossPercentBasedOnDeliveryDate(), 2);
    }

    /*
    * Profit/Loss Percent Based On Delivery Date With Sings
    */
    public function getProfitLossPBODDWSAttribute($value)
    {
        return round($this->profitLossPercentBasedOnDeliveryDateWithSings(), 2);
    }

    /*
     * Methods
     */

    // Calculates with latest product percent
    public function profitLossPercent()
    {
        if (!$this->product()->exists()) {
            return 'N/A';
        }

        $latestDetail = $this->product->latestProductDetail;

        if (!$latestDetail) return 'N/A';

        return $latestDetail->percent - $this->product_detail_percent;
    }

    // Calculate with product percent that matched with delivery date. Uses for delivery algorithm
    public function profitLossPercentBasedOnDeliveryDate()
    {
        if (!$this->product()->exists()) {
            return 'N/A';
        }

        $product = $this->product()->get()->first();

        if (!$product || empty($product)) return 'N/A';

        $productDetail = $product->productDetails()->where('ref_date', $this->delivery_date)->get()->first();

        if (!$productDetail || empty($productDetail)) return 'N/A';

        return $productDetail->percent - $this->product_detail_percent;
    }

    public function profitLossPercentBasedOnDeliveryDateWithSings()
    {
        $value = $this->profitLossPercentBasedOnDeliveryDate();

        if (is_numeric($value) && $value > 0) return '+' . $value;

        return $value;
    }

    // Attention: it returns string for positive values. See profitLossPercent method if you want integer
    public function profitLossPercentWithSings()
    {
        $value = $this->profitLossPercent();

        if (is_numeric($value) && $value > 0) return '+' . $value;

        return $value;
    }

    public function profitLoss()
    {
        $value = $this->profitLossPercent();

        return is_numeric($value)
            ? ($value / 100) * ($this->amount)
            : $value;
    }

    public function profitLossBasedOnDeliveryDate()
    {
        $value = $this->profitLossPercentBasedOnDeliveryDate();

        return is_numeric($value)
            ? ($value / 100) * ($this->amount)
            : $value;
    }
}
