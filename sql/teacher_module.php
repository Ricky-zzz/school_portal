<?php
session_start();
require("../partials/conn.php");

$action = $_GET['action'] ?? ($_POST['action'] ?? '');


// get teacher subject fr a semester
if ($action === 'get_subjects') {
    $teacher_id = $_POST['teacher_id'] ?? '';
    $semester_id = $_POST['semester_id'] ?? '';

    $sql = "SELECT s.subject_id, s.subject_code, s.name, se.code
                FROM subjects s
                JOIN semesters se ON se.id = ?
                WHERE s.teacher_id = ?
                  AND EXISTS (
                      SELECT 1
                      FROM students_subjects ss
                      WHERE ss.subject_id = s.subject_id
                        AND ss.semester_id = ?);
                ";
    $subjStmt = $pdo->prepare($sql);
    $subjStmt->execute([$semester_id,$teacher_id,$semester_id]);
    $semSubs = $subjStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($semSubs ?: []);
    exit;
}


if ($action === 'set_mode') {
    $mode = $_POST['mode'] ?? '';

    if ($mode === 'midterm' || $mode === 'fcg') {
        $_SESSION['mode'] = $mode;
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Invalid mode']);
    }
    exit;
}