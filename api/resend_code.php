<?php
include 'config.php';
session_start();

header('Content-Type: application/json');

function send_verification_email($email, $verification_code) {

    $subject = "Your Verification Code";
    $message = "Your new verification code is: " . $verification_code;
    $headers = "From: no-reply@yourdomain.com";

    return mail($email, $subject, $message, $headers);
}

if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to resend the code.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];

$new_verification_code = rand(10000, 99999);

$sql = "UPDATE users SET verification_code = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_verification_code, $user_id);
$result = $stmt->execute();

if ($result) {

    if (send_verification_email($email, $new_verification_code)) {
        echo json_encode(['status' => 'success', 'message' => 'Verification code sent successfully to your email.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to send verification email. Please try again.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to update verification code in the database.']);
}

$stmt->close();
$conn->close();
?>