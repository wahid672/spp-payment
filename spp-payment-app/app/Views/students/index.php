<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Student Management<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <h1>Student Management</h1>
    
    <div class="row mb-3">
        <div class="col-md-6">
            <a href="<?= site_url('students/create') ?>" class="btn btn-primary">Add Student</a>
            <a href="<?= site_url('students/export') ?>" class="btn btn-success">Export Students</a>
        </div>
        <div class="col-md-6">
            <form action="<?= site_url('students/import') ?>" method="POST" enctype="multipart/form-data" class="form-inline justify-content-end">
                <div class="input-group">
                    <input type="file" name="student_file" class="form-control" accept=".xlsx,.xls,.csv" required>
                    <button type="submit" class="btn btn-info">Import Students</button>
                </div>
            </form>
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
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Class</th>
                <th>Major</th>
                <th>SPP Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($students)): ?>
                <?php foreach ($students as $student): ?>
                <tr>
                    <td><?= esc($student['name'] ?? 'N/A') ?></td>
                    <td><?= esc($student['class'] ?? 'N/A') ?></td>
                    <td><?= esc($student['major'] ?? 'N/A') ?></td>
                    <td>Rp <?= number_format($student['spp_amount'] ?? 0, 0, ',', '.') ?></td>
                    <td>
                        <a href="<?= site_url('students/edit/' . ($student['id'] ?? '')) ?>" class="btn btn-warning">Edit</a>
                        <form action="<?= site_url('students/delete/' . ($student['id'] ?? '')) ?>" method="POST" style="display:inline;">
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this student?');">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No students found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
