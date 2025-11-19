<?php
require("../partials/teacher.php");
$teacher_name = $_SESSION['teacher_name'] ?? 'Proffesor';
$teacher_code = $_SESSION['teacher_code'] ?? 'N/A';

// Get selected subject primary from session
$subject_code = $_SESSION['subject_code'] ?? null;
$subject_name = $_SESSION['subject_name'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Teacher Portal - Select Mode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/simplex/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="d-flex flex-column align-items-center min-vh-100">

    <div class="menu-container bg-light rounded shadow p-4 mt-5" style="min-width:400px; max-width:600px;">

        <div class="menu-header bg-dark text-white text-center w-100 py-3 rounded-top mb-3">
            <h4 class="fw-bold mb-1"><?= htmlspecialchars(strtoupper($teacher_name)) ?></h4>
            <p class="fw-bold mb-0"><?= htmlspecialchars($teacher_code) ?></p>
        </div>

        <!-- Label -->
        <div class="mb-2 text-center">
            <span class="badge bg-dark fs-6">Selected Subject</span>
        </div>

        <!-- Selected Subject Card -->
        <?php if ($subject_code && $subject_name): ?>
            <div class="card mb-4 border-dark shadow-sm">
                <div class="card-body text-center">
                    <h5 class="card-title text-dark"><?= htmlspecialchars($subject_code) ?></h5>
                    <p class="card-text mb-0">
                        <strong>Subject:</strong> <?= htmlspecialchars($subject_name) ?>
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center mb-4">
                No subject selected. Please go back and select a subject.
            </div>
        <?php endif; ?>

        <!-- Mode Buttons -->
        <div class="d-grid gap-3 mb-3">
            <button class="btn btn-success btn-lg" onclick="setMode('midterm')">Midterm</button>
            <button class="btn btn-warning btn-lg" onclick="setMode('fcg')">Final Course Grade</button>
        </div>

        <a href="semester.php" class="text-decoration-none">
            <div class="card menu-card shadow-sm text-center p-3 border-primary text-primary">
                <i class="bi bi-arrow-left fs-2 mb-2"></i>
                <h5><strong>Back</strong></h5>
            </div>
        </a>
    </div>

    <script>
        async function setMode(mode) {
            let formData = new FormData();
            formData.append('action', 'set_mode');
            formData.append('mode', mode);

            const res = await fetch('../sql/teacher_module.php', {
                method: 'POST',
                body: formData
            });

            const data = await res.json();
            if (data.success) {
                window.location.href = 'grade.php';
            } else {
                alert('Failed to set mode.');
            }
        }
    </script>

</body>
</html>
