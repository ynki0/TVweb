<?php
session_start();
include 'api/config.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot password - Cinder TV</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
</head>
<body>
<div id="toast" class="toast"></div>

<?php
include 'api/config.php';

$token = $_GET['token'] ?? '';

if ($token) {

    $sql = "SELECT email FROM users WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo "<div class='error-message'>Invalid or expired token.</div>";
        $stmt->close();
        $conn->close();
        exit;
    }

    $stmt->bind_result($email);
    $stmt->fetch();
    $stmt->close();
    ?>

    <div class="navbar">
        <div class="navbar_top-right">
            <a href="login.php">
                <div class="navbar_register-button">Login</div>
            </a>
        </div>
        <div class="navbar_logo-container">
            <a href="index.php">
                <img src="assets/images/logo.png" class="navbar_logo">
            </a>
            <div class="navbar_logo-text">
                <a href="index.php">Cinder TV</a>
            </div>
        </div>
    </div>

    <div id="login">
        <form class="left" data-api="api/reset-password.php">
            <div class="title-with-login">
                <div class="login-title">Reset Password</div>
            </div>
            <div class="social-icons"></div>
            <div class="input-container">
                <input type="password" name="new_password" class="input-field" placeholder="New Password">
            </div>
            <div class="input-container">
                <input type="password" name="confirm_password" class="input-field" placeholder="Confirm Password">
            </div>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div class="forgot-login-container">
                <button type="button" class="btn login-button" onclick="reset_password()">Reset Password</button>
            </div>
        </form>
        <div class="right">
            <img src="assets/images/login-illustration.svg" alt="Login Illustration">
        </div>
    </div>

    <div class="copyright-section">
        Cinder TV © 2024 All rights reserved
    </div>

    <?php
} else {
    ?>

    <div class="navbar">
        <div class="navbar_top-right">
            <a href="login.php">
                <div class="navbar_register-button">Login</div>
            </a>
        </div>
        <div class="navbar_logo-container">
            <a href="index.php">
                <img src="assets/images/logo.png" class="navbar_logo">
            </a>
            <div class="navbar_logo-text">
                <a href="index.php">Cinder TV</a>
            </div>
        </div>
    </div>

    <div id="login">
        <form class="left" data-api="api/reset-password-request.php">
            <div class="title-with-login">
                <div class="login-title">Forgotten Password</div>
            </div>
            <div class="social-icons"></div>
            <div class="input-container">
                <input type="email" name="email" class="input-field" placeholder="Email">
            </div>
            <div class="forgot-login-container">
                <a href="login.php" class="forgot-password">Never-mind, I have it</a>
                <button type="button" class="btn login-button" onclick="request_reset()">Request</button>
            </div>
        </form>
        <div class="right">
            <img src="assets/images/login-illustration.svg" alt="Login Illustration">
        </div>
    </div>

    <div class="copyright-section">
        Cinder TV © 2024 All rights reserved
        <div class="terms-of-service">
            <a href="terms-of-service.php">Terms of Service</a>
        </div>
    </div>

    <?php
}
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
    function request_reset() {
        var button = $('.login-button');
        var form = button.closest('form');
        var actionUrl = form.data('api');

        button.addClass('loading');
        $.ajax({
            type: 'POST',
            url: actionUrl,
            data: form.serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                    var email = form.find('input[name="email"]').val();
                    $('#verify-email').text(email);
                    showToast(data.message);
                    openModal();
                } else {
                    showToast('Error: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                showToast('Failed to submit form.');
            },
            complete: function() {
                button.removeClass('loading');
            }
        });
    }

    function reset_password() {
        var button = $('.login-button');
        var form = button.closest('form');
        var actionUrl = form.data('api');

        button.addClass('loading');
        $.ajax({
            type: 'POST',
            url: actionUrl,
            data: form.serialize(),
            dataType: 'json',
            success: function(data) {
                if (data.status === 'success') {
                    showToast(data.message);
                } else {
                    showToast('Error: ' + data.message);
                }
            },
            error: function(xhr, status, error) {
                showToast('Failed to submit form.');
            },
            complete: function() {
                button.removeClass('loading');
            }
        });
    }

    function showToast(message) {
        var toast = $('#toast');
        toast.text(message);
        toast.addClass('show');
        setTimeout(function(){ toast.removeClass('show'); }, 3000);
    }

    $(window).on('load', function() {
        $('body').css('opacity', '1');
    });
</script>
</body>
</html>