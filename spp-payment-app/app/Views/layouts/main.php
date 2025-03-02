<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?> - SPP Payment System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= site_url() ?>">SPP Payment System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('dashboard') ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('students') ?>">
                            <i class="fas fa-users"></i> Students
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= site_url('payments') ?>">
                            <i class="fas fa-money-bill-wave"></i> Payments
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="reportsDropdown" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-chart-bar"></i> Reports
                        </a>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" href="<?= site_url('payments/monthly-report') ?>">
                                    Monthly Report
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= site_url('payments/annual-report') ?>">
                                    Annual Report
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= site_url('payments/unpaid') ?>">
                                    Unpaid Students
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" 
                           data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> 
                            <?= session()->get('user_name') ?? 'User' ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <a class="dropdown-item" href="<?= site_url('profile') ?>">
                                    <i class="fas fa-user-circle"></i> Profile
                                </a>
                            </li>
                            <?php if (session()->get('user_role') === 'admin'): ?>
                            <li>
                                <a class="dropdown-item" href="<?= site_url('settings') ?>">
                                    <i class="fas fa-cog"></i> Settings
                                </a>
                            </li>
                            <?php endif; ?>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?= site_url('auth/logout') ?>">
                                    <i class="fas fa-sign-out-alt"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        <?= $this->renderSection('content') ?>
    </main>

    <!-- Footer -->
    <footer class="footer mt-auto py-3 bg-light">
        <div class="container text-center">
            <span class="text-muted">SPP Payment System &copy; <?= date('Y') ?></span>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    
    <?= $this->renderSection('scripts') ?>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <script>
        $(document).ready(function() {
            showToast('Success', '<?= session()->getFlashdata('success') ?>', 'success');
        });
    </script>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <script>
        $(document).ready(function() {
            showToast('Error', '<?= session()->getFlashdata('error') ?>', 'error');
        });
    </script>
    <?php endif; ?>
</body>
</html>
