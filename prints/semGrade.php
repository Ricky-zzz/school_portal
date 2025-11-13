<?php
session_start(); 
require('../partials/conn.php');  
require('../fpdf/fpdf.php');  

if (!isset($_SESSION['stud_no'])) {
    echo "No student selected.";
    exit;
}

$student_number = $_SESSION["stud_no"];
$semester_id = $_SESSION['sem'] ?? null;

// Fetch student info
$studentSql = "SELECT students.stud_no, students.name AS stud_name, students.gender, 
                      courses.name AS course_name,
                      sem.code AS semester_code
               FROM students 
               LEFT JOIN courses ON courses.course_id = students.course_id
               LEFT JOIN semesters sem ON sem.id = ?
               WHERE students.stud_no = ?";
$studentStmt = $pdo->prepare($studentSql);
$studentStmt->execute([$semester_id, $student_number]);
$student = $studentStmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo "Student not found.";
    exit;
}

// Fetch subjects with grades 
$sql = "SELECT s.subject_code AS code, s.name, s.unit, ss.mid, ss.fcg
        FROM students_subjects ss
        JOIN subjects s ON ss.subject_id = s.subject_id
        JOIN students st ON ss.student_id = st.id
        WHERE st.stud_no = ? AND ss.semester_id = ?";
$subjStmt = $pdo->prepare($sql);
$subjStmt->execute([$student_number, $semester_id]);
$subjects = $subjStmt->fetchAll(PDO::FETCH_ASSOC);

// Compute GPA
$total_units = 0;
$total_points = 0;

foreach ($subjects as $s) {
    if ($s['fcg'] !== null) {
        $total_units += $s['unit'];
        $total_points += $s['fcg'] * $s['unit'];
    }
}
$gpa = ($total_units > 0) ? ($total_points / $total_units) : 0;

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();

// Header
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 10, 'SEMESTER GRADE REPORT', 0, 1, 'C');
$pdf->Ln(5);

// Student Info
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Student No:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 10, $student['stud_no'], 0, 0, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Gender:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, $student['gender'], 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Name:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 10, $student['stud_name'], 0, 0, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Course:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, $student['course_name'], 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Semester:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, $student['semester_code'], 0, 1, 'L');

$pdf->Ln(8);

// Table Header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Subject Code', 1, 0, 'C');
$pdf->Cell(70, 10, 'Subject Name', 1, 0, 'C');
$pdf->Cell(20, 10, 'Units', 1, 0, 'C');
$pdf->Cell(30, 10, 'Midterm', 1, 0, 'C');
$pdf->Cell(30, 10, 'Final Grade', 1, 1, 'C');

// Table Rows
$pdf->SetFont('Arial', '', 11);
foreach ($subjects as $s) {
    $pdf->Cell(40, 10, $s['code'], 1, 0, 'C');
    $pdf->Cell(70, 10, $s['name'], 1, 0, 'L');
    $pdf->Cell(20, 10, $s['unit'], 1, 0, 'C');
    $pdf->Cell(30, 10, $s['mid'] ?? 'N/A', 1, 0, 'C');
    $pdf->Cell(30, 10, $s['fcg'] ?? 'N/A', 1, 1, 'C');
}

// Footer
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(130, 10, 'GPA:', 0, 0, 'R');
$pdf->Cell(30, 10, number_format($gpa, 2), 1, 1, 'C');

$pdf->Output();
?>
