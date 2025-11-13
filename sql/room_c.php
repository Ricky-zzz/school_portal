<?php
header('Content-Type: application/json; charset=utf-8');
session_start();
require("../partials/conn.php");
require("dd.php");

$action = $_GET['action'] ?? ($_POST['action'] ?? '');
$response = [
    "success" => false,
    "message" => "Invalid action"
];

try {
    if ($action === 'get_rooms') {
        $rooms = $pdo->query("SELECT * FROM room ORDER BY room_name")->fetchAll(PDO::FETCH_ASSOC);  
        $response = [
            "success" => true,
            "message" => "Rooms fetched successfully.",
            "rooms" => $rooms ?: []
        ];
    }

    elseif ($action === 'add') {
        $room_name = htmlspecialchars(trim($_POST['newRoom'] ?? ''));
        
        if (empty($room_name)) {
            throw new Exception("Room name cannot be empty");
        }

        $check = $pdo->prepare("SELECT COUNT(*) FROM room WHERE room_name = ?");
        $check->execute([$room_name]);
        if ($check->fetchColumn() > 0) {
            throw new Exception("Room name already exists");
        }

        $stmt = $pdo->prepare("INSERT INTO room (room_name) VALUES (?)");
        $stmt->execute([$room_name]);

        $rooms = $pdo->query("SELECT * FROM room ORDER BY room_name")->fetchAll(PDO::FETCH_ASSOC);

        $response = [
            "success" => true,
            "message" => "Room added successfully.",
            "rooms" => $rooms
        ];
    }

    elseif ($action === 'delete') {
        $id = $_POST['del_id'] ?? null;
        if (!$id) {
            throw new Exception("Invalid room ID");
        }

        $counter = $pdo->prepare("SELECT COUNT(*) FROM subjects WHERE room_id = ?");
        $counter->execute([$id]);
        if ($counter->fetchColumn() > 0) {
            throw new Exception("Cannot delete room: it is assigned to one or more subjects.");
        }
        
        $stmt = $pdo->prepare("DELETE FROM room WHERE id = ?");
        $stmt->execute([$id]);

        $rooms = $pdo->query("SELECT * FROM room ORDER BY room_name")->fetchAll(PDO::FETCH_ASSOC);

        $response = [
            "success" => true,
            "message" => "Room deleted successfully.",
            "rooms" => $rooms
        ];
    }

    elseif ($action === 'edit') {
        $id = $_POST['edit_id'] ?? null;
        $new_name = htmlspecialchars(trim($_POST['newName'] ?? ''));
        
        if (!$id) {
            throw new Exception("Invalid room ID");
        }
        if (empty($new_name)) {
            throw new Exception("Room name cannot be empty");
        }

        $check = $pdo->prepare("SELECT COUNT(*) FROM room WHERE room_name = ? AND id != ?");
        $check->execute([$new_name, $id]);
        if ($check->fetchColumn() > 0) {
            throw new Exception("Room name already exists");
        }

        $stmt = $pdo->prepare("UPDATE room SET room_name = ? WHERE id = ?");
        $stmt->execute([$new_name, $id]);

        $rooms = $pdo->query("SELECT * FROM room ORDER BY room_name")->fetchAll(PDO::FETCH_ASSOC);

        $response = [
            "success" => true,
            "message" => "Room updated successfully.",
            "rooms" => $rooms
        ];
    }
} catch (Exception $e) {
    $response = [
        "success" => false,
        "message" => $e->getMessage()
    ];
}

echo json_encode($response);
exit;
?>
