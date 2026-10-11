<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: /pages/contact");
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if ($name === '' || $email === '' || $subject === '' || $message === '') {
    header("Location: /pages/contact?status=error&message=" . urlencode("Fill out all the fields idiot."));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: /pages/contact?status=error&message=" . urlencode("That email looks wrong."));
    exit;
}

// Prevent header injection
$name = str_replace(["\r", "\n"], ' ', $name);
$email = str_replace(["\r", "\n"], '', $email);
$subject = str_replace(["\r", "\n"], ' ', $subject);

$to = "byteseeker@byteseeker.link";
$email_subject = "Contact Form Submission: " . $subject;

$email_body = "You have received a new message from your website contact form.\n\n" .
              "Here are the details:\n" .
              "Name: $name\n" .
              "Email: $email\n" .
              "Subject: $subject\n" .
              "Message:\n$message\n";

$headers = [];
$headers[] = "From: ByteSeeker Contact Form <byteseeker@byteseeker.link>";
$headers[] = "Reply-To: $email";
$headers[] = "Content-Type: text/plain; charset=UTF-8";

if (mail($to, $email_subject, $email_body, implode("\r\n", $headers))) {
    header("Location: /pages/contact?status=success");
    exit;
}

header("Location: /pages/contact?status=error&message=" . urlencode("Idk what happened, but it's your fault."));
exit;
?>