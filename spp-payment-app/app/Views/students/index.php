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
            <?php foreach ($students as $student): ?>
            <tr>
                <td><?= $student['name'] ?></td>
                <td><?= $student['class'] ?></td>
                <td><?= $student['major'] ?></td>
                <td><?= $student['spp_amount'] ?></td>
                <td>
                    <a href="<?= site_url('students/edit/' . $student['id']) ?>" class="btn btn-warning">Edit</a>
                    <form action="<?= site_url('students/delete/' . $student['id']) ?>" method="POST" style="display:inline;">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?>
