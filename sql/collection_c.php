<?php
session_start();
require("../partials/conn.php");
require("dd.php");

function get_prices(PDO $pdo, int $student_id, int $semester_id): float
{
    $query = $pdo->prepare("
        SELECT SUM(s.price_unit * s.unit) AS total_tuition
        FROM students_subjects ss
        JOIN subjects s ON ss.subject_id = s.subject_id
        WHERE ss.student_id = ? AND ss.semester_id = ?
    ");
    $query->execute([$student_id, $semester_id]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return (float) ($result['total_tuition'] ?? 0.00);
}

function get_payments(PDO $pdo, int $student_id, int $semester_id): float
{
    $query = $pdo->prepare("
        SELECT IFNULL(SUM(cash + gcash), 0) AS total_payment
        FROM collections
        WHERE student_id = ? AND semester_id = ?
    ");
    $query->execute([$student_id, $semester_id]);
    $result = $query->fetch(PDO::FETCH_ASSOC);
    return (float) ($result['total_payment'] ?? 0.00);
}

function record_audit(PDO $pdo, int $user_id, string $module, string $refno, string $action): void
{
    $sql = "INSERT INTO audit_trail (user_id, module, refno, action) 
            VALUES (:user_id, :module, :refno, :action)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':user_id' => $user_id,
        ':module' => $module,
        ':refno' => $refno,
        ':action' => $action
    ]);
}

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

// serach student by stud no
if ($action === 'get_student') {
    $stud_no = $_GET['stud_no'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM students WHERE stud_no = ?");
    $stmt->execute([$stud_no]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    $c_stmt = $pdo->prepare("SELECT * FROM courses WHERE course_id = ?");
    $c_stmt->execute([$student["course_id"]]);
    $course = $c_stmt->fetch(PDO::FETCH_ASSOC);

    $student['course'] = $course ? $course['name'] : null;

    if (!$student) {
        echo json_encode([
            "error" => true,
            "message" => "Student not found"
        ]);
        exit;
    }


    echo json_encode($student);
    exit;
}

// get student balance
if ($action === 'get_balance') {
    $stud_id = $_GET['stud_id'] ?? '';
    $sem_id = $_GET['sem_id'] ?? '';

    if (empty($stud_id) || empty($sem_id)) {
        echo json_encode(['error' => true, 'message' => 'Missing parameters']);
        exit;
    }

    $studentQuery = $pdo->prepare("SELECT id FROM students WHERE id = ?");
    $studentQuery->execute([$stud_id]);
    $studentResult = $studentQuery->fetch(PDO::FETCH_ASSOC);

    if (!$studentResult) {
        echo json_encode(['error' => true, 'message' => 'Student not found']);
        exit;
    }

    $student_id = (int) $studentResult['id'];

    // Use helper functions
    $original_price = get_prices($pdo, $student_id, (int) $sem_id);
    $total_payment = get_payments($pdo, $student_id, (int) $sem_id);

    $balance = $original_price - $total_payment;
    if ($balance < 0)
        $balance = 0;

    echo json_encode([
        'student_id' => $student_id,
        'semester_id' => $sem_id,
        'original_price' => round($original_price, 2),
        'total_payment' => round($total_payment, 2),
        'balance' => round($balance, 2)
    ]);
    exit;
}

// get next Official Receipt number
if ($action === 'get_last_or') {
    $stmt = $pdo->query("SELECT MAX(or_number) AS last_or FROM collections");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $last_or = $row['last_or'] ?? "000000";
    $next_or = str_pad(((int) $last_or) + 1, 6, "0", STR_PAD_LEFT);
    echo json_encode(["next_or" => $next_or]);
    exit;
}

// ready all semesters
if ($action === 'get_semester') {
    $sql = "SELECT * from semesters";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $semester = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($semester ?: []);
    exit;
}

// search via name (grouped results)
if ($action === 'search_student') {
    $search = $_GET['name'] ?? '';

    $stmt = $pdo->prepare("
        SELECT 
            s.id AS student_id,
            s.stud_no,
            s.name AS student_name,
            c.name AS course_name,
            co.or_number,
            co.or_date
        FROM students s
        JOIN courses c ON s.course_id = c.course_id
        JOIN collections co ON s.id = co.student_id
        WHERE s.name LIKE :search
        ORDER BY co.or_date DESC
        LIMIT 20
    ");
    $stmt->execute([':search' => "%$search%"]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $grouped = [];
    foreach ($rows as $row) {
        $id = $row['student_id'];

        if (!isset($grouped[$id])) {
            $grouped[$id] = [
                'student_id' => $row['student_id'],
                'stud_no' => $row['stud_no'],
                'student_name' => $row['student_name'],
                'course_name' => $row['course_name'],
                'or_numbers' => []
            ];
        }

        $grouped[$id]['or_numbers'][] = [
            'or_number' => $row['or_number'],
            'or_date' => $row['or_date']
        ];
    }

    echo json_encode(array_values($grouped));
    exit;
}


if ($action === 'search_receipt') {
    $or_number = $_GET['or_number'] ?? '';

    // Fetch receipt
    $stmt = $pdo->prepare("SELECT * FROM collections WHERE or_number = ?");
    $stmt->execute([$or_number]);
    $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$receipt) {
        echo json_encode([
            "error" => true,
            "message" => "Receipt does not exist"
        ]);
        exit;
    }

    // Fetch student
    $s_stmt = $pdo->prepare("SELECT id, stud_no, name, course_id FROM students WHERE id = ?");
    $s_stmt->execute([$receipt["student_id"]]);
    $student = $s_stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch course
    $c_stmt = $pdo->prepare("SELECT name FROM courses WHERE course_id = ?");
    $c_stmt->execute([$student["course_id"]]);
    $course = $c_stmt->fetch(PDO::FETCH_ASSOC);

    // Merge all data into receipt
    $receipt["student"] = [
        "id" => $student["id"],
        "stud_no" => $student["stud_no"],
        "name" => $student["name"],
        "course" => $course["name"] ?? null
    ];

    echo json_encode([
        "success" => true,
        "receipt" => $receipt
    ]);
    exit;
}

// commit
$message = "";

if ($action === 'commit_transaction') {
    $student_id = $_POST['student_id'] ?? null;
    $semester_id = $_POST['semester_id'] ?? null;
    $or_number = $_POST['or_number'] ?? null;
    $payment = $_POST['payment'] ?? 0;
    $method = $_POST['method'] ?? null;
    $reference = $_POST['reference'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$student_id || !$semester_id || !$or_number || !$method) {
        echo json_encode(["error" => true, "message" => "Missing required fields"]);
        exit;
    }

    $sql = "INSERT INTO collections 
            (or_number, student_id, semester_id, cash, gcash, gcash_refno, user_id)
            VALUES (:or_number, :student_id, :semester_id, :cash, :gcash, :gcash_refno, :user_id)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':or_number' => $or_number,
        ':student_id' => $student_id,
        ':semester_id' => $semester_id,
        ':cash' => $method === 'cash' ? $payment : 0,
        ':gcash' => $method === 'gcash' ? $payment : 0,
        ':gcash_refno' => $method === 'gcash' ? $reference : null,
        ':user_id' => $user_id
    ]);

    record_audit($pdo, $user_id, 'collections', $or_number, 'A');

    echo json_encode(["success" => true, "message" => "Transaction committed successfully."]);
    exit;
}

if ($action === 'edit_transaction') {
    $student_id = $_POST['student_id'] ?? null;
    $semester_id = $_POST['semester_id'] ?? null;
    $or_number = $_POST['or_number'] ?? null;
    $payment = $_POST['payment'] ?? 0;
    $method = $_POST['method'] ?? null;
    $reference = $_POST['reference'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$student_id || !$semester_id || !$or_number || !$method) {
        echo json_encode(["error" => true, "message" => "Missing required fields"]);
        exit;
    }

    $sql = "UPDATE collections 
            SET student_id = :student_id,
                semester_id = :semester_id,
                cash = :cash,
                gcash = :gcash,
                gcash_refno = :gcash_refno,
                user_id = :user_id
            WHERE or_number = :or_number";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':student_id' => $student_id,
        ':semester_id' => $semester_id,
        ':cash' => $method === 'cash' ? $payment : 0,
        ':gcash' => $method === 'gcash' ? $payment : 0,
        ':gcash_refno' => $method === 'gcash' ? $reference : null,
        ':user_id' => $user_id,
        ':or_number' => $or_number
    ]);

    record_audit($pdo, $user_id, 'collections', $or_number, 'E'); // “U” for Update

    echo json_encode(["success" => true, "message" => "Transaction updated successfully."]);
    exit;
}

