<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo "Invalid request."; exit; }

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? '';

if (!$email || !$password || !$role) { echo "All fields are required."; exit; }
if (!in_array($role, ['student','staff','admin'])) { echo "Invalid role."; exit; }

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = ?");
$stmt->execute([$email, $role]);
$user = $stmt->fetch();

if (!$user) { echo "No account found with that email and role."; exit; }
if (!password_verify($password, $user['password'])) { echo "Incorrect password."; exit; }

$_SESSION['user_id']    = $user['user_id'];
$_SESSION['full_name']  = $user['full_name'];
$_SESSION['email']      = $user['email'];
$_SESSION['role']       = $user['role'];
$_SESSION['department'] = $user['department'] ?? '';

echo "success";