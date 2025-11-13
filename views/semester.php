<?php
require("../partials/conn.php");
require("../partials/session.php");
$semesters = $pdo->query("SELECT * FROM semesters")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" x-data="{ editModal: false, addModal: false, semester: {}, addSemester: { code: '', startdate: '', enddate: '', summer: '' } }">

<head>
    <meta charset="UTF-8">
    <title>Semesters</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/lux/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body>
    <?php $activePage = 'semesters';
    include "../partials/navbar.php"; ?>

    <div class="container mt-4">
<div class="my-3 d-flex align-items-center">
    <h2 class="mb-0 me-3">Semester</h2>
    <a href="../menu.php" class="btn btn-danger btn d-flex align-items-center">
        <i class="bi bi-box-arrow-left me-1"></i> Back
    </a>
</div>
        <?php
        include "../partials/balert.php";
        ?>

        <!-- Button to trigger Add Modal -->
        <button class="btn btn-success mb-3" @click="addModal = true; addSemester = { code: '', startdate: '', enddate: '', summer: '' }">
            Add Semester
        </button>

        <!-- Table -->
        <div>
            <table class="table table-striped">
                <thead>
                    <tr class="table-dark">
                        <th>Semester Code</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Summer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($semesters as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['code']) ?></td>
                            <td><?= htmlspecialchars(date('F j, Y', strtotime($row['start_date']))) ?></td>
                            <td><?= htmlspecialchars(date('F j, Y', strtotime($row['end_date']))) ?></td>
                            <td><?= htmlspecialchars($row['summer']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                    @click="semester=<?= htmlspecialchars(json_encode($row)) ?>; editModal=true">Edit</button>
                                <a href="../sql/semester_actions.php?del_id=<?= $row['id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    @click="if(!confirm('Delete this semester?')) $event.preventDefault()">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>

    <!-- Add Modal -->
    <template x-if="addModal">
        <div>
            <div class="modal fade show d-block" tabindex="-1" x-cloak @keydown.escape.window="addModal=false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="../sql/semester_actions.php">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Semester</h5>
                                <button type="button" class="btn-close" @click="addModal=false"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <label>Semester Code</label>
                                    <input type="text" class="form-control" name="addsemester_code" placeholder="Semester Code (e.g. 1st-Semester:2025-2026)" required x-model="addSemester.code">
                                </div>
                                <div class="mb-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="startdate" required x-model="addSemester.startdate">
                                </div>
                                <div class="mb-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="enddate" required x-model="addSemester.enddate">
                                </div>
                                <div class="mb-3">
                                    <label>Summer?</label>
                                    <select class="form-select" name="summer" required x-model="addSemester.summer">
                                        <option value="" disabled>Select</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="addModal=false">Close</button>
                                <button type="submit" class="btn btn-success">Add Semester</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>
    </template>

    <!-- Edit Modal -->
    <template x-if="editModal">
        <div>
            <div class="modal fade show d-block" tabindex="-1" x-cloak @keydown.escape.window="editModal=false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="../sql/semester_actions.php">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Semester</h5>
                                <button type="button" class="btn-close" @click="editModal=false"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="editsemester_id" :value="semester.id">

                                <div class="mb-3">
                                    <label>Semester Code</label>
                                    <input type="text" class="form-control" name="editsemester_code" required x-model="semester.code">
                                </div>
                                <div class="mb-3">
                                    <label>Start Date</label>
                                    <input type="date" class="form-control" name="edit_startdate" required x-model="semester.start_date">
                                </div>
                                <div class="mb-3">
                                    <label>End Date</label>
                                    <input type="date" class="form-control" name="edit_enddate" required x-model="semester.end_date">
                                </div>
                                <div class="mb-3">
                                    <label>Summer?</label>
                                    <select class="form-select" name="edit_summer" required x-model="semester.summer">
                                        <option value="" disabled>Select</option>
                                        <option value="Yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" @click="editModal=false">Close</button>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>
    </template>

</body>

</html>