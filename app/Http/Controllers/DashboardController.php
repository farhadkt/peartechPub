<?php

namespace App\Http\Controllers;

use App\Constants\OrderStatuses;
use App\Constants\OrderTypes;
use App\Constants\TransactionReasons;
use App\Imports\ProductImport;
use App\Order;
use App\Product;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable|void
     */
    public function index(Request $request)
    {
        // Get all the groups. The default group is "Energy and petroleum products"
        $groups = Product::where('active', 1)
            ->whereColumn('id', '=', 'second_root_id')
            ->orderByRaw('id = 2 desc')
            ->latest()->get();

        $products = Product::where('active', 1)->latest()->get();

        return view('dashboard.index', compact('products', 'groups'));
    }

    public function indexAjax(Request $request)
    {
        if (!$request->ajax()) return null;

        $buyOrders = $this->getBuyOrders();
        $sellOrders = $this->getSellOrders();
        $userPositions = $this->getUserPositions();
        $userHistory = $this->getUserHistory();

        $buyOrdersRendered = view('dashboard.partials.buy_orders', compact('buyOrders'))->render();
        $sellOrdersRendered = view('dashboard.partials.sell_orders', compact('sellOrders'))->render();
        $userPositionsRendered = view('dashboard.partials.positions', compact('userPositions'))->render();
        $userHistoryRendered = view('dashboard.partials.history', compact('userHistory'))->render();

        return [
            'bo' => $buyOrdersRendered,
            'so' => $sellOrdersRendered,
            'up' => $userPositionsRendered,
            'uh' => $userHistoryRendered,
        ];
    }

    public function watchlist(Request $request)
    {
        if (!$request->ajax()) return null;

        $result['data'] = $this->getWatchlist($request->has('wuh') ? true : false);

        return $result;
    }

    public function positions(Request $request)
    {
        if (!$request->ajax()) return null;

        $result['data'] = $this->getUserPositions($request->has('wuh') ? true : false);

        return $result;
    }

    public function unmatch(Request $request)
    {
        if (!$request->ajax()) return null;

        $result['data'] = $this->getUserUnmatch($request->has('wuh') ? true : false);

        return $result;
    }

    public function history(Request $request)
    {
        if (!$request->ajax()) return null;

        $result['data'] = $this->getUserHistory($request->has('wuh') ? true : false);

        return $result;
    }

    private function getWatchlist($wantsUniqueHash = false)
    {
        if ($wantsUniqueHash) {
            $res = Order::select(
                DB::raw('COUNT(id) AS count'),
                DB::raw('SUM(amount) AS amount_sum'),
                DB::raw('SUM(collateral) AS collateral_sum'),
                DB::raw('SUM(commission) AS commission_sum'),
                DB::raw('SUM(product_id) AS product_id_sum'), // !? Don't worry. Just wants to make really unique hash.
                DB::raw('SUM(UNIX_TIMESTAMP(delivery_date) / 1000000) AS delivery_date_sum'),
                DB::raw('SUM(UNIX_TIMESTAMP(validity_date) / 1000000) AS validity_date_sum')
            )
                ->whereIn('type', [OrderTypes::Sell, OrderTypes::Buy])
                ->where('status', OrderStatuses::Unmatched)
                ->get()
                ->first();

            return md5("{$res->count}{$res->amount_sum}{$res->collateral_sum}{$res->commission_sum}{$res->product_id_sum}{$res->delivery_date_sum}{$res->validity_date_sum}");
        }

        $sellOrders = Order::with('product.secondRoot')
            ->whereIn('type', [OrderTypes::Sell, OrderTypes::Buy])
            ->where('status', OrderStatuses::Unmatched)
            ->latest()->get();
        return $sellOrders;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getSellOrders()
    {
        $sellOrders = Order::with('product.secondRoot')
            ->where('type', OrderTypes::Sell)
            ->where('status', OrderStatuses::Unmatched)
            ->latest()->get();
        return $sellOrders;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getBuyOrders()
    {
        $buyOrders = Order::with('product.secondRoot')
            ->where('type', OrderTypes::Buy)
            ->where('status', OrderStatuses::Unmatched)
            ->latest()->get();
        return $buyOrders;
    }

    /**
     * @param bool $wantsUniqueHash
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|string
     */
    private function getUserPositions($wantsUniqueHash = false)
    {
        if ($wantsUniqueHash) {
            $res = Order::select(
                DB::raw('COUNT(id) AS count'),
                DB::raw('SUM(amount) AS amount_sum'),
                DB::raw('SUM(collateral) AS collateral_sum'),
                DB::raw('SUM(commission) AS commission_sum'),
                DB::raw('SUM(product_id) AS product_id_sum'), // !? Don't worry. Just wants to make really unique hash.
                DB::raw('SUM(UNIX_TIMESTAMP(delivery_date) / 1000000) AS delivery_date_sum'),
                DB::raw('SUM(UNIX_TIMESTAMP(validity_date) / 1000000) AS validity_date_sum')
            )
                ->where('user_id', auth()->user()->id)
                ->where('status', OrderStatuses::Matched)
                ->get()
                ->first();

            return md5("{$res->count}{$res->amount_sum}{$res->collateral_sum}{$res->commission_sum}{$res->product_id_sum}{$res->delivery_date_sum}{$res->validity_date_sum}");
        }

        $userPositions = Order::with('product.latestProductDetail', 'product.secondRoot')
            ->where('user_id', auth()->user()->id)
            ->where('status', OrderStatuses::Matched)
            ->latest()->get();
        return $userPositions;
    }

    /**
     * @param bool $wantsUniqueHash
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|string
     */
    private function getUserUnmatch($wantsUniqueHash = false)
    {
        if ($wantsUniqueHash) {
            $res = Order::select(
                DB::raw('COUNT(id) AS count'),
                DB::raw('SUM(amount) AS amount_sum'),
                DB::raw('SUM(collateral) AS collateral_sum'),
                DB::raw('SUM(commission) AS commission_sum'),
                DB::raw('SUM(product_id) AS product_id_sum'), // !? Don't worry. Just wants to make really unique hash.
                DB::raw('SUM(UNIX_TIMESTAMP(delivery_date) / 1000000) AS delivery_date_sum'),
                DB::raw('SUM(UNIX_TIMESTAMP(validity_date) / 1000000) AS validity_date_sum')
            )
                ->where('user_id', auth()->user()->id)
                ->where('status', OrderStatuses::Unmatched)
                ->get()
                ->first();

            return md5("{$res->count}{$res->amount_sum}{$res->collateral_sum}{$res->commission_sum}{$res->product_id_sum}{$res->delivery_date_sum}{$res->validity_date_sum}");
        }

        $userUnmatch = Order::with('product.latestProductDetail', 'product.secondRoot')
            ->where('user_id', auth()->user()->id)
            ->where('status', OrderStatuses::Unmatched)
            ->latest()->get();
        return $userUnmatch;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    private function getUserHistory($wantsUniqueHash = false)
    {
        if ($wantsUniqueHash) {
            $res = Order::select(
                DB::raw('COUNT(id) AS count'),
                DB::raw('SUM(amount) AS amount_sum'),
                DB::raw('SUM(collateral) AS collateral_sum'),
                DB::raw('SUM(commission) AS commission_sum'),
                DB::raw('SUM(product_id) AS product_id_sum'), // !? Don't worry. Just wants to make really unique hash.
                DB::raw('SUM(UNIX_TIMESTAMP(delivery_date) / 1000000) AS delivery_date_sum'),
                DB::raw('SUM(UNIX_TIMESTAMP(validity_date) / 1000000) AS validity_date_sum')
            )
                ->where('user_id', auth()->user()->id)
                ->where('status', OrderStatuses::Delivered)
                ->get()
                ->first();

            return md5("{$res->count}{$res->amount_sum}{$res->collateral_sum}{$res->commission_sum}{$res->product_id_sum}{$res->delivery_date_sum}{$res->validity_date_sum}");
        }

        $userHistory = Order::with('product.latestProductDetail', 'product.secondRoot')
            ->where('user_id', auth()->user()->id)
            ->where('status', OrderStatuses::Delivered)
            ->latest()->get();
        return $userHistory;
    }

}
