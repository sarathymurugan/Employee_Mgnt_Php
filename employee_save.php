<?php
include 'includes/db.php';

$id = $_POST['id'] ?? null;
$mode = $_POST['mode'] ?? 'add';
$name = trim($_POST['name']);
$phone = trim($_POST['phone']);
$address = trim($_POST['address']);

$upload_dir = "assets/uploads/";
$photo_name = "";
$doc_name = "";

// Validate phone
if (!preg_match('/^\d{10}$/', $phone)) {
    http_response_code(422);
    echo "Invalid phone number.";
    exit;
}

// Handle photo
if (!empty($_FILES['photo']['name'])) {
    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $photo_name = uniqid("photo_") . "." . $ext;
    move_uploaded_file($_FILES['photo']['tmp_name'], $upload_dir . $photo_name);
} elseif ($mode === 'edit') {
    // Keep old photo
    $stmt = $conn->prepare("SELECT photo FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    $photo_name = $stmt->fetchColumn();
}

// Handle document
if (!empty($_FILES['document']['name'])) {
    $ext = pathinfo($_FILES['document']['name'], PATHINFO_EXTENSION);
    $doc_name = uniqid("doc_") . "." . $ext;
    move_uploaded_file($_FILES['document']['tmp_name'], $upload_dir . $doc_name);
} elseif ($mode === 'edit') {
    // Keep old doc
    $stmt = $conn->prepare("SELECT document FROM employees WHERE id = ?");
    $stmt->execute([$id]);
    $doc_name = $stmt->fetchColumn();
}

if ($mode === 'add') {
    $stmt = $conn->prepare("INSERT INTO employees (name, phone, address, photo, document) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $phone, $address, $photo_name, $doc_name]);
    echo "Employee added successfully!";
} elseif ($mode === 'edit' && $id) {
    $stmt = $conn->prepare("UPDATE employees SET name=?, phone=?, address=?, photo=?, document=? WHERE id=?");
    $stmt->execute([$name, $phone, $address, $photo_name, $doc_name, $id]);
    echo "Employee updated successfully!";
} else {
    http_response_code(400);
    echo "Invalid request";
}
