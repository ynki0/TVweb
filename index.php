<?php
include 'api/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index - Cinder TV</title>
    <link rel="stylesheet" href="assets/css/main.css">
    <link rel="icon" type="image/png" href="assets/images/logo.png">
</head>
<body>

<div class="navbar">
    <div class="navbar_top-right">
        <a href="login.php"><div style="opacity: 80%;">Login</div></a>
        <a href="register.php"><div class="navbar_register-button">Get Started</div></a>
    </div>
    <div class="navbar_logo-container">
        <a href="index.php"><img src="assets/images/logo.png" class="navbar_logo"></a>
        <div class="navbar_logo-text"><a href="index.php">Cinder TV</a></div>
    </div>
</div>

<div id="advertisement">
    <div class="content">
        <h1>Stream. Enjoy. Repeat.</h1>
        <p>Explore a vast library of movies, series, and exclusive content right at your fingertips. Get started today and dive into an endless entertainment experience!</p>
        <button href="login.php">Get Started</button>
    </div>
    <img src="assets/images/advertisement.png">
</div>
<div id="channels">
    <div class="title">Explore Thousands of Channels</div>
    <div class="rectangles">
        <div class="rectangle">
            <img src="assets/images/hbo.png" alt="HBO Max">
        </div>
        <div class="rectangle">
            <img src="assets/images/netflix.png" alt="Netflix Originals">
        </div>
        <div class="rectangle">
            <img src="assets/images/disney.png" alt="Disney+">
        </div>
        <div class="rectangle">
            <img src="assets/images/espn.png" alt="ESPN">
        </div>
    </div>
</div>

<div id="features">
    <div class="title">Key Features</div>
    <div class="description">
        Discover the powerful features that make our service stand out.
    </div>
    <div class="row">
        <div class="rectangle">
            <img src="assets/images/logo.png" alt="Feature 1 Logo">
            <div class="feature-title">HD Streaming</div>
            <div class="feature-description">Watch in crystal clear high definition as if you're right there in the action.</div>
        </div>
        <div class="rectangle">
            <img src="assets/images/logo.png" alt="Feature 2 Logo">
            <div class="feature-title">Multi-Platform Support</div>
            <div class="feature-description">Access from any device, anywhere in the world. Your shows travel with you!</div>
        </div>
    </div>
    <div class="row">
        <div class="rectangle">
            <img src="assets/images/logo.png" alt="Feature 3 Logo">
            <div class="feature-title">Exclusive Content</div>
            <div class="feature-description">Enjoy original series and movies exclusive to Cinder TV subscribers.</div>
        </div>
        <div class="rectangle">
            <img src="assets/images/logo.png" alt="Feature 4 Logo">
            <div class="feature-title">No Ads</div>
            <div class="feature-description">Stream without interruptions with no ads across all platforms.</div>
        </div>
    </div>
</div>
<div id="pricing">
    <div class="title">Our Pricing Plans</div>
    <div class="description">
        Choose a plan that fits your needs.
    </div>
    <div class="cards">
        <?php
        $sql = "SELECT * FROM products";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {

            while($row = $result->fetch_assoc()) {
                echo '<div class="card">';
                echo '<div class="card-title">' . $row['expiry_time'] . ' DAYS</div>';
                echo '<div class="card-subtitle">' . $row['connections'] . ' CONNECTION</div>';
                echo '<div class="amount"><span class="dollar-sign">$</span>' . number_format($row['price'], 2) . '</div>';
                echo '<div class="features">';
                echo '<div>HD Streaming</div>';
                echo '<div>Multi-Platform Support</div>';
                echo '<div>Exclusive Content</div>';
                echo '<div>No Ads</div>';
                echo '</div>';
                echo '<a href="register.php" class="button">PURCHASE</a>';
                echo '</div>';
            }
        } else {
            echo '<p>No plans available.</p>';
        }
        ?>
    </div>
</div>
<div id="faq">
    <div class="title">Frequently Asked Questions</div>
    <div class="item" onclick="toggleFaq(1)">
        <div class="question">
            <span>What devices can I use to watch?</span>
            <img src="assets/images/arrow.png" id="faq-arrow-1" class="arrow">
        </div>
        <div class="answer" id="faq-answer-1">
            You can stream our content on any internet-connected device that offers the Cinder TV app, including smart TVs, smartphones, tablets, streaming media players, and game consoles.
        </div>
    </div>
    <div class="item" onclick="toggleFaq(2)">
        <div class="question">
            <span>How do I cancel my subscription?</span>
            <img src="assets/images/arrow.png" id="faq-arrow-2" class="arrow">
        </div>
        <div class="answer" id="faq-answer-2">
            You can cancel your subscription at any time in your account settings. Once cancelled, your subscription will stop at the end of the current paid period.
        </div>
    </div>
    <div class="item" onclick="toggleFaq(3)">
        <div class="question">
            <span>What payment methods are accepted?</span>
            <img src="assets/images/arrow.png" id="faq-arrow-3" class="arrow">
        </div>
        <div class="answer" id="faq-answer-3">
            We accept various payment methods including credit cards, debit cards, PayPal, and other digital wallets. Specific options may vary by region.
        </div>
    </div>
    <div class="item" onclick="toggleFaq(4)">
        <div class="question">
            <span>Is there a free trial available?</span>
            <img src="assets/images/arrow.png" id="faq-arrow-4" class="arrow">
        </div>
        <div class="answer" id="faq-answer-4">
            Yes, we offer a 7-day free trial for new subscribers. You can cancel anytime during the trial period without charge.
        </div>
    </div>
    <div class="item" onclick="toggleFaq(5)">
        <div class="question">
            <span>How do I reset my password?</span>
            <img src="assets/images/arrow.png" id="faq-arrow-5" class="arrow">
        </div>
        <div class="answer" id="faq-answer-5">
            If you've forgotten your password, you can reset it by clicking the "Forgot Password" link on the login page and following the instructions sent to your registered email address.
        </div>
    </div>
</div>

<div class="copyright-section">
    Cinder TV © 2024 All rights reserved
    <div class="terms-of-service">
        <a href="terms-of-service.php">Terms of Service</a>
    </div>
</div>

<script>
    function toggleFaq(number) {
        var answer = document.getElementById("faq-answer-" + number);
        var arrow = document.getElementById("faq-arrow-" + number);
        if (answer.style.display === "none" || answer.style.display === "") {
            answer.style.display = "block";
            arrow.style.transform = "rotate(180deg)";
        } else {
            answer.style.display = "none";
            arrow.style.transform = "rotate(0deg)";
        }
    }

    window.addEventListener('load', function() {
        document.body.style.opacity = '1';
    });
</script>

</body>
</html>