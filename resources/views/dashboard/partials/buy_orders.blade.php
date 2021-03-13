@foreach($buyOrders as $order)
    <div class="buy-orders">
        <div class="buy-orders-sub">
            {{ $order->product->name }} -
            {{ $order->currencyAmount }} -
            {{ \Carbon\Carbon::create($order->delivery_date)->format('M, Y') }}
        </div>
    </div>
@endforeach
