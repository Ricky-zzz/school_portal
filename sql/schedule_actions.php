<?php
session_start();
require("../partials/conn.php");
require("dd.php");

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// serach student by stud no
if ($action === 'get_student') {
    $stud_no = $_GET['stud_no'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM students WHERE stud_no = ?");
    $stmt->execute([$stud_no]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    $c_stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
    $c_stmt->execute([$student["course_id"]]);
    $course = $c_stmt->fetch(PDO::FETCH_ASSOC);

    $student['course'] = $course ? $course['name'] : null;

    if (!$student) {
        echo json_encode([
            "error" => true,
            "message" => "Student not found"
        ]);
        exit;
    }
    $_SESSION['stud_no'] = $student['stud_no'];


    echo json_encode($student);
    exit;
}

// get student subject fr a semester
if ($action === 'get_semsubs') {
    $stud_no = $_GET['stud_no'] ?? '';
    $sem_id = $_GET['sem_id'] ?? '';
    $sql = "SELECT s.subject_id, s.subject_code AS code, s.name, s.days, s.start_time, s.end_time, 
                 t.teacher_name, r.room_name, s.price_unit, s.unit
           FROM students_subjects ss
           JOIN semesters se ON ss.semester_id = se.id
           JOIN subjects s ON ss.subject_id = s.subject_id
           JOIN teacher t ON s.teacher_id = t.id
           JOIN room r ON s.room_id = r.id
           WHERE ss.student_id = ? AND ss.semester_id = ?";
    $subjStmt = $pdo->prepare($sql);
    $subjStmt->execute([$stud_no,$sem_id]);
    $semSubs = $subjStmt->fetchAll(PDO::FETCH_ASSOC);

    $_SESSION['sem'] = $sem_id;

    echo json_encode($semSubs ?: []);
    exit;
}
// ready all semesters
if ($action === 'get_semester') {
    $sql = "SELECT * from semesters";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $semester= $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($semester ?: []);
    exit;
}

// ready all availbale subjects
if ($action === 'get_subject') {
    $sql = "SELECT s.subject_id, s.subject_code AS code, s.name, s.days, s.start_time, s.end_time, t.teacher_name, r.room_name, s.price_unit, s.unit
            FROM subjects s
            JOIN teacher t ON s.teacher_id = t.id
            JOIN room r ON s.room_id = r.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($subjects ?: []);
    exit;
}
// search via name
if ($action === 'search_student') {
    $name = $_GET['name'] ?? '';

    $stmt = $pdo->prepare("SELECT id, stud_no, name
                           FROM students 
                           WHERE name LIKE ?");
    $stmt->execute(["%$name%"]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($students ?: []);
    exit;
}

// ADD DELETE SUBJECT
$message = "";

if ($action === 'add') {
    $student_id = $_POST['student_id'] ?? null;
    $subject_id = $_POST['subject_id'] ?? null;
    $semester_id = $_POST['semester_id'] ?? null;

    if ($student_id && $subject_id && $semester_id) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO students_subjects (student_id, subject_id, semester_id) VALUES (?, ?, ?)");
        $stmt->execute([$student_id, $subject_id, $semester_id]);
        $message = "Subject added successfully.";
    } else {
        $message = "Missing student, subject, or semester.";
    }

} 

// DELETE SUBJECT
if ($action === 'delete') {
    $student_id = $_POST['student_id'] ?? null;
    $subject_id = $_POST['subject_id'] ?? null;
    $semester_id = $_POST['semester_id'] ?? null;

    if ($student_id && $subject_id && $semester_id) {
        $gradeCheckStmt = $pdo->prepare("SELECT mid, fcg FROM students_subjects WHERE student_id = ? AND subject_id = ? AND semester_id = ?");
        $gradeCheckStmt->execute([$student_id, $subject_id, $semester_id]);
        $grades = $gradeCheckStmt->fetch(PDO::FETCH_ASSOC);

        if ($grades['mid'] !== null || $grades['fcg'] !== null) {
            $message = "Cannot delete student, they have grades assigned.";
        } else {
            $stmt = $pdo->prepare("DELETE FROM students_subjects WHERE student_id = ? AND subject_id = ? AND semester_id = ?");
            $stmt->execute([$student_id, $subject_id, $semester_id]);
            $message = "Subject deleted successfully.";
        }
    } else {
        $message = "Missing student, subject, or semester.";
    }
}

if ($action === 'add' || $action === 'delete') {
    $sql = "SELECT s.subject_id, s.subject_code AS code, s.name, s.days, s.start_time, s.end_time, 
                   t.teacher_name, r.room_name, s.price_unit, s.unit
            FROM students_subjects ss
            JOIN subjects s ON ss.subject_id = s.subject_id
            JOIN teacher t ON s.teacher_id = t.id
            JOIN room r ON s.room_id = r.id
            WHERE ss.student_id = ? AND ss.semester_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$student_id, $semester_id]);

    echo json_encode([
        "subjects" => $stmt->fetchAll(PDO::FETCH_ASSOC),
        "message" => $message
    ]);
    exit;
}


