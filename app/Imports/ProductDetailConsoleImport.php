<?php

namespace App\Imports;

use App\Product;
use App\ProductDetail;
use App\Traits\ExcelImportable;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithBatchInserts;


class ProductDetailConsoleImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts, WithProgressBar
{
    use Importable, ExcelImportable;

    /**
     * @param array $rows
     * @return ProductDetail|null
     * @throws \Exception
     */
    public function model(array $rows)
    {
        return $this->importProductDetailLogic($rows);
    }

    public function chunkSize(): int
    {
        return 80000;
    }

    public function batchSize(): int
    {
        return 5000;
    }

}
