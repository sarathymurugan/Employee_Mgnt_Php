<?php
include 'includes/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    http_response_code(400);
    echo "Invalid request";
    exit;
}

// Get old files
$stmt = $conn->prepare("SELECT photo, document FROM employees WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    http_response_code(404);
    echo "Employee not found";
    exit;
}

$upload_dir = "assets/uploads/";

// Delete files
if (!empty($row['photo']) && file_exists($upload_dir . $row['photo'])) {
    unlink($upload_dir . $row['photo']);
}

if (!empty($row['document']) && file_exists($upload_dir . $row['document'])) {
    unlink($upload_dir . $row['document']);
}

// Delete record
$stmt = $conn->prepare("DELETE FROM employees WHERE id = ?");
$stmt->execute([$id]);

echo "Employee deleted successfully!";
