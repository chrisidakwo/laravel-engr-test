<!DOCTYPE html>
<html>
<head>
    <title>Order Notification</title>
</head>
<body>
    <h1>New Order Submitted</h1>
    <p>Provider: {{ $order->provider_name }}</p>
    <p>Encounter Date: {{ $order->encounter_date }}</p>
    <p>Order Amount: {{ $order->order_amount }}</p>
    <p>Order Items:</p>
    <ul>
        @foreach ($order->order_items as $item)
            <li>{{ $item['name'] }} - {{ $item['quantity'] }} x {{ $item['unitPrice'] }}</li>
        @endforeach
    </ul>
</body>
</html>
