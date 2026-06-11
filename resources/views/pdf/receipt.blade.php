<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt {{ $receipt->uuid }}</title>
    <style>
        @page {
            margin: 0;
            size: 155mm 105mm;
        }

        body {
            margin: 0;
            font-family: DejaVu Sans, sans-serif;
            color: #000;
            font-size: 18px;
        }

        .page {
            position: relative;
            width: 155mm;
            height: 105mm;
            page-break-after: always;
            overflow: hidden;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .field {
            position: absolute;
            white-space: nowrap;
        }

        .field-block {
            position: absolute;
            line-height: 1.25;
            word-break: break-word;
        }

        .date {
            top: 4.8mm;
            left: 114mm;
            width: 30mm;
            font-size: 20px;
            letter-spacing: 0.05em;
        }

        .customer-name {
            top: 9.4mm;
            left: 111mm;
            width: 34mm;
            font-size: 18px;
        }

        .customer-address {
            top: 15mm;
            left: 111mm;
            width: 34mm;
            font-size: 18px;
            min-height: 10mm;
        }

        .row {
            position: absolute;
            left: 0;
            width: 155mm;
            height: 9.8mm;
            font-size: 16px;
        }

        .item-name {
            left: 33mm;
            width: 38mm;
            top: 0.6mm;
            min-height: 8mm;
            line-height: 1.0;
        }

        .gold-rate {
            left: 79mm;
            width: 11mm;
            top: 1.4mm;
            text-align: center;
        }

        .weight {
            left: 98mm;
            width: 12mm;
            top: 1.4mm;
            text-align: center;
        }

        .service-fee {
            left: 114mm;
            width: 14mm;
            top: 1.4mm;
            text-align: center;
        }

        .notes {
            left: 133mm;
            width: 10mm;
            top: 1.4mm;
            text-align: left;
            font-size: 14px;
            line-height: 1.0;
        }

        .total-in-words {
            left: 14mm;
            top: 80mm;
            width: 64mm;
            min-height: 10mm;
            font-size: 16px;
            line-height: 1.1;
        }

        .grand-total {
            left: 126mm;
            top: 76.5mm;
            width: 22mm;
            text-align: right;
            font-size: 20px;
            font-weight: 700;
        }

        .receipt-qr {
            position: absolute;
            left: 133mm;
            top: 86mm;
            width: 12mm;
            height: 12mm;
        }
    </style>
</head>
<body>
@php
    $detailPages = $receipt->details->chunk(6);
@endphp
@foreach($detailPages as $detailPage)
    <div class="page">
        <div class="field date">{{ optional($receipt->receipt_date)->format('d - m - y') }}</div>
        <div class="field-block customer-name">{{ $receipt->customer_name ?: '-' }}</div>
        <div class="field-block customer-address">{{ $receipt->customer_address ?: '-' }}</div>

        @foreach($detailPage as $detail)
            @php
                $itemTop = 43 + ($loop->index * 10.3);
                $inventoryStatus = optional(optional($detail->item)->inventoryStatus)->description
                    ?: optional(optional($detail->item)->inventoryStatus)->code
                    ?: '';
                if (strtolower(trim($inventoryStatus)) === 'general') {
                    $inventoryStatus = 'gen';
                }
                $itemName = trim($detail->item_name . ' - ' . ($detail->item_no ?: optional($detail->item)->item_no ?: '-'));
                if ($inventoryStatus !== '') {
                    $itemName .= ' (' . $inventoryStatus . ')';
                }
                $serviceFee = $showServiceFee && $detail->service_fee !== null
                    ? number_format((float) $detail->service_fee, 0, ',', '.')
                    : '';
            @endphp
            <div class="row" style="top: {{ $itemTop }}mm;">
                <div class="field-block item-name">{{ $itemName }}</div>
                <div class="field gold-rate">{{ number_format((float) $detail->item_gold_rate, 1, ',', '.') }}%</div>
                <div class="field weight">{{ number_format((float) $detail->item_weight, 2, ',', '.') }}</div>
                <div class="field service-fee">{{ $serviceFee }}</div>
                <div class="field-block notes">{{ $detail->notes ?: '' }}</div>
            </div>
        @endforeach

        <div class="field-block total-in-words">{{ $receiptTotalInWords }}</div>
        <div class="field grand-total">{{ number_format((float) $receipt->receipt_total, 0, ',', '.') }}</div>
        <img src="{{ $receiptQrUrl }}" alt="Receipt QR" class="receipt-qr">
    </div>
@endforeach
</body>
</html>
