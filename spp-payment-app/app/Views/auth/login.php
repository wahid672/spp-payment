<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>Login<?= $this->endSection() ?>

<?= $this->section('header') ?>
    <div class="logo-container">
        SPP<br>Pay
    </div>
    <h4>SPP Payment System</h4>
    <p class="text-muted">Silakan login untuk melanjutkan</p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <form action="<?= base_url('auth/authenticate') ?>" method="post" autocomplete="off">
        <?= csrf_field() ?>
        
        <div class="form-floating mb-3">
            <input type="text" 
                   class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>" 
                   id="username" 
                   name="username" 
                   placeholder="Username atau Email"
                   value="<?= old('username') ?>"
                   autocomplete="username"
                   required>
            <label for="username">Username atau Email</label>
            <?php if (session('errors.username')): ?>
                <div class="invalid-feedback">
                    <?= session('errors.username') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-floating mb-4">
            <input type="password" 
                   class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                   id="password" 
                   name="password" 
                   placeholder="Password"
                   autocomplete="current-password"
                   required>
            <label for="password">Password</label>
            <?php if (session('errors.password')): ?>
                <div class="invalid-feedback">
                    <?= session('errors.password') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-sign-in-alt me-2"></i> Login
            </button>
        </div>
    </form>
<?= $this->endSection() ?>

<?= $this->section('footer') ?>
    <p class="mb-0">
        <a href="<?= base_url('auth/forgot-password') ?>" class="text-muted">
            <i class="fas fa-lock me-1"></i> Lupa password?
        </a>
    </p>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    // Focus username field on load
    document.getElementById('username').focus();
});
</script>
<?= $this->endSection() ?>
