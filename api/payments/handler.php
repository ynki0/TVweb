<?php
$request_body = file_get_contents('php://input');
$secret = 'sb9PfPS2DZ5WZNWMHjvfPJDUQdkNAJ30syqEaZ3+BNNCdL8hiuhbAhsuATpAUzqaWLPwIzbCSxRnwCqnuPyygQ==';
$recv_signature = $_SERVER['HTTP_X_WEBHOOK_SIGNATURE'];
$signature = hash_hmac('sha256', $request_body, $secret);

if (!hash_equals($signature, $recv_signature)) {
    die('Invalid webhook request');
}

$data = json_decode($request_body, true);
$email = $data['customer']['emailAddress'];
$product_name = $data['products'][0]['product']['name'];

$conn = new mysqli('hostname', 'username', 'password', 'database');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$check_email = $conn->prepare("SELECT id, email, verified FROM users WHERE email = ?");
$check_email->bind_param("s", $email);
$check_email->execute();
$check_email->bind_result($user_id, $db_email, $db_verified);
$check_email->fetch();
$check_email->close();

if ($db_email != $email || !$db_verified) {
    die('User not found or not verified');
}

$product_stmt = $conn->prepare("SELECT id, expiry_time, connections FROM products WHERE name = ?");
$product_stmt->bind_param("s", $product_name);
$product_stmt->execute();
$product_stmt->bind_result($product_id, $expiry_time, $connections);
$product_stmt->fetch();
$product_stmt->close();

if (!$product_id) {
    die('Product not found');
}

$expiry_date = date('Y-m-d H:i:s', strtotime("+$expiry_time days"));
$insert_subscription = $conn->prepare("INSERT INTO subscriptions (user_id, product_id, expiry_date, connections) VALUES (?, ?, ?, ?)");
$insert_subscription->bind_param("iisi", $user_id, $product_id, $expiry_date, $connections);

if ($insert_subscription->execute()) {
    echo 'Subscription added successfully';
} else {
    echo 'Error adding subscription';
}

$insert_subscription->close();
$conn->close();
?>