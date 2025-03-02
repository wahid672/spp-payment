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
    
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .auth-card {
            width: 100%;
            max-width: 400px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            background-color: #fff;
        }
        .auth-header {
            text-align: center;
            padding: 2rem;
            border-bottom: 1px solid #eee;
        }
        .auth-body {
            padding: 2rem;
        }
        .auth-footer {
            text-align: center;
            padding: 1.5rem;
            border-top: 1px solid #eee;
            background-color: #f8f9fa;
            border-bottom-left-radius: 15px;
            border-bottom-right-radius: 15px;
        }
        .logo-container {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: #007bff;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: bold;
            line-height: 1.2;
            text-align: center;
        }
        .form-floating {
            margin-bottom: 1rem;
        }
        .form-floating:last-of-type {
            margin-bottom: 1.5rem;
        }
        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }
        .btn-primary {
            padding: 0.8rem;
            font-weight: 500;
        }
        .alert {
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        .text-muted {
            color: #6c757d !important;
        }
        .auth-header h4 {
            margin-bottom: 0.5rem;
            color: #343a40;
        }
        .auth-header p {
            margin-bottom: 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <?= $this->renderSection('header') ?>
            </div>
            
            <div class="auth-body">
                <?php if (session()->has('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= session('success') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?= $this->renderSection('content') ?>
            </div>
            
            <div class="auth-footer">
                <?= $this->renderSection('footer') ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom Scripts -->
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
