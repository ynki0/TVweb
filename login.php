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
    <title>Login - Cinder TV</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
</head>
<body>
<div id="toast" class="toast"></div>

<div class="navbar">
    <div class="navbar_top-right">
        <a href="register.php">
            <div class="navbar_register-button">Register</div>
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
    <form class="left" data-api="api/login.php">
        <div class="title-with-login">
            <div class="login-title">Login to your account</div>
            <div class="login-with">Login with</div>
        </div>
        <div class="social-icons">
            <a href="https://discord.com/api/oauth2/authorize?client_id=1224098166324990133&redirect_uri=https://cinder.tv/api/discord.php&response_type=code&scope=identify%20email" class="discord-auth">
                <img src="assets/images/discord.svg" alt="Login with Discord">
            </a>
        </div>
        <div class="input-container">
            <input type="email" name="email" class="input-field" placeholder="Email">
        </div>
        <div class="input-container">
            <input type="password" name="password" class="input-field" placeholder="Password" id="password">
            <img src="assets/images/password.svg" alt="Toggle Password" class="toggle-password">
        </div>
        <div class="forgot-login-container">
            <a href="forgot-password.php" class="forgot-password">Forgot your password?</a>
            <button type="button" class="btn login-button" onclick="login()">Login</button>
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>

    function login() {
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
                    setTimeout(function() {
                        window.location.href = "dashboard.php";
                    }, 1000);
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

    $(document).ready(function() {
        $('.toggle-password').click(function() {
            var $password = $('#password');
            var $toggle = $('.toggle-password');
            if ($password.attr('type') === 'password') {
                $password.attr('type', 'text');
                $toggle.css('opacity', '0.4');
            } else {
                $password.attr('type', 'password');
                $toggle.css('opacity', '1');
            }
        });

        $(window).on('load', function() {
            $('body').css('opacity', '1');
        });
    });
</script>
</body>
</html>