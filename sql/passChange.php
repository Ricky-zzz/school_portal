<?php
session_start();
require("../partials/conn.php");
require("dd.php");

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

$student_id = $_SESSION['student_id'] ?? null;

if (isset($_POST['old_password'] )) {

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