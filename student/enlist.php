<?php
require("../partials/student.php");
require("../partials/conn.php");

$student_id = $_SESSION['student_id'] ?? '';
$student_name = $_SESSION['student_name'] ?? 'Unknown';
$student_number = $_SESSION['student_number'] ?? 'N/A';
$student_course = $_SESSION['course'] ?? 'N/A'; 

if (!$student_id) {
    header("Location: ../login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM semesters ORDER BY id DESC LIMIT 1");
$latest_sem = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$latest_sem) {
    die("<div class='alert alert-danger text-center mt-5'>No semester found. Please contact the registrar.</div>");
}

$latest_sem_id = $latest_sem['id'];
$latest_sem_code = htmlspecialchars($latest_sem['code']);

$check = $pdo->prepare("SELECT * FROM students_subjects WHERE student_id = ? AND semester_id = ?");
$check->execute([$student_id, $latest_sem_id]);
$already_enlisted = $check->fetch(PDO::FETCH_ASSOC);

if (isset($_POST['enlist'])) {
    if (!$already_enlisted) {
        $insert = $pdo->prepare("INSERT INTO students_subjects (student_id, semester_id) VALUES (?, ?)");
        $insert->execute([$student_id, $latest_sem_id]);
        $_SESSION['success'] = "Successfully enlisted for $latest_sem_code!";
        header("Location: enlist.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Enlistment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/cosmo/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</head>

<body class="d-flex justify-content-center  bg-light mt-5 ">

<div class="container border border-info rounded shadow p-4" style="max-height:500px;">
    
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="menu.php" class="btn btn-danger btn-sm d-flex align-items-center">
            <i class="bi bi-box-arrow-left me-1"></i> Back
        </a>
        <h4 class="fw-bold mb-0 flex-grow-1 text-center">Enlistment</h4>
        <div style="width: 65px;"></div>
    </div>

    <!-- Success / Alert Message -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <!-- Student Info Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white fw-bold">
            Student Information
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="fw-bold">Student #</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($student_number) ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold">Name</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($student_name) ?>" readonly>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="fw-bold">Course</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($student_course) ?>" readonly>
                </div>
            </div>
        </div>
    </div>

    <!-- Enlistment Card -->
    <div class="card shadow-sm">
        <div class="card-body text-center">
            <p class="fw-bold mb-1">Upcomming School Year / Semester:</p>
            <h5 class="text-primary mb-4"><?= $latest_sem_code ?></h5>

            <?php if ($already_enlisted): ?>
                <div class="alert alert-success text-center">
                    <i class="bi bi-check-circle"></i> You are already enlisted for <strong><?= $latest_sem_code ?></strong>.
                </div>
            <?php else: ?>
                <form method="post" class="text-center">
                    <button type="submit" name="enlist" class="btn btn-success">
                        <i class="bi bi-person-plus"></i> Enlist Now
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

</div>
</body>
</html>
