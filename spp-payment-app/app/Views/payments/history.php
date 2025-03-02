<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Payment History<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1>Payment History</h1>
        </div>
    </div>

    <!-- Student Information Card -->
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Student Information</h5>
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>Name</strong></td>
                            <td>: <?= $student['name'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Class</strong></td>
                            <td>: <?= $student['class'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Major</strong></td>
                            <td>: <?= $student['major'] ?></td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <td width="150"><strong>Monthly SPP</strong></td>
                            <td>: Rp <?= number_format($student['spp_amount'], 0, ',', '.') ?></td>
                        </tr>
                        <tr>
                            <td><strong>Parent Name</strong></td>
                            <td>: <?= $student['parent_name'] ?></td>
                        </tr>
                        <tr>
                            <td><strong>Parent Phone</strong></td>
                            <td>: <?= $student['parent_phone'] ?></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment History Table -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Payment History</h5>
            
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Payment Date</th>
                            <th>Month/Year</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $months = [
                            1 => 'January', 2 => 'February', 3 => 'March',
                            4 => 'April', 5 => 'May', 6 => 'June',
                            7 => 'July', 8 => 'August', 9 => 'September',
                            10 => 'October', 11 => 'November', 12 => 'December'
                        ];
                        foreach ($payments as $payment): 
                        ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($payment['payment_date'])) ?></td>
                            <td><?= $months[$payment['payment_month']] ?> <?= $payment['payment_year'] ?></td>
                            <td>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
                            <td><?= ucfirst($payment['payment_method']) ?></td>
                            <td>
                                <span class="badge bg-<?= $payment['status'] == 'success' ? 'success' : 
                                    ($payment['status'] == 'pending' ? 'warning' : 'danger') ?>">
                                    <?= ucfirst($payment['status']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= site_url('payments/receipt/' . $payment['id']) ?>" 
                                   class="btn btn-sm btn-info" target="_blank">
                                    Receipt
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No payment history found</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Payment Summary -->
            <div class="mt-4">
                <h6>Payment Summary</h6>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h6 class="card-title">Total Paid</h6>
                                <h4 class="card-text">
                                    Rp <?= number_format(array_sum(array_column(
                                        array_filter($payments, fn($p) => $p['status'] == 'success'),
                                        'amount'
                                    )), 0, ',', '.') ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-warning text-dark">
                            <div class="card-body">
                                <h6 class="card-title">Pending Payments</h6>
                                <h4 class="card-text">
                                    Rp <?= number_format(array_sum(array_column(
                                        array_filter($payments, fn($p) => $p['status'] == 'pending'),
                                        'amount'
                                    )), 0, ',', '.') ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-danger text-white">
                            <div class="card-body">
                                <h6 class="card-title">Failed Payments</h6>
                                <h4 class="card-text">
                                    Rp <?= number_format(array_sum(array_column(
                                        array_filter($payments, fn($p) => $p['status'] == 'failed'),
                                        'amount'
                                    )), 0, ',', '.') ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?= site_url('payments') ?>" class="btn btn-secondary">Back to Payments</a>
    </div>
</div>
<?= $this->endSection() ?>
