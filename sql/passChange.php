<?php
session_start();
require("../partials/conn.php");
require("dd.php");

$role = $_SESSION['user_type'] ?? '';

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

$student_id = $_SESSION['student_id'] ?? null;
$teacher_id = $_SESSION['teacher_id'] ?? null;

if($role === 'student' && $student_id) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password']; 
    $confirm_password = $_POST['confirm_password']; 


    $stmt = $pdo->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student['pass'] == $old_password) {
        if ($new_password === $confirm_password) {
            $update_stmt = $pdo->prepare("UPDATE students SET pass = ? WHERE id = ?");
            $update_stmt->execute([$new_password, $student_id]);

            $_SESSION["error"] = "Password changed successfully.";
            header("Location: ../student/menu.php");
            exit;
        } else {
            $_SESSION["error"] = "New Password and Confirm Password do not match.";
            header("Location: ../student/menu.php");
            exit;
        }       
    }else {
        $_SESSION["error"] = "Old Password Incorrect.";
        header("Location: ../student/menu.php");
        exit;
    }

}

if($role === 'teacher' && $teacher_id) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password']; 
    $confirm_password = $_POST['confirm_password']; 


    $stmt = $pdo->prepare("SELECT * FROM teacher WHERE id = ?");
    $stmt->execute([$teacher_id]);
    $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($teacher['password'] == $old_password) {
        if ($new_password === $confirm_password) {
            $update_stmt = $pdo->prepare("UPDATE teacher SET password = ? WHERE id = ?");
            $update_stmt->execute([$new_password, $teacher_id]);

            $_SESSION["error"] = "Password changed successfully.";
            header("Location: ../teacher/menu.php");
            exit;
        } else {
            $_SESSION["error"] = "New Password and Confirm Password do not match.";
            header("Location: ../teacher/menu.php");
            exit;
        }       
    }else {
        $_SESSION["error"] = "Old Password Incorrect.";
        header("Location: ../teacher/menu.php");
        exit;
    }

}

