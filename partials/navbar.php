<style>
    /* Sidebar gradient and style tweaks */
    .offcanvas-start {
        background: linear-gradient(180deg, #1a1919ff 0%, #252525ff 100%);
        color: white;
    }

    .offcanvas .nav-link {
        padding: 0.75rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1rem;
        border-radius: 0;
        transition: background-color 0.2s ease, padding-left 0.2s ease;
    }

    .offcanvas .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.15);
        padding-left: 1.5rem;
    }

    .offcanvas .nav-link.active {
        background-color: #f8f9fa !important;
        color: #000000ff !important;
        font-weight: 600;
        box-shadow: inset 4px 0 0 #ffc107;
    }

    .offcanvas .offcanvas-title {
        font-size: 1.2rem;
    }

    .sidebar-toggle-btn {
        z-index: 1045;
        padding: 6px 10px;
        transition: background-color 0.3s ease;
    }

    .sidebar-toggle-btn:hover {
        background-color: #031838ff;
    }

    .offcanvas.show~.sidebar-toggle-btn {
        opacity: 0;
        pointer-events: none;
    }
</style>


<button
    class="btn btn-primary rounded-end position-fixed top-50 start-0 translate-middle-y shadow-lg sidebar-toggle-btn p-4 fs-2"
    type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarNav" aria-controls="sidebarNav">
    <i class="bi bi-arrow-bar-right"></i>
</button>



<div class="offcanvas offcanvas-start text-bg-primary" tabindex="-1" id="sidebarNav" aria-labelledby="sidebarNavLabel"
    data-bs-scroll="true" data-bs-backdrop="true">

    <div class="offcanvas-header border-bottom border-light">
        <h5 class="offcanvas-title fw-bold text-light" id="sidebarNavLabel">
            <a class="nav-link" href="../menu.php">
                <i class="bi bi-mortarboard-fill me-2"></i> Menu
            </a>

        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0 d-flex flex-column">
        <ul class="navbar-nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= ($activePage == 'student') ? 'active' : 'text-light' ?>" href="student.php">
                    <i class="bi bi-people-fill"></i> Students
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activePage == 'courses') ? 'active' : 'text-light' ?>" href="courses.php">
                    <i class="bi bi-journal-bookmark-fill"></i> Courses
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activePage == 'list') ? 'active' : 'text-light' ?>" href="list.php">
                    <i class="bi bi-list-ul"></i> List
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activePage == 'schedule') ? 'active' : 'text-light' ?>" href="schedule.php">
                    <i class="bi bi-calendar-check-fill"></i> Subject
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activePage == 'Grading') ? 'active' : 'text-light' ?>" href="grading.php">
                    <i class="bi bi-person-check-fill"></i> Grade
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activePage == 'bacthPrint') ? 'active' : 'text-light' ?>" href="printGrade.php">
                    <i class="bi bi-printer "></i> Print Grades
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activePage == 'semester') ? 'active' : 'text-light' ?>" href="semester.php">
                    <i class="bi bi-calendar"></i> Semester
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activePage == 'collection') ? 'active' : 'text-light' ?>" href="collection.php">
                    <i class="bi bi-bookshelf"></i> Collection
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activePage == 'audit_trail') ? 'active' : 'text-light' ?>" href="audit.php">
                    <i class="bi bi-hourglass"></i> Audit Trail
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activePage == 'teacher') ? 'active' : 'text-light' ?>" href="teacher.php">
                    <i class="bi bi-person-badge-fill"></i> Teacher
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activePage == 'room') ? 'active' : 'text-light' ?>" href="room.php">
                    <i class="bi bi-door-open-fill"></i> Room
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($activePage == 'print') ? 'active' : 'text-light' ?>" href="../prints/print.php"
                    target="_blank">
                    <i class="bi bi-printer-fill"></i> Report
                </a>
            </li>
        </ul>

        <hr class="border-light my-3">

        <ul class="navbar-nav mt-auto">
            <li class="nav-item">
                <a class="nav-link text-light" href="../sql/login_actions.php?logout=y">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </li>
        </ul>
    </div>
</div>