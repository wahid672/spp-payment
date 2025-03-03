<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Add Student<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Add Student</h1>
        </div>
        <div class="col text-end">
            <a href="<?= site_url('students') ?>" class="btn btn-secondary">Back to List</a>
        </div>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <?= form_open('students/store') ?>
                <div class="mb-3">
                    <label for="name" class="form-label">Student Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="class" class="form-label">Class</label>
                            <select class="form-select" id="class" name="class" required>
                                <option value="">Select Class</option>
                                <option value="X" <?= old('class') == 'X' ? 'selected' : '' ?>>X</option>
                                <option value="XI" <?= old('class') == 'XI' ? 'selected' : '' ?>>XI</option>
                                <option value="XII" <?= old('class') == 'XII' ? 'selected' : '' ?>>XII</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="major" class="form-label">Major</label>
                            <select class="form-select" id="major" name="major" required>
                                <option value="">Select Major</option>
                                <option value="RPL" <?= old('major') == 'RPL' ? 'selected' : '' ?>>RPL</option>
                                <option value="TKJ" <?= old('major') == 'TKJ' ? 'selected' : '' ?>>TKJ</option>
                                <option value="MM" <?= old('major') == 'MM' ? 'selected' : '' ?>>MM</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="spp_amount" class="form-label">SPP Amount</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="number" class="form-control" id="spp_amount" name="spp_amount" 
                               value="<?= old('spp_amount', '500000') ?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="parent_name" class="form-label">Parent Name</label>
                    <input type="text" class="form-control" id="parent_name" name="parent_name" 
                           value="<?= old('parent_name') ?>" required>
                </div>

                <div class="mb-3">
                    <label for="parent_phone" class="form-label">Parent Phone</label>
                    <input type="tel" class="form-control" id="parent_phone" name="parent_phone" 
                           value="<?= old('parent_phone') ?>" required>
                    <div class="form-text">Example: 08123456789</div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary">Add Student</button>
                </div>
            <?= form_close() ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
