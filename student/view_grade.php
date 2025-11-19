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
    <title>View Grades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/brite/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body x-data="studentGradesApp()">
<div class=" border border-info rounded shadow p-2 m-5" style="min-height:50vh;">

    <div class="container mt-5">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="../student/menu.php" class="btn btn-danger btn-sm d-flex align-items-center">
                <i class="bi bi-box-arrow-left me-1"></i> Back
            </a>
            <h2 class="fw-bold mb-0 flex-grow-1 text-center">My Grades</h2>
            <div style="width: 65px;"></div>
        </div>


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
            <select x-model="semester" class="form-select w-50" @change="fetchGrades">
                <option value="" selected>Select a semester</option>
                <template x-for="sem in availableSemesters" :key="sem.id">
                    <option :value="sem.id" x-text="sem.code"></option>
                </template>
            </select>
        </div>

        <!-- Grades Table -->
        <template x-if="grades.length > 0">
            <div class="table-responsive mt-4">
                <table class="table table-striped">
                    <thead>
                        <tr class="table-dark ">
                            <th>Code</th>
                            <th>Description</th>
                            <th>Units</th>
                            <th>Midterm</th>
                            <th>Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="sub in grades" :key="sub.subject_id">
                            <tr>
                                <td x-text="sub.code"></td>
                                <td x-text="sub.name"></td>
                                <td x-text="sub.unit"></td>
                                <td x-text="sub.mid ? parseFloat(sub.mid).toFixed(2) : '—'"></td>
                                <td x-text="sub.fcg ? parseFloat(sub.fcg).toFixed(2) : '—'"></td>
                            </tr>
                        </template>
                    </tbody>
                    <tfoot>
                        <tr class="table-light fw-bold">
                            <td colspan="2" class="text-end">Total Units:</td>
                            <td x-text="totalUnits"></td>
                            <td class="text-end">GPA:</td>
                            <td x-text="gpa.toFixed(2)"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </template>

        <!-- No grades message -->
        <template x-if="semester && grades.length === 0">
            <div class="alert alert-warning mt-3 text-center">
                No grades found for this semester.
            </div>
        </template>
    </div>

    </div>

    <script>
        function studentGradesApp() {
            return {
                student: { id: "<?= $student_id ?>", course: "" },
                semester: "",
                availableSemesters: [],
                grades: [],
                totalUnits: 0,
                gpa: 0,

                async init() {
                    // fetch student info (includes course)
                    let info = await fetch(`../sql/grade_c.php?action=get_student&stud_no=<?= $student_number ?>`);
                    let data = await info.json();
                    if (data && !data.error) this.student = data;

                    // load available semesters
                    let sems = await fetch(`../sql/grade_c.php?action=get_semester`);
                    this.availableSemesters = await sems.json();
                },

                async fetchGrades() {
                    if (!this.semester) return;
                    let res = await fetch(`../sql/grade_c.php?action=get_semsubs&stud_no=${this.student.id}&sem_id=${this.semester}`);
                    this.grades = await res.json();
                    this.calculateGPA();
                },

                calculateGPA() {
                    let totalPoints = 0, totalUnits = 0;
                    this.grades.forEach(s => {
                        let grade = parseFloat(s.fcg);
                        let unit = parseInt(s.unit || 0);
                        if (!isNaN(grade) && grade >= 1.00 && grade <= 5.00) {
                            totalPoints += grade * unit;
                            totalUnits += unit;
                        }
                    });
                    this.totalUnits = totalUnits;
                    this.gpa = totalUnits > 0 ? totalPoints / totalUnits : 0;
                }
            };
        }
    </script>

</body>

</html>