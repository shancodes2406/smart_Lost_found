<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']); exit;
}

$full_name  = trim($_POST['full_name'] ?? '');
$email      = trim($_POST['email'] ?? '');
$password   = $_POST['password'] ?? '';
$phone      = trim($_POST['phone'] ?? '') ?: null;
$role       = $_POST['role'] ?? 'student';
$department = $_POST['department'] ?? '';

if (!$full_name || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Required fields missing.']); exit;
}

$check = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
$check->execute([$email]);
if ($check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'An account with this email already exists.']); exit;
}

$hashed = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, phone, role, department) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->execute([$full_name, $email, $hashed, $phone, $role, $department]);

echo json_encode(['success' => true]);