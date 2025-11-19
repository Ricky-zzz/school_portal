<?php
require("../partials/conn.php");
require("../partials/session.php");

$courses = $pdo->query("SELECT * FROM courses")->fetchAll();


$sql = "SELECT students.*, courses.name AS course_name 
        FROM students 
        LEFT JOIN courses ON courses.course_id = students.course_id";
$students = $pdo->query($sql)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en" x-data="{ 
    showAdd:false, 
    showEdit:false, 
    addStudent:{ 
        student_number:'', 
        student_name:'', 
        gender:'', 
        course_id: <?= $courses[0]['course_id'] ?? 'null' ?> 
    }, 
    currentStudent:{} 
}">

<head>
    <meta charset="UTF-8">
    <title>Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body>
    <?php
    $activePage = 'student';
    include "../partials/navbar.php";
    ?>


    <div class="container mt-4">
<div class="my-3 d-flex align-items-center">
    <h2 class="mb-0 me-3">Student </h2>
    <a href="../menu.php" class="btn btn-danger btn d-flex align-items-center">
        <i class="bi bi-box-arrow-left me-1"></i> Back
    </a>
</div>
        <button class="btn btn-primary mb-3" @click="showAdd = true">+ Add Student</button>

        <?php
        include "../partials/balert.php";
        ?>

        <table class="table table-striped">
            <thead>
                <tr class="table-dark">
                    <th>Student #</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Course</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['stud_no']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td><?= htmlspecialchars($row['course_name']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" @click="
                        currentStudent = {
                            id: <?= $row['id'] ?>,
                            student_number: '<?= addslashes($row['stud_no']) ?>',
                            student_name: '<?= addslashes($row['name']) ?>',
                            gender: '<?= $row['gender'] ?>',
                            course_id: <?= $row['course_id'] ?? $courses[0]['course_id'] ?>
                        };
                        showEdit = true;
                    ">Edit</button>

                            <a href="../sql/student_actions.php?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm"
                                @click="if(!confirm('Delete this student?')) $event.preventDefault()">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- ADD MODAL -->
    <template x-if="showAdd">
        <div>
            <div class="modal fade show d-block" tabindex="-1" @keydown.escape.window="showAdd = false">
                <div class="modal-dialog">
                    <form method="POST" action="../sql/student_actions.php" class="modal-content">
                        <input type="hidden" name="action" value="add">
                        <input type="hidden" name="data"
                            :value="JSON.stringify({...addStudent, course_id: parseInt(addStudent.course_id)})">

                        <div class="modal-header">
                            <h5 class="modal-title">Add Student</h5>
                            <button type="button" class="btn-close" @click="showAdd = false"></button>
                        </div>
                        <div class="modal-body">
                            <input class="form-control mb-2" placeholder="Student #" x-model="addStudent.student_number" autofocus required>
                            <input class="form-control mb-2" placeholder="Name" x-model="addStudent.student_name" required>
                            <select class="form-control mb-2" x-model="addStudent.gender" required>
                                <option value="" disabled>Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <select class="form-control mb-2" x-model="addStudent.course_id" required>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?= $c['course_id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="showAdd=false">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>
    </template>

    <!-- EDIT MODAL -->
    <template x-if="showEdit">
        <div>
            <div class="modal fade show d-block" tabindex="-1" @keydown.escape.window="showEdit = false">
                <div class="modal-dialog">
                    <form method="POST" action="../sql/student_actions.php" class="modal-content">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="data"
                            :value="JSON.stringify({...currentStudent, course_id: parseInt(currentStudent.course_id)})">

                        <div class="modal-header">
                            <h5 class="modal-title">Edit Student</h5>
                            <button type="button" class="btn-close" @click="showEdit = false"></button>
                        </div>
                        <div class="modal-body">
                            <input class="form-control mb-2" placeholder="Student #" x-model="currentStudent.student_number" required>
                            <input class="form-control mb-2" placeholder="Name" x-model="currentStudent.student_name" required>
                            <select class="form-control mb-2" x-model="currentStudent.gender" required>
                                <option value="" disabled>Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                            <select class="form-control mb-2" x-model="currentStudent.course_id" required>
                                <?php foreach ($courses as $c): ?>
                                    <option value="<?= $c['course_id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="showEdit=false">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>
    </template>

</body>

</html>