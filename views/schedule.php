<?php
require("../partials/session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Schedule</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
    <?php
    $activePage = 'schedule';
    include "../partials/navbar.php";
    ?>

    <div class="container mt-4" x-data="scheduleApp()">
        <div class="my-3 d-flex align-items-center">
            <h2 class="mb-0 me-3">Student-Subject Enrollment</h2>
            <a href="../menu.php" class="btn btn-danger btn d-flex align-items-center">
                <i class="bi bi-box-arrow-left me-1"></i> Back
            </a>
        </div>

        <!-- Student Search -->
        <div class="mb-4 w-50">
            <label>Student #</label>
            <div class="input-group">
                <input type="text" class="form-control" x-model="stud_no" autofocus placeholder="Enter Student #" @keyup.enter="fetchStudent()">

                <button class="btn btn-primary" @click="showFilter = true">📝 Filter</button>
            </div>
        </div>
        <?php
        include "../partials/alert.php";
        ?>

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
                            <select x-ref="semesterSelect" class="form-select" x-model="semester" @change="fetchSemSubs()">

                                <option value="" selected>Select a semester</option>
                                <template x-for="sem in availableSemesters" :key="sem.id">
                                    <option :value="sem.id" x-text="sem.code"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="col-md-9 col-12" x-show="semester && availableSubjects.length > 0">
                    <label class="form-label fw-bold">
                        <h5>Available Subjects:</h5>
                    </label>
                    <div class="input-group">
                        <select x-model="subject" class="form-select">
                            <option value="" disabled selected>Select a Subject</option>
                            <template x-for="subj in availableSubjects" :key="subj.subject_id">
                                <option :value="subj.subject_id"
                                    x-text="subj.code + ' / ' + subj.name + ' / ' + subj.days + ' / ' + subj.start_time + '-' + subj.end_time + ' / ' + subj.room_name + ' / ' + subj.teacher_name + ' / ' + subj.price_unit + ' / ' + subj.unit">
                                </option>
                            </template>
                        </select>
                        <button class="btn btn-success " @click="addSubject" :disabled="!subject">Add</button>
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
                        <a href="../prints/assesament.php" target="_blank"
                            class="btn btn-primary bg-info text-white rounded-pill px-4 py-2">
                            Print
                        </a>
                    </div>


                    <table class="table table-striped ">
                        <thead>
                            <tr class="table-dark">
                                <th>Code</th>
                                <th>Description</th>
                                <th>Days</th>
                                <th>Time</th>
                                <th>Room</th>
                                <th>Teacher</th>
                                <th>Price per Unit</th>
                                <th>Unit</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <template x-if="semSubs.length > 0">
                            <tbody>
                                <template x-for="subj in semSubs" :key="subj.subject_id">
                                    <tr>
                                        <td x-text="subj.code"></td>
                                        <td x-text="subj.name"></td>
                                        <td x-text="subj.days"></td>
                                        <td x-text="subj.start_time + ' - ' + subj.end_time"></td>
                                        <td x-text="subj.room_name"></td>
                                        <td x-text="subj.teacher_name"></td>
                                        <td x-text="subj.price_unit"></td>
                                        <td x-text="subj.unit"></td>
                                        <td>
                                            <button class="btn btn-danger btn-sm"
                                                @click="if(confirm('Are you sure you want to delete this subject?')) deleteSubject(subj.subject_id)">
                                                Delete
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </template>

                        <template x-if="semSubs.length > 0">
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td colspan="4" class="text-end">Total Units:</td>
                                    <td colspan="2" x-text="semSubs.reduce((sum, s) => sum + parseInt(s.unit), 0)">
                                    </td>
                                    <td class="text-end">Total Price:</td>
                                    <td
                                        x-text="'P'+semSubs.reduce((sum, s) => sum + (parseFloat(s.price_unit) * parseInt(s.unit)), 0)">
                                    </td>
                                </tr>
                            </tfoot>
                        </template>
                    </table>
                </div>
            </div>
        </template>

        <!--  Filter Modal -->
        <?php
        include "../partials/filter_modal.php";
        ?>
    </div>

<script>
    function scheduleApp() {
        return {
            stud_no: "",
            subject_code: "",
            student: null,
            subject: null,
            semester: null,
            availableSubjects: [],
            availableSemesters: [],
            semSubs: [],
            alert: null,
            showFilter: false,
            searchName: "",
            searchResults: [],

            async fetchStudent() {
                let res = await fetch(`../sql/schedule_actions.php?action=get_student&stud_no=${this.stud_no}`);
                let data = await res.json();

                if (data.error) {
                    this.student = null;
                    this.alert = data.message;
                    return;
                }
                this.student = data;

                let allSem = await fetch(`../sql/schedule_actions.php?action=get_semester`);
                this.availableSemesters = await allSem.json();

                this.$nextTick(() => {
                    this.$refs.semesterSelect?.focus();
                });
            },

            async fetchSemSubs() {
                if (!this.student || !this.semester) return;
                let res = await fetch(`../sql/schedule_actions.php?action=get_semsubs&stud_no=${this.student.id}&sem_id=${this.semester}`);
                this.semSubs = await res.json();

                let allSubjects = await fetch(`../sql/schedule_actions.php?action=get_subject`);
                this.availableSubjects = await allSubjects.json();
            },

            async addSubject() {
                if (!this.student || !this.subject || !this.semester) return;
                let formData = new FormData();
                formData.append("action", "add");
                formData.append("student_id", this.student.id);
                formData.append("subject_id", this.subject);
                formData.append("semester_id", this.semester);

                let res = await fetch("../sql/schedule_actions.php", {
                    method: "POST",
                    body: formData
                });
                let data = await res.json();
                this.alert = data.message;
                await this.fetchSemSubs();
            },

            async deleteSubject(subject_id) {
                if (!this.student || !this.semester) return;
                let formData = new FormData();
                formData.append("action", "delete");
                formData.append("student_id", this.student.id);
                formData.append("subject_id", subject_id);
                formData.append("semester_id", this.semester);

                let res = await fetch("../sql/schedule_actions.php", {
                    method: "POST",
                    body: formData
                });
                let data = await res.json();
                this.alert = data.message;
                await this.fetchSemSubs();
            },

            async filterStudent() {
                if (!this.searchName) return;
                let res = await fetch(`../sql/schedule_actions.php?action=search_student&name=${this.searchName}`);
                this.searchResults = await res.json();
            },

            selectStudent(stud_no) {
                this.stud_no = stud_no;
                this.fetchStudent();
                this.showFilter = false;
            }
        };
    }
</script>

</body>


</html>