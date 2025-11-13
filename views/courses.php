<?php
require("../partials/conn.php");
require("../partials/session.php");
$courses = $pdo->query("SELECT * FROM courses")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" x-data="{ editModal:false, course:{} }">

<head>
    <meta charset="UTF-8">
    <title>Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/lux/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body>
    <?php
    $activePage = 'courses';
    include "../partials/navbar.php";
    ?>

    <div class="container mt-4">
        <div class="my-3 d-flex align-items-center">
            <h2 class="mb-0 me-3">Courses</h2>
            <a href="../menu.php" class="btn btn-danger btn d-flex align-items-center">
                <i class="bi bi-box-arrow-left me-1"></i> Back
            </a>
        </div>
        <?php
        include "../partials/balert.php";
        ?>

        <!-- Add Course -->
        <form class="row g-3" action="../sql/course_actions.php" method="POST">
            <div class="col-md-6">
                <input type="text" class="form-control" name="addcourse" autofocus required placeholder="Course Name">
            </div>
            <div class="col-md-6">
                <button type="submit" class="btn btn-success">Add</button>
            </div>
        </form>

        <!-- Table -->
        <div class="mt-4">
            <table class="table table-striped">
                <thead>
                    <tr class="table-dark">
                        <th>Course Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($courses as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                    @click="course=<?= htmlspecialchars(json_encode($row)) ?>; editModal=true">
                                    Edit
                                </button>
                                <a href="../sql/course_actions.php?del_id=<?= $row['course_id'] ?>"
                                    class="btn btn-danger btn-sm"
                                    @click="if(!confirm('Delete this course?')) $event.preventDefault()">
                                    Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ===================== EDIT MODAL ===================== -->
    <template x-if="editModal">
        <div>
            <div class="modal fade show d-block" tabindex="-1" x-cloak @keydown.escape.window="editModal=false">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="../sql/course_actions.php">
                            <div class="modal-header">
                                <h5 class="modal-title">Edit Course</h5>
                                <button type="button" class="btn-close" @click="editModal=false"></button>
                            </div>
                            <div class="modal-body">
                                <input type="hidden" name="editcourse_id" :value="course.course_id">
                                <div class="form-group">
                                    <label>Course Name</label>
                                    <input type="text" class="form-control" name="editcourse_name" x-model="course.name" required>
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



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>