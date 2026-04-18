<?php
include '../includes/config.php';
$page_title = 'Mail Settings';
include 'includes/admin-header.php';

$s = [];
try { $s = $pdo->query("SELECT * FROM mail_settings LIMIT 1")->fetch() ?: []; } catch(Exception $e) {
    echo '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Email tables not set up yet. <a href="../setup-email-tables.php" style="color:var(--accent);font-weight:700;">Run setup first →</a></div>';
    include 'includes/admin-footer.php'; exit;
}

$success = $error = '';
if ($_POST && isset($_POST['save_settings'])) {
    try {
        $pdo->prepare("UPDATE mail_settings SET
            smtp_host=?, smtp_port=?, smtp_user=?, smtp_pass=?,
            from_name=?, from_email=?, admin_email=?,
            auto_reply_enabled=?, admin_alert_enabled=?, status_email_enabled=?
            WHERE id=1")
        ->execute([
            trim($_POST['smtp_host']),
            (int)$_POST['smtp_port'],
            trim($_POST['smtp_user']),
            !empty($_POST['smtp_pass']) ? $_POST['smtp_pass'] : ($s['smtp_pass'] ?? ''),
            trim($_POST['from_name']),
            trim($_POST['from_email']),
            trim($_POST['admin_email']),
            isset($_POST['auto_reply'])      ? 1 : 0,
            isset($_POST['admin_alert'])     ? 1 : 0,
            isset($_POST['status_email'])    ? 1 : 0,
        ]);
        $success = 'Settings saved successfully.';
        $s = $pdo->query("SELECT * FROM mail_settings LIMIT 1")->fetch();
    } catch(Exception $e) { $error = $e->getMessage(); }
}

// Test send
if ($_POST && isset($_POST['test_send'])) {
    require_once '../includes/mailer.php';
    $testTo = trim($_POST['test_email'] ?? '');
    if (filter_var($testTo, FILTER_VALIDATE_EMAIL)) {
        $html = '<h2 style="color:#0b1437">✅ Test Email</h2><p>If you\'re reading this, your Mctech-hub email system is working correctly!</p><p style="color:#7b84b0;font-size:13px;">Sent: '.date('F j, Y g:i A').'</p>';
        $sent = Mailer::send($testTo, 'Test Recipient', '✅ Mctech-hub Email Test', $html);
        $success = $sent ? "Test email sent to <strong>{$testTo}</strong> successfully!" : "Test failed. Check your SMTP settings or server mail config.";
    } else { $error = 'Enter a valid email address for the test.'; }
}
?>

<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<div style="display:grid; grid-template-columns:1fr 340px; gap:1rem; align-items:start;">

<!-- ── SMTP Settings Form ── -->
<form method="POST">
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-cog"></i> SMTP Configuration</h3>
        <span class="badge badge-blue">Mail Engine</span>
    </div>
    <div class="card-body" style="padding:1.5rem;">

        <div style="background:var(--blue-soft); border-radius:var(--r-sm); padding:1rem 1.2rem; margin-bottom:1.5rem; border-left:3px solid var(--blue);">
            <p style="margin:0; font-size:.78rem; color:var(--text-2); line-height:1.6;">
                <strong style="color:var(--blue);">How it works:</strong> Mctech-hub uses your SMTP server to send emails. 
                Works with Gmail, Outlook, Mailgun, SendGrid, or any SMTP provider. 
                If left blank, the server's default <code>mail()</code> function is used.
            </p>
        </div>

        <div class="form-grid">
            <div class="form-group">
                <label>SMTP Host</label>
                <input type="text" name="smtp_host" placeholder="smtp.gmail.com" value="<?php echo htmlspecialchars($s['smtp_host'] ?? ''); ?>">
                <div class="form-hint">e.g. smtp.gmail.com · smtp-mail.outlook.com · smtp.mailgun.org</div>
            </div>
            <div class="form-group">
                <label>SMTP Port</label>
                <select name="smtp_port">
                    <option value="587" <?php echo ($s['smtp_port'] ?? 587) == 587 ? 'selected' : ''; ?>>587 (STARTTLS — recommended)</option>
                    <option value="465" <?php echo ($s['smtp_port'] ?? 587) == 465 ? 'selected' : ''; ?>>465 (SSL)</option>
                    <option value="25"  <?php echo ($s['smtp_port'] ?? 587) == 25  ? 'selected' : ''; ?>>25 (Plain — not recommended)</option>
                </select>
            </div>
            <div class="form-group">
                <label>SMTP Username</label>
                <input type="text" name="smtp_user" placeholder="your@email.com" value="<?php echo htmlspecialchars($s['smtp_user'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>SMTP Password</label>
                <input type="password" name="smtp_pass" placeholder="<?php echo !empty($s['smtp_pass']) ? '••••••••••••' : 'Enter password'; ?>">
                <div class="form-hint">For Gmail: use an App Password, not your main password.</div>
            </div>
            <div class="form-group">
                <label>Sender Name</label>
                <input type="text" name="from_name" value="<?php echo htmlspecialchars($s['from_name'] ?? 'Mctech-hub Systems'); ?>">
            </div>
            <div class="form-group">
                <label>Sender Email</label>
                <input type="email" name="from_email" value="<?php echo htmlspecialchars($s['from_email'] ?? 'noreply@mctech-hub.com'); ?>">
            </div>
            <div class="form-group full-width">
                <label>Admin Notification Email</label>
                <input type="email" name="admin_email" placeholder="you@yourcompany.com" value="<?php echo htmlspecialchars($s['admin_email'] ?? ''); ?>">
                <div class="form-hint">Where new lead alerts are sent. This is your email address.</div>
            </div>
        </div>

        <hr style="border:none; border-top:1px solid var(--border); margin:1.25rem 0;">
        <p style="font-size:.78rem; font-weight:700; color:var(--text); margin-bottom:.75rem;">Email Automations</p>
        <div style="display:flex; flex-direction:column; gap:.6rem;">
            <?php
            $toggles = [
                ['auto_reply',   'auto_reply_enabled',   'fas fa-reply',             'var(--accent)',  'Auto-reply to website visitors', 'Send a professional confirmation email to visitors when they submit the contact form.'],
                ['admin_alert',  'admin_alert_enabled',  'fas fa-bell',              'var(--blue)',   'New lead notification to admin', 'Send you an email alert when a new contact form submission is received.'],
                ['status_email', 'status_email_enabled', 'fas fa-envelope',          'var(--green)',  'Status update emails to leads',  'Automatically notify leads when you change their status in the admin.'],
            ];
            foreach ($toggles as [$name, $col, $ico, $cl, $lbl, $hint]):
            ?>
            <label style="display:flex; align-items:flex-start; gap:.85rem; padding:.85rem 1rem; background:var(--page-bg); border-radius:var(--r-sm); cursor:pointer; border:1.5px solid <?php echo isset($_POST[$name]) || (!empty($s[$col]) && $s[$col]) ? 'var(--accent)' : 'var(--border)'; ?>; transition:var(--t);" id="tog-<?php echo $name; ?>">
                <div style="width:38px; height:38px; border-radius:10px; background:<?php echo $cl; ?>18; display:flex; align-items:center; justify-content:center; color:<?php echo $cl; ?>; font-size:.85rem; flex-shrink:0; margin-top:1px;">
                    <i class="<?php echo $ico; ?>"></i>
                </div>
                <div style="flex:1; min-width:0;">
                    <div style="font-size:.78rem; font-weight:700; color:var(--text);"><?php echo $lbl; ?></div>
                    <div style="font-size:.67rem; color:var(--text-3); margin-top:2px; line-height:1.45;"><?php echo $hint; ?></div>
                </div>
                <input type="hidden" name="<?php echo $name; ?>" value="0">
                <div style="position:relative; width:42px; height:24px; flex-shrink:0; margin-top:6px;">
                    <input type="checkbox" name="<?php echo $name; ?>" value="1" <?php echo !empty($s[$col]) ? 'checked' : ''; ?> style="opacity:0; position:absolute; inset:0; cursor:pointer; z-index:2;"
                        onchange="document.getElementById('tog-<?php echo $name; ?>').style.borderColor=this.checked?'var(--accent)':'var(--border)'">
                    <div class="toggle-track" style="width:42px; height:24px; border-radius:20px; background:<?php echo !empty($s[$col]) ? 'var(--accent)' : '#e2e8f0'; ?>; position:absolute; inset:0; transition:.2s; pointer-events:none;"></div>
                    <div style="width:18px; height:18px; border-radius:50%; background:#fff; position:absolute; top:3px; left:<?php echo !empty($s[$col]) ? '21px' : '3px'; ?>; transition:.2s; box-shadow:0 1px 4px rgba(0,0,0,.25); pointer-events:none;"></div>
                </div>
            </label>
            <?php endforeach; ?>
        </div>

        <div class="form-actions">
            <button type="submit" name="save_settings" value="1" class="btn btn-primary"><i class="fas fa-save"></i> Save Settings</button>
        </div>
    </div>
</div>
</form>

<!-- ── Right column ── -->
<div>
    <!-- Test email -->
    <div class="card" style="margin-bottom:1rem;">
        <div class="card-header"><h3><i class="fas fa-flask"></i> Send Test Email</h3></div>
        <div class="card-body" style="padding:1.25rem;">
            <form method="POST">
                <div class="form-group">
                    <label>Test Recipient Email</label>
                    <input type="email" name="test_email" placeholder="your@email.com" required value="<?php echo htmlspecialchars($s['admin_email'] ?? ''); ?>">
                </div>
                <button type="submit" name="test_send" value="1" class="btn btn-blue" style="width:100%;"><i class="fas fa-paper-plane"></i> Send Test Email</button>
            </form>
        </div>
    </div>

    <!-- Quick guides -->
    <div class="card">
        <div class="card-header"><h3><i class="fas fa-book"></i> Quick SMTP Guides</h3></div>
        <div class="card-body" style="padding:1.1rem;">
            <?php
            $guides = [
                ['Gmail','smtp.gmail.com','587','your@gmail.com','Use an App Password from Google Account → Security → App Passwords'],
                ['Outlook','smtp-mail.outlook.com','587','your@outlook.com','Use your regular Outlook password'],
                ['Mailgun','smtp.mailgun.org','587','postmaster@yourdomain.com','From Mailgun dashboard → Sending → Domains → SMTP'],
                ['SendGrid','smtp.sendgrid.net','587','apikey','Use "apikey" as username and your API key as password'],
            ];
            foreach ($guides as [$name, $host, $port, $user, $hint]):
            ?>
            <div style="padding:.7rem; border-radius:var(--r-sm); border:1px solid var(--border); margin-bottom:.55rem; font-size:.72rem;">
                <strong style="color:var(--text); font-size:.78rem;"><?php echo $name; ?></strong><br>
                <span style="color:var(--text-3);">Host:</span> <code style="color:var(--blue); font-size:.72rem;"><?php echo $host; ?></code>
                <span style="color:var(--text-3);">· Port:</span> <code style="color:var(--accent);"><?php echo $port; ?></code>
                <span style="color:var(--text-3);">· User:</span> <code><?php echo $user; ?></code><br>
                <span style="color:var(--text-3); font-size:.67rem; line-height:1.5;"><?php echo $hint; ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

</div><!-- /grid -->

<script>
// Live toggle track color
document.querySelectorAll('input[type=checkbox]').forEach(cb => {
    cb.addEventListener('change', function() {
        const track = this.closest('label').querySelector('.toggle-track');
        const thumb = this.closest('label').querySelector('[style*="border-radius:50%"]');
        if (track) track.style.background = this.checked ? 'var(--accent)' : '#e2e8f0';
        if (thumb) thumb.style.left = this.checked ? '21px' : '3px';
    });
});
</script>

<?php include 'includes/admin-footer.php'; ?>
