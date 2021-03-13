<?php

namespace App\Http\Controllers;

use App\Constants\CommissionOptions;
use App\Constants\OrderStatuses;
use App\Constants\OrderTypes;
use App\Constants\TransactionReasons;
use App\Constants\TransactionTypes;
use App\Http\Requests\StoreOrder;
use App\Order;
use App\Product;
use App\Setting;
use App\Transaction;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreOrder $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function store(StoreOrder $request)
    {
        Gate::authorize('orders-create');

        $user = auth()->user();
        $product = Product::findOrFail($request->product_id);
        $commission = $request->amount * Setting::key('commission');

        try {
            $order = $this->createOrder($request, $product, $commission);
        } catch (\Exception $e) {
            alert()->error($e->getMessage())->persistent(true, false);
            return redirect()->back();
        }

        $order = $user->orders()->save($order);
        $collateralAmount = ($order->amount * $order->collateral) / 100;

        $userDecColl = (new Transaction())
            ->decrease($collateralAmount, TransactionReasons::Collateral, $order->id, $user->id);
        $user->decrease($collateralAmount);

        $userDecComm = (new Transaction())
            ->decrease($commission, TransactionReasons::Commission, $order->id, $user->id);
        $user->decrease($commission);

        $adminIncComm = (new Transaction())
            ->increase($commission, TransactionReasons::Commission, $order->id, CommissionOptions::UserAccount);
        $systemCommissionSaver = User::find(CommissionOptions::UserAccount);
        $systemCommissionSaver->increase($commission);

        alert()->success('Order submitted successfully')->persistent(true, false);

        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreOrder $request, $id)
    {
        Gate::authorize('orders-update');

        $user = auth()->user();
        $product = Product::findOrFail($request->product_id);
        $commission = $request->amount * Setting::key('commission');
        $commission = round($commission, 2);

        $result = [
            'success' => false,
            'messageTitle' => 'Failed!'
        ];

        try {
            $order = $this->updateOrder($id, $request, $product, $commission);
        } catch (\Exception $e) {
            $result['messageBody'] = $e->getMessage();
            return response()->json($result, 400);
        }

        $order = $user->orders()->save($order);
        $collateralAmount = ($order->amount * $order->collateral) / 100;
        $collateralAmount = round($collateralAmount, 2);

        $orderTransactions = $order->transactions;

        foreach ($orderTransactions as $transaction) {
            if ($transaction->user_id == $user->id &&
                $transaction->type == TransactionTypes::Dec &&
                $transaction->reason == TransactionReasons::Collateral) {

                $oldCollateralAmount = $transaction->amount;
                $amountDiff = $collateralAmount - $oldCollateralAmount;
                $transaction->decrease($collateralAmount, TransactionReasons::Collateral, $order->id, $user->id);
                $user->decrease($amountDiff);

            } elseif ($transaction->user_id == $user->id &&
                $transaction->type == TransactionTypes::Dec &&
                $transaction->reason == TransactionReasons::Commission) {

                $oldCommission = $transaction->amount;
                $amountDiff = $commission - $oldCommission;
                $transaction->decrease($commission, TransactionReasons::Commission, $order->id, $user->id);
                $user->decrease($amountDiff);

            } elseif ($transaction->user_id == CommissionOptions::UserAccount &&
                $transaction->type == TransactionTypes::Inc &&
                $transaction->reason == TransactionReasons::Commission) {

                $oldCommission = $transaction->amount;
                $amountDiff = $commission - $oldCommission;
                $transaction->increase($commission, TransactionReasons::Commission, $order->id, CommissionOptions::UserAccount);
                $systemCommissionSaver = User::find(CommissionOptions::UserAccount);
                $systemCommissionSaver->increase($amountDiff);
            }
        }

        $result['success'] = true;
        $result['messageTitle'] = 'Updated!';
        $result['messageBody'] = 'The operation has done successfully';

        return response()->json($result, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroyUnmatch($id)
    {
        $order = Order::where('id', $id)->where('status', OrderStatuses::Unmatched)->first();

        $result = [
            'success' => false,
            'messageTitle' => 'Failed!'
        ];

        if (!$order) {
            $result['messageBody'] = 'Order not found or has been matched';
            return $result;
        }

        if ($order->user_id !== auth()->user()->id) {
            $result['messageBody'] = 'Unauthorized request';
            return $result;
        }

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

        // For check if there is only 1 transaction for each type, user , order
        if (count($userDecColl) != 1 || count($userDecComm) != 1 || count($adminIncComm) != 1) {
            $result['messageBody'] = 'Can\'t delete order';
            return $result;
        }

        // get records
        $userDecColl = $userDecColl->first();
        $userDecComm = $userDecComm->first();
        $adminIncComm = $adminIncComm->first();

        // last double check. Note: maybe it's not necessary!
        if (!$userDecColl || !$userDecComm || !$adminIncComm) {
            $result['messageBody'] = 'There is problem with your request';
            return $result;
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

        $result['success'] = true;
        $result['messageTitle'] = 'Deleted!';
        $result['messageBody'] = 'The operation has done successfully';

        return $result;
    }

    /**
     * @param StoreOrder $request
     * @param $commission
     * @param $product
     * @return Order
     * @throws \Exception
     */
    private function createOrder(StoreOrder $request, Product $product, $commission)
    {
        $order = new Order();

        if (!$product->latestProductDetail()->exists()) {
            throw new \Exception('There is no info about product detail.');
        }

        $order->product_id = $request->product_id;
        $order->amount = $request->amount;
        $order->delivery_date = (new \DateTime($request->delivery_date))->format('Y-m-d');
        $order->validity_date = (new \DateTime($request->validity_date))->format('Y-m-d');
        $order->collateral = $request->collateral;
        $order->type = $request->type;
        $order->commission = $commission;
        $order->status = OrderStatuses::Unmatched;
        $order->product_detail_percent = $product->latestProductDetail->percent;

        return $order;
    }

    private function updateOrder($id, StoreOrder $request, Product $product, $commission)
    {
        $order = Order::find($id);

        if ($order->status != OrderStatuses::Unmatched) {
            throw new \Exception('The order is matched with another order.');
        }

        if (!$product->latestProductDetail()->exists()) {
            throw new \Exception('There is no info about product detail.');
        }

        $order->product_id = $request->product_id;
        $order->amount = $request->amount;
        $order->delivery_date = (new \DateTime($request->delivery_date))->format('Y-m-d');
        $order->validity_date = (new \DateTime($request->validity_date))->format('Y-m-d');
        $order->collateral = $request->collateral;
        $order->commission = $commission;
        $order->product_detail_percent = $product->latestProductDetail->percent;

        return $order;
    }
}
