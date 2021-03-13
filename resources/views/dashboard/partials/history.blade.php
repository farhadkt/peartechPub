<table
    class="table table-head-fixed text-nowrap table-hover table-valign-middle table-sort">
    <thead>
    <tr>
        <th>Product</th>
        <th>Status</th>
        <th>Amount</th>
        <th>Buy/Sell</th>
        <th>Delivery</th>
        <th>Collateral</th>
        <th>Profit/Loss</th>
    </tr>
    </thead>
    <tbody>
    @foreach($userHistory as $order)
        <tr>
            <td>{{ $order->product->name }}</td>
            <td>{{ __('Delivered') }}</td>
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
