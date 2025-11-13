<?php
session_start();

require("../partials/conn.php"); 
header('Content-Type: application/json; charset=utf-8');

$action = $_GET['action'] ?? ($_POST['action'] ?? '');


if ($action === 'get_semester') {
    $sql = "SELECT * from semesters";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $semester= $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($semester ?: []);
    exit;
}


if ($action === 'get_courses') {
    $sql = "SELECT * from courses";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $courses= $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($courses ?: []);
    exit;
}

if ($action === 'get_subject') {
    $sql = "SELECT * from subjects";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($subjects ?: []);
    exit;
}




if ($action === 'get_batch_grades') {
    $semester = $_GET['semester'] ?? 'all';
    $course = $_GET['course'] ?? 'all';
    $subject = $_GET['subject'] ?? 'all';

    // Store filters in session for PDF batch printing
    $_SESSION['sem'] = $semester;
    $_SESSION['crs'] = $course;
    $_SESSION['sub'] = $subject;

    $sql = "
        SELECT ss.id, st.stud_no, st.name, st.gender, 
               c.name AS course, se.code AS semester, 
               sb.subject_code, sb.name AS subject_name,
               ss.mid, ss.fcg
        FROM students_subjects ss
        JOIN students st ON ss.student_id = st.id
        JOIN subjects sb ON ss.subject_id = sb.subject_id
        JOIN courses c ON st.course_id = c.course_id
        JOIN semesters se ON ss.semester_id = se.id
        WHERE 1
    ";

    $params = [];

    if ($semester !== 'all') {
        $sql .= " AND se.id = ?";
        $params[] = $semester;
    }
    if ($course !== 'all') {
        $sql .= " AND c.course_id = ?";
        $params[] = $course;
    }
    if ($subject !== 'all') {
        $sql .= " AND sb.subject_id = ?";
        $params[] = $subject;
    }

    $sql .= " 
        ORDER BY 
            se.code ASC,          -- Semester first
            c.name ASC,           -- Then course
            st.name ASC,          -- Then student name
            sb.subject_code ASC   -- Then subjects
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($results ?: []);
    exit;
}


echo json_encode(["error" => "Invalid action"]);
exit;