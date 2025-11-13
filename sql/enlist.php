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
    $sql = "SELECT s.subject_id, s.subject_code AS code, s.name, s.unit, 
               se.code AS semester, ss.mid, ss.fcg
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

// ADD/Update Grade
$message = "";

if ($action === 'save') {
    $student_id = $_POST['student_id'] ?? null;
    $subject_id = $_POST['subject_id'] ?? null;
    $semester_id = $_POST['semester_id'] ?? null;
    $grade_type = $_POST['grade_type'] ?? null; 
    $grade_value = $_POST['grade_value'] ?? null;

    
    $sql = "UPDATE students_subjects SET $grade_type = ? WHERE student_id = ? AND subject_id = ? AND semester_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$grade_value, $student_id, $subject_id, $semester_id]);
    echo json_encode(["success" => true, "message" =>  "Grade updated successfully."]);

    exit;
}

