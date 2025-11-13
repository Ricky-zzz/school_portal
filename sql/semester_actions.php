<?php
require("../partials/conn.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Add semester
    if (isset($_POST['addsemester_code'], $_POST['startdate'], $_POST['enddate'], $_POST['summer'])) {
        $stmt = $pdo->prepare("INSERT INTO semesters (code, start_date, end_date, summer) VALUES (:code, :start_date, :end_date, :summer)");
        $stmt->execute([
            ':code' => htmlspecialchars($_POST['addsemester_code']),
            ':start_date' => $_POST['startdate'],
            ':end_date' => $_POST['enddate'],
            ':summer' => htmlspecialchars($_POST['summer']),
        ]);
        header("Location: ../views/semester.php?msg=Semester added successfully");
        exit;
    }

    // Edit semester
    if (isset($_POST['editsemester_id'], $_POST['editsemester_code'], $_POST['edit_startdate'], $_POST['edit_enddate'], $_POST['edit_summer'])) {
        $stmt = $pdo->prepare("UPDATE semesters SET code = :code, start_date = :start_date, end_date = :end_date, summer = :summer WHERE id = :id");
        $stmt->execute([
            ':code' => htmlspecialchars($_POST['editsemester_code']),
            ':start_date' => $_POST['edit_startdate'],
            ':end_date' => $_POST['edit_enddate'],
            ':summer' => htmlspecialchars($_POST['edit_summer']),
            ':id' => $_POST['editsemester_id'],
        ]);
        header("Location: ../views/semester.php?msg=Semester updated successfully");
        exit;
    }
}

// DELETE semester
if (isset($_GET['del_id'])) {
    $id = $_GET['del_id'];
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM students_subjects WHERE semester_id = :id");
    $stmt->execute([':id' => $id]);
    $count = $stmt->fetchColumn();

    if ($count > 0) {
        header("Location: ../views/semester.php?msg=Cannot delete. Students are enrolled in this semester.");
        exit;
    }
    $stmt = $pdo->prepare("DELETE FROM semesters WHERE id = :id");
    $stmt->execute([':id' => $id]);
    header("Location: ../views/semester.php?msg=Semester deleted successfully");
    exit;
}
?>
