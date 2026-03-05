<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $receipt->uuid }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #000;
            margin: 12px;
        }

        .center {
            text-align: center;
        }

        .title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 4px;
        }

        .muted {
            color: #222;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            vertical-align: top;
            padding: 2px 0;
        }

        .text-right {
            text-align: right;
        }

        .totals td {
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="center">
        <div class="title">{{ $receipt->store ? $receipt->store->name : config('app.name') }}</div>
        @if($receipt->store && $receipt->store->code)
        <div class="muted">{{ $receipt->store->code }}</div>
        @endif
        <div>RECEIPT</div>
    </div>

    <div class="divider"></div>

    <table>
        <tr>
            <td>ID</td>
            <td class="text-right">{{ $receipt->uuid }}</td>
        </tr>
        <tr>
            <td>Date</td>
            <td class="text-right">{{ optional($receipt->receipt_date)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td>Customer</td>
            <td class="text-right">{{ $receipt->customer_name ?: '-' }}</td>
        </tr>
        <tr>
            <td>Sales</td>
            <td class="text-right">{{ optional($receipt->salesUser)->name ?: '-' }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    @foreach($receipt->details as $detail)
    <div>
        <strong>{{ $detail->item_name }}</strong><br>
        {{ $detail->item_no ?: optional($detail->item)->item_no ?: '-' }}<br>
        {{ number_format($detail->item_weight, 2, ',', '.') }} gr / {{ number_format($detail->item_gold_rate, 2, ',', '.') }}%
    </div>
    <table>
        <tr>
            <td>Sales Price</td>
            <td class="text-right">{{ $detail->sales_price !== null ? ('Rp ' . number_format($detail->sales_price, 2, ',', '.')) : '-' }}</td>
        </tr>
        @if($showServiceFee)
        <tr>
            <td>Service Fee</td>
            <td class="text-right">{{ $detail->service_fee !== null ? ('Rp ' . number_format($detail->service_fee, 2, ',', '.')) : '-' }}</td>
        </tr>
        @endif
        <tr>
            <td><strong>Subtotal</strong></td>
            <td class="text-right"><strong>{{ $detail->line_total !== null ? ('Rp ' . number_format($detail->line_total, 2, ',', '.')) : '-' }}</strong></td>
        </tr>
    </table>
    @if(!$loop->last)
    <div class="divider"></div>
    @endif
    @endforeach

    <div class="divider"></div>

    <table class="totals">
        <tr>
            <td><strong>Total</strong></td>
            <td class="text-right"><strong>Rp {{ number_format($receipt->receipt_total, 2, ',', '.') }}</strong></td>
        </tr>
    </table>

    @if($receipt->customer_address)
    <div class="divider"></div>
    <div>
        <strong>Address</strong><br>
        {{ $receipt->customer_address }}
    </div>
    @endif

    <div class="divider"></div>
    <div class="center">
        Thank you
    </div>
</body>
</html>
