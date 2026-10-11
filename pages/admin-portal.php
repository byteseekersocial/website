<?php
session_set_cookie_params(0);
session_start();

// Load credentials from the external file
$config = include('/var/www/html/byteseekerwebsite/config.php');
$authorized_users = $config['users'];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['user_auth'])) {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';

    // Check if user exists and verify the HASHED password
    if (isset($authorized_users[$user]) && password_verify($pass, $authorized_users[$user])) {
        $_SESSION['authenticated'] = true;
        header("Location: /pages/admin-portal");
        exit;
    } else {
        $error = "Access Denied: Invalid Credentials";
    }
}

$is_logged_in = $_SESSION['authenticated'] ?? false;

// 2. Database connection - only fetch if logged in
if ($is_logged_in) {
    $db = new PDO('sqlite:/var/www/html/byteseekerwebsite/submissions.db');
    $messages = $db->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ByteSeeker Terminal | Admin</title>
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; color: #f8fafc; padding: 40px; }
        h1 { color: #38bdf8; border-bottom: 2px solid #1e293b; padding-bottom: 10px; }
        
        .msg-card { 
            background: #1e293b; 
            padding: 20px; 
            margin-bottom: 20px; 
            border-radius: 12px; 
            position: relative; 
            border: 1px solid #334155;
            transition: opacity 0.3s;
        }
        
        .status-star {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
            color: #94a3b8;
        }
        .answered { 
            border-left: 4px solid #10b981; 
            opacity: 0.7; 
        }
        .answered .status-star { 
            color: #fbbf24; 
            text-shadow: 0 0 10px rgba(251, 191, 36, 0.4);
        }

        textarea { 
            width: 100%; height: 80px; margin-top: 15px; 
            background: #0f172a; color: #fff; border: 1px solid #334155; 
            padding: 10px; border-radius: 8px; font-family: inherit;
        }
        button { 
            background: #2563eb; color: white; border: none; 
            padding: 10px 20px; border-radius: 6px; cursor: pointer; 
            margin-top: 10px; font-weight: 600;
        }
        button:hover { background: #1d4ed8; }
        .meta { color: #94a3b8; font-size: 0.9em; margin-bottom: 10px; }

        /* Modal Styles */
        .auth-overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(12px);
            display: <?= $is_logged_in ? 'none' : 'flex' ?>;
            align-items: center; justify-content: center; z-index: 9999;
        }
        .auth-card {
            background: #1e293b; padding: 40px; border-radius: 16px;
            border: 1px solid #334155; width: 100%; max-width: 320px; 
            text-align: center; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        .auth-card h2 { color: #38bdf8; margin: 0 0 20px 0; font-size: 1.5rem; }
        .auth-card input {
            width: 100%; padding: 12px; margin: 8px 0;
            background: #0f172a; border: 1px solid #334155; color: white;
            border-radius: 8px; box-sizing: border-box; outline: none;
        }
        .auth-card input:focus { border-color: #2563eb; }
        .error-msg { color: #ef4444; font-size: 0.85em; margin-bottom: 15px; }
    </style>
</head>
<body>

    <!-- Popup Overlay -->
    <?php if (!$is_logged_in): ?>
    <div class="auth-overlay">
        <form class="auth-card" method="POST">
            <input type="hidden" name="user_auth" value="1">
            <h2>Access Required</h2>
            <?php if (isset($error)): ?><div class="error-msg"><?= $error ?></div><?php endif; ?>
            <input type="text" name="username" placeholder="Username" required autofocus>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" style="width: 100%; margin-top: 15px;">Authenticate</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Only render content if logged in -->
    <?php if ($is_logged_in): ?>
        <!-- Replace your <h1>Incoming Transmissions</h1> with this -->
<div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #1e293b; margin-bottom: 20px; padding-bottom: 10px;">
    <h1 style="border-bottom: none; margin: 0;">Incoming Transmissions</h1>
    <a href="/scripts/logout" style="text-decoration: none; color: #94a3b8; border: 1px solid #334155; padding: 6px 12px; border-radius: 6px; font-size: 0.85em; transition: all 0.2s;" onmouseover="this.style.color='#ef4444';this.style.borderColor='#ef4444'" onmouseout="this.style.color='#94a3b8';this.style.borderColor='#334155'">
        Terminate Session
    </a>
</div>

        <?php foreach ($messages as $m): ?>
            <div class="msg-card <?= $m['is_answered'] ? 'answered' : '' ?>">
                <div class="status-star">
                    <?= $m['is_answered'] ? '★' : '☆' ?>
                </div>

                <div class="meta">
                    <strong>From:</strong> <?= htmlspecialchars($m['name']) ?> (<?= htmlspecialchars($m['email']) ?>)<br>
                    <strong>Received:</strong> <?= $m['created_at'] ?>
                </div>
                
                <strong>Subject:</strong> <?= htmlspecialchars($m['subject']) ?><br>
                <p style="background: #0f172a; padding: 15px; border-radius: 6px; border: 1px inset #334155;">
                    <?= nl2br(htmlspecialchars($m['message'])) ?>
                </p>
                
                <form action="/scripts/send_reply" method="POST">
                    <input type="hidden" name="to_email" value="<?= $m['email'] ?>">
                    <input type="hidden" name="orig_subject" value="<?= $m['subject'] ?>">
                    <textarea name="reply_text" placeholder="Draft transmission..."></textarea><br>
                    <button type="submit">Dispatch Reply</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <!-- Optional: Background text for when blurred -->
        <h1 style="opacity: 0.1; pointer-events: none;">Restricted Terminal Content</h1>
    <?php endif; ?>

</body>
</html>