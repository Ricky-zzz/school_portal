<?php
require("../partials/session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Batch Grade Printing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/lux/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
    <?php
    $activePage = 'bacthPrint';
    include "../partials/navbar.php";
    ?>

    <div class="container mt-4" x-data="batchPrintApp()">
        <div class="my-3 d-flex align-items-center">
            <h2 class="mb-0 me-3">Batch Printing</h2>
            <a href="../menu.php" class="btn btn-danger btn d-flex align-items-center">
                <i class="bi bi-box-arrow-left me-1"></i> Back
            </a>
        </div>

        <?php
        include "../partials/alert.php";
        ?>

        <!-- Filter Controls -->
        <div class="row my-3">
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Semester:</label>
                <select x-model="semester" class="form-select">
                    <option value="all">All</option>
                    <template x-for="sem in semesters" :key="sem.id">
                        <option :value="sem.id" x-text="sem.code"></option>
                    </template>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Course:</label>
                <select x-model="course" class="form-select">
                    <option value="all">All</option>
                    <template x-for="crs in courses" :key="crs.course_id">
                        <option :value="crs.course_id" x-text="crs.name"></option>
                    </template>
                </select>
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Subject:</label>
                <select x-model="subject" class="form-select">
                    <option value="all">All</option>
                    <template x-for="sub in subjects" :key="sub.subject_id">
                        <option :value="sub.subject_id" x-text="sub.name"></option>
                    </template>
                </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100" @click="fetchBatchGrades()">Filter</button>
            </div>
        </div>

        <!-- Results Table -->
        <template x-if="grades.length > 0">
            <div class="table-responsive mt-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5>Filtered Student Grades</h5>
                    <a href="../prints/batch_grades.php" target="_blank"
                        class="btn btn-info text-white rounded-pill px-4 py-2">Print</a>
                </div>

                <table class="table table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Student #</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Course</th>
                            <th>Semester</th>
                            <th>Subject</th>
                            <th>Midterm</th>
                            <th>Final Grade</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in grades" :key="row.id">
                            <tr>
                                <td x-text="row.stud_no"></td>
                                <td x-text="row.name"></td>
                                <td x-text="row.gender"></td>
                                <td x-text="row.course"></td>
                                <td x-text="row.semester"></td>
                                <td x-text="row.subject_name"></td>
                                <td x-text="row.mid ?? 'N/A'"></td>
                                <td x-text="row.fcg ?? 'N/A'"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </template>

        <template x-if="grades.length === 0 && !loading">
            <p class="text-muted mt-4">No data found. Try adjusting filters.</p>
        </template>
    </div>

    <script>
        function batchPrintApp() {
            return {
                semesters: [],
                courses: [],
                subjects: [],
                semester: 'all',
                course: 'all',
                subject: 'all',
                grades: [],
                alert: null,
                loading: false,

                async init() {

                    let allSemesters = await fetch('../sql/print_actions.php?action=get_semester');
                    this.semesters = await allSemesters.json();

                    let allCourses = await fetch('../sql/print_actions.php?action=get_courses');
                    this.courses = await allCourses.json();

                    let allSubjects = await fetch('../sql/print_actions.php?action=get_subject');
                    this.subjects = await allSubjects.json();
                },

                async fetchBatchGrades() {
                    this.loading = true;
                    this.alert = 'Fetching records...';
                    let url = `../sql/print_actions.php?action=get_batch_grades&semester=${this.semester}&course=${this.course}&subject=${this.subject}`;
                    let res = await fetch(url);
                    this.grades = await res.json();
                    this.alert = `Found ${this.grades.length} records.`;
                    this.loading = false;
                }
            };
        }
    </script>
</body>

</html>