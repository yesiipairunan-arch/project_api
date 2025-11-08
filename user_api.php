<?php
header("Content-Type: application/json");
require_once "database.php";

$method = $_SERVER['REQUEST_METHOD'];

// ambil parameter id jika ada
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

switch ($method) {

    // 🔹 GET (READ)
    case 'GET':
        if ($id) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            $stmt = $conn->query("SELECT * FROM users");
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        echo json_encode($data);
        break;

    // 🔹 POST (CREATE)
    case 'POST':
        $input = json_decode(file_get_contents("php://input"), true);
        if (!isset($input['username'], $input['email'], $input['password'])) {
            echo json_encode(["message" => "Data tidak lengkap"]);
            exit;
        }

        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->execute([
            $input['username'],
            $input['email'],
            password_hash($input['password'], PASSWORD_DEFAULT)
        ]);

        echo json_encode(["message" => "User berhasil ditambahkan"]);
        break;

    // 🔹 PUT (UPDATE)
    case 'PUT':
        if (!$id) {
            echo json_encode(["message" => "ID diperlukan untuk update"]);
            exit;
        }

        $input = json_decode(file_get_contents("php://input"), true);

        $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, password = ? WHERE id = ?");
        $stmt->execute([
            $input['username'],
            $input['email'],
            password_hash($input['password'], PASSWORD_DEFAULT),
            $id
        ]);

        echo json_encode(["message" => "User berhasil diperbarui"]);
        break;

    // 🔹 DELETE (DELETE)
    case 'DELETE':
        if (!$id) {
            echo json_encode(["message" => "ID diperlukan untuk delete"]);
            exit;
        }

        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(["message" => "User berhasil dihapus"]);
        break;

    default:
        echo json_encode(["message" => "Metode tidak didukung"]);
        break;
}
?>
