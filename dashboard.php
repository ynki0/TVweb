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

if ($user['purchased'] == 0) {
    header('Location: next-step.php');
    exit;
}

$sql = "SELECT s.id, s.username, s.password, s.expiry_date, s.connections, p.name as plan_name, p.price, p.shop_identifier
        FROM subscriptions s
        JOIN products p ON s.product_id = p.id
        WHERE s.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$subscriptions = [];
while ($row = $result->fetch_assoc()) {
    $subscriptions[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Cinder TV</title>
    <link rel="stylesheet" href="assets/css/main.css" />
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <script type="text/javascript" src="https://pay.sellsn.io/embed.min.js"></script>
    <style>
        .subscription-container {
            margin-bottom: 30px;
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        .subscription-container:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .iptv-details {
            margin-top: 20px;
        }
        #modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
        }
        #modal-overlay.modal-open {
            display: block;
        }
        #subscription-modal {
            border-radius: 40px;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #1f1f20;
            padding: 20px 40px 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            display: none;
            z-index: 9999;
        }
        #subscription-modal.modal-open {
            display: block;
        }
    </style>
</head>
<body>
<div class="navbar">
    <div class="navbar_top-right"></div>
    <div class="navbar_logo-container">
        <a href="index.php"><img src="assets/images/logo.png" class="navbar_logo"></a>
        <div class="navbar_logo-text"><a href="index.php">Cinder TV</a></div>
    </div>
</div>
<div id="dashboard">
    <div class="dashboard-title">Dashboard</div>
    <div class="flex-row">
        <?php if (count($subscriptions) === 0): ?>
            <div class="no-subscription">
                <div class="no-subscription-text">You have no subscriptions. Please subscribe to a plan.</div>
            </div>
        <?php else: ?>
            <?php foreach ($subscriptions as $index => $subscription): ?>
                <div class="subscription-container" data-username="<?php echo htmlspecialchars($subscription['username']); ?>" data-password="<?php echo htmlspecialchars($subscription['password']); ?>">
                    <div class="subscription-box">
                        <div class="current-subscription">Active Subscription</div>
                        <div class="subscription-details">
                            <div class="left-column">
                                <div class="subscription-option"><?php echo htmlspecialchars($subscription['plan_name']); ?></div>
                                <div class="connection-detail">Expires at <?php echo htmlspecialchars($subscription['expiry_date']); ?> </div>
                            </div>
                            <div class="right-column">
                                <?php
                                echo '<button class="renew-button" data-sellsn-pid="' . $subscription['shop_identifier'] . '" data-sellsn-sid="dfb31342-98e7-4c03-9505-d24043088fb7" data-sellsn-email="' . $_SESSION['email'] . '">RENEW</button>';
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
    <div class="how-to-install">How to install?</div>
    <div class="install-details">You are welcome to use any Xtream Codes TV player - we have no affiliation with any!</div>
    <div class="recommendation">We recommend using <b>IPTV Smarters</b> or <b>TiviMate</b><br>Tivimate costs 30$ to set up, but it is one of the best players out there! Well worth the money if you're looking for a great option to TV!</div>
    <div class="app-section">
        <div class="app-column left-apps">
            <img src="assets/images/tivimate.png" style="width: 250px; height: auto; margin-top: 25px;">
            <div style="color: #3E3E3E; font-size: 27px; margin-top: 15px;"><b>Downloader code:</b> 366972</div>
            <div style="display: flex; justify-content: space-between; margin-top: -5px;">
                <div class="app-button" style="flex-basis: calc(50% - 10px);">PLAY STORE</div>
                <div class="app-button" style="flex-basis: calc(50% - 10px);">APK</div>
            </div>
        </div>
        <div class="vertical-line"></div>
        <div class="app-column right-apps">
            <img src="assets/images/iptvsmarters.png" style="width: 250px; height: auto; margin-top: 25px;">
            <div style="display: flex; justify-content: space-between; flex-wrap: wrap; margin-top: 25px;">
                <div class="app-button" style="flex-basis: calc(16.666% - 10px);">PLAY STORE</div>
                <div class="app-button" style="flex-basis: calc(16.666% - 10px);">APK</div>
                <div class="app-button" style="flex-basis: calc(16.666% - 10px);">APP STORE</div>
                <div class="app-button" style="flex-basis: calc(16.666% - 10px);">MAC APP</div>
                <div class="app-button" style="flex-basis: calc(16.666% - 10px);">WIN APP</div>
                <div class="app-button" style="flex-basis: calc(16.666% - 10px);">WEB PLAYER</div>
            </div>
        </div>
    </div>
    <div class="firestick-instructions">
        <div class="firestick-title">FireStick</div>
        1. Go into Find > Search then download and install the app called Downloader.<br>
        2. After downloading Downloader return to the FireStick home screen.<br>
        3. Go into Settings > My Fire TV > About > Click Fire TV Stick 7 times.<br>
        4. Press the back button > Developer Options > Install from unknown apps > Downloader and change the status to on.<br>
        5. Open the Downloader app and enter the URL “https:
        6. Enjoy!
    </div>
</div>
<div class="copyright-section">
    Cinder TV © 2024 All rights reserved
    <div class="terms-of-service">
        <a href="terms-of-service.php">Terms of Service</a>
    </div>
</div>
<div id="modal-overlay" class="modal-closed"></div>
<div id="subscription-modal" class="modal-closed">
    <h1>Subscription Details</h1>
    <p><b>Username: </b><span id="modal-username"></span></p>
    <p><b>Password: </b><span id="modal-password"></span></p>
    <div class="buttons">
        <a class="button close-button" onclick="closeModal()">Close</a>
    </div>
</div>
<script>
    document.querySelectorAll('.subscription-container').forEach(container => {
        container.addEventListener('click', function () {
            const username = this.dataset.username;
            const password = this.dataset.password;
            openModal(username, password);
        });
    });

    document.querySelectorAll('.renew-button').forEach(button => {
        button.addEventListener('click', function (event) {
            event.stopPropagation();

        });
    });

    function openModal(username, password) {
        document.getElementById('modal-username').textContent = username;
        document.getElementById('modal-password').textContent = password;
        document.getElementById('modal-overlay').classList.add('modal-open');
        document.getElementById('subscription-modal').classList.add('modal-open');
    }

    function closeModal() {
        document.getElementById('modal-overlay').classList.remove('modal-open');
        document.getElementById('subscription-modal').classList.remove('modal-open');
    }

    window.addEventListener('load', function() {
        document.body.style.opacity = '1';
    });
</script>
</body>
</html>