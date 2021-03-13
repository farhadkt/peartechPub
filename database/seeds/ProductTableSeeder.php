<?php

use App\Imports\ProductImport;
use App\Product;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = base_path() . '/database/initData/18100030_MetaData.csv';
        $importedProducts = Excel::toArray(new ProductImport(), $path);
        $importedProducts = $importedProducts[0];
        $tempProducts = [];

        foreach ($importedProducts as $importedProduct)
        {
            if ($importedProduct['member_name'] == '' || $importedProduct['member_name'] == 'Canada') continue;

            $product = new Product();
            $product->name = $importedProduct['member_name'];
            $product->classification_code = $importedProduct['classification_code'];
            $product->type = 1;
            $product->save();

            // Temporally save member_id and parent_member_id
            $tempProducts[] = [
                'id' => $product->id,
                'member_id' => $importedProduct['member_id'],
                'parent_id' => $importedProduct['parent_member_id'],
            ];
        }

        foreach ($tempProducts as $key => $currentProduct) {
            if (!empty($currentProduct['parent_id'])) {
                foreach ($tempProducts as $k => $search) {
                    if ($search['member_id'] == $currentProduct['parent_id']) {
                        Product::where('id', $currentProduct['id'])->update(['parent_id' => $search['id']]);
                        continue 2;
                    }
                }
            }
        }

        $products = Product::orderBy('id', 'desc')->get();

        foreach ($products as $p) {
            $secondRootId = $this->getProductSecondRoot($p->id);
            Product::where('id', $p->id)->update(['second_root_id' => $secondRootId]);
        }

    }

    public function getProductSecondRoot($productId)
    {
        // Find current product id
        $product = Product::find($productId);

        if ( $product->exists() ) {

            // Find parent product
            $parent = Product::find($product->parent_id);

            if ($parent) {

                // Find parent parent product
                $parentParent = Product::find($parent->parent_id);

                if ($parentParent) {
                    // If there is parent parent we have to dig more
                    return $this->getProductSecondRoot($parent->id);
                } else {
                    // If there is not parent parent we are at the end of tree,
                    // so $parent is root parent and $product is second root parent and we return $product id
                    return $product->id;
                }

            } else {
                // if there is no parent at all current product id consider as parent
                return $product->id;
            }
        }

        return null;
    }

    // this method used in first version. No use anymore.
    public function products()
    {
        return [
            ['name' => 'Cheese', 'type' => \ProductTypes::Product],
            ['name' => 'Coffee and tea', 'type' => \ProductTypes::Product],
            ['name' => 'Eggs', 'type' => \ProductTypes::Product],
            ['name' => 'Butter', 'type' => \ProductTypes::Product],
            ['name' => 'Clothing and footwear', 'type' => \ProductTypes::Product],
        ];
    }
}
