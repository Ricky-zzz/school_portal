<?php
require("../partials/conn.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ADD
    if (isset($_POST['addcourse'])) {
        $stmt = $pdo->prepare("INSERT INTO courses (name) VALUES (:name)");
        $stmt->execute([':name' => htmlspecialchars($_POST['addcourse'])]);
        header("Location: ../views/courses.php?msg=Course added successfully");
        exit;
    }

    if (isset($_POST['editcourse_id'], $_POST['editcourse_name'])) {
        $stmt = $pdo->prepare("UPDATE courses SET name = :name WHERE course_id = :id");
        $stmt->execute([
            ':name' => htmlspecialchars($_POST['editcourse_name']),
            ':id' => $_POST['editcourse_id']
        ]);
        header("Location: ../views/courses.php?msg=Course updated successfully");
        exit;
    }
}

// DELETE
if (isset($_GET['del_id'])) {
    $id = $_GET['del_id'];

    $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE course_id = :id");
    $stmt->execute([':id' => $id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        header("Location: ../views/courses.php?msg=Cannot delete. Students are enrolled in this course.");
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM courses WHERE course_id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: ../views/courses.php?msg=Course deleted successfully");
    exit;
}
