<?php
include 'config.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to verify your email.']);
    exit;
}

$user_id = $_SESSION['user_id'];

if (!isset($_POST['code'])) {
    echo json_encode(['status' => 'error', 'message' => 'Verification code is required.']);
    exit;
}

$verification_code = $_POST['code'];

$sql = "SELECT verification_code, email FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($stored_verification_code, $email);
$stmt->fetch();
$stmt->close();

if ($stored_verification_code == $verification_code) {

    $sql = "UPDATE users SET verified = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $result = $stmt->execute();

    if ($result) {

        $_SESSION['email'] = $email;
        $_SESSION['logged_in'] = true;

        echo json_encode(['status' => 'success', 'message' => 'Email verified successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update verification status in the database.']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid verification code.']);
}

$conn->close();
?>