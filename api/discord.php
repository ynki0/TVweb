<?php
include 'config.php';
session_start();

header('Content-Type: application/json');

$clientID = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';
$redirectURI = 'https://cinder.tv/dashboard.php';

$code = $_GET['code'] ?? null;
if (!$code) {
    echo json_encode(['status' => 'error', 'message' => 'No code provided']);
    exit;
}

$url = 'https://discord.com/api/oauth2/token';
$data = http_build_query([
    'client_id' => $clientID,
    'client_secret' => $clientSecret,
    'grant_type' => 'authorization_code',
    'code' => $code,
    'redirect_uri' => $redirectURI
]);

$options = [
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded",
        'content' => $data
    ]
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);
$response = json_decode($response, true);

if (!isset($response['access_token'])) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to get access token']);
    exit;
}

$token = $response['access_token'];

$options = [
    'http' => [
        'header' => "Authorization: Bearer $token"
    ]
];

$context = stream_context_create($options);
$user = file_get_contents('https://discord.com/api/users/@me', false, $context);
$user = json_decode($user, true);

if (!isset($user['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to get user details']);
    exit;
}

$sql = "SELECT id, verified FROM users WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user['email']);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($user_id, $verified);
    $stmt->fetch();
    if (!$verified) {
        echo json_encode(['status' => 'error', 'message' => 'Account not verified. Please check your email for a verification link.']);
    } else {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        echo json_encode(['status' => 'success', 'message' => 'Login successful.']);
    }
} else {

    $sql = "INSERT INTO users (email, verified, discord_id) VALUES (?, 1, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $user['email'], $user['id']);
    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        echo json_encode(['status' => 'success', 'message' => 'User registered and logged in successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Registration failed.']);
    }
}

$stmt->close();
$conn->close();
?>