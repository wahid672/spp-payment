<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Add Student<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <h1>Add Student</h1>
    <form action="<?= site_url('students/store') ?>" method="POST">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Class</label>
            <input type="text" name="class" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Major</label>
            <input type="text" name="major" class="form-control" required>
        </div>
        <div class="form-group">
            <label>SPP Amount</label>
            <input type="number" name="spp_amount" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Parent Name</label>
            <input type="text" name="parent_name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Parent Phone</label>
            <input type="text" name="parent_phone" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-success">Add Student</button>
    </form>
</div>
<?= $this->endSection() ?>
