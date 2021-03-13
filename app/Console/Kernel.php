<?php

namespace App\Console;

use App\Constants\CommissionOptions;
use App\Constants\OrderStatuses;
use App\Constants\OrderTypes;
use App\Constants\TransactionReasons;
use App\Constants\TransactionTypes;
use App\Order;
use App\Transaction;
use App\User;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            // Run this task schedule every 5 seconds
            for ($i=0; $i<=11; $i++) {
                $this->matchOrders(); // We assume that the execution of this command takes two seconds
                sleep(3);
            }
        })
            ->everyMinute()
            ->name('match_orders')
            ->withoutOverlapping();

        $schedule->call(function () {
            $this->orderDelivery();
        })
            ->everyFiveMinutes()
            ->name('deliver_orders')
            ->withoutOverlapping();

        $schedule->call(function () {
            $this->removeUnmatchOrders();
        })
            ->dailyAt('01:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }

    public function matchOrders()
    {
        try {
            $buyOrders = Order::where('type', OrderTypes::Buy)
                ->whereNull('matched_order_id')
                ->where('status', OrderStatuses::Unmatched)
                ->orderBy('id')
                ->get();

            foreach ($buyOrders as $buyOrder) {
                Log::alert($buyOrder);
                $sellOrder = Order::with('product.latestProductDetail')
                    ->where('type', OrderTypes::Sell)
                    ->whereNull('matched_order_id')
                    ->where('status', OrderStatuses::Unmatched)
                    ->where('product_id', $buyOrder->product_id)
                    ->where('amount', $buyOrder->amount)
                    ->where('collateral', $buyOrder->collateral)
                    ->where('delivery_date', $buyOrder->delivery_date)
                    ->where('user_id', '!=', $buyOrder->user_id)
                    ->orderBy('id')
                    ->first();

                if ($sellOrder) {
                    $randomHash = md5(microtime(true) . uniqid(1, true) . rand(1000000, 9999999));
                    $latestProductDetail = $sellOrder->product->latestProductDetail;

                    if (!$latestProductDetail || is_null($latestProductDetail) || empty($latestProductDetail)) continue;

                    $sellOrder->matched_order_id = $buyOrder->id;
                    $sellOrder->product_detail_percent = $latestProductDetail->percent;
                    $sellOrder->status = OrderStatuses::Matched;
                    $sellOrder->match_hash = $randomHash;
                    $sellOrder->save();

                    $buyOrder->matched_order_id = $sellOrder->id;
                    $buyOrder->product_detail_percent = $latestProductDetail->percent;
                    $buyOrder->status = OrderStatuses::Matched;
                    $buyOrder->match_hash = $randomHash;
                    $buyOrder->save();
                }
            }
        } // end try
        catch (\Exception $e) {
            Log::alert($e);
        }
    }

    public function orderDelivery()
    {
        try{
            $date = date('Y-m') . '-01';
            $matchedOrders = Order::select('*', DB::raw("GROUP_CONCAT(DISTINCT id SEPARATOR ',') AS order_ids"))
                ->where('status', OrderStatuses::Matched)
                ->where('delivery_date', '<=', $date)
                ->groupBy('match_hash')
                ->get();

            foreach ($matchedOrders as $order) {
                $productDetailPercent = $order->product_detail_percent;
                $productId = $order->product_id;
                $collateral = $order->collateral; //for example 10 %
                $orderIds = explode(',', $order->order_ids); //for example 60,61
                $amount = $order->amount; //for example 1000 CAD

                $profitLoss = $order->profitLossPercentBasedOnDeliveryDate();
                if (!$profitLoss || $profitLoss == 'N/A' || !is_numeric($profitLoss))
                    continue;

                $applyProfitLoss = ($profitLoss >= 0) ? $profitLoss : -$profitLoss;
                $applyProfitLoss = ($profitLoss >= $order->collateral) ? $order->collateral : $applyProfitLoss; //for example: Apply 10 %
                $transactionValue = ($applyProfitLoss * $order->amount) / 100; //for example: 10 * 1000 / 100 = 10 CAD

                $buyerSellerOrders = Order::whereIn('id', $orderIds)->get();

                foreach ($buyerSellerOrders as $buyerSellerOrder) {
                    $type = $buyerSellerOrder->type;
                    $orderId = $buyerSellerOrder->id;
                    $orderUser = $buyerSellerOrder->user;
                    $orderUserId = $orderUser->id;
                    // the value that decreased in submit order must return back to user balance
                    $primaryCollateral = ($buyerSellerOrder->collateral * $buyerSellerOrder->amount) / 100;

                    if ($profitLoss >= 0) { //Buyer won; Seller lost
                        if ($type == OrderTypes::Buy) { //Buyer
                            //Update Users.Balance = Users.Balance + Transaction_Value Where User_id = user_id;//+10 CAD in our example
                            //Insert Transactions type:increase, reason:buy, user_id:user_id, other fields;
                            $orderUser->increase($primaryCollateral);
                            $orderUser->increase($transactionValue);
                            (new Transaction())->increase($transactionValue, TransactionReasons::Buy, $orderId, $orderUserId);
                        } else {
                            //Update Users.Balance = Users.Balance - Transaction_Value Where User_id = user_id;
                            //Insert Transactions type:decrease, reason:sell, user_id:user_id, other fields;
                            $orderUser->increase($primaryCollateral);
                            $orderUser->decrease($transactionValue);
                            (new Transaction())->decrease($transactionValue, TransactionReasons::Sell, $orderId, $orderUserId);
                        }
                    } else { //Buyer lost; Seller won
                        if ($type == OrderTypes::Buy) { //Buyer
                            //Update Users.Balance = Users.Balance - Transaction_Value Where User_id = user_id;
                            //Insert Transactions type:decrease, reason:buy, user_id:user_id, other fields;
                            $orderUser->increase($primaryCollateral);
                            $orderUser->decrease($transactionValue);
                            (new Transaction())->decrease($transactionValue, TransactionReasons::Buy, $orderId, $orderUserId);
                        } else {
                            //Update Users.Balance = Users.Balance + Transaction_Value Where User_id = user_id;
                            //Insert Transactions type:increase, reason:sell, user_id:user_id, other fields;
                            $orderUser->increase($primaryCollateral);
                            $orderUser->increase($transactionValue);
                            (new Transaction())->increase($transactionValue, TransactionReasons::Sell, $orderId, $orderUserId);
                        }
                    }

                }

                //Update Orders Set status='delivered' Where ids IN (Order_ids);
                foreach ($buyerSellerOrders as $orderNeededStatusUpdate) {
                    $orderNeededStatusUpdate->status = OrderStatuses::Delivered;
                    $orderNeededStatusUpdate->save();
                }

            } // end foreach
        } // end try
        catch (\Exception $e) {
            Log::alert($e);
        }
    }

    public function removeUnmatchOrders()
    {
        try {
            $orders = Order::where('status', OrderStatuses::Unmatched)
                ->whereNull('matched_order_id')
                ->whereDate('validity_date', '<', date('Y-m-d'))
                ->get();

            foreach ($orders as $order) {
                $user = User::findOrFail($order->user_id);

                $userDecColl = Transaction::where('user_id', $user->id)
                    ->where('order_id', $order->id)
                    ->where('type', TransactionTypes::Dec)
                    ->where('reason', TransactionReasons::Collateral)
                    ->get();

                $userDecComm = Transaction::where('user_id', $user->id)
                    ->where('order_id', $order->id)
                    ->where('type', TransactionTypes::Dec)
                    ->where('reason', TransactionReasons::Commission)
                    ->get();

                $adminIncComm = Transaction::where('user_id', CommissionOptions::UserAccount)
                    ->where('order_id', $order->id)
                    ->where('type', TransactionTypes::Inc)
                    ->where('reason', TransactionReasons::Commission)
                    ->get();

                // For check if there is only 1 transaction for each type, user and order
                if (count($userDecColl) != 1 || count($userDecComm) != 1 || count($adminIncComm) != 1) {
                    continue;
                }

                // get records
                $userDecColl = $userDecColl->first();
                $userDecComm = $userDecComm->first();
                $adminIncComm = $adminIncComm->first();

                // last double check. Note: maybe it's not necessary!
                if (!$userDecColl || !$userDecComm || !$adminIncComm) {
                    continue;
                }

                // return back user collateral amount
                (new Transaction())
                    ->increase($userDecColl->amount, TransactionReasons::Collateral, $order->id, $user->id);
                $user->increase($userDecColl->amount);

                // return back user commission
                (new Transaction())
                    ->increase($userDecComm->amount, TransactionReasons::Commission, $order->id, $user->id);
                $user->increase($userDecComm->amount);

                // decrease admin commission
                (new Transaction())
                    ->decrease($adminIncComm->amount, TransactionReasons::Commission, $order->id, CommissionOptions::UserAccount);
                $systemCommissionSaver = User::find(CommissionOptions::UserAccount);
                $systemCommissionSaver->decrease($adminIncComm->amount);

                // Remove order
                $order->delete();
            }
        } // end try
        catch (\Exception $e) {
            Log::alert($e);
        }
    }

}
