<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Edit Student<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <h1>Edit Student</h1>
    <form action="<?= site_url('students/update/' . $student['id']) ?>" method="POST">
        <div class="form-group">
            <label>Name</label>
            <input type="text" name="name" class="form-control" value="<?= $student['name'] ?>" required>
        </div>
        <div class="form-group">
            <label>Class</label>
            <input type="text" name="class" class="form-control" value="<?= $student['class'] ?>" required>
        </div>
        <div class="form-group">
            <label>Major</label>
            <input type="text" name="major" class="form-control" value="<?= $student['major'] ?>" required>
        </div>
        <div class="form-group">
            <label>SPP Amount</label>
            <input type="number" name="spp_amount" class="form-control" value="<?= $student['spp_amount'] ?>" required>
        </div>
        <div class="form-group">
            <label>Parent Name</label>
            <input type="text" name="parent_name" class="form-control" value="<?= $student['parent_name'] ?>" required>
        </div>
        <div class="form-group">
            <label>Parent Phone</label>
            <input type="text" name="parent_phone" class="form-control" value="<?= $student['parent_phone'] ?>" required>
        </div>
        <button type="submit" class="btn btn-success">Update Student</button>
    </form>
</div>
<?= $this->endSection() ?>
