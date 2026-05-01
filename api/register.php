<?php
include 'config.php';
session_start();
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid email format.'
    ]);
    exit;
}

$check_email = $conn->prepare("SELECT email, verified FROM users WHERE email = ?");
$check_email->bind_param("s", $email);
$check_email->execute();
$check_email->bind_result($db_email, $db_verified);
$check_email->fetch();
$check_email->close();

if ($db_email) {
    if ($db_verified == 0) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Email already exists but not confirmed. Please check your email for a verification code.'
        ]);
        exit;
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Email already exists.'
        ]);
        exit;
    }
}

if (isset($_POST['password'])) {
    $password = $_POST['password'];

    if (strlen($password) < 8 || !preg_match("#[0-9]+#", $password) ||
        !preg_match("#[a-zA-Z]+#", $password) || !preg_match("#\W+#", $password)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Password must be at least 8 characters long and include numbers, letters, and special characters.'
        ]);
        exit;
    }

    $verification_code = rand(10000, 99999);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (email, password, verification_code, verified) VALUES (?, ?, ?, 0)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $email, $hashed_password, $verification_code);
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['email'] = $email;
        $_SESSION['logged_in'] = true;
        echo json_encode([
            'status' => 'success',
            'message' => 'Please check your email for a verification code.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error executing statement: ' . $stmt->error
        ]);
    }
    $stmt->close();
} elseif (isset($_POST['discord_id'])) {

    $discord_id = $_POST['discord_id'];

    $sql = "INSERT INTO users (email, discord_id, verified) VALUES (?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $discord_id);
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['email'] = $email;
        $_SESSION['logged_in'] = true;
        echo json_encode([
            'status' => 'success',
            'message' => 'Logged in successfully with Discord.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error executing statement: ' . $stmt->error
        ]);
    }
    $stmt->close();
}

$conn->close();
?>