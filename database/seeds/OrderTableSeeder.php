<?php

use App\Order;
use Illuminate\Database\Seeder;

class OrderTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Order::unguard();
        foreach ($this->orders() as $orderDetail) {
            Order::create($orderDetail);
        }
        Order::reguard();
    }

    public function orders()
    {
        return [
            [
                'product_id' => 4,
                'user_id' => 10,
                'amount' => 1000,
                'delivery_date' => '2020-08-01',
                'validity_date' => '2020-10-01',
                'collateral' => 10.3,
                'commission' => 5.6,
                'type' => \App\Constants\OrderTypes::Buy,
                'status' => \App\Constants\OrderStatuses::Unmatched,
            ],
            [
                'product_id' => 4,
                'user_id' => 11,
                'amount' => 1000,
                'delivery_date' => '2020-08-01',
                'validity_date' => '2020-10-01',
                'collateral' => 10.3,
                'commission' => 5.6,
                'type' => \App\Constants\OrderTypes::Sell,
                'status' => \App\Constants\OrderStatuses::Unmatched,
            ],
            [
                'product_id' => 4,
                'user_id' => 12,
                'amount' => 1000,
                'delivery_date' => '2020-08-01',
                'validity_date' => '2020-10-01',
                'collateral' => 10.3,
                'commission' => 5.6,
                'type' => \App\Constants\OrderTypes::Sell,
                'status' => \App\Constants\OrderStatuses::Unmatched,
            ],
            [
                'product_id' => 4,
                'user_id' => 10,
                'amount' => 1000,
                'delivery_date' => '2020-08-01',
                'validity_date' => '2020-10-01',
                'collateral' => 10.3,
                'commission' => 5.6,
                'type' => \App\Constants\OrderTypes::Buy,
                'status' => \App\Constants\OrderStatuses::Unmatched,
            ],
            //======================================================================================================
            [
                'product_id' => 5,
                'user_id' => 20,
                'amount' => 1000,
                'delivery_date' => '2020-08-01',
                'validity_date' => '2020-10-01',
                'collateral' => 10.3,
                'commission' => 5.6,
                'type' => \App\Constants\OrderTypes::Buy,
                'status' => \App\Constants\OrderStatuses::Unmatched,
            ],
            [
                'product_id' => 5,
                'user_id' => 21,
                'amount' => 1000,
                'delivery_date' => '2020-08-01',
                'validity_date' => '2020-10-01',
                'collateral' => 10.3,
                'commission' => 5.6,
                'type' => \App\Constants\OrderTypes::Sell,
                'status' => \App\Constants\OrderStatuses::Unmatched,
            ],
            //======================================================================================================
            [
                'product_id' => 1,
                'user_id' => 21,
                'amount' => 1000,
                'delivery_date' => '2020-08-01',
                'validity_date' => '2020-10-01',
                'collateral' => 10.3,
                'commission' => 5.6,
                'type' => \App\Constants\OrderTypes::Sell,
                'status' => \App\Constants\OrderStatuses::Unmatched,
            ],
        ];
    }
}
