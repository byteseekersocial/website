<?php
if ($_SERVER["REQUEST_METHOD"] !== "POST") exit;

$to = $_POST['to_email'];
$reply_content = $_POST['reply_text'];
$subject = "Re: " . $_POST['orig_subject'];

// The Email Body (The one the user sees)
$email_body = "
<html>
<body style='font-family: Arial, sans-serif; background-color: #0f172a; padding: 20px;'>
    <div style='max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden;'>
        <div style='background-color: #2563eb; color: #ffffff; padding: 30px; text-align: center;'>
            <h1 style='margin: 0; font-size: 20px; letter-spacing: 1px;'>ByteSeeker Response</h1>
        </div>
        <div style='padding: 30px; color: #334155; line-height: 1.8; font-size: 16px;'>
            " . nl2br(htmlspecialchars($reply_content)) . "
        </div>
        <div style='background-color: #f8fafc; color: #94a3b8; padding: 20px; text-align: center; font-size: 12px;'>
            You are getting this message because you messaged us
        </div>
    </div>
</body>
</html>
";

$headers = [
    "MIME-Version: 1.0",
    "Content-Type: text/html; charset=UTF-8",
    "From: ByteSeeker <byteseeker@byteseeker.link>",
    "Reply-To: byteseeker@byteseeker.link"
];

// Determine if mail sent
$sent = mail($to, $subject, $email_body, implode("\r\n", $headers));
// Update the database to mark as answered
if ($sent) {
    try {
        $db = new PDO('sqlite:/var/www/html/byteseekerwebsite/submissions.db');
        // We use the email to mark it, or you can pass an 'id' from the form for better accuracy
        $stmt = $db->prepare("UPDATE messages SET is_answered = 1 WHERE email = ?");
        $stmt->execute([$to]);
    } catch (Exception $e) {
        // Log error if needed
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Sent | ByteSeeker</title>
    <style>
        body { 
            background-color: #0f172a; 
            color: #f8fafc; 
            font-family: 'Inter', -apple-system, sans-serif; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            height: 100vh; 
            margin: 0;
        }
        .container { 
            text-align: center; 
            padding: 40px; 
            background: #1e293b; 
            border-radius: 16px; 
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            max-width: 400px;
        }
        h1 { color: #38bdf8; font-size: 24px; margin-bottom: 10px; }
        p { color: #94a3b8; margin-bottom: 30px; }
        .btn {
            background-color: #2563eb;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            transition: background 0.2s;
        }
        .btn:hover { background-color: #1d4ed8; }
    </style>
</head>
<body>
    <div class="container">
        <?php if ($sent): ?>
            <h1>Transmission Sent</h1>
            <p>Your reply has been successfully embedded and dispatched.</p>
        <?php else: ?>
            <h1 style="color: #ef4444;">System Error</h1>
            <p>The message failed to send. Check your server logs.</p>
        <?php endif; ?>
        
        <!-- FIXED LINK: Added /pages/ prefix -->
        <a href="/pages/admin-portal" class="btn">Return to Terminal</a>
    </div>
</body>
</html>