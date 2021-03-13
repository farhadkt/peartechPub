<?php

namespace App\Imports;

use App\Product;
use App\ProductDetail;
use App\Traits\ExcelImportable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;


class ProductImport implements ToArray, WithHeadingRow
{

    /**
     * @param array $rows
     * @return Product|ProductDetail|null
     * @throws \Exception
     */
    public function array(array $rows)
    {
        if ($rows['product_id'] == '' || $rows['cansim_id'] == '' || $rows['url'] == '' || $rows['cube_notes'] == '') return null;

        return new Product([
            'name' => $rows['product_id'],
            'type' => 1,
            'member_id' => $rows['url'],
            'parent_id' => $rows['cube_notes'],
        ]);
    }

}
