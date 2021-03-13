<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductDetail extends Model
{
    /*
     * Relations
     */
    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
