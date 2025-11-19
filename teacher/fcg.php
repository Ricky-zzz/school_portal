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
    <title>Teacher Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/simplex/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body class="d-flex flex-column  align-items-center min-vh-100">




    <div class="menu-container bg-light rounded shadow p-2 mt-5" style="min-width:400px;" x-data="pickSem()" x-init="initForm()">

        <?php include "../partials/alert.php"; ?>

        <div class="menu-header bg-primary text-white text-center w-100 py-3 rounded-top my-2">
            <h4 class="fw-bold mb-1"><?= htmlspecialchars(strtoupper($teacher_name)) ?></h4>
            <p class="fw-bold mb-0"><?= htmlspecialchars($teacher_code) ?></p>
        </div>

        <template x-if="availableSemesters.length > 0">
            <div class=" card menu-card shadow-sm text-center p-4 border-danger mb-2 ">
                <div class="mb-2">
                    <div x-show="availableSemesters.length > 0" x-transition>
                        <label for="semester-select" class="form-label text-danger fw-bold d-flex align-items-center mb-1">
                            <i class="bi bi-calendar-check me-2"></i> Select Semester:
                        </label>
                        <div class="input-group has-validation">
                            <span class="input-group-text bg-danger text-white border-danger">
                                <i class="bi bi-list-task"></i>
                            </span>
                            <select
                                x-model="semester"
                                class="form-select form-control-lg border-danger"
                                @change="fetchBalance"
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

        <div class="card menu-card shadow-sm text-center p-3 border-info mb-2 text-info" @click="goto_mid">
            <i class="bi bi-journal-text fs-2 mb-2"></i>
            <h5><strong>Midterms </strong> </h5>
        </div>


        <div class="card menu-card shadow-sm text-center p-3 border-danger text-danger mb-2" @click="goto_fcg">
            <i class="bi bi-key fs-2 mb-2"></i>
            <h5>
                <strong>Final Course Grade</strong>
            </h5>
        </div>

        <a href="menu.php" class="text-decoration-none">
            <div class="card menu-card shadow-sm text-center p-3 border border-warning text-warning">
                <i class="bi bi-box-arrow-left fs-2 mb-2"></i>
                <h5>
                    <strong>back</strong>
                </h5>
            </div>
        </a>
    </div>

    <?php
    require("../partials/pass.php");
    ?>
    <script>
        function pickSem() {
            return {
                teacher_id: <?php echo json_encode($teacher_id); ?>,
                semester: null,
                availableSemesters: [],
                alert: null,

                async initForm() {
                    if (!this.availableSemesters.length) {
                        const semRes = await fetch("../sql/collection_c.php?action=get_semester");
                        this.availableSemesters = await semRes.json();
                        console.log(this.availableSemesters);
                    }
                },

                goto_fcg() {
                    if (this.semester) {
                        window.location.href = `fcg.php?sem=${this.semester}`;
                    } else {
                        alert("Please select a semester first.");
                    }
                },
                goto_mid() {
                    if (this.semester) {
                        window.location.href = `mid.php?sem=${this.semester}`;
                    } else {
                        alert("Please select a semester first.");
                    }
                },

            }
        }
    </script>
</body>

</html>