if ($action === 'deleteTransaction') {
    $or_number = $_POST['or_number'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$or_number) {
        echo json_encode(["error" => true, "message" => "Missing Receipt Number."]);
        exit;
    }

    $check = $pdo->prepare("SELECT COUNT(*) FROM collections WHERE or_number = :or_number");
    $check->execute([":or_number" => $or_number]);
    $exists = $check->fetchColumn();

    if (!$exists) {
        echo json_encode(["error" => true, "message" => "Transaction not found."]);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM collections WHERE or_number = :or_number");
    $stmt->execute([":or_number" => $or_number]);

    if ($user_id) {
        record_audit($pdo, $user_id, 'collections', $or_number, 'D');
    }

    echo json_encode(["success" => true, "message" => "Transaction deleted successfully."]);
    exit;
}

if ($action === 'get_balances') {
    $student_id = $_GET['stud_id'] ?? 0;
    $semester_id = $_GET['sem_id'] ?? 0;

    if (!$student_id || !$semester_id) {
        echo json_encode(["error" => true, "message" => "Missing parameters."]);
        exit;
    }

    $total_tuition = get_prices($pdo, $student_id, (int) $semester_id);


    $p_sql = "SELECT or_number, or_date, cash, gcash 
              FROM collections 
              WHERE student_id = ? AND semester_id = ?
              ORDER BY or_date ASC";
    $p_stmt = $pdo->prepare($p_sql);
    $p_stmt->execute([$student_id, $semester_id]);
    $payments = $p_stmt->fetchAll(PDO::FETCH_ASSOC);

    $total_payments = 0;
    foreach ($payments as $p) {
        $total_payments += ($p['cash'] > 0 ? (float) $p['cash'] : (float) $p['gcash']);
    }

    $remaining = $total_tuition - $total_payments;

    echo json_encode([
        "error" => false,
        "tuition" => $total_tuition,
        "total_paid" => $total_payments,
        "remaining" => $remaining,
        "payments" => $payments
    ]);
    exit;
}





