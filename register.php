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
    <title>Register - Cinder TV</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <style>
        @keyframes spinner {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .loading-spinner {
            border: 2px solid transparent;
            border-top: 2px solid #fff;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spinner 1s linear infinite;
            display: inline-block;
            vertical-align: middle;
        }
        .btn.loading .loading-spinner {
            display: inline-block;
        }
        .btn.loading span {
            display: none;
        }
    </style>
</head>
<body>
<div id="toast" class="toast"></div>

<div id="modal-overlay" class="modal-closed"></div>
<div id="email-verification-modal" class="modal-closed">
    <div class="svg-placeholder"><img src="assets/images/email-verify.png" alt="Verify Email"></div>
    <h1>Verify your email address</h1>
    <p>We have sent a 5-digit verification code to <strong id="verify-email"></strong><br>You might need to check your <strong>spam folder</strong>.</p>
    <div class="input-boxes">
        <input type="text" maxlength="1">
        <input type="text" maxlength="1">
        <input type="text" maxlength="1">
        <input type="text" maxlength="1">
        <input type="text" maxlength="1">
    </div>
    <div class="buttons">
        <a class="button verify-button" id="verify-button">Verify</a>
        <label id="resend-label">Re-send code</label>
    </div>
</div>

<div class="navbar">
    <div class="navbar_top-right">
        <a href="login.php"><div class="navbar_register-button">Login</div></a>
    </div>
    <div class="navbar_logo-container">
        <a href="index.php"><img src="assets/images/logo.png" class="navbar_logo" alt="Logo"></a>
        <div class="navbar_logo-text"><a href="index.php">Cinder TV</a></div>
    </div>
</div>

<div id="login">
    <form class="left" data-api="api/register.php">
        <div class="title-with-login">
            <div class="login-title">Create an account</div>
            <div class="login-with">Register with</div>
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
            <img src="assets/images/password.svg" alt="Toggle Password" class="toggle-password" onclick="togglePassword()">
        </div>
        <div class="forgot-login-container">
            <a href="login.php" class="forgot-password">Already have an account?</a>
            <button type="button" class="btn login-button" onclick="register()">Register</button>
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
    function register() {
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
                showToast('Failed to submit form: ' + error);
            },
            complete: function() {
                button.removeClass('loading');
            }
        });
    }

    function resendVerificationCode() {
        var email = $('#verify-email').text();
        $.ajax({
            type: 'POST',
            url: 'api/resend_code.php',
            data: { email: email },
            dataType: 'json',
            success: function(data) {
                showToast(data.message);
            },
            error: function(xhr, status, error) {
                showToast('Failed to resend code: ' + error);
            }
        });
    }

    function submitVerificationCode() {
        var code = $('.input-boxes input').map(function() { return this.value; }).get().join('');
        if (code.length == 5) {
            $.ajax({
                type: 'POST',
                url: 'api/verify_email.php',
                data: { code: code },
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        showToast(data.message);
                        setTimeout(function() {
                            window.location.href = "dashboard.php";
                        }, 1000);
                        closeModal();
                    } else {
                        showToast('Failed to submit form.');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('Failed to verify code: ' + error);
                }
            });
        }
    }

    function openModal() {
        $("#modal-overlay, #email-verification-modal").removeClass("modal-closed").addClass("modal-open");
    }

    function closeModal() {
        $("#modal-overlay, #email-verification-modal").removeClass("modal-open").addClass("modal-closed");
    }

    function showToast(message) {
        var toast = $('#toast');
        toast.text(message);
        toast.addClass('show');
        setTimeout(function(){ toast.removeClass('show'); }, 3000);
    }

    $(document).ready(function() {
        $('#verify-button').click(submitVerificationCode);
        $('#resend-label').click(resendVerificationCode);

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

        $(".input-boxes input").keyup(function() {
            if (this.value.length == this.maxLength) {
                var $next = $(this).next('input');
                if ($next.length) {
                    $next.focus();
                } else {
                    submitVerificationCode();
                }
            }
        }).keydown(function(e) {
            if (e.which === 8 && !this.value) {
                $(this).prev('input').focus();
            }
        });

        $(window).on('load', function() {
            $('body').css('opacity', '1');
        });
    });
</script>
</body>
</html>