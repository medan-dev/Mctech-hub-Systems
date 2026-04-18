<?php
/**
 * Mctech-hub Systems — Email Tables Setup
 * Run once: http://localhost/mctech-hub/setup-email-tables.php
 */
include 'includes/config.php';

$sqls = [
    // Mail settings
    "CREATE TABLE IF NOT EXISTS mail_settings (
        id          INT PRIMARY KEY DEFAULT 1,
        smtp_host   VARCHAR(255)    DEFAULT '',
        smtp_port   INT             DEFAULT 587,
        smtp_user   VARCHAR(255)    DEFAULT '',
        smtp_pass   VARCHAR(255)    DEFAULT '',
        from_name   VARCHAR(100)    DEFAULT 'Mctech-hub Systems',
        from_email  VARCHAR(100)    DEFAULT 'noreply@mctech-hub.com',
        admin_email VARCHAR(100)    DEFAULT 'admin@mctech-hub.com',
        auto_reply_enabled  BOOLEAN DEFAULT TRUE,
        admin_alert_enabled BOOLEAN DEFAULT TRUE,
        status_email_enabled BOOLEAN DEFAULT FALSE,
        updated_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",

    // Seed default row
    "INSERT IGNORE INTO mail_settings (id) VALUES (1)",

    // Email logs
    "CREATE TABLE IF NOT EXISTS email_logs (
        id         INT PRIMARY KEY AUTO_INCREMENT,
        lead_id    INT     NULL,
        direction  ENUM('outbound','inbound') DEFAULT 'outbound',
        to_email   VARCHAR(255),
        from_email VARCHAR(255),
        subject    VARCHAR(255),
        body       MEDIUMTEXT,
        status     ENUM('sent','failed')      DEFAULT 'sent',
        sent_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (lead_id),
        INDEX (sent_at)
    )",
];

$ok = true;
foreach ($sqls as $sql) {
    try {
        $pdo->exec($sql);
    } catch (PDOException $e) {
        echo "<p style='color:red'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
        $ok = false;
    }
}

if ($ok) {
    echo "<div style='font-family:sans-serif; max-width:500px; margin:60px auto; background:#ecfdf5; padding:30px; border-radius:16px; border:2px solid #10b981;'>
        <h2 style='color:#065f46; margin:0 0 10px;'>✅ Email tables created successfully!</h2>
        <p style='color:#047857; margin:0 0 16px;'>Both <code>mail_settings</code> and <code>email_logs</code> tables are ready.</p>
        <a href='admin/mail-settings.php' style='display:inline-block; background:#e63946; color:#fff; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:700;'>→ Configure Email Settings</a>
        <p style='margin:12px 0 0; font-size:12px; color:#6ee7b7;'>You can delete this file (setup-email-tables.php) after running it.</p>
    </div>";
} else {
    echo "<div style='font-family:sans-serif; max-width:500px; margin:60px auto; background:#fef2f2; padding:30px; border-radius:16px;'>
        <h2 style='color:#991b1b;'>❌ Some errors occurred above.</h2>
    </div>";
}
?>
