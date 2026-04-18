<?php
/**
 * Mctech-hub Systems — Complete Email + Tracking Tables Setup
 * Visit: http://localhost/mctech-hub/setup-tracking-tables.php
 */
include 'includes/config.php';

$sqls = [

    // ── 1. Mail settings ──────────────────────────────────────────────────────
    "CREATE TABLE IF NOT EXISTS mail_settings (
        id                   INT               PRIMARY KEY DEFAULT 1,
        smtp_host            VARCHAR(255)       DEFAULT '',
        smtp_port            INT                DEFAULT 587,
        smtp_user            VARCHAR(255)       DEFAULT '',
        smtp_pass            VARCHAR(255)       DEFAULT '',
        from_name            VARCHAR(100)       DEFAULT 'Mctech-hub Systems',
        from_email           VARCHAR(100)       DEFAULT 'noreply@mctech-hub.com',
        admin_email          VARCHAR(100)       DEFAULT '',
        auto_reply_enabled   TINYINT(1)         DEFAULT 1,
        admin_alert_enabled  TINYINT(1)         DEFAULT 1,
        status_email_enabled TINYINT(1)         DEFAULT 0,
        updated_at           TIMESTAMP          DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
    "INSERT IGNORE INTO mail_settings (id) VALUES (1)",

    // ── 2. Email logs ─────────────────────────────────────────────────────────
    "CREATE TABLE IF NOT EXISTS email_logs (
        id         INT       PRIMARY KEY AUTO_INCREMENT,
        lead_id    INT                   NULL,
        direction  ENUM('outbound','inbound') DEFAULT 'outbound',
        to_email   VARCHAR(255),
        from_email VARCHAR(255),
        subject    VARCHAR(255),
        body       MEDIUMTEXT,
        status     ENUM('sent','failed')  DEFAULT 'sent',
        sent_at    TIMESTAMP             DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_lead_id  (lead_id),
        INDEX idx_sent_at  (sent_at)
    )",

    // ── 3. Page visits (traffic tracking) ────────────────────────────────────
    "CREATE TABLE IF NOT EXISTS page_visits (
        id             INT       PRIMARY KEY AUTO_INCREMENT,
        session_id     VARCHAR(64),
        page_url       VARCHAR(500),
        referrer       VARCHAR(500)           DEFAULT '',
        ip_address     VARCHAR(45)            DEFAULT '',
        user_agent     VARCHAR(500)           DEFAULT '',
        browser        VARCHAR(100)           DEFAULT 'Other',
        os             VARCHAR(100)           DEFAULT 'Other',
        device_type    ENUM('desktop','mobile','tablet') DEFAULT 'desktop',
        country        VARCHAR(100)           DEFAULT '',
        city           VARCHAR(100)           DEFAULT '',
        is_new_visitor TINYINT(1)             DEFAULT 1,
        visited_at     TIMESTAMP             DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_session   (session_id),
        INDEX idx_page_url  (page_url(191)),
        INDEX idx_visited   (visited_at),
        INDEX idx_device    (device_type),
        INDEX idx_country   (country(50))
    )",

    // ── 4. Email subscribers ──────────────────────────────────────────────────
    "CREATE TABLE IF NOT EXISTS email_subscribers (
        id             INT       PRIMARY KEY AUTO_INCREMENT,
        email          VARCHAR(100)           UNIQUE NOT NULL,
        name           VARCHAR(100)           DEFAULT '',
        source         ENUM('popup','contact_form','newsletter','manual') DEFAULT 'popup',
        status         ENUM('active','unsubscribed') DEFAULT 'active',
        subscribed_at  TIMESTAMP             DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_status (status),
        INDEX idx_email  (email)
    )",

];

$ok = true;
$msgs = [];
foreach ($sqls as $sql) {
    $tableName = '';
    if (preg_match('/TABLE.*?`?(\w+)`?\s*\(/i', $sql, $m)) $tableName = $m[1];
    else if (strpos($sql, 'INSERT IGNORE') !== false) $tableName = 'mail_settings (seed)';

    try {
        $pdo->exec($sql);
        $msgs[] = ['ok', $tableName ?: 'Query'];
    } catch (PDOException $e) {
        $msgs[] = ['err', ($tableName ?: 'Query') . ': ' . $e->getMessage()];
        $ok = false;
    }
}

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Mctech-hub Setup</title>
<style>body{font-family:'Segoe UI',sans-serif;background:#f0f4ff;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0;padding:20px;box-sizing:border-box;}
.box{background:#fff;border-radius:20px;padding:32px;max-width:540px;width:100%;box-shadow:0 12px 40px rgba(11,20,55,.12);}
h1{font-size:1.3rem;margin:0 0 6px;color:#0b1437;}
h2{font-size:.85rem;font-weight:700;color:#7b84b0;margin:0 0 20px;text-transform:uppercase;letter-spacing:1px;}
.item{display:flex;align-items:center;gap:10px;padding:8px 12px;border-radius:10px;margin-bottom:6px;font-size:.82rem;}
.item.ok{background:#ecfdf5;color:#065f46;} .item.err{background:#fef2f2;color:#991b1b;}
.item i{flex-shrink:0;}
.btn{display:inline-block;background:#e63946;color:#fff;padding:11px 22px;border-radius:10px;text-decoration:none;font-weight:700;font-size:.85rem;margin-top:16px;margin-right:8px;}
.btn.sec{background:#0b1437;}
p.note{font-size:.72rem;color:#7b84b0;margin-top:12px;}</style></head><body><div class='box'>";

if ($ok) {
    echo "<h1>✅ Setup complete!</h1>
    <h2>Tables created successfully</h2>";
} else {
    echo "<h1>⚠️ Setup completed with some errors</h1>
    <h2>Review the results below</h2>";
}

foreach ($msgs as [$type, $msg]) {
    $icon = $type === 'ok' ? '✓' : '✕';
    echo "<div class='item {$type}'><span>{$icon}</span><span>" . htmlspecialchars($msg) . "</span></div>";
}

echo "<div style='margin-top:20px;'>
    <a href='admin/mail-settings.php' class='btn'>⚙️ Configure Email</a>
    <a href='admin/analytics.php' class='btn sec'>📊 View Analytics</a>
</div>
<p class='note'>🔒 Security tip: Delete this file after running it (<code>setup-tracking-tables.php</code>).</p>
</div></body></html>";
?>
