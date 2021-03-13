<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    /*
     * Relations
     */
    public function productDetails()
    {
        return $this->hasMany('App\ProductDetail');
    }

    public function latestProductDetail()
    {
        return $this->hasOne('App\ProductDetail')->orderByDesc('ref_date');
    }

    public function parent()
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function secondRoot()
    {
        return $this->belongsTo(Product::class, 'second_root_id');
    }
}
