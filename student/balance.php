<?php
require("../partials/student.php");
$student_name = $_SESSION['student_name'] ?? '';
$student_number = $_SESSION['student_number'] ?? '';
$student_id = $_SESSION['student_id'] ?? ''; 
$student_course = $_SESSION['course'] ?? 'N/A'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Balance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/cosmo/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body x-data="balanceApp()">
<div class="border border-info rounded shadow p-2 m-5" style="min-height:50vh;">

    <div class="container mt-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="../student/menu.php" class="btn btn-danger btn-sm d-flex align-items-center">
                <i class="bi bi-box-arrow-left me-1"></i> Back
            </a>
            <h2 class="fw-bold mb-0 flex-grow-1 text-center">My Balance</h2>
            <div style="width: 65px;"></div>
        </div>

        <!-- Student Info -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white fw-bold">
                Student Information
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Student #</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($student_number) ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Name</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($student_name) ?>" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="fw-bold">Course</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($student_course) ?>" readonly>
                    </div>
                </div>
            </div>
        </div>

        <!-- Semester Selection -->
        <div class="mb-3">
            <label class="form-label fw-bold">Select Semester</label>
            <select x-model="semester" class="form-select w-50" @change="fetchBalances">
                <option value="">Select a semester</option>
                <template x-for="sem in availableSemesters" :key="sem.id">
                    <option :value="sem.id" x-text="sem.code"></option>
                </template>
            </select>
        </div>

        <!-- Payment Table -->
        <template x-if="balances.payments.length > 0">
            <div class="table-responsive mt-4">
                <table class="table table-striped">
                    <thead>
                        <tr class="table-dark text-center">
                            <th>OR #</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="p in balances.payments" :key="p.or_number">
                            <tr class="text-center">
                                <td x-text="p.or_number"></td>
                                <td x-text="new Date(p.or_date).toLocaleDateString()"></td>
                                <td x-text="p.cash > 0 ? 'Cash' : 'GCash'"></td>
                                <td x-text="Number(p.cash > 0 ? p.cash : p.gcash).toFixed(2)"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold text-end">
                            <td colspan="3">Total Tuition:</td>
                            <td x-text="balances.tuition.toFixed(2)"></td>
                        </tr>
                        <tr class="table-light fw-bold text-end">
                            <td colspan="3">Total Payments:</td>
                            <td x-text="balances.total_paid.toFixed(2)"></td>
                        </tr>
                        <tr class="table-info fw-bold text-end">
                            <td colspan="3">Remaining Balance:</td>
                            <td x-text="balances.remaining.toFixed(2)"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </template>

        <!-- No Payments Message -->
        <template x-if="semester && balances.payments.length === 0">
            <div class="alert alert-warning mt-3 text-center">
                No payments found for this semester.
            </div>
        </template>
    </div>
</div>

<script>
function balanceApp() {
    return {
        studentId: "<?= $student_id ?>",
        semester: "",
        availableSemesters: [],
        balances: { tuition: 0, total_paid: 0, remaining: 0, payments: [] },

        async init() {
            const sems = await fetch(`../sql/grade_c.php?action=get_semester`);
            this.availableSemesters = await sems.json();
        },

        async fetchBalances() {
            if (!this.semester) return;
            const res = await fetch(`../sql/collection_c.php?action=get_balances&stud_id=${this.studentId}&sem_id=${this.semester}`);
            const data = await res.json();
            if (!data.error) this.balances = data;
        }
    };
}
</script>
</body>
</html>
