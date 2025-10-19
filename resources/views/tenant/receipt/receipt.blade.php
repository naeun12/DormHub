<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .header {
            background-color: #3e4a61;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header .left {
            font-size: 13px;
            line-height: 1.5;
        }

        .header .right {
            text-align: right;
            font-size: 14px;
        }

        .receipt-title {
            font-weight: bold;
            font-size: 20px;
            margin-bottom: 5px;
        }

        .receipt-body {
            background-color: #f9f9f9;
            padding: 20px 30px;
        }

        .row {
            margin-bottom: 8px;
        }

        .label {
            font-weight: bold;
            display: inline-block;
            width: 120px;
        }

        .options {
            margin: 15px 0;
        }

        .options label {
            margin-right: 20px;
            font-weight: bold;
        }

        .signature {
            margin-top: 50px;
            text-align: right;
            font-style: italic;
            font-weight: bold;
        }

        .verification {
            margin-top: 40px;
            padding: 12px;
            border: 2px dashed #3e4a61;
            background-color: #fff;
            font-weight: bold;
            color: #3e4a61;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- HEADER -->
    <div class="header">
        <div class="left">
            <div><strong>{{ $tenant->room->dorm->dormName }}</strong></div>
            <div>Address: {{ $tenant->room->dorm->address }}</div>
            <div>Email: {{ $tenant->room->landlord->email }}</div>
            <div>Phone: {{ $tenant->room->landlord->phoneNumber }}</div>
        </div>
        <div class="right">
            <div class="receipt-title">RECEIPT</div>
            <div>No. {{ str_pad($tenant->approvedID, 6, '0', STR_PAD_LEFT) }}</div>
            <div>Date: {{ \Carbon\Carbon::now()->format('Y-m-d') }}</div>
        </div>
    </div>

    <!-- BODY -->
    <div class="receipt-body">
        <div class="row">
            <span class="label">Tenant Name:</span> {{ $tenant->firstname }} {{ $tenant->lastname }}
        </div>
        <div class="row">
            <span class="label">Room #:</span> {{ $tenant->room->roomNumber }}
        </div>
        <div class="row">
            <span class="label">Room Type:</span> {{ $tenant->room->roomType }}
        </div>
        <div class="row">
            <span class="label">Monthly Rate:</span> PHP {{ number_format($tenant->room->price, 2) }}
        </div>
        <div class="row">
            <span class="label">Account #:</span> {{ $tenant->approvedID }}
        </div>

        <div class="options">
            <input type="radio" checked> For {{ $tenant->source_type }}
        </div>

        <div class="row">
            <span class="label">Amount Paid:</span> PHP {{ number_format($latestPayment->amount ?? 0, 2) }}
        </div>
        <div class="row">
            <span class="label">Payment Method:</span>
            <label>
                <input type="checkbox" {{ ($latestPayment && $latestPayment->paymentType == 'Gcash') ? 'checked' : '' }}> GCash
            </label>
        </div>

        <div class="signature">Authorized Signature</div>

        <div class="verification">
            ✅ Verified & Tagged by Landlord
        </div>
    </div>

</body>

</html>
