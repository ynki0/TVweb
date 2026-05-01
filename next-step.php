<?php
session_start();
include 'api/config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT purchased FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user['purchased'] == 1) {
    header('Location: next-step.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>One last step... - Cinder TV</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <script
            type="text/javascript"
            src="https://pay.sellsn.io/embed.min.js"
    ></script>
</head>
<body>

<div class="navbar">
    <div class="navbar_top-right"></div>
    <div class="navbar_logo-container">
        <a href="#"><img src="assets/images/logo.png" class="navbar_logo"></a>
        <div class="navbar_logo-text"><a href="#">Cinder TV</a></div>
    </div>
</div>

<div id="pricing" style="margin-top: 140px;">
    <div class="title">Please purchase a subscription to continue!</div>
    <div class="description">
        Dive into an expansive library of movies, series, and exclusive content, all streamed in stunning HD to ensure your entertainment is always just a click away.
    </div>

    <div class="cards">
        <?php
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {

            while($row = $result->fetch_assoc()) {
                echo '<div class="card">';
                echo '<div class="card-title">' . $row['expiry_time'] . ' DAYS</div>';
                echo '<div class="card-subtitle">' . $row['connections'] . ' CONNECTIONS</div>';
                echo '<div class="amount"><span class="dollar-sign">$</span>' . number_format($row['price'], 2) . '</div>';
                echo '<div class="features">';
                echo '<div>HD Streaming</div>';
                echo '<div>Multi-Platform Support</div>';
                echo '<div>Exclusive Content</div>';
                echo '<div>No Ads</div>';
                echo '</div>';
                echo '<button class="button" data-sellsn-pid="' . $row['shop_identifier'] . '" data-sellsn-sid="dfb31342-98e7-4c03-9505-d24043088fb7" data-sellsn-email="' . $_SESSION['email'] . '">PURCHASE</button>';
                echo '</div>';
            }
        } else {
            echo '<p>No plans available.</p>';
        }
        ?>
    </div>
</div>

<div class="copyright-section">
    Cinder TV © 2024 All rights reserved
    <div class="terms-of-service">
        <a href="terms-of-service.php">Terms of Service</a>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    $(window).on('load', function() {
        $('body').css('opacity', '1');
    });
</script>

</body>
</html>