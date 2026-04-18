<?php
/**
 * Mctech-hub Systems — SMTP Mailer
 * Handles all outgoing emails: auto-replies, admin alerts, lead replies
 * Reads SMTP config from mail_settings table; falls back to PHP mail()
 */

class Mailer {

    private static function getSettings() {
        global $pdo;
        try {
            $s = $pdo->query("SELECT * FROM mail_settings LIMIT 1")->fetch();
            return $s ?: [];
        } catch (Exception $e) { return []; }
    }

    // ── Send via SMTP using PHP socket ──
    private static function smtpSend($settings, $to, $toName, $subject, $htmlBody) {
        $host     = $settings['smtp_host'];
        $port     = (int)($settings['smtp_port'] ?? 587);
        $user     = $settings['smtp_user'];
        $pass     = $settings['smtp_pass'];
        $fromEmail= $settings['from_email'];
        $fromName = $settings['from_name'] ?? 'Mctech-hub Systems';

        try {
            $socket = @fsockopen(($port == 465 ? 'ssl://' : '') . $host, $port, $errno, $errstr, 10);
            if (!$socket) return false;

            $read = fgets($socket, 512);
            if (!self::smtpOk($read)) { fclose($socket); return false; }

            // EHLO
            $tls = $port == 587;
            fputs($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n");
            while ($line = fgets($socket, 512)) { if (substr($line, 3, 1) === ' ') break; }

            // STARTTLS for port 587
            if ($tls) {
                fputs($socket, "STARTTLS\r\n");
                $r = fgets($socket, 512);
                if (!self::smtpOk($r)) { fclose($socket); return false; }
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
                fputs($socket, "EHLO " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . "\r\n");
                while ($line = fgets($socket, 512)) { if (substr($line, 3, 1) === ' ') break; }
            }

            // AUTH LOGIN
            fputs($socket, "AUTH LOGIN\r\n");
            fgets($socket, 512);
            fputs($socket, base64_encode($user) . "\r\n");
            fgets($socket, 512);
            fputs($socket, base64_encode($pass) . "\r\n");
            $authResp = fgets($socket, 512);
            if (!self::smtpOk($authResp)) { fclose($socket); return false; }

            // MAIL FROM
            fputs($socket, "MAIL FROM:<{$fromEmail}>\r\n");
            fgets($socket, 512);

            // Parse multiple recipients
            $recipients = is_array($to) ? $to : [$to];
            foreach ($recipients as $r) {
                fputs($socket, "RCPT TO:<{$r}>\r\n");
                fgets($socket, 512);
            }

            // DATA
            fputs($socket, "DATA\r\n");
            fgets($socket, 512);

            $boundary = md5(uniqid());
            $toHeader = is_array($to) ? implode(', ', $to) : "{$toName} <{$to}>";
            $msg  = "From: {$fromName} <{$fromEmail}>\r\n";
            $msg .= "To: {$toHeader}\r\n";
            $msg .= "Reply-To: {$fromEmail}\r\n";
            $msg .= "Subject: =?UTF-8?B?" . base64_encode($subject) . "?=\r\n";
            $msg .= "MIME-Version: 1.0\r\n";
            $msg .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
            $msg .= "X-Mailer: Mctech-hub Mailer/2.0\r\n";
            $msg .= "Date: " . date('r') . "\r\n\r\n";

            $plain = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody));
            $msg .= "--{$boundary}\r\n";
            $msg .= "Content-Type: text/plain; charset=UTF-8\r\n\r\n";
            $msg .= $plain . "\r\n\r\n";

            $msg .= "--{$boundary}\r\n";
            $msg .= "Content-Type: text/html; charset=UTF-8\r\n\r\n";
            $msg .= $htmlBody . "\r\n\r\n";
            $msg .= "--{$boundary}--\r\n.\r\n";

            fputs($socket, $msg);
            $sendResp = fgets($socket, 512);
            fputs($socket, "QUIT\r\n");
            fclose($socket);

            return self::smtpOk($sendResp);
        } catch (Exception $e) { return false; }
    }

    private static function smtpOk($line) {
        $code = (int)substr(trim($line), 0, 3);
        return $code >= 200 && $code < 400;
    }

    // ── Send via PHP mail() fallback ──
    private static function phpMailSend($to, $toName, $subject, $htmlBody, $settings) {
        $from = $settings['from_email'] ?? 'noreply@mctech-hub.com';
        $name = $settings['from_name'] ?? 'Mctech-hub Systems';
        $boundary = md5(uniqid());
        $headers  = "From: {$name} <{$from}>\r\n";
        $headers .= "Reply-To: {$from}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/alternative; boundary=\"{$boundary}\"\r\n";
        $headers .= "X-Mailer: Mctech-hub Mailer/2.0\r\n";
        $plain  = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $htmlBody));
        $body   = "--{$boundary}\r\nContent-Type: text/plain; charset=UTF-8\r\n\r\n{$plain}\r\n\r\n";
        $body  .= "--{$boundary}\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n{$htmlBody}\r\n\r\n--{$boundary}--";
        return @mail(is_array($to) ? implode(', ', $to) : $to, $subject, $body, $headers);
    }

    // ── Log email to DB ──
    private static function log($leadId, $toEmail, $fromEmail, $subject, $body, $status, $direction = 'outbound') {
        global $pdo;
        try {
            $pdo->prepare("INSERT INTO email_logs (lead_id, direction, to_email, from_email, subject, body, status) VALUES (?,?,?,?,?,?,?)")
                ->execute([$leadId, $direction, $toEmail, $fromEmail, $subject, $body, $status]);
        } catch (Exception $e) {}
    }

    // ── PUBLIC: Main send method ──
    public static function send($to, $toName, $subject, $htmlBody, $leadId = null) {
        $s = self::getSettings();
        $from = $s['from_email'] ?? 'noreply@mctech-hub.com';

        $sent = false;
        if (!empty($s['smtp_host']) && !empty($s['smtp_user'])) {
            $sent = self::smtpSend($s, $to, $toName, $subject, $htmlBody);
        }
        if (!$sent) {
            $sent = self::phpMailSend($to, $toName, $subject, $htmlBody, $s);
        }

        self::log($leadId, is_array($to) ? implode(', ', $to) : $to, $from, $subject, $htmlBody, $sent ? 'sent' : 'failed');
        return $sent;
    }

    // ────────────────────────────────────────────
    // EMAIL TEMPLATES
    // ────────────────────────────────────────────

    // Wrap content in standard HTML email layout
    private static function wrap($preheader, $content) {
        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Mctech-hub Systems</title>
</head>
<body style="margin:0; padding:0; background:#f0f4ff; font-family:'Segoe UI',Arial,sans-serif; -webkit-font-smoothing:antialiased;">
<!-- Preheader -->
<span style="display:none; max-height:0; overflow:hidden; mso-hide:all;">{$preheader}</span>
<!-- Wrapper -->
<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4ff; padding: 32px 16px;">
  <tr><td align="center">
    <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%;">

      <!-- Header -->
      <tr><td style="background:#0b1437; border-radius:20px 20px 0 0; padding:28px 36px; text-align:center;">
        <p style="margin:0; color:rgba(255,255,255,.4); font-size:11px; letter-spacing:3px; text-transform:uppercase; margin-bottom:8px;">Mctech-hub Systems</p>
        <h1 style="margin:0; color:#ffffff; font-size:22px; font-weight:800; letter-spacing:-0.5px;">Your Digital Growth Partner</h1>
        <p style="margin:8px 0 0; color:rgba(255,255,255,.4); font-size:12px;">Uganda · Africa · Global</p>
      </td></tr>

      <!-- Accent bar -->
      <tr><td height="4" style="background:linear-gradient(90deg,#e63946,#4361ee,#10b981);"></td></tr>

      <!-- Body -->
      <tr><td style="background:#ffffff; padding:36px; border-radius:0 0 20px 20px;">
        {$content}
      </td></tr>

      <!-- Footer -->
      <tr><td style="padding:24px 36px; text-align:center;">
        <p style="margin:0 0 6px; color:#7b84b0; font-size:12px;">
          <a href="http://localhost/mctech-hub/" style="color:#e63946; text-decoration:none; font-weight:700;">mctech-hub.com</a>
          &nbsp;|&nbsp; Kampala, Uganda
          &nbsp;|&nbsp; <a href="https://wa.me/256700000000" style="color:#e63946; text-decoration:none;">WhatsApp</a>
        </p>
        <p style="margin:0; color:#c8d0e8; font-size:11px;">© 2026 Mctech-hub Systems. All rights reserved.</p>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }

    // Template 1: Auto-reply to website visitor
    public static function tplAutoReply($name, $service, $message) {
        $name_h    = htmlspecialchars($name);
        $service_h = htmlspecialchars($service ?: 'General Inquiry');
        $msg_h     = nl2br(htmlspecialchars($message));
        $content   = <<<HTML
<h2 style="margin:0 0 6px; color:#0b1437; font-size:20px; font-weight:800;">Hi {$name_h} 👋</h2>
<p style="margin:0 0 20px; color:#7b84b0; font-size:14px;">We've received your message and we're on it!</p>

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f0f4ff; border-radius:14px; padding:20px; margin-bottom:24px;">
  <tr>
    <td style="font-size:13px; color:#3d4572; line-height:1.7;">
      <strong style="color:#0b1437;">✓ Service Interested In:</strong><br>
      <span style="color:#e63946; font-weight:700;">{$service_h}</span><br><br>
      <strong style="color:#0b1437;">✓ Your Message:</strong><br>
      {$msg_h}
    </td>
  </tr>
</table>

<p style="margin:0 0 12px; color:#3d4572; font-size:14px; line-height:1.7;">
  A member of our team will review your request and reach out to you within <strong style="color:#0b1437;">24 hours</strong>.
  For urgent matters, feel free to WhatsApp us directly.
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
  <tr>
    <td width="48%" style="padding-right:8px;">
      <a href="https://wa.me/256700000000" style="display:block; background:#25D366; color:#fff; text-align:center; padding:13px; border-radius:10px; font-weight:700; font-size:13px; text-decoration:none;">
        💬 WhatsApp Us
      </a>
    </td>
    <td width="48%" style="padding-left:8px;">
      <a href="http://localhost/mctech-hub/portfolio.php" style="display:block; background:#0b1437; color:#fff; text-align:center; padding:13px; border-radius:10px; font-weight:700; font-size:13px; text-decoration:none;">
        🚀 View Our Work
      </a>
    </td>
  </tr>
</table>

<hr style="border:none; border-top:1px solid #e4e9f7; margin:24px 0;">
<p style="margin:0; color:#7b84b0; font-size:12px; text-align:center;">
  You're receiving this because you contacted us at <strong>mctech-hub.com</strong>
</p>
HTML;
        return self::wrap("We've received your message! We'll be in touch within 24 hours.", $content);
    }

    // Template 2: Admin notification of new lead
    public static function tplAdminAlert($name, $email, $phone, $service, $message) {
        $name_h  = htmlspecialchars($name);
        $email_h = htmlspecialchars($email);
        $phone_h = htmlspecialchars($phone);
        $svc_h   = htmlspecialchars($service ?: 'General');
        $msg_h   = nl2br(htmlspecialchars($message));
        $time    = date('F j, Y \a\t g:i A');
        $content = <<<HTML
<div style="background:#fff0f1; border-radius:14px; padding:16px 20px; margin-bottom:24px; border-left:4px solid #e63946;">
  <p style="margin:0; color:#e63946; font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">🔴 New Lead Received</p>
  <p style="margin:4px 0 0; color:#0b1437; font-size:18px; font-weight:800;">{$name_h}</p>
  <p style="margin:4px 0 0; color:#7b84b0; font-size:12px;">{$time}</p>
</div>

<table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:20px;">
  <tr>
    <td style="padding:8px 12px; background:#f8faff; border-radius:8px; margin-bottom:6px; font-size:13px; color:#3d4572; border-left:3px solid #4361ee; margin-bottom:8px;">
      <strong style="color:#0b1437; display:block; margin-bottom:2px;">Email</strong>
      <a href="mailto:{$email_h}" style="color:#4361ee; text-decoration:none;">{$email_h}</a>
    </td>
  </tr>
  <tr><td height="6"></td></tr>
  <tr>
    <td style="padding:8px 12px; background:#f8faff; border-radius:8px; font-size:13px; color:#3d4572; border-left:3px solid #10b981;">
      <strong style="color:#0b1437; display:block; margin-bottom:2px;">Phone / WhatsApp</strong>
      <a href="tel:{$phone_h}" style="color:#10b981; text-decoration:none;">{$phone_h}</a>
    </td>
  </tr>
  <tr><td height="6"></td></tr>
  <tr>
    <td style="padding:8px 12px; background:#f8faff; border-radius:8px; font-size:13px; color:#3d4572; border-left:3px solid #f59e0b;">
      <strong style="color:#0b1437; display:block; margin-bottom:2px;">Service Interested In</strong>
      {$svc_h}
    </td>
  </tr>
  <tr><td height="6"></td></tr>
  <tr>
    <td style="padding:12px; background:#f8faff; border-radius:8px; font-size:13px; color:#3d4572; border-left:3px solid #7c3aed;">
      <strong style="color:#0b1437; display:block; margin-bottom:6px;">Message</strong>
      {$msg_h}
    </td>
  </tr>
</table>

<a href="http://localhost/mctech-hub/admin/leads.php" style="display:block; background:linear-gradient(135deg,#e63946,#c1121f); color:#fff; text-align:center; padding:14px; border-radius:12px; font-weight:700; font-size:14px; text-decoration:none; letter-spacing:0.3px;">
  → View Lead in Admin Portal
</a>
HTML;
        return self::wrap("New lead from {$name_h} — {$svc_h}", $content);
    }

    // Template 3: Custom reply from admin to lead
    public static function tplAdminReply($leadName, $messageBody, $adminName = 'Mctech-hub Team') {
        $lead_h  = htmlspecialchars($leadName);
        $admin_h = htmlspecialchars($adminName);
        $msg_h   = nl2br(htmlspecialchars($messageBody));
        $content = <<<HTML
<h2 style="margin:0 0 6px; color:#0b1437; font-size:20px; font-weight:800;">Hi {$lead_h},</h2>
<p style="margin:0 0 24px; color:#7b84b0; font-size:14px;">You have a reply from the Mctech-hub team</p>

<div style="background:#f0f4ff; border-radius:14px; padding:20px 24px; margin-bottom:24px; border-left:4px solid #4361ee;">
  <p style="margin:0 0 8px; font-size:11px; text-transform:uppercase; letter-spacing:2px; color:#7b84b0; font-weight:700;">{$admin_h} wrote:</p>
  <p style="margin:0; color:#0b1437; font-size:14px; line-height:1.8;">{$msg_h}</p>
</div>

<table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
  <tr>
    <td width="48%" style="padding-right:8px;">
      <a href="https://wa.me/256700000000" style="display:block; background:#25D366; color:#fff; text-align:center; padding:13px; border-radius:10px; font-weight:700; font-size:13px; text-decoration:none;">
        💬 Reply via WhatsApp
      </a>
    </td>
    <td width="48%" style="padding-left:8px;">
      <a href="http://localhost/mctech-hub/contact.php" style="display:block; background:#0b1437; color:#fff; text-align:center; padding:13px; border-radius:10px; font-weight:700; font-size:13px; text-decoration:none;">
        ✉ Send Another Message
      </a>
    </td>
  </tr>
</table>

<hr style="border:none; border-top:1px solid #e4e9f7; margin:24px 0;">
<p style="margin:0; color:#7b84b0; font-size:12px; text-align:center;">
  Mctech-hub Systems · Kampala, Uganda
</p>
HTML;
        return self::wrap("New message from Mctech-hub regarding your inquiry", $content);
    }

    // Template 4: Status update notification
    public static function tplStatusUpdate($leadName, $newStatus, $note = '') {
        $lead_h  = htmlspecialchars($leadName);
        $statusColors = ['contacted'=>'#4361ee','proposal'=>'#f59e0b','closed'=>'#10b981'];
        $statusLabels = ['contacted'=>'Your inquiry is being reviewed','proposal'=>'We\'re preparing a proposal for you','closed'=>'Your project has been closed'];
        $statusEmoji  = ['contacted'=>'👋','proposal'=>'📋','closed'=>'✅'];
        $col   = $statusColors[$newStatus] ?? '#4361ee';
        $label = $statusLabels[$newStatus] ?? 'Status updated';
        $emoji = $statusEmoji[$newStatus] ?? '📌';
        $note_html = $note ? "<div style='background:#f8faff; border-radius:10px; padding:14px; margin-top:16px; font-size:13px; color:#3d4572; line-height:1.7;'><strong style='color:#0b1437;'>A note from our team:</strong><br>" . nl2br(htmlspecialchars($note)) . "</div>" : '';
        $content = <<<HTML
<h2 style="margin:0 0 6px; color:#0b1437; font-size:20px; font-weight:800;">Hi {$lead_h} {$emoji}</h2>
<p style="margin:0 0 24px; color:#7b84b0; font-size:14px;">There's an update on your inquiry</p>

<div style="text-align:center; background:{$col}18; border-radius:16px; padding:24px; margin-bottom:20px; border:2px solid {$col}44;">
  <div style="font-size:36px; margin-bottom:8px;">{$emoji}</div>
  <p style="margin:0; font-size:16px; font-weight:800; color:{$col};">{$label}</p>
</div>

{$note_html}

<table width="100%" cellpadding="0" cellspacing="0" style="margin-top:24px;">
  <tr>
    <td>
      <a href="https://wa.me/256700000000" style="display:block; background:#e63946; color:#fff; text-align:center; padding:14px; border-radius:12px; font-weight:700; font-size:14px; text-decoration:none;">
        💬 Chat With Us on WhatsApp
      </a>
    </td>
  </tr>
</table>

<hr style="border:none; border-top:1px solid #e4e9f7; margin:24px 0;">
<p style="margin:0; color:#7b84b0; font-size:12px; text-align:center;">Mctech-hub Systems · Kampala, Uganda</p>
HTML;
        return self::wrap("Update on your inquiry — {$label}", $content);
    }

    // Template 5: Welcome email for new newsletter subscriber
    public static function tplWelcomeSubscriber($name) {
        $name_h = htmlspecialchars($name);
        $content = <<<HTML
<h2 style="margin:0 0 6px; color:#0b1437; font-size:20px; font-weight:800;">Welcome aboard, {$name_h}! 🎉</h2>
<p style="margin:0 0 20px; color:#7b84b0; font-size:14px;">You've just joined our community of forward-thinking businesses.</p>

<div style="background:linear-gradient(135deg,#0b1437,#1a2c6b); border-radius:16px; padding:24px; margin-bottom:24px; text-align:center;">
  <p style="margin:0 0 8px; color:rgba(255,255,255,.6); font-size:12px; text-transform:uppercase; letter-spacing:2px;">What you'll get</p>
  <div style="display:flex; justify-content:center; gap:12px; flex-wrap:wrap; margin-top:14px;">
    <div style="background:rgba(255,255,255,.08); border-radius:12px; padding:12px 16px; color:#fff; font-size:12px; font-weight:600;">💡 Tech Tips</div>
    <div style="background:rgba(255,255,255,.08); border-radius:12px; padding:12px 16px; color:#fff; font-size:12px; font-weight:600;">🚀 Case Studies</div>
    <div style="background:rgba(255,255,255,.08); border-radius:12px; padding:12px 16px; color:#fff; font-size:12px; font-weight:600;">🤖 AI Insights</div>
    <div style="background:rgba(255,255,255,.08); border-radius:12px; padding:12px 16px; color:#fff; font-size:12px; font-weight:600;">🌍 Africa Tech</div>
  </div>
</div>

<p style="margin:0 0 16px; color:#3d4572; font-size:14px; line-height:1.7;">
  Thank you for subscribing to the Mctech-hub newsletter. We share practical insights on web technology, AI, and digital growth specifically for businesses in Uganda, Africa, and beyond.
</p>

<table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
  <tr>
    <td width="48%" style="padding-right:8px;">
      <a href="http://localhost/mctech-hub/blog.php" style="display:block; background:#e63946; color:#fff; text-align:center; padding:13px; border-radius:10px; font-weight:700; font-size:13px; text-decoration:none;">
        📖 Read Our Blog
      </a>
    </td>
    <td width="48%" style="padding-left:8px;">
      <a href="http://localhost/mctech-hub/contact.php" style="display:block; background:#0b1437; color:#fff; text-align:center; padding:13px; border-radius:10px; font-weight:700; font-size:13px; text-decoration:none;">
        💬 Talk to Us
      </a>
    </td>
  </tr>
</table>

<hr style="border:none; border-top:1px solid #e4e9f7; margin:24px 0;">
<p style="margin:0; color:#7b84b0; font-size:12px; text-align:center;">
  You subscribed at <strong>mctech-hub.com</strong> · <a href="http://localhost/mctech-hub/" style="color:#e63946; text-decoration:none;">Unsubscribe anytime</a>
</p>
HTML;
        return self::wrap("Welcome to Mctech-hub — your digital growth starts here!", $content);
    }

    // Template 6: Broadcast email to all subscribers
    public static function tplBroadcast($name, $subject, $bodyText) {
        $name_h = htmlspecialchars($name);
        $body_h = nl2br(htmlspecialchars($bodyText));
        $content = <<<HTML
<h2 style="margin:0 0 6px; color:#0b1437; font-size:20px; font-weight:800;">Hi {$name_h} 👋</h2>
<p style="margin:0 0 24px; color:#7b84b0; font-size:14px;">A new message from the Mctech-hub team</p>

<div style="font-size:14px; color:#3d4572; line-height:1.8; margin-bottom:28px;">
  {$body_h}
</div>

<table width="100%" cellpadding="0" cellspacing="0" style="margin:24px 0;">
  <tr>
    <td width="48%" style="padding-right:8px;">
      <a href="https://wa.me/256700000000" style="display:block; background:#25D366; color:#fff; text-align:center; padding:13px; border-radius:10px; font-weight:700; font-size:13px; text-decoration:none;">
        💬 WhatsApp Us
      </a>
    </td>
    <td width="48%" style="padding-left:8px;">
      <a href="http://localhost/mctech-hub/contact.php" style="display:block; background:#e63946; color:#fff; text-align:center; padding:13px; border-radius:10px; font-weight:700; font-size:13px; text-decoration:none;">
        🚀 Get Started
      </a>
    </td>
  </tr>
</table>

<hr style="border:none; border-top:1px solid #e4e9f7; margin:24px 0;">
<p style="margin:0; color:#7b84b0; font-size:12px; text-align:center;">
  You're receiving this because you subscribed at <strong>mctech-hub.com</strong>
</p>
HTML;
        return self::wrap($subject, $content);
    }
}
