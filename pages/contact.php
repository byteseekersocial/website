<!DOCTYPE html>
<html lang="en">
<!-- Head -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/scripts/style.css">
    <title>ByteSeeker | Contact</title>
    <script>
        fetch('/scripts/head.html')
            .then(response => response.text())
            .then(data => {
                document.head.insertAdjacentHTML('beforeend', data);
            });
    </script>
</head>
 <!-- Page -->
<body class="dark-mode">
    <div data-include="/scripts/banner.html"></div>
    <div data-include="/scripts/header.html"></div>
    
    <main class="container">
        <?php
        if (isset($_GET['status'])) {
            if ($_GET['status'] == 'success') {
                echo '<p class="status-message success-message">I got your message. Now go on, git. Go away.</p>';
            } elseif ($_GET['status'] == 'error') {
                $errorMessage = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Something happened, idk what, but its your fault.';
                echo '<p class="status-message error-message">⚠️ You lost the game. (error message -): ' . $errorMessage . '</p>';
            }
        }
        ?>

        <div class="contact">
            <h2>Contact Me</h2>
            <p>Fill out what's below, then get outta here</p>

            <form action="/scripts/process_form.php" method="POST" class="contact-form">
                <div class="form-group">
                    <label for="name">Your Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Your Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="subject">Subject:</label>
                    <input type="text" id="subject" name="subject" required>
                </div>
                <div class="form-group">
                    <label for="message">Message:</label>
                    <textarea id="message" name="message" rows="6" required></textarea>
                </div>
                <div>
                    <button type="submit" class="buy-button">Send Message</button>
                </div>
            </form>
        </div>

      <script>
 // Check if the URL contains the '?status=' parameter
    if (window.location.search.includes('status=')) {
        
        // Wait for 5 seconds (5000 milliseconds)
        setTimeout(function() {
            // Redirect to the same page, but without the query string (?status=...)
            window.location.href = window.location.pathname;
        }, 5000); // You can adjust this time (e.g., 3000 for 3 seconds)
    }
      </script>

    </main>
<!-- Footer -->
<div data-include="/scripts/footer.html"></div>

<script src="/scripts/inject.js"></script>
<script src="/scripts/script.js"></script>
</body>
</html>