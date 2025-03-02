<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        .receipt {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .school-address {
            font-size: 14px;
            color: #666;
        }
        .receipt-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 20px 0;
        }
        .receipt-no {
            text-align: right;
            margin-bottom: 20px;
        }
        .info-table {
            width: 100%;
            margin-bottom: 30px;
        }
        .info-table td {
            padding: 5px;
        }
        .info-table td:first-child {
            width: 150px;
            font-weight: bold;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
        }
        .signature {
            margin-top: 80px;
        }
        .note {
            margin-top: 30px;
            font-size: 12px;
            color: #666;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="school-name">SCHOOL NAME</div>
            <div class="school-address">
                School Address Line 1<br>
                School Address Line 2<br>
                Phone: (123) 456-7890
            </div>
        </div>

        <div class="receipt-title">PAYMENT RECEIPT</div>
        
        <div class="receipt-no">
            Receipt No: <?= str_pad($payment['id'], 6, '0', STR_PAD_LEFT) ?>
        </div>

        <table class="info-table">
            <tr>
                <td>Date</td>
                <td>: <?= date('d F Y', strtotime($payment['payment_date'])) ?></td>
            </tr>
            <tr>
                <td>Student Name</td>
                <td>: <?= $payment['student_name'] ?></td>
            </tr>
            <tr>
                <td>Class</td>
                <td>: <?= $payment['class'] ?></td>
            </tr>
            <tr>
                <td>Payment For</td>
                <td>: SPP <?= date('F Y', mktime(0, 0, 0, $payment['payment_month'], 1, $payment['payment_year'])) ?></td>
            </tr>
            <tr>
                <td>Payment Method</td>
                <td>: <?= ucfirst($payment['payment_method']) ?></td>
            </tr>
            <tr>
                <td>Amount</td>
                <td class="amount">: Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
            </tr>
            <?php if (!empty($payment['notes'])): ?>
            <tr>
                <td>Notes</td>
                <td>: <?= $payment['notes'] ?></td>
            </tr>
            <?php endif; ?>
        </table>

        <div class="footer">
            <div class="place-date">
                <?= date('d F Y') ?>
            </div>
            <div class="signature">
                _____________________<br>
                Finance Officer
            </div>
        </div>

        <div class="note">
            This is a computer-generated document. No signature is required.
        </div>
    </div>
</body>
</html>
