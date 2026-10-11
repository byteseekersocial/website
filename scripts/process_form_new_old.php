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

// Clean inputs
$name = str_replace(["\r", "\n"], ' ', $name);
$email = str_replace(["\r", "\n"], '', $email);
$subject = str_replace(["\r", "\n"], ' ', $subject);

$to = "byteseeker@byteseeker.link";
$email_subject = "ByteSeeker | New Message: " . $subject;

// HTML Email Template
$email_body = "
<html>
<body style='font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;'>
    <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.1);'>
        <div style='background-color: #2563eb; color: #ffffff; padding: 20px; text-align: center;'>
            <h1 style='margin: 0; font-size: 24px;'>ByteSeeker Contact Form</h1>
        </div>
        <div style='padding: 25px; color: #333333; line-height: 1.6;'>
            <p style='margin-bottom: 20px; border-bottom: 1px solid #eeeeee; padding-bottom: 10px;'>
                <strong>From:</strong> $name ($email)
            </p>
            <p><strong>Subject:</strong> $subject</p>
            <div style='background-color: #f9fafb; border-left: 4px solid #2563eb; padding: 15px; margin-top: 20px; font-style: italic;'>
                " . nl2br(htmlspecialchars($message)) . "
            </div>
        </div>
        <div style='background-color: #f3f4f6; color: #6b7280; padding: 15px; text-align: center; font-size: 12px;'>
            Sent from byteseeker.cc server.
        </div>
    </div>
</body>
</html>
";

// Headers for HTML Email
$headers = [];
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/html; charset=UTF-8";
$headers[] = "From: ByteSeeker Contact <byteseeker@byteseeker.link>";
$headers[] = "Reply-To: $email";
$headers[] = "X-Priority: 1 (Highest)";

if (mail($to, $email_subject, $email_body, implode("\r\n", $headers))) {
    header("Location: /pages/contact?status=success");
    exit;
}

header("Location: /pages/contact?status=error&message=" . urlencode("Idk what happened, but it's your fault."));
exit;
?>