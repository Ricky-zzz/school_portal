<?php
session_start();
require('../partials/conn.php');
require('../fpdf/fpdf.php');


$semester_id = $_SESSION['sem'] ?? 'all';
$course_id = $_SESSION['crs'] ?? 'all';
$subject_id = $_SESSION['sub'] ?? 'all';


$sql = "
    SELECT st.stud_no, st.name AS stud_name, st.gender, 
           c.name AS course_name, se.code AS semester_code,
           sb.subject_code, sb.name AS subject_name,
           ss.mid, ss.fcg
FROM students_subjects ss
LEFT JOIN students st ON ss.student_id = st.id
LEFT JOIN subjects sb ON ss.subject_id = sb.subject_id
LEFT JOIN courses c ON st.course_id = c.course_id
LEFT JOIN semesters se ON ss.semester_id = se.id

    WHERE 1
";

$params = [];

if ($semester_id !== 'all') {
    $sql .= " AND se.id = ?";
    $params[] = $semester_id;
}
if ($course_id !== 'all') {
    $sql .= " AND c.course_id = ?";
    $params[] = $course_id;
}
if ($subject_id !== 'all') {
    $sql .= " AND sb.subject_id = ?";
    $params[] = $subject_id;
}

$sql .= " ORDER BY se.code ASC, c.name ASC, st.name ASC, sb.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$records) {
    echo "No records found for batch printing.";
    exit;
}

// Create PDF
$pdf = new FPDF('L', 'mm', 'A4'); // Landscape
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 10, 'BATCH PRINTING - STUDENT GRADES', 0, 1, 'C');
$pdf->Ln(5);

// Table Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Student #', 1, 0, 'C');
$pdf->Cell(30, 10, 'Name', 1, 0, 'C');
$pdf->Cell(20, 10, 'Gender', 1, 0, 'C');
$pdf->Cell(40, 10, 'Course', 1, 0, 'C');
$pdf->Cell(50, 10, 'Semester', 1, 0, 'C');
$pdf->Cell(55, 10, 'Subject', 1, 0, 'C');
$pdf->Cell(25, 10, 'Midterm', 1, 0, 'C');
$pdf->Cell(25, 10, 'Final Grade', 1, 1, 'C');

// Table Data
$pdf->SetFont('Arial', '', 11);

$lastSem = $lastCourse = $lastStud = '';

foreach ($records as $row) {
    $sameStudent = ($row['stud_no'] === $lastStud);
    $sameCourse = ($row['course_name'] === $lastCourse);
    $sameSem = ($row['semester_code'] === $lastSem);

    // Student #
    if (!$sameStudent) {
        $pdf->Cell(30, 10, $row['stud_no'], 1, 0, 'C');
        $lastStud = $row['stud_no'];
    } else {
        $pdf->Cell(30, 10, '', 1, 0, 'C');
    }

    // Name
    if (!$sameStudent) {
        $pdf->Cell(30, 10, $row['stud_name'], 1, 0, 'L');
    } else {
        $pdf->Cell(30, 10, '', 1, 0, 'L');
    }

    // Gender
    if (!$sameStudent) {
        $pdf->Cell(20, 10, $row['gender'], 1, 0, 'C');
    } else {
        $pdf->Cell(20, 10, '', 1, 0, 'C');
    }

    // Course
    if (!$sameCourse) {
        $pdf->Cell(40, 10, $row['course_name'], 1, 0, 'L');
        $lastCourse = $row['course_name'];
    } else {
        $pdf->Cell(40, 10, '', 1, 0, 'L');
    }

    // Semester
    if (!$sameSem) {
        $pdf->Cell(50, 10, $row['semester_code'], 1, 0, 'C');
        $lastSem = $row['semester_code'];
    } else {
        $pdf->Cell(50, 10, '', 1, 0, 'C');
    }

    // Subject
    $pdf->Cell(55, 10, $row['subject_name'], 1, 0, 'L');
    $pdf->Cell(25, 10, $row['mid'] ?? 'N/A', 1, 0, 'C');
    $pdf->Cell(25, 10, $row['fcg'] ?? 'N/A', 1, 1, 'C');
}

$pdf->Output();
?>