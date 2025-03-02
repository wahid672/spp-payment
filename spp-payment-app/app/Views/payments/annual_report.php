<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Annual Payment Report<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1>Annual Payment Report</h1>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?= site_url('payments/annual-report') ?>" method="GET" class="row g-3">
                <div class="col-md-8">
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

    <!-- Report Chart -->
    <div class="card mb-4">
        <div class="card-body">
            <canvas id="annualChart" height="100"></canvas>
        </div>
    </div>

    <!-- Report Table -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Total Payments</th>
                            <th>Total Students</th>
                            <th>Average per Student</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalPayments = 0;
                        $totalStudents = 0;
                        foreach ($report as $data): 
                            $totalPayments += $data['total_success'];
                            $totalStudents = max($totalStudents, $data['total_students']);
                        ?>
                        <tr>
                            <td><?= date('F', mktime(0, 0, 0, $data['payment_month'], 1)) ?></td>
                            <td>Rp <?= number_format($data['total_success'], 0, ',', '.') ?></td>
                            <td><?= $data['total_students'] ?></td>
                            <td>Rp <?= $data['total_students'] > 0 ? 
                                number_format($data['total_success'] / $data['total_students'], 0, ',', '.') : 
                                0 ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-primary">
                            <th>Total</th>
                            <th>Rp <?= number_format($totalPayments, 0, ',', '.') ?></th>
                            <th><?= $totalStudents ?></th>
                            <th>Rp <?= $totalStudents > 0 ? 
                                number_format($totalPayments / $totalStudents, 0, ',', '.') : 
                                0 ?></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Export Report</h5>
            <div class="btn-group">
                <a href="<?= site_url('payments/export-report/annual') ?>?year=<?= $year ?>&format=xlsx" 
                   class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </a>
                <a href="<?= site_url('payments/export-report/annual') ?>?year=<?= $year ?>&format=pdf" 
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
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('annualChart').getContext('2d');
    
    // Prepare data for the chart
    const months = <?= json_encode(array_map(function($data) {
        return date('F', mktime(0, 0, 0, $data['payment_month'], 1));
    }, $report)) ?>;
    
    const payments = <?= json_encode(array_map(function($data) {
        return $data['total_success'];
    }, $report)) ?>;

    const students = <?= json_encode(array_map(function($data) {
        return $data['total_students'];
    }, $report)) ?>;

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Total Payments (Rp)',
                data: payments,
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1,
                yAxisID: 'y'
            }, {
                label: 'Total Students',
                data: students,
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                type: 'line',
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Total Payments (Rp)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Total Students'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
});
</script>
<?= $this->endSection() ?>
