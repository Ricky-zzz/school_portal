<?php
require("../partials/teacher.php");
$teacher_name = $_SESSION['teacher_name'] ?? 'Proffesor';
$teacher_code = $_SESSION['teacher_code'] ?? 'N/A';
    $_SESSION['subject_id'] = $_POST['subject_id'] ?? null;
    $_SESSION['subject_code'] = $_POST['subject_code'] ?? null;
    $_SESSION['subject_name'] = $_POST['subject_name'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teacher Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/simplex/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="d-flex flex-column  align-items-center min-vh-100">

    <?php if (!empty($_SESSION["error"])): ?>
        <div class="alert alert-danger alert-dismissible fade show w-50 text-center mb-3" role="alert">
            <?= htmlspecialchars($_SESSION["error"]) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION["error"]); ?>
    <?php endif; ?>


    <div class="menu-container bg-light rounded shadow p-2 mt-5" style="min-width:600px; max-width:900px;">
        <div class="menu-header bg-dark text-white text-center w-100 py-3 rounded-top my-2">
            <h4 class="fw-bold mb-1"><?= htmlspecialchars(strtoupper($teacher_name)) ?></h4>
            <p class="fw-bold mb-0"><?= htmlspecialchars($teacher_code) ?></p>
        </div>


        <a href="semester.php" class="text-decoration-none">
            <div class="card menu-card shadow-sm text-center p-3 border-info mb-2 text-info">
                <i class="bi bi-journal-text fs-2 mb-2"></i>
                <h5><strong>Grade Students </strong> </h5>
            </div>
        </a>

        <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
            <div class="card menu-card shadow-sm text-center p-3 border-warning text-warning mb-2">
                <i class="bi bi-key fs-2 mb-2"></i>
                <h5>
                    <strong>Change Password</strong></h5>
            </div>
        </a>

        <a href="../sql/login_actions.php?logout=y" class="text-decoration-none">
            <div class="card menu-card shadow-sm text-center p-3 border border-primary text-primary">
                <i class="bi bi-box-arrow-right fs-2 mb-2"></i>
                <h5>
                    <strong>Logout</strong></h5>
            </div>
        </a>
    </div>

    <?php
    require("../partials/pass.php");
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>