<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductImport;
use App\Imports\ProductDetailImport;
use App\Product;
use App\ProductDetail;
use App\TempImportedProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class ProductController extends Controller
{
    public function chartDetails(Request $request)
    {
        if (!$request->ajax()) return 'Hey :)';

        $request->validate(['product_id' => 'bail|required|numeric|exists:products,id']);

        return ProductDetail::select('ref_date as date', DB::raw('CAST(`percent` AS CHAR)+0 as value'))
            ->where('product_id', $request->product_id)
            ->get()->toArray();
    }

    public function importForm()
    {
        Gate::authorize('products-import');
        return view('products.import');
    }

    public function parseImport(ProductImport $request)
    {
        Gate::authorize('products-import');

        // Direct import
        if ($request->has('direct_import')) {
            Excel::import(new ProductDetailImport(), $request->file('file'));
            alert()->success('Products imported successfully');
            return redirect()->route('products.import.form');
        }

        // Turn csv to array
        $rawData = Excel::toArray(new ProductDetailImport(), $request->file('file'));
        // Getting first sheet
        $rawData = $rawData[0];

        // When have no result
        if (count($rawData) <= 0) return redirect()->back();

        // Get csv headers
        $dataHeaderFields = [];
        foreach ($rawData[0] as $key => $value) {
            $dataHeaderFields[] = $key;
        }

        $products = Product::select(
            'name', DB::raw("CONCAT(`name`, ' ', COALESCE(`classification_code`, '')) AS wcc"
        )
        )->get()->toArray();

        $data = [];
        foreach ($rawData as $object) {
            if (
                (in_array($object['north_american_product_classification_system_napcs'], array_column($products, 'name'))
                    || in_array($object['north_american_product_classification_system_napcs'], array_column($products, 'wcc')))
                && $object['geo'] == 'Canada'
                && $object['value'] != ''
            ) {
                $data[] = $object;
            }
        }

        // Import data to temporary table
        $tempImportedData = TempImportedProduct::create([
            'header' => json_encode($dataHeaderFields),
            'data' => json_encode($data)
        ]);

        $tempImportedId = $tempImportedData->id;

        // Required fields to import in primary table
        $requiredDbFields = $this->requiredDbFields();

        return view('products.import_fields',
            compact('data', 'dataHeaderFields', 'requiredDbFields', 'tempImportedId')
        );
    }

    public function import(Request $request)
    {
        Gate::authorize('products-import');

        $tempImported = TempImportedProduct::find($request->temp_imported_id);
        $tempData = json_decode($tempImported->data, true);

        foreach ($tempData as $row) {

            $productDetail = new ProductDetail();

            // Find product with concatenation classification_code
            $product = Product::whereRaw(
                "CONCAT(`name`, ' ', COALESCE(`classification_code`, '')) = ?",
                [$row['north_american_product_classification_system_napcs']]
            )->first();

            // If not find then try without classification_code
            if (empty($product)) {
                $product = Product::whereRaw(
                    "`name` = ?",
                    [$row['north_american_product_classification_system_napcs']]
                )->first();
            }

            $productDetail->product_id = $product->id;
//            $productDetail->product_id = $productNames
//                ->firstWhere('name', $row['north_american_product_classification_system_napcs'])->id;

            foreach ($request->fields as $index => $field) {
                if ($field == 'ref_date') {
                    $productDetail->$field = (new \DateTime($row[$index]))->format('Y-m-d');
                    continue;
                }

                if ($field == 'percent') {
                    $productDetail->$field = $row[$index] ?? 0;
                    continue;
//                    if ($row[$index] == '' || is_null($row[$index]) || empty($row[$index])) {
//                        $productDetail->percent = 0;
//                        $productDetail->active = 0;
//                        continue;
//                    }
                }

                $productDetail->$field = $row[$index];
            }

            $productDetail->save();
        }

        alert()->success('Product details imported successfully');
        return redirect()->route('products.import.form');
    }

    public function findByGroup($group)
    {
        $products = Product::where([['second_root_id', '=', $group], ['active', '=', 1]])
            ->latest()->get();

        $output = "";
        $selected = false;
        foreach ($products as $key => $product) {
            if ($group == 2) {
                $selected = $key == 10 ? true : false;
            } else {
                $selected = $key == 0 ? true : false;
            }
            $output .= "<option value='{$product->id}' ";
            $output .= 'data-content="' . $product->name . '" ';
            $output .= 'title="' . $product->name . '" ';
            $output .= ($selected ? 'selected' : '') . ">";
            $output .= $product->name;
            $output .= "</option>";
        }

        echo $output;
    }

    // For import data
    private function requiredDbFields()
    {
        return [
            'ref_date' => [
                'possibleMatches' => [
                    'REF_DATE', 'ref_date'
                ]
            ],
            'code' => [
                'possibleMatches' => [
                    'DGUID', 'dguid'
                ]
            ],
            'geo' => [
                'possibleMatches' => [
                    'GEO', 'geo'
                ]
            ],
            'uom' => [
                'possibleMatches' => [
                    'UOM', 'uom'
                ]
            ],
            'percent' => [
                'possibleMatches' => [
                    'VALUE', 'value'
                ]
            ],
        ];
    }

}
