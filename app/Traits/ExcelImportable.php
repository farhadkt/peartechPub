<?php


namespace App\Traits;


use App\Product;
use App\ProductDetail;
use Illuminate\Support\Facades\DB;

trait ExcelImportable
{
    public function importProductDetailLogic (array $rows)
    {
        if ($rows['geo'] != 'Canada' || !$rows['value']) return null;

        // Find product with concatenation classification_code
        $product = Product::whereRaw(
            "CONCAT(`name`, ' ', COALESCE(`classification_code`, '')) = ?",
            [$rows['north_american_product_classification_system_napcs']]
        )->first();

        // If not find then try without classification_code
        if (empty($product)) {
            $product = Product::whereRaw(
                "`name` = ?",
                [$rows['north_american_product_classification_system_napcs']]
            )->first();
        }

        if (!empty($product)) {
            ProductDetail::unguard();
            $pArr = [
                'product_id' => $product->id,
                'ref_date' => (new \DateTime($rows['ref_date']))->format('Y-m-d'),
                'code' => $rows['dguid'],
                'geo' => $rows['geo'],
                'uom' => $rows['uom'],
                'percent' => $rows['value'] ?? 0,
            ];

//            if ($rows['value'] == '' || is_null($rows['value']) || empty($rows['value'])) {
//                $pArr['percent'] = 0;
//                $pArr['active'] = 0;
//            }

            $res = new ProductDetail($pArr);
            ProductDetail::reguard();

            return $res;
        }

        return null;
    }
}
