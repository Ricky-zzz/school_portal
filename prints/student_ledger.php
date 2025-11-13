<?php
session_start(); 
require('../partials/conn.php');  
require('../fpdf/fpdf.php');  

$student_id = $_GET['stud_id'] ?? 0;
$semester_id = $_GET['sem_id'] ?? 0;

// Fetch student info
$student_sql = "SELECT s.stud_no, s.name, s.gender, c.name AS course_name
FROM students s
LEFT JOIN courses c ON s.course_id = c.course_id
WHERE s.id = :student_id";
$stmt = $pdo->prepare($student_sql);
$stmt->execute([':student_id' => $student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    echo "Student not found.";
    exit;
}

// Fetch semester info
$sem_sql = "SELECT code FROM semesters WHERE id = ?";
$sem_stmt = $pdo->prepare($sem_sql);
$sem_stmt->execute([$semester_id]); 
$semester = $sem_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch Original Balance
$balance_query = $pdo->prepare("
    SELECT COALESCE(SUM(s.price_unit * s.unit), 0) AS total_tuition
    FROM students_subjects ss
    JOIN subjects s ON ss.subject_id = s.subject_id
    WHERE ss.student_id = ? AND ss.semester_id = ?
");
$balance_query->execute([$student_id, $semester_id]);
$balance = (float) $balance_query->fetchColumn();

// Fetch all payments done
$p_sql = "SELECT * FROM collections WHERE student_id = ? AND semester_id = ?";
$p_stmt = $pdo->prepare($p_sql);    
$p_stmt->execute([$student_id, $semester_id]);
$payments = $p_stmt->fetchAll(PDO::FETCH_ASSOC);

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();

// Set margins and table start
$pdf->SetLeftMargin(25);
$pdf->SetRightMargin(25);
$start_x = $pdf->GetX();

// Header
$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 10, 'Student Ledger', 0, 1, 'C');
$pdf->Ln(5);

// Student Info
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, 'Student No:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, $student['stud_no'], 0, 0, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 8, 'Gender:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 8, $student['gender'], 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, 'Name:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(60, 8, $student['name'], 0, 0, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 8, 'Course:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 8, $student['course_name'], 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 8, 'Semester:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 8, $semester['code'], 0, 1, 'L');

$pdf->Ln(8);

// Table header
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetX($start_x);
$pdf->Cell(37, 10, 'OR #', 1, 0, 'C');
$pdf->Cell(37, 10, 'Date', 1, 0, 'C');
$pdf->Cell(37, 10, 'Type', 1, 0, 'C');
$pdf->Cell(52, 10, 'Amount', 1, 1, 'C');

$pdf->SetFont('Arial', '', 12);
$total_payments = 0.0;

// Table rows
foreach ($payments as $pay) {
    $or_no = $pay['or_number'];
    $date = date('Y-m-d', strtotime($pay['or_date']));
    $type = $pay['cash'] > 0 ? 'Cash' : 'GCash';
    $amount = $pay['cash'] > 0 ? (float)$pay['cash'] : (float)$pay['gcash'];

    $total_payments += $amount;

    $pdf->SetX($start_x);
    $pdf->Cell(37, 10, $or_no, 1, 0, 'C');
    $pdf->Cell(37, 10, $date, 1, 0, 'C');
    $pdf->Cell(37, 10, $type, 1, 0, 'C');
    $pdf->Cell(52, 10, number_format($amount, 2), 1, 1, 'R');
}

$pdf->Ln(8);
$pdf->SetFont('Arial', 'B', 13);
// Original Balance - Total Payments row
$pdf->SetX($start_x);
$pdf->Cell(111, 10, 'Original Balance - Total Payments', 1, 0, 'R');
$pdf->Cell(52, 10, number_format($balance, 2) . ' - ' . number_format($total_payments, 2), 1, 1, 'R');

// Remaining Balance row
$remainder = $balance - $total_payments;
$pdf->SetX($start_x);
$pdf->Cell(111, 10, 'Remaining Balance', 1, 0, 'R');
$pdf->Cell(52, 10, number_format($remainder, 2), 1, 1, 'R');

$pdf->Output();
?>
