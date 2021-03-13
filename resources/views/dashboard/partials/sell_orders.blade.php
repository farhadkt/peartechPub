@foreach($sellOrders as $order)
    <div class="sell-orders">
        <div class="sell-orders-sub">
            {{ $order->product->name }} -
            {{ $order->currencyAmount }} -
            {{ \Carbon\Carbon::create($order->delivery_date)->format('M, Y') }}
        </div>
    </div>
@endforeach
