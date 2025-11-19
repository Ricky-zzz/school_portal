<?php
require("../partials/conn.php");
require("../partials/session.php");
$order_by = $_GET['order_by'] ?? '';
$valid_columns = ['stud_no', 'name', 'gender', 'course_name'];
$order_by_sql = in_array($order_by, $valid_columns) ? $order_by : '';

$sql = "SELECT students.*, courses.name AS course_name 
        FROM students 
        LEFT JOIN courses ON courses.course_id = students.course_id";
if ($order_by_sql) {
    $sql .= " ORDER BY " . $order_by_sql;
}

$students = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Students List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
    <?php
    $activePage = 'list';
    include "../partials/navbar.php";
    ?>

    <div class="container mt-4 w-50">
        <div class="my-3 d-flex align-items-center">
            <h2 class="mb-0 me-3">List</h2>
            <a href="../menu.php" class="btn btn-danger btn d-flex align-items-center">
                <i class="bi bi-box-arrow-left me-1"></i> Back
            </a>
        </div>
        <form method="GET" action="list.php" class="row g-2">
            <div class="col-auto">
                <label for="order_by" class="col-form-label">Order By:</label>
            </div>
            <div class="col-auto">
                <select class="form-select" name="order_by" id="order_by" onchange="this.form.submit()">
                    <option value="" <?= $order_by == '' ? 'selected' : '' ?>>No Order</option>
                    <option value="stud_no" <?= $order_by == 'stud_no' ? 'selected' : '' ?>>Student #</option>
                    <option value="name" <?= $order_by == 'name' ? 'selected' : '' ?>>Student Name</option>
                    <option value="gender" <?= $order_by == 'gender' ? 'selected' : '' ?>>Gender</option>
                    <option value="course_name" <?= $order_by == 'course_name' ? 'selected' : '' ?>>Course</option>
                </select>
            </div>
        </form>
    </div>

    <div class="container mt-4">
        <table class="table table-striped">
            <thead>
                <tr class="table-dark">
                    <th>Student #</th>
                    <th>Name</th>
                    <th>Gender</th>
                    <th>Course</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($students as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['stud_no']) ?></td>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['gender']) ?></td>
                        <td><?= htmlspecialchars($row['course_name']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>