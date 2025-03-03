<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Today's Payments</div>
                            <div class="text-lg fw-bold">Rp <?= number_format($stats['total_today'] ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <i class="fas fa-calendar fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?= site_url('payments') ?>">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Monthly Revenue</div>
                            <div class="text-lg fw-bold">Rp <?= number_format($stats['total_month'] ?? 0, 0, ',', '.') ?></div>
                        </div>
                        <i class="fas fa-money-bill-wave fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?= site_url('payments/monthly-report') ?>">View Report</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Unpaid Students</div>
                            <div class="text-lg fw-bold"><?= $unpaidCount ?? 0 ?> students</div>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?= site_url('payments/unpaid') ?>">View List</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="me-3">
                            <div class="text-white-75 small">Total Students</div>
                            <div class="text-lg fw-bold"><?= $totalStudents ?? 0 ?> students</div>
                        </div>
                        <i class="fas fa-users fa-2x text-white-50"></i>
                    </div>
                </div>
                <div class="card-footer d-flex align-items-center justify-content-between">
                    <a class="small text-white stretched-link" href="<?= site_url('students') ?>">View Details</a>
                    <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Trends Chart -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-chart-line me-1"></i>
                    Payment Trends
                </div>
                <div class="card-body">
                    <canvas id="paymentTrendsChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-list me-1"></i>
                    Recent Payments
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <?php if (!empty($recentPayments)): ?>
                            <?php foreach ($recentPayments as $payment): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?= esc($payment['student_name'] ?? 'Unknown Student') ?></h6>
                                        <small>
                                            <?= date('d/m/Y', strtotime($payment['payment_date'] ?? 'now')) ?>
                                        </small>
                                    </div>
                                    <p class="mb-1">Rp <?= number_format($payment['amount'] ?? 0, 0, ',', '.') ?></p>
                                    <small class="text-muted">
                                        Class <?= esc($payment['class'] ?? '-') ?> - 
                                        <?= ucfirst($payment['payment_method'] ?? 'unknown') ?>
                                    </small>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="list-group-item text-center text-muted">
                                No recent payments
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="<?= site_url('payments') ?>" class="btn btn-primary btn-sm">View All Payments</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bolt me-1"></i>
                    Quick Actions
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="<?= site_url('payments/create') ?>" class="btn btn-primary btn-lg w-100 mb-3">
                                <i class="fas fa-plus-circle"></i> Record Payment
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= site_url('students/create') ?>" class="btn btn-success btn-lg w-100 mb-3">
                                <i class="fas fa-user-plus"></i> Add Student
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= site_url('payments/monthly-report') ?>" class="btn btn-info btn-lg w-100 mb-3">
                                <i class="fas fa-chart-bar"></i> Monthly Report
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= site_url('payments/unpaid') ?>" class="btn btn-warning btn-lg w-100 mb-3">
                                <i class="fas fa-exclamation-circle"></i> Unpaid List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Payment Trends Chart
    const trendsData = <?= json_encode($trends ?? []) ?>;
    
    try {
        const chartData = {
            labels: Array.isArray(trendsData) ? trendsData.map(item => (item.month || 'Unknown') + ' ' + (item.year || '')) : [],
            datasets: [{
                label: 'Monthly Revenue',
                data: Array.isArray(trendsData) ? trendsData.map(item => parseFloat(item.total) || 0) : [],
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        };

        new Chart(document.getElementById('paymentTrendsChart'), {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Payment Trends (Last 6 Months)'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + (value || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                            }
                        }
                    }
                }
            }
        });
    } catch (error) {
        console.error('Error initializing chart:', error);
        document.getElementById('paymentTrendsChart').innerHTML = 'Error loading chart data';
    }
});
</script>
<?= $this->endSection() ?>
