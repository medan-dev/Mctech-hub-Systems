<?php
/**
 * Newsletter subscribe endpoint — AJAX handler
 * Auto-creates email_subscribers table if it doesn't exist yet
 */
header('Content-Type: application/json');

// Buffer any PHP warnings to prevent JSON corruption
ob_start();

include 'includes/config.php';

// Suppress output from includes so JSON stays clean
ob_clean();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'msg' => 'Invalid request.']);
    exit;
}

$email  = trim(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '');
$name   = trim(strip_tags($_POST['name']   ?? ''));
$source = trim(strip_tags($_POST['source'] ?? 'popup'));

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => false, 'msg' => 'Please enter a valid email address.']);
    exit;
}

// ── Auto-create the table if it doesn't exist ──
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS email_subscribers (
        id            INT         PRIMARY KEY AUTO_INCREMENT,
        email         VARCHAR(100) UNIQUE NOT NULL,
        name          VARCHAR(100) DEFAULT '',
        source        ENUM('popup','contact_form','newsletter','manual') DEFAULT 'popup',
        status        ENUM('active','unsubscribed') DEFAULT 'active',
        subscribed_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_email  (email)
    )");
} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'msg' => 'Database not ready: ' . $e->getMessage()]);
    exit;
}

// ── Check already-subscribed cookie ──
if (!empty($_COOKIE['mct_sub']) && empty($_COOKIE['mct_resubscribe'])) {
    echo json_encode(['ok' => true, 'msg' => 'Already subscribed! ✓', 'already' => true]);
    exit;
}

try {
    // Check for existing subscriber
    $chk = $pdo->prepare("SELECT id, status FROM email_subscribers WHERE email = ?");
    $chk->execute([$email]);
    $row = $chk->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        if ($row['status'] === 'unsubscribed') {
            $pdo->prepare("UPDATE email_subscribers SET status='active', name=?, subscribed_at=NOW() WHERE id=?")
                ->execute([$name ?: null, $row['id']]);
        }
        setcookie('mct_sub', '1', time() + 86400 * 30, '/');
        echo json_encode(['ok' => true, 'msg' => "You're already on our list! ✓", 'already' => true]);
        exit;
    }

    // Insert new subscriber
    $pdo->prepare("INSERT INTO email_subscribers (email, name, source) VALUES (?,?,?)")
        ->execute([$email, $name, $source]);

    setcookie('mct_sub', '1', time() + 86400 * 30, '/');

    // ── Send welcome email (silently skip if mail not configured) ──
    try {
        // Auto-create mail_settings table too if missing
        $pdo->exec("CREATE TABLE IF NOT EXISTS mail_settings (
            id                   INT PRIMARY KEY DEFAULT 1,
            smtp_host            VARCHAR(255) DEFAULT '',
            smtp_port            INT          DEFAULT 587,
            smtp_user            VARCHAR(255) DEFAULT '',
            smtp_pass            VARCHAR(255) DEFAULT '',
            from_name            VARCHAR(100) DEFAULT 'Mctech-hub Systems',
            from_email           VARCHAR(100) DEFAULT 'noreply@mctech-hub.com',
            admin_email          VARCHAR(100) DEFAULT '',
            auto_reply_enabled   TINYINT(1)   DEFAULT 1,
            admin_alert_enabled  TINYINT(1)   DEFAULT 1,
            status_email_enabled TINYINT(1)   DEFAULT 0,
            updated_at           TIMESTAMP    DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )");
        $pdo->exec("INSERT IGNORE INTO mail_settings (id) VALUES (1)");

        $ms = $pdo->query("SELECT * FROM mail_settings LIMIT 1")->fetch(PDO::FETCH_ASSOC);

        if (!empty($ms['auto_reply_enabled'])) {
            require_once 'includes/mailer.php';
            $html = Mailer::tplWelcomeSubscriber($name ?: 'there');
            Mailer::send($email, $name ?: 'Subscriber', '🎉 Welcome to the Mctech-hub Newsletter!', $html);

            if (!empty($ms['admin_email'])) {
                $adminHtml = "<div style='font-family:sans-serif;padding:20px;max-width:480px;'>
                    <h2 style='color:#0b1437;'>📧 New Newsletter Subscriber</h2>
                    <p><b>Email:</b> " . htmlspecialchars($email) . "</p>
                    <p><b>Name:</b> " . htmlspecialchars($name ?: 'N/A') . "</p>
                    <p><b>Source:</b> " . htmlspecialchars($source) . "</p>
                    <p><b>Time:</b> " . date('F j, Y g:i A') . "</p>
                    <a href='http://localhost/mctech-hub/admin/subscribers.php'
                       style='background:#e63946;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:700;display:inline-block;margin-top:10px;'>
                       View Subscribers →
                    </a>
                </div>";
                Mailer::send($ms['admin_email'], 'Admin', "📧 New Subscriber: {$email}", $adminHtml);
            }
        }
    } catch (Exception $mailErr) {
        // Mail failure should not block subscription success
    }

    echo json_encode(['ok' => true, 'msg' => 'Thank you for subscribing! Check your inbox ✓']);

} catch (PDOException $e) {
    // Return the real error for debugging
    $errMsg = $e->getMessage();

    // Friendly message for duplicate key (shouldn't reach here but just in case)
    if (strpos($errMsg, 'Duplicate') !== false || strpos($errMsg, 'duplicate') !== false) {
        setcookie('mct_sub', '1', time() + 86400 * 30, '/');
        echo json_encode(['ok' => true, 'msg' => "You're on the list! ✓", 'already' => true]);
    } else {
        echo json_encode(['ok' => false, 'msg' => 'Database error: ' . $errMsg]);
    }
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'msg' => 'Error: ' . $e->getMessage()]);
}
