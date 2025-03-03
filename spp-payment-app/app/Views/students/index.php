<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Student Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-3">
        <div class="col">
            <h1>Student Management</h1>
        </div>
        <div class="col text-end">
            <a href="<?= site_url('students/create') ?>" class="btn btn-primary">Add Student</a>
        </div>
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

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>NIS</th>
                            <th>Class</th>
                            <th>Major</th>
                            <th>SPP Amount</th>
                            <th>Parent Name</th>
                            <th>Parent Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($students)): ?>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?= esc($student['name'] ?? 'N/A') ?></td>
                                    <td><?= esc($student['nis'] ?? 'N/A') ?></td>
                                    <td><?= esc($student['class'] ?? 'N/A') ?></td>
                                    <td><?= esc($student['major'] ?? 'N/A') ?></td>
                                    <td>Rp <?= number_format($student['spp_amount'] ?? 0, 0, ',', '.') ?></td>
                                    <td><?= esc($student['parent_name'] ?? 'N/A') ?></td>
                                    <td><?= esc($student['parent_phone'] ?? 'N/A') ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="<?= site_url('students/edit/' . ($student['id'] ?? '')) ?>" 
                                               class="btn btn-sm btn-warning">Edit</a>
                                            <?= form_open('students/delete/' . ($student['id'] ?? ''), ['style' => 'display:inline']) ?>
                                                <?= csrf_field() ?>
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this student?');">
                                                    Delete
                                                </button>
                                            <?= form_close() ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="8" class="text-center">No students found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
