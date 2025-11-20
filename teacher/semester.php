<?php
require("../partials/teacher.php");
$teacher_name = $_SESSION['teacher_name'] ?? 'Proffesor';
$teacher_code = $_SESSION['teacher_code'] ?? 'N/A';
$teacher_id = $_SESSION['teacher_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teacher Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/simplex/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="d-flex flex-column align-items-center min-vh-100" x-data="gradingApp" x-init="init()">

    <?php include "../partials/alert.php"; ?>

    <div class="menu-container bg-light rounded shadow p-4 mt-3 w-75">
        <div class="container mt-2">
            <div class="row mb-4 align-items-center">
                <div class="col-md-6 d-flex align-items-center gap-2">
                    <a href="menu.php" class="btn btn-danger btn-sm d-flex align-items-center">
                        <i class="bi bi-box-arrow-left me-1"></i> Back
                    </a>
                    <h2 class="fw-bold mb-0 ms-2">Student Grading</h2>
                </div>

                <div class="col-md-6 text-end fw-bold">
                    Logged in as:
                    <span class="text-primary"><?= htmlspecialchars($teacher_name) ?></span>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white fw-bold">
                    Grading Information
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <template x-if="availableSemesters.length > 0">
                                <div class="card menu-card shadow-sm text-center p-4 border-dark mb-2">
                                    <div class="mb-2">
                                        <label for="semester-select" class="form-label text-dark fw-bold d-flex align-items-center mb-1">
                                            <i class="bi bi-calendar-check me-2"></i> Select Semester:
                                        </label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text bg-dark text-white border-dark">
                                                <i class="bi bi-list-task"></i>
                                            </span>
                                            <select
                                                x-model="semester"
                                                class="form-select form-control-lg border-dark"
                                                @change="fetchSubjects"
                                                aria-label="Semester selection dropdown">
                                                <option value="">-- Choose a Semester --</option>
                                                <template x-for="sem in availableSemesters" :key="sem.id">
                                                    <option :value="sem.id" x-text="sem.code"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="col-md-4 mb-3">
                            <template x-if="semSubs.length > 0">
                                <div class="card menu-card shadow-sm text-center p-4 border-dark mb-2">
                                    <div class="mb-2">
                                        <label for="subject-select" class="form-label text-dark fw-bold d-flex align-items-center mb-1">
                                            <i class="bi bi-book me-2"></i> Select Subject:
                                        </label>
                                        <div class="input-group has-validation">
                                            <span class="input-group-text bg-dark text-white border-dark">
                                                <i class="bi bi-list-task"></i>
                                            </span>
                                            <select
                                                x-model="subject"
                                                class="form-select form-control-lg border-dark"
                                                aria-label="Subject selection dropdown"
                                                @change="fetchGrades">
                                                <option value="">-- Choose a Subject --</option>
                                                <template x-for="sub in semSubs" :key="sub.subject_id">
                                                    <option :value="sub.subject_id" x-text="sub.subject_code"></option>
                                                </template>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="col-md-4 mb-3" x-show="subject">
                            <div class="card menu-card shadow-sm text-center p-4 border-dark mb-2">
                                <label class="form-label text-dark fw-bold d-flex align-items-center mb-1">
                                    <i class="bi bi-gear me-2"></i> Select Period:
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-dark text-white border-dark">
                                        <i class="bi bi-sliders"></i>
                                    </span>
                                    <select
                                        x-model="mode"
                                        class="form-select form-control-lg border-dark"
                                        @change="fetchGrades">
                                        <option value="">-- Choose Mode --</option>
                                        <option value="mid">Midterms</option>
                                        <option value="fcg">Final Course Grade</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Tables -->
    <div class="menu-container bg-light rounded shadow p-5 mt-3 w-75"
        x-show="mode && subject">


        <div class="container mt-1">
            <div class="row mb-4 align-items-center">
                <div class="col-md-6 d-flex align-items-center justify-content-between w-100">
                    <p class="fs-4 fw-bold mb-0 d-flex align-items-center flex-wrap">
                        <span>Grading Students for:</span>
                        <span class="text-primary mx-1" x-text="availableSemesters.find(s => s.id == semester)?.code || 'N/A'"></span>
                        <i class="bi bi-arrow-right mx-1"></i>
                        <span class="text-primary mx-1" x-text="semSubs.find(s => s.subject_id == subject)?.subject_code || 'N/A'"></span>
                        <i class="bi bi-arrow-right mx-1"></i>
                        <span class="text-primary mx-1" x-text="mode === 'mid' ? 'Midterms' : mode === 'fcg' ? 'Final Course Grade' : 'N/A'"></span>
                    </p>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-info" @click="pasteGrades('female')">Paste Female Grades</button>
                        <button type="button" class="btn btn-success" @click="pasteGrades('male')">Paste Male Grades</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Female Students Table -->
        <template x-if="female_students.length > 0">
            <div class="mt-3">
                <h5 class="text-info fw-bold">Female Students</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Student No.</th>
                                <th>Student Name</th>
                                <th>Update Grade</th>
                                <th x-text="mode === 'mid' ? 'Midterm' : mode === 'fcg' ? 'Final Course Grade' : 'Grade'"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(student, index) in female_students" :key="student.student_id || index">
                                <tr>
                                    <td x-text="student.stud_no"></td>
                                    <td x-text="student.name"></td>
                                    <td>
                                        <select class="form-select" x-model="student.gradePreview" @change="confirmGrade(student)">
                                            <option value="">-- Select Grade --</option>
                                            <template x-for="g in ['5.00','4.75','4.50','4.25','4.00','3.75','3.50','3.25','3.00','2.75','2.50','2.25','2.00','1.75','1.50','1.25','1.00']" :key="g">
                                                <option :value="g" x-text="g"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td x-text="student.grade ? student.grade : 'Not graded yet'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <!-- Male Students Table -->
        <template x-if="male_students.length > 0">
            <div class="mt-3">
                <h5 class="text-success fw-bold">Male Students</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>Student No.</th>
                                <th>Student Name</th>
                                <th>Update Grade</th>
                                <th x-text="mode === 'mid' ? 'Midterm' : mode === 'fcg' ? 'Final Course Grade' : 'Grade'"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(student, index) in male_students" :key="student.student_id || index">
                                <tr>
                                    <td x-text="student.stud_no"></td>
                                    <td x-text="student.name"></td>
                                    <td>
                                        <select class="form-select" x-model="student.gradePreview" @change="confirmGrade(student)">
                                            <option value="">-- Select Grade --</option>
                                            <template x-for="g in ['5.00','4.75','4.50','4.25','4.00','3.75','3.50','3.25','3.00','2.75','2.50','2.25','2.00','1.75','1.50','1.25','1.00']" :key="g">
                                                <option :value="g" x-text="g"></option>
                                            </template>
                                        </select>
                                    </td>
                                    <td x-text="student.grade ? student.grade : 'Not graded yet'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>
    </div>
    <!-- Preview & Confirm Modal -->
    <div class="modal fade" id="pastePreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Pasted Grades Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <pre x-text="pastePreviewText"></pre>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" @click="confirmPaste()">Confirm & Update Grades</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener("alpine:init", () => {
            Alpine.data("gradingApp", () => ({
                teacher_id: <?php echo json_encode($teacher_id); ?>,
                semester: null,
                availableSemesters: [],
                alert: null,
                subject: null,
                semSubs: [],
                mode: null,
                male_students: [],
                female_students: [],
                pastePreviewText: "",
                pasteGradesData: [],


                async init() {
                    if (!this.availableSemesters.length) {
                        const semRes = await fetch("../sql/collection_c.php?action=get_semester");
                        this.availableSemesters = await semRes.json();
                    }
                },

                async fetchSubjects() {
                    this.subject = null;
                    this.mode = null;
                    this.male_students = [];
                    this.female_students = [];

                    let formData = new FormData();
                    formData.append('action', 'get_subjects');
                    formData.append('teacher_id', this.teacher_id);
                    formData.append('semester_id', this.semester);

                    const res = await fetch("../sql/teacher_module.php", {
                        method: "POST",
                        body: formData
                    });

                    this.semSubs = await res.json();
                },


                async fetchGrades() {
                    let formData = new FormData();
                    formData.append('action', 'get_grades');
                    formData.append('subject_id', this.subject);
                    formData.append('semester_id', this.semester);

                    const res = await fetch("../sql/teacher_module.php", {
                        method: "POST",
                        body: formData
                    });

                    const data = await res.json();

                    const assignGrade = (students) => students.map(s => ({
                        ...s,
                        grade: this.mode === 'mid' ? s.mid : this.mode === 'fcg' ? s.fcg : null,
                        gradePreview: this.mode === 'mid' ? s.mid : this.mode === 'fcg' ? s.fcg : null
                    }));

                    this.male_students = assignGrade(data.male || []);
                    this.female_students = assignGrade(data.female || []);
                },

                async confirmGrade(student) {
                    student.grade = student.gradePreview;
                    if (!student.grade) return;

                    let formData = new FormData();
                    formData.append('action', 'update_grade');
                    formData.append('student_id', student.student_id);
                    formData.append('grade', student.grade);
                    formData.append('subject_id', this.subject);
                    formData.append('semester_id', this.semester);
                    formData.append('mode', this.mode);

                    const res = await fetch("../sql/teacher_module.php", {
                        method: "POST",
                        body: formData
                    });

                    const result = await res.json();
                    this.alert = result.success ? "Grade updated successfully!" : "Something went wrong";
                },

                pasteGrades(gender) {
                    navigator.clipboard.readText().then(text => {
                        const lines = text.split(/\r?\n/).filter(Boolean);
                        let students = gender === 'male' ? this.male_students : this.female_students;

                        let previewArr = [];
                        this.pasteGradesData = [];

                        students.forEach((s, i) => {
                            const line = lines[i]?.trim();
                            if (!line) return;

                            const parts = line.split(/\s+/);
                            const grade = parts.length > 1 ? parts[parts.length - 1] : parts[0];

                            previewArr.push(`${s.name} -> ${grade}`);
                            this.pasteGradesData.push({
                                student_id: s.student_id,
                                grade: grade
                            });
                        });

                        this.pastePreviewText = previewArr.join("\n");

                        const modalEl = document.getElementById('pastePreviewModal');
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                    });
                },

                async confirmPaste() {
                    if (!this.pasteGradesData.length) return;

                    let formData = new FormData();
                    formData.append('action', 'mass_update_grades');
                    formData.append('subject_id', this.subject);
                    formData.append('semester_id', this.semester);
                    formData.append('mode', this.mode);
                    formData.append('grades', JSON.stringify(this.pasteGradesData));

                    const res = await fetch("../sql/teacher_module.php", {
                        method: "POST",
                        body: formData
                    });

                    const result = await res.json();

                    if (result.success) {
                        this.alert = "All grades updated successfully!";
                        this.pasteGradesData.forEach(p => {
                            let student = this.male_students.find(s => s.student_id === p.student_id) ||
                                this.female_students.find(s => s.student_id === p.student_id);
                            if (student) {
                                student.grade = p.grade;
                                student.gradePreview = p.grade;
                            }
                        });
                        student.gradePreview = "";
                    } else {
                        this.alert = "Something went wrong with the update!";
                    }

                    const modalEl = document.getElementById('pastePreviewModal');
                    bootstrap.Modal.getInstance(modalEl).hide();

                    this.pasteGradesData = [];
                    this.pastePreviewText = "";
                },

            }))
        });
    </script>

</body>

</html>