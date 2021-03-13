<?php

namespace App\Http\Controllers;

use App\Constants\TransactionReasons;
use App\Constants\TransactionTypes;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $userBalance = $user->balance;
        $userCollateral = $user->totalCollateral();
        $userCommission = $user->totalCommission();
        // Soon remove
//        $transactions = Transaction::whereTypeIn($request->query('types'))
//            ->whereReasonIn($request->query('reasons'))
//            ->whereMinAmount($request->query('min_amount'))
//            ->whereMaxAmount($request->query('max_amount'));
//
//        if (!$user->isAdmin()) {
//            $transactions = $transactions->where('user_id', $user->id);
//        }
//
//        $transactions = $transactions->latest()->paginate(25);
//        $transactionTypes = TransactionTypes::listReverse();
//        $transactionReasons = TransactionReasons::listReverse();

        return view('transactions.index', compact('userBalance', 'userCollateral', 'userCommission'));
    }

    public function indexPost(Request $request)
    {
        if ($request->method() != 'POST' || !$request->ajax()) {
            return 'Hey:)';
        }

        $user = Auth::user();

        //$transactions = Transaction::with('user:id,name', 'order.product.secondRoot');

        $transactions = DB::table('transactions')
            ->select('transactions.*', 'users.name AS user_name', 'orders.product_id AS order_product_id', 'products.name AS product_name', 'products_second_root.name AS products_second_root_name')
            ->leftJoin('users', 'users.id', '=', 'transactions.user_id')
            ->leftJoin('orders', 'orders.id', '=', 'transactions.order_id')
            ->leftJoin('products', 'products.id', '=', 'orders.product_id')
            ->leftJoin('products AS products_second_root', 'products_second_root.id', '=', 'products.second_root_id')
            ->orderBy('updated_at', 'DESC');

        if (!$user->isAdmin()) {
            $transactions = $transactions->where('transactions.user_id', $user->id);
        }

        $transactions = $transactions->latest()->get();

        $resultTransactions = [];
        foreach ($transactions as $key => $value) {
            $resultTransactions[$key] = [
                'amount' => $value->amount,
                'comment' => $value->comment,
                'created_at' => $value->created_at,
                'created_at_string' => (new Carbon($value->created_at))->format('Y-m-d H:i'),
                'id' => $value->id,
                'order_id' => $value->order_id,
                'reason' => $value->reason,
                'reason_label' => TransactionReasons::listReverse()[$value->reason],
                'type' => $value->type,
                'type_label' => TransactionTypes::listReverse()[$value->type],
                'updated_at' => $value->updated_at,
                'user_id' => $value->user_id,
                'user' => [
                    'id' => $value->user_id,
                    'name' => $value->user_name
                ]
            ];

            if ($value->product_name && $value->order_product_id)
                $resultTransactions[$key]['order']['id'] = $value->order_id;

            if ($value->product_name && $value->order_product_id) {
                $resultTransactions[$key]['order']['product'] = [
                    'id' => $value->order_product_id,
                    'name' => $value->product_name,
                ];
            }

            if ($value->product_name && $value->order_product_id && $value->products_second_root_name)
                $resultTransactions[$key]['order']['product']['second_root']['name'] = $value->products_second_root_name;
        }

        return [
            'data' => $resultTransactions,
            'is_admin' => $user->isAdmin()
        ];
    }

    public function detail(Request $request)
    {
        if ($request->method() != 'POST' || !$request->ajax() || !$request->has('id')) {
            return 'Hey:)';
        }

        $transaction = Transaction::with('user', 'order.product.secondRoot')->findOrFail($request->id);

        if (!$transaction->order()->exists()) {
            return [
                'success' => false,
                'messageTitle' => 'No More Detail!',
                'messageBody' => 'The order has been removed'
            ];
        }

        return [
            'data' => $transaction,
            'success' => true,
            'messageTitle' => '',
        ];
    }
}
