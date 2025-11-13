<?php
require('../partials/conn.php');  
require('../fpdf/fpdf.php');  


$sql = "SELECT students.stud_no, students.name AS stud_name, students.gender, 
               courses.name AS course_name 
        FROM students 
        LEFT JOIN courses ON courses.course_id = students.course_id";
$stmt = $pdo->query($sql);
$students = $stmt->fetchAll();


$count_query = "SELECT COUNT(*) AS student_count FROM students";
$count_stmt = $pdo->query($count_query);
$count_row = $count_stmt->fetch();
$student_count = $count_row['student_count'];


$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 10, 'STUDENT RECORDS', 0, 1, 'C'); 
$pdf->Ln();

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Student #', 1, 0, 'C'); 
$pdf->Cell(70, 10, 'Student Name', 1, 0, 'C');
$pdf->Cell(30, 10, 'Gender', 1, 0, 'C');
$pdf->Cell(40, 10, 'Course', 1, 1, 'C'); 

$pdf->SetFont('Arial', '', 12);
if ($students) {
    foreach ($students as $row) {
        $pdf->Cell(40, 10, $row['stud_no'], 1, 0, 'C');
        $pdf->Cell(70, 10, $row['stud_name'], 1, 0, 'C');
        $pdf->Cell(30, 10, $row['gender'], 1, 0, 'C');
        $pdf->Cell(40, 10, $row['course_name'], 1, 1, 'C');
    }
}

$pdf->Ln();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(160, 10, 'Total Records:', 0, 0, 'R');
$pdf->Cell(20, 10, $student_count, 1, 1, 'C'); 

$pdf->Output();
?>
