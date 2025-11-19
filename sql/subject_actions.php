<?php
session_start();
require("../partials/conn.php");

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $subject_code = $_POST['subject_code'] ?? '';
    $name = $_POST['name'] ?? '';
    $days = $_POST['days'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $teacher_id = $_POST['teacher_id'] ?? '';
    $room_id = $_POST['room_id'] ?? '';
    $unit = $_POST['unit'] ?? 0;

    $stmt = $pdo->prepare("INSERT INTO subjects (subject_code, name, days, start_time, end_time, teacher_id, room_id, unit) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$subject_code, $name, $days, $start_time, $end_time, $teacher_id, $room_id, $unit])) {
        $_SESSION['message'] = "Subject added successfully!";
    } else {
        $_SESSION['message'] = "Failed to add subject.";
    }
    header("Location: ../views/subject.php");
    exit;
}

if ($action === 'edit') {
    $subject_id = $_POST['subject_id'] ?? '';
    $subject_code = $_POST['subject_code'] ?? '';
    $name = $_POST['name'] ?? '';
    $days = $_POST['days'] ?? '';
    $start_time = $_POST['start_time'] ?? '';
    $end_time = $_POST['end_time'] ?? '';
    $teacher_id = $_POST['teacher_id'] ?? '';
    $room_id = $_POST['room_id'] ?? '';
    $unit = $_POST['unit'] ?? 0;

    $stmt = $pdo->prepare("UPDATE subjects 
                           SET subject_code=?, name=?, days=?, start_time=?, end_time=?, teacher_id=?, room_id=?, unit=? 
                           WHERE subject_id=?");
    if ($stmt->execute([$subject_code, $name, $days, $start_time, $end_time, $teacher_id, $room_id, $unit, $subject_id])) {
        $_SESSION['message'] = "Subject updated successfully!";
    } else {
        $_SESSION['message'] = "Failed to update subject.";
    }
    header("Location: ../views/subject.php");
    exit;
}

if ($action === 'delete' || isset($_GET['del_id'])) {
    $subject_id = $_GET['del_id'] ?? 0;
    $stmt = $pdo->prepare("DELETE FROM subjects WHERE subject_id=?");
    if ($stmt->execute([$subject_id])) {
        $_SESSION['message'] = "Subject deleted successfully!";
    } else {
        $_SESSION['message'] = "Failed to delete subject.";
    }
    header("Location: ../views/subject.php");
    exit;
}
?>
