<?php
require("partials/session.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.8/dist/zephyr/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-menu a {
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .dashboard-menu a:hover {
            transform: scale(1.02);
            background-color: #f8f9fa;
        }

        .dashboard-menu .card {
            min-height: 130px;
        }
    </style>
</head>

<body>

    <div class="container py-5">
        <h2 class="text-center mb-2"><i class="bi bi-mortarboard-fill me-2"></i>Menu</h2>
        <h2 class="text-center mb-5"><i class="bi bi-person-badge-fill me-2"></i>Welcome <?php echo($_SESSION["username"])?> </h2>
        <div class="row row-cols-1 row-cols-md-4 g-4 dashboard-menu">

            <div class="col">
                <a href="views/student.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-people-fill fs-2 mb-2"></i>
                        <h5>Students</h5>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="views/courses.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-journal-bookmark-fill fs-2 mb-2"></i>
                        <h5>Courses</h5>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="views/subject.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-book fs-2 mb-2"></i>
                        <h5>Subject</h5>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="views/list.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-list-ul fs-2 mb-2"></i>
                        <h5>List</h5>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="views/schedule.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-calendar-check-fill fs-2 mb-2"></i>
                        <h5>Enlist</h5>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="views/grading.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-person-check-fill fs-2 mb-2"></i>
                        <h5>Grade</h5>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="views/printGrade.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-printer fs-2 mb-2"></i>
                        <h5>Print Grades</h5>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="views/semester.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-calendar fs-2 mb-2"></i>
                        <h5>Semester</h5>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="views/collection.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-bookshelf fs-2 mb-2"></i>
                        <h5>Collections</h5>
                    </div>
                </a>
            </div>
            <div class="col">
                <a href="views/audit.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-hourglass fs-2 mb-2"></i>
                        <h5>Audit Trail</h5>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="views/teacher.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-person-badge-fill fs-2 mb-2"></i>
                        <h5>Teacher</h5>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="views/room.php" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-door-open-fill fs-2 mb-2"></i>
                        <h5>Room</h5>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="prints/print.php" target="_blank" class="text-decoration-none text-dark">
                    <div class="card text-center shadow-sm p-3">
                        <i class="bi bi-printer-fill fs-2 mb-2"></i>
                        <h5>Report</h5>
                    </div>
                </a>
            </div>

            <div class="col">
                <a href="sql/login_actions.php?logout=y" class="text-decoration-none text-danger">
                    <div class="card text-center shadow-sm p-3 border border-danger">
                        <i class="bi bi-box-arrow-right fs-2 mb-2"></i>
                        <h5>Logout</h5>
                    </div>
                </a>
            </div>

        </div>
    </div>

</body>

</html>