<?php
session_start();

if (empty($_SESSION["student_id"])) {
    header("Location: ../index.php");
    exit;
}