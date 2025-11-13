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


$sql = "SELECT s.subject_code AS code, s.name, s.days, s.start_time, s.end_time, 
               t.teacher_name, r.room_name, s.price_unit, s.unit
        FROM students_subjects ss
        JOIN semesters se ON ss.semester_id = se.id
        JOIN subjects s ON ss.subject_id = s.subject_id
        JOIN teacher t ON s.teacher_id = t.id
        JOIN room r ON s.room_id = r.id
        JOIN students st ON ss.student_id = st.id
        WHERE st.stud_no = ? AND ss.semester_id = ?";
$subjStmt = $pdo->prepare($sql);
$subjStmt->execute([$student_number, $semester_id]);
$enrolled_subjects = $subjStmt->fetchAll(PDO::FETCH_ASSOC);


$total_units = 0;
$total_price = 0;
foreach ($enrolled_subjects as $enrolled_subject) {
    $total_units += $enrolled_subject['unit'];
    $total_price += $enrolled_subject['price_unit'] * $enrolled_subject['unit'];
}


$pdf = new FPDF();
$pdf->AddPage();


$pdf->SetFont('Arial', 'B', 15);
$pdf->Cell(0, 10, 'ASSESSMENT FORM', 0, 1, 'C'); 
$pdf->Ln(5);


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Student Number:', 0, 0, 'L'); 
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(70, 10, $student['stud_no'], 0, 0, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Gender:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, $student['gender'], 0, 1, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Student Name:', 0, 0, 'L'); 
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(70, 10, $student['stud_name'], 0, 0, 'L');

$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Course:', 0, 0, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 10, $student['course_name'], 0, 1, 'L');

$pdf->SetFont('Arial','B',12);
$pdf->Cell(40,8,'Semester:',0,0);
$pdf->SetFont('Arial','',12);
$pdf->Cell(70,8,$student['semester_code'],0,1);

$pdf->Ln(5);


$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(25, 10, 'Code', 1, 0, 'C');
$pdf->Cell(50, 10, 'Subject Name', 1, 0, 'C');
$pdf->Cell(20, 10, 'Days', 1, 0, 'C');
$pdf->Cell(30, 10, 'Time', 1, 0, 'C');
$pdf->Cell(30, 10, 'Teacher', 1, 0, 'C');
$pdf->Cell(20, 10, 'Room', 1, 0, 'C');
$pdf->Cell(15, 10, 'Units', 1, 1, 'C');

$pdf->SetFont('Arial', '', 11);
foreach ($enrolled_subjects as $subj) {

        $start = date("H:i", strtotime($subj['start_time'])); // 09:00
    $end   = date("H:i", strtotime($subj['end_time']));   // 10:30
    $time  = $start . '-' . $end;

    
    $pdf->Cell(25, 10, $subj['code'], 1, 0, 'C');
    $pdf->Cell(50, 10, $subj['name'], 1, 0, 'L');
    $pdf->Cell(20, 10, $subj['days'], 1, 0, 'C');
    $pdf->Cell(30, 10, $time, 1, 0, 'C');
    $pdf->Cell(30, 10, $subj['teacher_name'], 1, 0, 'C');
    $pdf->Cell(20, 10, $subj['room_name'], 1, 0, 'C');
    $pdf->Cell(15, 10, $subj['unit'], 1, 1, 'C');
}


$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(160, 10, 'Total Units:', 0, 0, 'R');
$pdf->Cell(30, 10, $total_units, 1, 1, 'C');

$pdf->Cell(160, 10, 'Total Price:', 0, 0, 'R');
$pdf->Cell(30, 10, number_format($total_price, 2), 1, 1, 'C');


$pdf->Output();
?>
