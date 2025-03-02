<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Unpaid Students Report<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h1>Unpaid Students Report</h1>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="<?= site_url('payments/unpaid') ?>" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select">
                        <?php 
                        $months = [
                            1 => 'January', 2 => 'February', 3 => 'March',
                            4 => 'April', 5 => 'May', 6 => 'June',
                            7 => 'July', 8 => 'August', 9 => 'September',
                            10 => 'October', 11 => 'November', 12 => 'December'
                        ];
                        foreach ($months as $num => $name): ?>
                            <option value="<?= $num ?>" <?= $month == $num ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select">
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                            <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>>
                                <?= $y ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">View Report</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5>Report Period</h5>
                    <p class="mb-0"><?= $months[$month] ?> <?= $year ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <h5>Total Unpaid Students</h5>
                    <p class="mb-0"><?= count($students) ?> students</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped" id="unpaidTable">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Parent Name</th>
                            <th>Parent Phone</th>
                            <th>SPP Amount</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                        <tr>
                            <td><?= $student['name'] ?></td>
                            <td><?= $student['class'] ?></td>
                            <td><?= $student['parent_name'] ?></td>
                            <td><?= $student['parent_phone'] ?></td>
                            <td>Rp <?= number_format($student['spp_amount'], 0, ',', '.') ?></td>
                            <td>
                                <div class="btn-group">
                                    <a href="<?= site_url('payments/create?student_id=' . $student['id']) ?>" 
                                       class="btn btn-sm btn-primary">
                                        Record Payment
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-warning send-reminder" 
                                            data-student="<?= htmlspecialchars(json_encode($student)) ?>">
                                        Send Reminder
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="6" class="text-center">No unpaid students found for this period</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Export Options -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Export Report</h5>
            <div class="btn-group">
                <a href="<?= site_url('payments/export-report/unpaid') ?>?month=<?= $month ?>&year=<?= $year ?>&format=xlsx" 
                   class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </a>
                <a href="<?= site_url('payments/export-report/unpaid') ?>?month=<?= $month ?>&year=<?= $year ?>&format=pdf" 
                   class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export to PDF
                </a>
                <button type="button" class="btn btn-warning" id="sendAllReminders">
                    <i class="fas fa-bell"></i> Send Reminders to All
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reminder Modal -->
<div class="modal fade" id="reminderModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Send Payment Reminder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to send a payment reminder to:</p>
                <p class="student-info mb-0"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="sendReminderBtn">Send Reminder</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = new bootstrap.Modal(document.getElementById('reminderModal'));
    let selectedStudent = null;

    // Handle individual reminder button clicks
    document.querySelectorAll('.send-reminder').forEach(button => {
        button.addEventListener('click', function() {
            selectedStudent = JSON.parse(this.dataset.student);
            document.querySelector('.student-info').textContent = 
                `${selectedStudent.name} (Class ${selectedStudent.class})`;
            modal.show();
        });
    });

    // Handle send reminder confirmation
    document.getElementById('sendReminderBtn').addEventListener('click', function() {
        if (selectedStudent) {
            // Send reminder via WhatsApp
            const message = `Dear ${selectedStudent.parent_name},\n\n` +
                          `This is a reminder that the SPP payment for ${selectedStudent.name} ` +
                          `for ${document.querySelector('[name="month"] option:checked').text} ${document.querySelector('[name="year"]').value} ` +
                          `(Rp ${parseInt(selectedStudent.spp_amount).toLocaleString()}) is due.\n\n` +
                          `Please make the payment as soon as possible.\n\n` +
                          `Thank you.`;

            // You might want to implement this as an AJAX call to your server
            console.log('Sending reminder:', message);
            
            // Close modal after sending
            modal.hide();
            
            // Show success message
            alert('Reminder sent successfully!');
        }
    });

    // Handle send all reminders button
    document.getElementById('sendAllReminders').addEventListener('click', function() {
        if (confirm('Are you sure you want to send reminders to all unpaid students?')) {
            const students = <?= json_encode($students) ?>;
            console.log('Sending reminders to:', students);
            alert(`Reminders sent to ${students.length} students!`);
        }
    });
});
</script>
<?= $this->endSection() ?>
