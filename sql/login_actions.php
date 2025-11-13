<?php
session_start();
require("../partials/conn.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
                header("Location: ../index.php");
                exit;
            }
        } else {
            $_SESSION["error"] = "Please fill in all fields.";
            header("Location: ../index.php");
            exit;
        }
    }

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
                header("Location: ../login.php");
                exit;
            }
        } else {
            $_SESSION["error"] = "Please fill in all fields.";
            header("Location: ../login.php");
            exit;
        }
    }
}

// Logout handler
if (isset($_GET['logout']) && $_GET['logout'] === 'y') {
    $user_type = $_SESSION["user_type"] ?? '';
    session_unset();
    session_destroy();
    if ($user_type === "admin") {
        header("Location: ../login.php");
    } else {
        header("Location: ../index.php");
    }
    exit;
}


