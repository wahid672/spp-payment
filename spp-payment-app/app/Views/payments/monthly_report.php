<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Monthly Payment Report<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1>Monthly Payment Report</h1>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?= site_url('payments/monthly-report') ?>" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        <?php 
                        $months = [
                            1 => 'January', 2 => 'February', 3 => 'March',
                            4 => 'April', 5 => 'May', 6 => 'June',
                            7 => 'July', 8 => 'August', 9 => 'September',
                            10 => 'October', 11 => 'November', 12 => 'December'
                        ];
                        foreach ($months as $num => $name): ?>
                            <option value="<?= $num ?>" <?= $month == $num ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                            <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>>
                                <?= $y ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">View Report</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Successful Payments</h6>
                    <h4 class="card-text">Rp <?= number_format($report['total_success'], 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h6 class="card-title">Total Pending Payments</h6>
                    <h4 class="card-text">Rp <?= number_format($report['total_pending'], 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Failed Payments</h6>
                    <h4 class="card-text">Rp <?= number_format($report['total_failed'], 0, ',', '.') ?></h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">Total Students</h6>
                    <h4 class="card-text"><?= $report['total_students'] ?></h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Export Report</h5>
            <div class="btn-group">
                <a href="<?= site_url('payments/export-report/monthly') ?>?month=<?= $month ?>&year=<?= $year ?>&format=xlsx" 
                   class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </a>
                <a href="<?= site_url('payments/export-report/monthly') ?>?month=<?= $month ?>&year=<?= $year ?>&format=pdf" 
                   class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export to PDF
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Add any additional JavaScript for charts or interactivity here
</script>
<?= $this->endSection() ?>
