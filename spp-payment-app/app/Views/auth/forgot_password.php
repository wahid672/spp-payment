<?= $this->extend('layouts/auth') ?>

<?= $this->section('title') ?>Lupa Password<?= $this->endSection() ?>

<?= $this->section('header') ?>
    <h4>Lupa Password</h4>
    <p class="text-muted">Masukkan email Anda untuk mereset password</p>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <form action="<?= base_url('auth/reset-password') ?>" method="post">
        <?= csrf_field() ?>
        
        <div class="form-floating mb-4">
            <input type="email" 
                   class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>" 
                   id="email" 
                   name="email" 
                   placeholder="Email"
                   value="<?= old('email') ?>"
                   required>
            <label for="email">Email</label>
            <?php if (session('errors.email')): ?>
                <div class="invalid-feedback">
                    <?= session('errors.email') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fas fa-paper-plane me-2"></i> Kirim Link Reset
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
