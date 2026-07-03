<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice - {{ $reservation->reservation_code }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            background: #fff;
        }
        .header-table {
            width: 100%;
            margin-bottom: 30px;
            border-bottom: 2px solid #10b981;
            padding-bottom: 20px;
        }
        .header-title {
            font-size: 26px;
            font-weight: bold;
            color: #10b981;
            text-transform: uppercase;
        }
        .invoice-details {
            text-align: right;
            font-size: 13px;
            color: #777;
        }
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-col {
            width: 50%;
            vertical-align: top;
        }
        .info-title {
            font-size: 12px;
            font-weight: bold;
            color: #999;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        .info-content {
            font-size: 14px;
            color: #333;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .items-table th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 10px;
            font-size: 12px;
            font-weight: bold;
            text-align: left;
            color: #475569;
        }
        .items-table td {
            border-bottom: 1px dashed #e2e8f0;
            padding: 12px 10px;
            font-size: 13px;
        }
        .items-table .text-right {
            text-align: right;
        }
        .items-table .text-center {
            text-align: center;
        }
        .totals-table {
            width: 100%;
            margin-top: 20px;
        }
        .totals-col-left {
            width: 60%;
            vertical-align: bottom;
            text-align: center;
        }
        .totals-col-right {
            width: 40%;
        }
        .totals-row {
            font-size: 13px;
            color: #666;
            margin-bottom: 5px;
        }
        .totals-row span {
            float: right;
            font-weight: bold;
            color: #333;
        }
        .grand-total {
            font-size: 18px;
            font-weight: bold;
            color: #10b981;
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            margin-top: 10px;
        }
        .grand-total span {
            float: right;
            font-weight: bold;
            color: #10b981;
        }
        .qr-section {
            border: 1px solid #e2e8f0;
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            display: inline-block;
            text-align: center;
        }
        .qr-section p {
            margin: 5px 0 0 0;
            font-size: 10px;
            color: #666;
        }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <!-- Header -->
        <table class="header-table" cellpadding="0" cellspacing="0">
            <tr>
                <td>
                    <span class="header-title">Food Rescue Marketplace</span><br>
                    <span style="font-size: 12px; color: #777;">Trincomalee, Sri Lanka</span>
                </td>
                <td class="invoice-details">
                    <strong>INVOICE RECEIPT</strong><br>
                    Order Code: {{ $reservation->reservation_code }}<br>
                    Date: {{ $reservation->created_at->format('M d, Y H:i') }}<br>
                    Status: <span style="color: #10b981; font-weight: bold;">PAID</span>
                </td>
            </tr>
        </table>

        @php
            $isDelivery = $reservation->delivery_method === 'delivery';
            $address = $isDelivery ? $reservation->delivery_address : '';
        @endphp

        <!-- Partner & Fulfillment Info -->
        <table class="info-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="info-col">
                    <div class="info-title">Fulfillment Details</div>
                    <div class="info-content">
                        <strong>Method:</strong> {{ $isDelivery ? 'Home Delivery' : 'Store Pickup' }}<br>
                        <strong>Date/Time:</strong> {{ $reservation->pickup_time->format('M d, Y H:i') }}<br>
                        <strong>{{ $isDelivery ? 'Delivery Address:' : 'Store Address:' }}</strong> {{ $isDelivery ? $address : $reservation->business->address }}
                    </div>
                </td>
                <td class="info-col" style="padding-left: 20px;">
                    <div class="info-title">Merchant Partner</div>
                    <div class="info-content">
                        <strong>Store:</strong> {{ $reservation->business->business_name }}<br>
                        <strong>Phone:</strong> {{ $reservation->business->phone }}<br>
                        <strong>Email:</strong> {{ $reservation->business->email }}
                    </div>
                </td>
            </tr>
        </table>

        <!-- Items Summary -->
        <table class="items-table" cellpadding="0" cellspacing="0">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-center" style="width: 80px;">Qty</th>
                    <th class="text-right" style="width: 120px;">Unit Price</th>
                    <th class="text-right" style="width: 120px;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reservation->items as $item)
                    <tr>
                        <td><strong>{{ $item->food_name }}</strong></td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-right">Rs. {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">Rs. {{ number_format($item->total_price, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals & QR Code -->
        <table class="totals-table" cellpadding="0" cellspacing="0">
            <tr>
                <td class="totals-col-left">
                    @if($reservation->qrCode && $reservation->qrCode->qr_image_path)
                        <div class="qr-section">
                            <img src="{{ public_path($reservation->qrCode->qr_image_path) }}" style="width: 120px; height: 120px;" alt="QR Code"><br>
                            <p>Verification Code: {{ $reservation->reservation_code }}</p>
                        </div>
                    @endif
                </td>
                <td class="totals-col-right">
                    <div class="totals-row">
                        Basket Subtotal:
                        <span>Rs. {{ number_format($reservation->subtotal, 2) }}</span>
                    </div>
                    
                    @if($reservation->loyalty_discount > 0)
                        <div class="totals-row" style="color: #ef4444;">
                            Loyalty Discount:
                            <span>- Rs. {{ number_format($reservation->loyalty_discount, 2) }}</span>
                        </div>
                    @endif

                    @if($isDelivery)
                        <div class="totals-row">
                            Home Delivery Fee:
                            <span>Rs. {{ number_format($reservation->delivery_fee, 2) }}</span>
                        </div>
                    @endif

                    <div class="grand-total">
                        Total Amount Paid:
                        <span>Rs. {{ number_format($reservation->total_amount, 2) }}</span>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div class="footer">
            <p>Thank you for rescuing surplus food and saving CO₂ emissions!</p>
            <p style="font-size: 10px; color: #ccc; margin-top: 10px;">This receipt is generated automatically. Security Transaction ID: {{ sha1($reservation->id) }}</p>
        </div>
    </div>
</body>
</html>
