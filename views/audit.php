<?php
require("../partials/conn.php");
require("../partials/session.php");

// Fetch all audit trail records with user names
$audit_sql = "
    SELECT a.id, u.name AS username, a.module, a.refno, a.action, a.action_datetime
    FROM audit_trail a
    LEFT JOIN users u ON a.user_id = u.id
    ORDER BY a.action_datetime DESC
";
$audit_records = $pdo->query($audit_sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Audit Trail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.0/dist/lux/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>

<body>
    <?php 
    $activePage = 'audit_trail';
    include "../partials/navbar.php"; 
    ?>

    <div class="container mt-4" x-data>
        <div class="my-3 d-flex align-items-center">
            <h2 class="mb-0 me-3">Audit Trail</h2>
            <a href="../menu.php" class="btn btn-danger d-flex align-items-center">
                <i class="bi bi-box-arrow-left me-1"></i> Back
            </a>
        </div>

        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Note: These records are immutable and acts as a ledger of system user actions.
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr class="table-dark text-center align-middle">
                        <th>ID</th>
                        <th>User</th>
                        <th>Module</th>
                        <th>Reference No.</th>
                        <th>Action</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($audit_records)): ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No audit records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($audit_records as $record): ?>
                            <tr class="text-center">
                                <td><?= htmlspecialchars($record['id']) ?></td>
                                <td><?= htmlspecialchars($record['username'] ?? 'Unknown') ?></td>
                                <td><?= htmlspecialchars($record['module']) ?></td>
                                <td><?= htmlspecialchars($record['refno']) ?></td>
                                <td>
                                    <?php
                                    $action = $record['action'];
                                    $badgeClass = match ($action) {
                                        'A' => 'success',
                                        'E' => 'warning',
                                        'D' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $badgeClass ?>">
                                        <?= htmlspecialchars($action) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars(date('F j, Y g:i A', strtotime($record['action_datetime']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
