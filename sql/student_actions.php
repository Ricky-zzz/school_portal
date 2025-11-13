<?php
require("../partials/conn.php");

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($action === 'add') {
    $data = json_decode($_POST['data'], true);

    $sql = "INSERT INTO students (stud_no, name, gender, course_id) 
            VALUES (:stud_no, :name, :gender, :course_id)";
    $stmt = $pdo->prepare($sql);
    
    $stmt->execute([
        ":stud_no"   => $data['student_number'],
        ":name"      => $data['student_name'],
        ":gender"    => $data['gender'],
        ":course_id" => $data['course_id']
    ]);

    header("Location: ../views/student.php?msg=Student added successfully");
    exit;
}

if ($action === 'edit') {
    $data = json_decode($_POST['data'], true);

    $sql = "UPDATE students 
            SET stud_no = :stud_no, name = :name, gender = :gender, course_id = :course_id 
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ":stud_no"   => $data['student_number'],
        ":name"      => $data['student_name'],
        ":gender"    => $data['gender'],
        ":course_id" => $data['course_id'],
        ":id"        => $data['id']
    ]);

    header("Location: ../views/student.php?msg=Student updated successfully");
    exit;
}

if ($action === 'delete') {
    $id = intval($_GET['id']);

    $sql = "DELETE FROM students WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":id" => $id]);

    header("Location: ../views/student.php?msg=Student deleted successfully");
    exit;
}
