<?php
include 'config.php';
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format.']);
    exit;
}

$sql = "SELECT id FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 0) {
    echo json_encode(['status' => 'error', 'message' => 'No user found with that email address.']);
    $stmt->close();
    $conn->close();
    exit;
}

$token = bin2hex(random_bytes(16));

$update_sql = "UPDATE users SET reset_token = ? WHERE email = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("ss", $token, $email);
$update_stmt->execute();
$update_stmt->close();

$reset_link = "http://cinder.tv/forgot-password.php?token=$token";
$subject = "Password Reset Request";
$message = "Please click on the following link to reset your password: $reset_link";
$headers = "From: no-reply@yourwebsite.com";

mail($email, $subject, $message, $headers);

echo json_encode(['status' => 'success', 'message' => 'Check your email for the password reset link.']);

$stmt->close();
$conn->close();
?>