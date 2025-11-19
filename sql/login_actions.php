<?php
session_start();
require("../partials/conn.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // 1. STUDENT LOGIN HANDLING
    if (isset($_POST["student_number"])) {
        $stud_no = trim($_POST["student_number"]);
        $password = trim($_POST["password"]);

        if ($stud_no !== "" && $password !== "") {
            $stmt = $pdo->prepare("SELECT * FROM students WHERE stud_no = ?");
            $stmt->execute([$stud_no]);
            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($student && $password === $student["pass"]) {
                $_SESSION["student_id"] = $student["id"];
                $_SESSION["student_name"] = $student["name"];
                $_SESSION["student_number"] = $student["stud_no"];
                $_SESSION["user_type"] = "student";

                $cq = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
                $cq->execute([$student["course_id"]]);
                $course = $cq->fetch(PDO::FETCH_ASSOC);
                $_SESSION['course'] = $course["name"];

                header("Location: ../student/menu.php");
                exit; 
            } else {
                $_SESSION["error"] = "Invalid student number or password.";
            }
        } else {
            $_SESSION["error"] = "Please fill in all fields.";
        }
        
        header("Location: ../index.php");
        exit;
    }

    // 2. ADMIN LOGIN HANDLING
    else if (isset($_POST["username"])) {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);

        if ($username !== "" && $password !== "") {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE name = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && $password === $user["password"]) {
                $_SESSION["user_id"] = $user["id"];
                $_SESSION["username"] = $user["name"];
                $_SESSION["user_type"] = "admin";

                header("Location: ../menu.php");
                exit; 
            } else {
                $_SESSION["error"] = "Invalid username or password.";
            }
        } else {
            $_SESSION["error"] = "Please fill in all fields.";
        }

        header("Location: ../login.php");
        exit;
    }

    // 3. TEACHER LOGIN HANDLING
    else if (isset($_POST["teacher_code"])) {
        $teacher_code = trim($_POST["teacher_code"]); 
        $password = trim($_POST["password"]);

        if ($teacher_code !== "" && $password !== "") {
            $stmt = $pdo->prepare("SELECT * FROM teacher WHERE teacher_code = ?");
            $stmt->execute([$teacher_code]);
            $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($teacher && $password === $teacher["password"]) {
                $_SESSION["teacher_id"] = $teacher["id"];
                $_SESSION["teacher_name"] = $teacher["teacher_name"];
                $_SESSION["teacher_code"] = $teacher["teacher_code"];
                $_SESSION["user_type"] = "teacher";

                header("Location: ../teacher/menu.php");
                exit; 
            } else {
                $_SESSION["error"] = "Invalid teacher code or password."; 
            }
        } else {
            $_SESSION["error"] = "Please fill in all fields.";
        }
        
        header("Location: ../login2.php");
        exit;
    }
}

if (isset($_GET['logout']) && $_GET['logout'] === 'y') {
    $user_type = $_SESSION["user_type"] ?? '';
    session_unset();
    session_destroy();
    if ($user_type === "admin") {
        header("Location: ../login.php");
    } else if ($user_type === "student") {
        header("Location: ../index.php");
    }
    else {
        header("Location: ../login2.php");
    }
    exit;
}