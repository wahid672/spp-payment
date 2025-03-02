<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Payment Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <h1>Payment Management</h1>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?= site_url('payments') ?>" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Class</label>
                    <input type="text" name="class" class="form-control" value="<?= $filter['class'] ?? '' ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        <option value="">All Months</option>
                        <?php 
                        $months = [
                            '1' => 'January', '2' => 'February', '3' => 'March',
                            '4' => 'April', '5' => 'May', '6' => 'June',
                            '7' => 'July', '8' => 'August', '9' => 'September',
                            '10' => 'October', '11' => 'November', '12' => 'December'
                        ];
                        foreach ($months as $num => $name): ?>
                            <option value="<?= $num ?>" <?= ($filter['month'] ?? '') == $num ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="success" <?= ($filter['status'] ?? '') == 'success' ? 'selected' : '' ?>>Success</option>
                        <option value="pending" <?= ($filter['status'] ?? '') == 'pending' ? 'selected' : '' ?>>Pending</option>
                        <option value="failed" <?= ($filter['status'] ?? '') == 'failed' ? 'selected' : '' ?>>Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="mb-3">
        <a href="<?= site_url('payments/create') ?>" class="btn btn-primary">Record Payment</a>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Payments Table -->
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Amount</th>
                    <th>Month/Year</th>
                    <th>Payment Date</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments as $payment): ?>
                <tr>
                    <td><?= $payment['student_name'] ?></td>
                    <td><?= $payment['class'] ?></td>
                    <td>Rp <?= number_format($payment['amount'], 0, ',', '.') ?></td>
                    <td><?= $months[$payment['payment_month']] ?> <?= $payment['payment_year'] ?></td>
                    <td><?= date('d/m/Y', strtotime($payment['payment_date'])) ?></td>
                    <td><?= ucfirst($payment['payment_method']) ?></td>
                    <td>
                        <span class="badge bg-<?= $payment['status'] == 'success' ? 'success' : ($payment['status'] == 'pending' ? 'warning' : 'danger') ?>">
                            <?= ucfirst($payment['status']) ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= site_url('payments/receipt/' . $payment['id']) ?>" class="btn btn-sm btn-info" target="_blank">
                            Receipt
                        </a>
                        <a href="<?= site_url('payments/history?student_id=' . $payment['student_id']) ?>" class="btn btn-sm btn-secondary">
                            History
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($payments)): ?>
                <tr>
                    <td colspan="8" class="text-center">No payments found</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= $this->endSection() ?>
