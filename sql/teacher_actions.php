<?php
require("../partials/conn.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // ADD
    if (isset($_POST['addteacher'])) {
        $teacher_name = htmlspecialchars(trim($_POST['addteacher'])); 
        
        $name_parts = explode(' ', $teacher_name);
        $initials = '';
        foreach ($name_parts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        }
        
        $stmt_id = $pdo->query("SELECT MAX(id) AS max_id FROM teacher");
        $result = $stmt_id->fetch(PDO::FETCH_ASSOC);
        
        $next_id = ($result['max_id'] === null) ? 1 : $result['max_id'] + 1;
        
        $sequential_num = str_pad($next_id, 3, '0', STR_PAD_LEFT);
        
        $teacher_code = $initials .'-'. $sequential_num;

        $stmt = $pdo->prepare("INSERT INTO teacher (teacher_name, teacher_code) VALUES (:teacher_name, :teacher_code)");
        $stmt->execute(
            [
                ':teacher_name' => $teacher_name,
                ':teacher_code' => $teacher_code
            ]
        );
        
        header("Location: ../views/teacher.php?msg=New Teacher added successfully with code: " . $teacher_code);
        exit;
    }

    if (isset($_POST['edit_id'], $_POST['edit_teacher'])) {
        $teacher_name = htmlspecialchars($_POST['edit_teacher']);
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
