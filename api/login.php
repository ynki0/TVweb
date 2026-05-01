<?php
include 'config.php';
session_start();

header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
    exit;
}

$sql = "SELECT id, password, verified FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'User does not exist.']);
    $stmt->close();
    exit;
}

$stmt->bind_result($user_id, $hashed_password, $verified);
$stmt->fetch();

if (password_verify($password, $hashed_password)) {
    if (!$verified) {
        echo json_encode(['status' => 'error', 'message' => 'Account not verified.']);
    } else {

        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $email;
        $_SESSION['logged_in'] = true;

        echo json_encode(['status' => 'success', 'message' => 'Login successful.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
}

$stmt->close();
?>