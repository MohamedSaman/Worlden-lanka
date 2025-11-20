<!DOCTYPE html>
<html>

<head>
    <title>{{ $title ?? 'Sales Receipt' }} - Print</title>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 1cm;
        }

        body {
            font-family: "Courier New", monospace;
            font-size: 14px;
            color: #000 !important;
            line-height: 1.4;
        }

        * {
            color: #000 !important;
        }

        .company-header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }

        .company-name {
            font-size: 32px;
            font-weight: bold;
            color: #000 !important;
            margin: 0;
        }

        .company-address {
            font-size: 12px;
            color: #000 !important;
            margin: 5px 0;
        }

        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            color: #000 !important;
            text-align: center;
            margin: 20px 0;
            text-decoration: underline;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }

        .info-section {
            width: 48%;
        }

        .info-section h6 {
            color: #000 !important;
            font-weight: bold;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .info-section table {
            width: 100%;
            font-size: 12px;
        }

        .info-section td {
            padding: 3px 0;
            vertical-align: top;
        }

        .info-section .label {
            font-weight: bold;
            width: 40%;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            border: 2px solid #000;
        }

        .items-table th {
            background-color: #000 !important;
            color: #fff !important;
            padding: 8px 6px;
            text-align: left;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid #000 !important;
        }

        .items-table td {
            padding: 6px;
            border: 1px solid #000 !important;
            font-size: 12px;
            vertical-align: top;
        }

        .items-table tbody tr:nth-child(even) {
            background-color: #f9f9f9 !important;
        }

        .summary-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .summary-box {
            width: 400px;
            border: 2px solid #000;
            padding: 15px;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #ccc;
        }

        .summary-row.total {
            font-size: 16px;
            font-weight: bold;
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 10px 0;
            margin-top: 10px;
        }

        .payment-section {
            margin-top: 20px;
            border: 1px solid #000;
            padding: 10px;
        }

        .payment-section h6 {
            color: #000 !important;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .payment-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #ddd;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #000;
            font-weight: bold;
        }

        .return-items {
            margin-top: 20px;
        }

        .return-items h6 {
            color: #000 !important;
            font-weight: bold;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .notes-section {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #000;
        }

        .notes-section h6 {
            color: #000 !important;
            font-weight: bold;
            margin-bottom: 5px;
            font-size: 14px;
        }

        @media print {

            .no-print,
            .btn,
            .modal-footer,
            button {
                display: none !important;
            }

            body {
                padding: 0;
                margin: 0;
            }

            .receipt-container {
                box-shadow: none;
                border: none;
            }

            * {
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .items-table th {
                background-color: #000 !important;
                color: #fff !important;
            }
        }
    </style>
</head>

<body>
    <div class="receipt-container">
        <!-- Company Header -->
        <div class="company-header">
            <div class="company-name">PLUS</div>
            <div class="company-address">NO 20/2/1, 2nd FLOOR, HUNTER BUILDING, BANKSHALLL STREET, COLOMBO-11</div>
            <div class="company-address">Phone: 011 - 2332786 | Email: plusaccessories.lk@gmail.com</div>
        </div>

        <div class="receipt-title">SALES RECEIPT</div>

        <!-- Customer and Invoice Info -->
        <div class="info-row">
            <div class="info-section">
                <h6>CUSTOMER DETAILS</h6>
                <table>
                    {!! $customerInfo ?? '' !!}
                </table>
            </div>
            <div class="info-section">
                <h6>INVOICE DETAILS</h6>
                <table>
                    {!! $invoiceInfo ?? '' !!}
                </table>
            </div>
        </div>

        <!-- Items Purchased -->
        <h6 style="color: #000; font-weight: bold; margin-top: 20px; margin-bottom: 10px;">Items Purchased</h6>
        {!! $itemsTable ?? '' !!}

        <!-- Return Items (if any) -->
        @if(isset($returnItems) && $returnItems)
        <div class="return-items">
            <h6>Returned Items</h6>
            {!! $returnItems !!}
        </div>
        @endif

        <!-- Payment Information -->
        @if(isset($paymentInfo) && $paymentInfo)
        <div class="payment-section">
            <h6>PAYMENT INFORMATION</h6>
            {!! $paymentInfo !!}
        </div>
        @endif

        <!-- Summary -->
        <div class="summary-section">
            <div class="summary-box">
                {!! $summaryInfo ?? '' !!}
            </div>
        </div>

        <!-- Notes -->
        @if(isset($notes) && $notes)
        <div class="notes-section">
            <h6>NOTES</h6>
            <p>{{ $notes }}</p>
        </div>
        @endif

        <div class="footer">
            <p>Thank you for your purchase!</p>
        </div>
    </div>
</body>

</html>