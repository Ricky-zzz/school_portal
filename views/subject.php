<?php
require("../partials/conn.php");
require("../partials/session.php");

// Fetch all subjects with teacher and room info
$subs = $pdo->query("
    SELECT s.*, t.teacher_name, r.room_name
    FROM subjects s
    JOIN teacher t ON s.teacher_id = t.id
    JOIN room r ON s.room_id = r.id
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all teachers for dropdown
$teachers = $pdo->query("SELECT * FROM teacher")->fetchAll(PDO::FETCH_ASSOC);

// Fetch all rooms for dropdown
$rooms = $pdo->query("SELECT * FROM room")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en" x-data="{ addModal:false, editModal:false, currentSubject:{} }">

<head>
    <meta charset="UTF-8">
    <title>Subjects Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-light">

    <?php
    $activePage = 'subject';
    include "../partials/navbar.php";
    ?>

    <?php if (!empty($_SESSION["message"])): ?>
        <div class="alert alert-danger">
            <?= htmlspecialchars($_SESSION["message"]) ?>
        </div>
        <?php unset($_SESSION["message"]); ?>
    <?php endif; ?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Subjects</h2>
                <a href="../menu.php" class="btn btn-danger btn d-flex align-items-center mx-3">
                    <i class="bi bi-box-arrow-left me-1"></i> Back
                </a>
            </div>
            <button class="btn btn-primary" @click="addModal=true"><i class="bi bi-plus-lg"></i> Add Subject</button>
        </div>

        <table class="table table-striped table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Days</th>
                    <th>Time</th>
                    <th>Teacher</th>
                    <th>Room</th>
                    <th>Units</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($subs as $sub): ?>
                    <tr>
                        <td><?= htmlspecialchars($sub['subject_code']) ?></td>
                        <td><?= htmlspecialchars($sub['name']) ?></td>
                        <td><?= htmlspecialchars($sub['days']) ?></td>
                        <td><?= htmlspecialchars($sub['start_time']) ?> - <?= htmlspecialchars($sub['end_time']) ?></td>
                        <td><?= htmlspecialchars($sub['teacher_name']) ?></td>
                        <td><?= htmlspecialchars($sub['room_name']) ?></td>
                        <td><?= htmlspecialchars($sub['unit']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm"
                                @click="currentSubject=<?= htmlspecialchars(json_encode($sub)) ?>; editModal=true">
                                Edit
                            </button>
                            <a href="../sql/subject_actions.php?del_id=<?= $sub['subject_id'] ?>"
                                class="btn btn-danger btn-sm"
                                @click="if(!confirm('Delete this subject?')) $event.preventDefault()">
                                Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- ADD MODAL -->
    <template x-if="addModal">
        <div x-cloak>
            <div class="modal fade show d-block" tabindex="-1" @keydown.escape.window="addModal=false">
                <div class="modal-dialog">
                    <form method="POST" action="../sql/subject_actions.php" class="modal-content">
                        <input type="hidden" name="action" value="add">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Subject</h5>
                            <button type="button" class="btn-close" @click="addModal=false"></button>
                        </div>
                        <div class="modal-body">
                            <input type="text" class="form-control mb-2" name="subject_code" placeholder="Subject Code"
                                required>
                            <input type="text" class="form-control mb-2" name="name" placeholder="Subject Name"
                                required>
                            <input type="text" class="form-control mb-2" name="days" placeholder="Days (e.g. MWF)"
                                required>
                            <div class="d-flex gap-2 mb-2">
                                <input type="time" class="form-control" name="start_time" required>
                                <input type="time" class="form-control" name="end_time" required>
                            </div>
                            <select class="form-select mb-2" name="teacher_id" required>
                                <option value="">Select Teacher</option>
                                <?php foreach ($teachers as $t): ?>
                                    <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['teacher_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-select mb-2" name="room_id" required>
                                <option value="">Select Room</option>
                                <?php foreach ($rooms as $r): ?>
                                    <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['room_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="number" class="form-control mb-2" name="unit" placeholder="Units" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="addModal=false">Cancel</button>
                            <button type="submit" class="btn btn-primary">Add Subject</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>
    </template>

    <!-- EDIT MODAL -->
    <template x-if="editModal">
        <div x-cloak>
            <div class="modal fade show d-block" tabindex="-1" @keydown.escape.window="editModal=false">
                <div class="modal-dialog">
                    <form method="POST" action="../sql/subject_actions.php" class="modal-content">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="subject_id" :value="currentSubject.subject_id">
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Subject</h5>
                            <button type="button" class="btn-close" @click="editModal=false"></button>
                        </div>
                        <div class="modal-body">
                            <input type="text" class="form-control mb-2" name="subject_code" placeholder="Subject Code"
                                x-model="currentSubject.subject_code" required>
                            <input type="text" class="form-control mb-2" name="name" placeholder="Subject Name"
                                x-model="currentSubject.name" required>
                            <input type="text" class="form-control mb-2" name="days" placeholder="Days"
                                x-model="currentSubject.days" required>
                            <div class="d-flex gap-2 mb-2">
                                <input type="time" class="form-control" name="start_time"
                                    x-model="currentSubject.start_time" required>
                                <input type="time" class="form-control" name="end_time"
                                    x-model="currentSubject.end_time" required>
                            </div>
                            <select class="form-select mb-2" name="teacher_id" x-model="currentSubject.teacher_id"
                                required>
                                <option value="">Select Teacher</option>
                                <?php foreach ($teachers as $t): ?>
                                    <option :value="<?= $t['id'] ?>"><?= htmlspecialchars($t['teacher_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-select mb-2" name="room_id" x-model="currentSubject.room_id" required>
                                <option value="">Select Room</option>
                                <?php foreach ($rooms as $r): ?>
                                    <option :value="<?= $r['id'] ?>"><?= htmlspecialchars($r['room_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="number" class="form-control mb-2" name="unit" placeholder="Units"
                                x-model="currentSubject.unit" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" @click="editModal=false">Cancel</button>
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-backdrop fade show"></div>
        </div>
    </template>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>