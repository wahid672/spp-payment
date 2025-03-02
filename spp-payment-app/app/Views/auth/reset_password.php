<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>Reset Password<?= $this->endSection() ?>

<?= $this->section('header') ?>
    <h4>Reset Password</h4>
    <p class="text-muted">Masukkan password baru Anda</p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <form action="<?= base_url('auth/update-password/' . $token) ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="form-floating mb-3">
            <input type="password" 
                   class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>" 
                   id="password" 
                   name="password" 
                   placeholder="Password Baru"
                   required>
            <label for="password">Password Baru</label>
            <?php if (session('errors.password')): ?>
                <div class="invalid-feedback">
                    <?= session('errors.password') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="form-floating mb-4">
            <input type="password" 
                   class="form-control <?= session('errors.password_confirm') ? 'is-invalid' : '' ?>" 
                   id="password_confirm" 
                   name="password_confirm" 
                   placeholder="Konfirmasi Password"
                   required>
            <label for="password_confirm">Konfirmasi Password</label>
            <?php if (session('errors.password_confirm')): ?>
                <div class="invalid-feedback">
                    <?= session('errors.password_confirm') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-key me-2"></i> Reset Password
            </button>
        </div>
    </form>
<?= $this->endSection() ?>

<?= $this->section('footer') ?>
    <p class="mb-0">
        <a href="<?= base_url('auth/login') ?>" class="text-muted">
            <i class="fas fa-arrow-left me-1"></i> Kembali ke Login
        </a>
    </p>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password strength indicator
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('password_confirm');
    
    function checkPasswordMatch() {
        if (confirmPassword.value === '') {
            confirmPassword.classList.remove('is-valid', 'is-invalid');
            return;
        }
        
        if (password.value === confirmPassword.value) {
            confirmPassword.classList.remove('is-invalid');
            confirmPassword.classList.add('is-valid');
        } else {
            confirmPassword.classList.remove('is-valid');
            confirmPassword.classList.add('is-invalid');
        }
    }

    password.addEventListener('input', checkPasswordMatch);
    confirmPassword.addEventListener('input', checkPasswordMatch);

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});
</script>
<?= $this->endSection() ?>
