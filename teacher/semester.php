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

<body class="d-flex flex-column  align-items-center min-vh-100">




    <div class="menu-container bg-light rounded shadow p-4 mt-5"
        style="min-width:600px; max-width:900px;"
        x-data="pickSem()" x-init="initForm()">


        <?php include "../partials/alert.php"; ?>

        <div class="menu-header bg-dark text-white text-center w-100 py-3 rounded-top my-2">
            <h4 class="fw-bold mb-1"><?= htmlspecialchars(strtoupper($teacher_name)) ?></h4>
            <p class="fw-bold mb-0"><?= htmlspecialchars($teacher_code) ?></p>
        </div>

        <template x-if="availableSemesters.length > 0">
            <div class=" card menu-card shadow-sm text-center p-4 border-dark mb-2 ">
                <div class="mb-2">
                    <div x-show="availableSemesters.length > 0" x-transition>
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
            </div>
        </template>

        <!-- Subject Cards -->
        <template x-if="semSubs.length > 0">
            <div class="w-100 mt-3">

                <h5 class="text-dark fw-bold mb-3 text-center"
                    x-text="`Subjects for Semester ${availableSemesters.find(sem => sem.id == semester)?.code || ''}`">
                </h5>

                <div class="row">
                    <template x-for="sub in semSubs" :key="sub.subject_id">
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm border-dark h-100 cursor-pointer"
                                @click="selectSubject(sub.subject_id, sub.subject_code, sub.name)">
                                <div class="card-body cursor-pointer" >
                                    <h5 class="card-title text-dark" x-text="sub.subject_code"></h5>
                                    <p class="card-text mb-0">
                                        <strong>Subject:</strong>
                                        <span x-text="sub.name"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>


            </div>
        </template>


        <!-- No subjects message -->
        <div x-show="semester && semSubs.length === 0" class="alert alert-primary mt-3 text-center">
            No subjects found for this semester.
        </div>

        <a href="menu.php" class="text-decoration-none">
            <div class="card menu-card shadow-sm text-center p-3 border border-primary text-primary">
                <i class="bi bi-box-arrow-left fs-2 mb-2"></i>
                <h5>
                    <strong>back</strong>
                </h5>
            </div>
        </a>
    </div>

    <script>
        function pickSem() {
            return {
                teacher_id: <?php echo json_encode($teacher_id); ?>,
                semester: null,
                availableSemesters: [],
                alert: null,
                subject: null,
                semSubs: [],

                async initForm() {
                    if (!this.availableSemesters.length) {
                        const semRes = await fetch("../sql/collection_c.php?action=get_semester");
                        this.availableSemesters = await semRes.json();
                    }
                },
                async fetchSubjects() {
                    let formData = new FormData();
                    formData.append('action', 'get_subjects');
                    formData.append('teacher_id', this.teacher_id);
                    formData.append('semester_id', this.semester);

                    const res = await fetch("../sql/teacher_module.php", {
                        method: "POST",
                        body: formData
                    });

                    const data = await res.json();
                    this.semSubs = data;
                },

                async selectSubject(subject_id, subject_code, subject_name) {
                    let formData = new FormData();
                    formData.append('action', 'set_session');
                    formData.append('subject_id', subject_id);
                    formData.append('subject_code', subject_code);
                    formData.append('subject_name', subject_name);

                    const res = await fetch('../sql/teacher_module.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await res.json();
                    if (data.success) {
                        window.location.href = 'mode.php';
                    }
                }
            }
        }
    </script>
</body>

</html>