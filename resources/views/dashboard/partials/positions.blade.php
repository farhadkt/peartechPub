<table
    class="table table-head-fixed text-nowrap table-hover table-valign-middle table-sort">
    <thead>
    <tr>
        <th>Product
            <span><i class="fas fa-sort-up"></i></span>
            <span><i class="fas fa-sort-down"></i></span>
        </th>
        <th>Status</th>
        <th>Amount
            <span><i class="fas fa-sort-up"></i></span>
            <span><i class="fas fa-sort-down"></i></span>
        </th>
        <th>Buy/Sell
            <span><i class="fas fa-sort-up"></i></span>
            <span><i class="fas fa-sort-down"></i></span>
        </th>
        <th>Delivery</th>
        <th>Collateral
            <span><i class="fas fa-sort-up"></i></span>
            <span><i class="fas fa-sort-down"></i></span>
        </th>
        <th>Profit/Loss
            <span><i class="fas fa-sort-up"></i></span>
            <span><i class="fas fa-sort-down"></i></span>
        </th>
    </tr>
    </thead>
    <tbody>
    @foreach($userPositions as $order)
        <tr>
            <td>{{ $order->product->name }}</td>
            <td>{{ __('Matched') }}</td>
            <td>{{ $order->currencyAmount}}</td>
            <td>
                @if ($order->type == OrderTypes::Buy)
                    <span class="badge  buy-color"> Buy </span>
                @else
                    <span class="badge  sell-color"> Sell </span>
                @endif
            </td>
            <td>
                {{ \Carbon\Carbon::create($order->delivery_date)->format('M Y') }}
            </td>
            <td>{{ $order->castedCollateral }}</td>
            <td>
                {!! Render::profitOrLoseFormatter($order->profitLossPercentWithSings(), $order->profitLoss()) !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
