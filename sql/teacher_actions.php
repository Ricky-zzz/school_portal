<?php
require("../partials/conn.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ADD
    if (isset($_POST['addteacher'])) {
        $teacher_name = htmlspecialchars($_POST['addteacher']);
        $stmt = $pdo->prepare("INSERT INTO teacher (teacher_name) VALUES (:teacher_name)");
        $stmt->execute(params: 
        [':teacher_name' =>$teacher_name]);
        header("Location: ../views/teacher.php?msg=New Teacher added successfully");
        exit;
    }

    if (isset($_POST['edit_id'], $_POST['edit_teacher'])) {
        $teacher_name =htmlspecialchars($_POST['edit_teacher']);
        $e_id = htmlspecialchars($_POST['edit_id']);
        $stmt = $pdo->prepare("UPDATE teacher SET teacher_name = :teacher_name WHERE id = :id");
        $stmt->execute([
            ':teacher_name' => $teacher_name,
            ':id' => $e_id
        ]);
        header("Location: ../views/teacher.php?msg=Teacher info updated successfully");
        exit;
    }
}

// DELETE
if (isset($_GET['del_id'])) {
    $id = $_GET['del_id'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM subjects WHERE teacher_id = :id");
    $stmt->execute([':id' => $id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        header("Location: ../views/teacher.php?msg=Cannot delete. Teacher has subjects.");
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM teacher WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: ../views/teacher.php?msg=Teacher deleted successfully");
    exit;
}
