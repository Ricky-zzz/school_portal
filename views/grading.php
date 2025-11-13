<?php
require("../partials/session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/lux/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
    <?php
    $activePage = 'Grading';
    include "../partials/navbar.php";
    ?>

    <div class="container mt-4" x-data="gradingApp()">
        <div class="my-3 d-flex align-items-center">
            <h2 class="mb-0 me-3">Student Grading</h2>
            <a href="../menu.php" class="btn btn-danger btn d-flex align-items-center">
                <i class="bi bi-box-arrow-left me-1"></i> Back
            </a>
        </div>

        <?php
        include "../partials/alert.php";
        ?>

        <!-- Student Search -->
        <div class="mb-4 w-50">
            <label>Student #</label>
            <div class="input-group">
                <input type="text" class="form-control" x-model="stud_no" autofocus placeholder="Enter Student #" @keyup.enter="fetchStudent()">
                <button class="btn btn-primary" @click="showFilter = true">📝 Filter</button>
            </div>
        </div>

        <!-- Student Info -->
        <template x-if="student">
            <div class="mt-4">
                <div class="row">
                    <div class="col-12 col-md-6 mb-3">
                        <label>Student Name</label>
                        <div class="input-group">
                            <input type="text" class="form-control" :value="student?.name || ''"
                                placeholder="Student Name" readonly>
                        </div>
                    </div>
                    <div class="col-12 col-md-6 mb-3">
                        <label>Student Course</label>
                        <div class="input-group">
                            <input type="text" class="form-control" :value="student?.course || ''" placeholder="Course"
                                readonly>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <template x-if="student">
            <div class="row my-3">
                <div class="col-md-3 col-12">
                    <div x-show="availableSemesters.length > 0">
                        <label class="form-label fw-bold">
                            <h5>Semester:</h5>
                        </label>
                        <div class="input-group">
                            <select x-model="semester" class="form-select" @change="fetchSemSubs" x-ref="semesterSelect">
                                <option value="" selected>Select a semester</option>
                                <template x-for="sem in availableSemesters" :key="sem.id">
                                    <option :value="sem.id" x-text="sem.code"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </template>

        <!-- Enrolled Subjects -->
        <template x-if="semester">
            <div class="mt-4">
                <div class="table-responsive">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Enrolled Subjects</h5>
                        <a href="../prints/semGrade.php" target="_blank"
                            class="btn btn-primary bg-info text-white rounded-pill px-4 py-2">
                            Print
                        </a>
                    </div>

                    <table class="table table-striped ">
                        <thead>
                            <tr class="table-dark">
                                <th>Code</th>
                                <th>Description</th>
                                <th>Units</th>
                                <th>Midterm Grade</th>
                                <th>Final Course Grade</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <template x-if="semSubs.length > 0">
                            <tbody>
                                <template x-for="subj in semSubs" :key="subj.subject_id">
                                    <tr>
                                        <td x-text="subj.code"></td>
                                        <td x-text="subj.name"></td>
                                        <td x-text="subj.unit"></td>
                                        <td x-text="subj.mid ? parseFloat(subj.mid).toFixed(2) : 'No grade yet'"></td>
                                        <td x-text="subj.fcg ? parseFloat(subj.fcg).toFixed(2) : 'No grade yet'"></td>

                                        <td>
                                            <button class="btn btn-success mb-3" @click="openAddGradeModal(subj)"><i class="bi bi-pencil-square"></i> Update Grade</button>

                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </template>

                        <template x-if="semSubs.length > 0">
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td colspan="3" class="text-end">Total Units:</td>
                                    <td x-text="semSubs.reduce((sum, s) => sum + parseInt(s.unit || 0), 0)"></td>
                                    <td class="text-end">GPA:</td>
                                    <td>
                                        <span x-text="gpa.toFixed(2)"></span>
                                    </td>
                                </tr>
                            </tfoot>

                        </template>
                    </table>
                </div>
            </div>
        </template>

        <!-- ADD GRADE MODAL -->
        <template x-if="showAdd">
            <div>
                <div class="modal fade show d-block" tabindex="-1" @keydown.escape.window="showAdd = false">
                    <div class="modal-dialog">
                        <form class="modal-content">
                            <input type="hidden" name="action" value="add_grade">
                            <input type="hidden" name="subject_id" :value="addGrade.subject_id">
                            <input type="hidden" name="stud_no" :value="stud_no">

                            <div class="modal-header">
                                <h5 class="modal-title">Add Grade</h5>
                                <button type="button" class="btn-close" @click="showAdd = false"></button>
                            </div>

                            <div class="modal-body">
                                <div class="mb-2">
                                    <label>Subject</label>
                                    <input type="text" class="form-control" :value="addGrade.subject_name" readonly>
                                </div>

                                <div class="mb-2">
                                    <label>Grade Type</label>
                                    <select class="form-select" name="grade_type" x-model="addGrade.grade_type"
                                        required>
                                        <option value="" disabled>Select Type</option>
                                        <option value="mid">Midterm</option>
                                        <option value="fcg">Final Course Grade</option>
                                    </select>
                                </div>

                                <div class="mb-2">
                                    <label>Grade</label>
                                    <select class="form-select" name="grade_value" x-model="addGrade.grade_value"
                                        required>
                                        <option value="" disabled>Select Grade</option>
                                        <template x-for="grade in gradeOptions" :key="grade">
                                            <option :value="grade" x-text="grade"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="showAdd = false">Cancel</button>
                                <button type="button" @click="saveGrade()" class="btn btn-primary">Save Grade</button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="modal-backdrop fade show"></div>
            </div>
        </template>


        <!--  Filter Modal -->
        <?php
        include "../partials/filter_modal.php";
        ?>
    </div>


    <script>
        function gradingApp() {
            return {
                stud_no: "",
                subject_code: "",
                student: null,
                semester: null,
                availableSemesters: [],
                semSubs: [],
                alert: null,
                showFilter: false,
                searchName: "",
                searchResults: [],
                showAdd: false,
                addGrade: {
                    subject_id: null,
                    subject_name: '',
                    grade_type: '',
                    grade_value: ''
                },
                gradeOptions: ['1.00', '1.25', '1.50', '1.75', '2.00', '2.25', '2.50', '2.75', '3.00', '4.00', '5.00', 'Dropped'],
                gpa: 0,

                async fetchStudent() {
                    let res = await fetch(`../sql/grade_c.php?action=get_student&stud_no=${this.stud_no}`);
                    let data = await res.json();

                    if (data.error) {
                        this.student = null;
                        this.alert = data.message;
                        return;
                    }
                    this.student = data;

                    let allSem = await fetch(`../sql/grade_c.php?action=get_semester`);
                    this.availableSemesters = await allSem.json();

                    this.$nextTick(() => {
                        this.$refs.semesterSelect?.focus();
                    });
                },

                async fetchSemSubs() {
                    if (!this.student || !this.semester) return;
                    let res = await fetch(`../sql/grade_c.php?action=get_semsubs&stud_no=${this.student.id}&sem_id=${this.semester}`);
                    this.semSubs = await res.json();
                    this.calculateGPA();
                },

                async saveGrade() {
                    if (!this.student || !this.semester) return;
                    let formData = new FormData();
                    formData.append("action", "save");
                    formData.append("student_id", this.student.id);
                    formData.append("subject_id", this.addGrade.subject_id);
                    formData.append("semester_id", this.semester);
                    formData.append("grade_type", this.addGrade.grade_type);
                    formData.append("grade_value", this.addGrade.grade_value);

                    let res = await fetch("../sql/grade_c.php", {
                        method: "POST",
                        body: formData
                    });
                    let data = await res.json();
                    this.alert = data.message;
                    this.showAdd = false;
                    this.addGrade = {
                        subject_id: null,
                        subject_name: '',
                        grade_type: '',
                        grade_value: ''
                    };

                    await this.fetchSemSubs();
                },
                async filterStudent() {
                    if (!this.searchName) return;
                    let res = await fetch(`../sql/grade_c.php?action=search_student&name=${this.searchName}`);
                    this.searchResults = await res.json();
                },

                calculateGPA() {
                    if (!this.semSubs || this.semSubs.length === 0) {
                        this.gpa = 0;
                        return;
                    }

                    let totalPoints = 0;
                    let totalUnits = 0;

                    this.semSubs.forEach(s => {
                        const unit = parseInt(s.unit || 0);
                        const gradeValue = parseFloat(s.fcg);

                        if (!isNaN(gradeValue) && gradeValue >= 1.00 && gradeValue <= 5.00) {
                            totalPoints += gradeValue * unit;
                            totalUnits += unit;
                        }
                    });

                    this.gpa = totalUnits > 0 ? (totalPoints / totalUnits) : 0;
                },

                selectStudent(stud_no) {
                    this.stud_no = stud_no;
                    this.fetchStudent();
                    this.showFilter = false;
                },
                openAddGradeModal(subj) {
                    this.addGrade = {
                        subject_id: subj.subject_id,
                        subject_name: subj.name,
                        grade_type: '',
                        grade_value: ''
                    };
                    this.showAdd = true;
                },

            };
        }
    </script>
</body>

</html>