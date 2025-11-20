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
    $subjStmt->execute([$semester_id, $teacher_id, $semester_id]);
    $semSubs = $subjStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($semSubs ?: []);
    exit;
}

if ($action === 'get_grades') {
    $subject_id = $_POST['subject_id'] ?? '';
    $semester_id = $_POST['semester_id'] ?? '';

    $sql = "SELECT 
                s.id AS student_id, 
                s.name,
                s.stud_no, 
                s.gender, 
                ss.mid, 
                ss.fcg
            FROM students s
            LEFT JOIN students_subjects ss 
                ON s.id = ss.student_id
            WHERE ss.subject_id = :subject_id
              AND ss.semester_id = :semester_id
            ORDER BY s.name ASC";

    $students_subjectsStmt = $pdo->prepare($sql);
    $students_subjectsStmt->execute([
        ':subject_id' => $subject_id,
        ':semester_id' => $semester_id
    ]);

    $students_subjects = $students_subjectsStmt->fetchAll(PDO::FETCH_ASSOC);

    $male_students = [];
    $female_students = [];

    foreach ($students_subjects as $row) {
        $gender = strtolower(trim($row['gender']));
        if ($gender === 'male') {
            $male_students[] = $row;
        } elseif ($gender === 'female') {
            $female_students[] = $row;
        }
    }

    echo json_encode([
        'male' => $male_students,
        'female' => $female_students
    ]);

    exit;
}

if ($_POST['action'] === 'update_grade') {
    $student_id = $_POST['student_id'];
    $grade = floatval($_POST['grade']);
    $subject_id = $_POST['subject_id'];
    $semester_id = $_POST['semester_id'];
    $mode = $_POST['mode']; 

    if ($mode === 'mid') {
        $stmt = $pdo->prepare("UPDATE students_subjects SET mid = ? WHERE student_id = ? AND subject_id = ? AND semester_id = ?");
    } else {
        $stmt = $pdo->prepare("UPDATE students_subjects SET fcg = ? WHERE student_id = ? AND subject_id = ? AND semester_id = ?");
    }
    $result = $stmt->execute([$grade, $student_id, $subject_id, $semester_id]);

    echo json_encode(['success' => true]);
    exit;
}

if ($_POST['action'] === 'mass_update_grades') {
    $subject_id = $_POST['subject_id'] ?? '';
    $semester_id = $_POST['semester_id'] ?? '';
    $mode = $_POST['mode'] ?? '';
    $grades = json_decode($_POST['grades'] ?? '[]', true);

    if (!$subject_id || !$semester_id || !$mode || !is_array($grades)) {
        echo json_encode(['success' => false, 'message' => 'Invalid input']);
        exit;
    }

    $pdo->beginTransaction();
    try {
        if ($mode === 'mid') {
            $stmt = $pdo->prepare("UPDATE students_subjects SET mid = ? WHERE student_id = ? AND subject_id = ? AND semester_id = ?");
        } else {
            $stmt = $pdo->prepare("UPDATE students_subjects SET fcg = ? WHERE student_id = ? AND subject_id = ? AND semester_id = ?");
        }

        foreach ($grades as $g) {
            $student_id = $g['student_id'] ?? null;
            $grade = floatval($g['grade'] ?? 0);

            if ($student_id && $grade) {
                $stmt->execute([$grade, $student_id, $subject_id, $semester_id]);
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }

    exit;
}

