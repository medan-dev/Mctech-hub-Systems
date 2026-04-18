<?php
include '../includes/config.php';
require_once '../includes/mailer.php';
$page_title = 'Leads';

/* ── Handle status update (with optional email) ── */
if ($_POST && isset($_POST['update_status'])) {
    $lead_id   = (int)$_POST['lead_id'];
    $newStatus = $_POST['status'];
    $pdo->prepare("UPDATE contacts SET status=? WHERE id=?")->execute([$newStatus, $lead_id]);

    // Status email
    try { $s = $pdo->query("SELECT * FROM mail_settings LIMIT 1")->fetch() ?: []; } catch(Exception $e) { $s=[]; }
    if (!empty($s['status_email_enabled'])) {
        $lead = $pdo->prepare("SELECT * FROM contacts WHERE id=?")->execute([$lead_id]) ?
                $pdo->prepare("SELECT * FROM contacts WHERE id=?")->execute([$lead_id]) : null;
        // re-fetch properly
        $stmt = $pdo->prepare("SELECT * FROM contacts WHERE id=?"); $stmt->execute([$lead_id]);
        $lead = $stmt->fetch();
        if ($lead && !empty($lead['email']) && in_array($newStatus, ['contacted','proposal','closed'])) {
            $html = Mailer::tplStatusUpdate($lead['name'], $newStatus);
            Mailer::send($lead['email'], $lead['name'], "Update on your inquiry — Mctech-hub Systems", $html, $lead_id);
        }
    }
    header('Location: leads.php?status=' . ($_GET['status'] ?? 'all') . '&msg=status_updated');
    exit;
}

/* ── Handle reply send ── */
if ($_POST && isset($_POST['send_reply'])) {
    $lead_id = (int)$_POST['reply_lead_id'];
    $subject = trim($_POST['reply_subject']);
    $body    = trim($_POST['reply_body']);
    $stmt    = $pdo->prepare("SELECT * FROM contacts WHERE id=?"); $stmt->execute([$lead_id]);
    $lead    = $stmt->fetch();
    $sent    = false;
    if ($lead && !empty($lead['email']) && $subject && $body) {
        $html = Mailer::tplAdminReply($lead['name'], $body, $_SESSION['admin_username'] ?? 'Mctech-hub Team');
        $sent = Mailer::send($lead['email'], $lead['name'], $subject, $html, $lead_id);
        // Also update status to contacted if still new
        if ($lead['status'] === 'new') {
            $pdo->prepare("UPDATE contacts SET status='contacted' WHERE id=?")->execute([$lead_id]);
        }
    }
    header('Location: leads.php?status=' . ($_GET['status'] ?? 'all') . '&msg=' . ($sent ? 'reply_sent' : 'reply_failed'));
    exit;
}

/* ── Data ── */
$statusFilter = $_GET['status'] ?? 'all';
$stmt = $statusFilter === 'all'
    ? $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC")
    : $pdo->prepare("SELECT * FROM contacts WHERE status=? ORDER BY created_at DESC");
if ($statusFilter !== 'all') $stmt->execute([$statusFilter]);
$leads = $stmt->fetchAll();

$counts = [
    'all'       => $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn(),
    'new'       => $pdo->query("SELECT COUNT(*) FROM contacts WHERE status='new'")->fetchColumn(),
    'contacted' => $pdo->query("SELECT COUNT(*) FROM contacts WHERE status='contacted'")->fetchColumn(),
    'proposal'  => $pdo->query("SELECT COUNT(*) FROM contacts WHERE status='proposal'")->fetchColumn(),
    'closed'    => $pdo->query("SELECT COUNT(*) FROM contacts WHERE status='closed'")->fetchColumn(),
];

// Check if mail is configured
$mailOk = false;
try { $ms = $pdo->query("SELECT auto_reply_enabled, smtp_host FROM mail_settings LIMIT 1")->fetch(); $mailOk = !empty($ms['smtp_host']) || $ms !== false; } catch(Exception $e){}

include 'includes/admin-header.php';
?>

<style>
.content-card        { background:var(--white); border-radius:var(--r-xl); border:1px solid var(--border); box-shadow:var(--sh-sm); overflow:hidden; }
.content-card-header { display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid var(--border-2); }
.content-card-header h3 { font-size:.9rem; font-weight:700; color:var(--text); display:flex; align-items:center; gap:7px; }
.content-card-body   { padding:0; }
.action-btns         { display:flex; gap:5px; }

/* Filter tabs */
.lead-filter {
    padding:.42rem .9rem; border-radius:20px; text-decoration:none;
    font-size:.74rem; font-weight:600; color:var(--text-3);
    background:var(--white); border:1.5px solid var(--border);
    transition:var(--t); white-space:nowrap;
}
.lead-filter:hover  { border-color:var(--accent); color:var(--accent); }
.lead-filter.active { background:var(--accent); color:#fff; border-color:var(--accent); box-shadow:0 3px 10px rgba(230,57,70,.25); }
.lead-filter .count { opacity:.7; margin-left:3px; }
.lead-filter.active .count { opacity:1; }

/* Expand panel */
.lead-details-row td  { padding:0 !important; }
.lead-detail-panel    { padding:.9rem 1.15rem; background:#fffbfb; border-left:3px solid var(--accent); animation:slideDown .2s ease; }
@keyframes slideDown  { from {opacity:0; transform:translateY(-6px);} to {opacity:1; transform:translateY(0);} }
.lead-detail-panel p  { margin:4px 0; font-size:.76rem; color:var(--text); }
.lead-detail-panel .msg { margin-top:.6rem; padding-top:.6rem; border-top:1px solid var(--border); font-style:italic; color:var(--text-2); font-size:.76rem; line-height:1.55; }

/* Reply Modal */
.reply-modal-overlay {
    display:none; position:fixed; inset:0;
    background:rgba(11,20,55,.55); z-index:9999;
    align-items:center; justify-content:center; padding:1rem;
}
.reply-modal-overlay.open { display:flex; }
.reply-modal {
    background:#fff; border-radius:var(--r-xl); width:100%; max-width:560px;
    box-shadow:var(--sh-xl); animation:fadeUp .25s ease;
}
@keyframes fadeUp { from{opacity:0;transform:translateY(20px);} to{opacity:1;transform:translateY(0);} }
.reply-modal-header {
    display:flex; justify-content:space-between; align-items:center;
    padding:1rem 1.25rem; border-bottom:1px solid var(--border);
}
.reply-modal-header h3 { font-size:.9rem; font-weight:700; color:var(--text); margin:0; display:flex; align-items:center; gap:7px; }
.reply-modal-body { padding:1.25rem; }
</style>

<?php
// Flash messages
$flashMap = [
    'status_updated' => ['success', '<i class="fas fa-check-circle"></i> Lead status updated.'],
    'reply_sent'     => ['success', '<i class="fas fa-check-circle"></i> Reply email sent successfully!'],
    'reply_failed'   => ['danger',  '<i class="fas fa-exclamation-triangle"></i> Email send failed. Check your <a href="mail-settings.php" style="font-weight:700;color:var(--accent);">SMTP settings</a>.'],
];
if (isset($_GET['msg'], $flashMap[$_GET['msg']])):
    [$type, $txt] = $flashMap[$_GET['msg']];
?>
<div class="alert alert-<?php echo $type; ?>"><?php echo $txt; ?></div>
<?php endif; ?>

<?php if (!$mailOk): ?>
<div class="alert" style="background:var(--yellow-soft); color:#92400e; border:1px solid #fcd34d;">
    <i class="fas fa-info-circle"></i>
    Email not configured. <a href="mail-settings.php" style="font-weight:700; color:#92400e; text-decoration:underline;">Set up SMTP →</a> to enable auto-replies and manual replies.
</div>
<?php endif; ?>

<!-- Filter Tabs -->
<div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:1.1rem; align-items:center;">
    <?php foreach (['all'=>'All','new'=>'New','contacted'=>'Contacted','proposal'=>'Proposal','closed'=>'Closed'] as $k=>$v): ?>
    <a href="leads.php?status=<?php echo $k; ?>" class="lead-filter <?php echo $statusFilter===$k?'active':''; ?>">
        <?php echo $v; ?> <span class="count">(<?php echo $counts[$k]; ?>)</span>
    </a>
    <?php endforeach; ?>
    <a href="email-logs.php" class="link-pill" style="margin-left:auto;"><i class="fas fa-history"></i> Email Logs</a>
</div>

<div class="content-card">
    <div class="content-card-header">
        <h3><i class="fas fa-envelope-open-text" style="color:var(--accent); font-size:.82rem;"></i>
            <?php echo ucfirst($statusFilter); ?> Leads
        </h3>
        <span class="badge badge-blue"><?php echo count($leads); ?> total</span>
    </div>
    <div class="content-card-body">
        <?php if ($leads):
            $avColors = ['red','blue','green','yellow','purple'];
            $badgeMap = ['new'=>'badge-danger','contacted'=>'badge-info','proposal'=>'badge-yellow','closed'=>'badge-green'];
        ?>
        <table class="admin-table">
            <thead><tr><th>Contact</th><th>Service</th><th>Status</th><th>Date</th><th>Update Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($leads as $i => $lead): $c = $avColors[$i % 5]; ?>
            <tr style="cursor:pointer;" onclick="toggleDetail(<?php echo $lead['id']; ?>)">
                <td>
                    <div class="cell-avatar">
                        <div class="cell-av <?php echo $c; ?>"><?php echo strtoupper(substr($lead['name'],0,1)); ?></div>
                        <div>
                            <div class="cell-av-name"><?php echo htmlspecialchars($lead['name']); ?></div>
                            <?php if (!empty($lead['email'])): ?><div class="cell-av-sub"><?php echo htmlspecialchars($lead['email']); ?></div><?php endif; ?>
                        </div>
                    </div>
                </td>
                <td><?php echo htmlspecialchars($lead['service_interest'] ?: '—'); ?></td>
                <td>
                    <span class="badge <?php echo $badgeMap[$lead['status']] ?? 'badge-gray'; ?>">
                        <span class="status-dot <?php echo $lead['status'] === 'new' ? 'new' : 'active'; ?>"></span>
                        <?php echo ucfirst($lead['status']); ?>
                    </span>
                </td>
                <td style="color:var(--text-3); font-size:.72rem; white-space:nowrap;"><?php echo date('M j, Y', strtotime($lead['created_at'])); ?></td>
                <td onclick="event.stopPropagation()">
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="lead_id" value="<?php echo $lead['id']; ?>">
                        <input type="hidden" name="update_status" value="1">
                        <select name="status" onchange="this.form.submit()"
                            style="padding:4px 8px; border-radius:8px; border:1.5px solid var(--border); font-size:.73rem; font-family:inherit; cursor:pointer; background:var(--white); color:var(--text);">
                            <option value="new"       <?php echo $lead['status']==='new'?'selected':''; ?>>New</option>
                            <option value="contacted"  <?php echo $lead['status']==='contacted'?'selected':''; ?>>Contacted</option>
                            <option value="proposal"   <?php echo $lead['status']==='proposal'?'selected':''; ?>>Proposal</option>
                            <option value="closed"     <?php echo $lead['status']==='closed'?'selected':''; ?>>Closed</option>
                        </select>
                    </form>
                </td>
                <td onclick="event.stopPropagation()">
                    <div class="action-btns">
                        <?php if (!empty($lead['email'])): ?>
                        <button class="btn btn-xs btn-primary" title="Reply by Email"
                            onclick="openReply(<?php echo $lead['id']; ?>, '<?php echo addslashes(htmlspecialchars($lead['name'])); ?>', '<?php echo addslashes(htmlspecialchars($lead['email'])); ?>')">
                            <i class="fas fa-reply"></i>
                        </button>
                        <?php endif; ?>
                        <?php if (!empty($lead['email'])): ?>
                        <a class="btn btn-xs btn-secondary" href="mailto:<?php echo htmlspecialchars($lead['email']); ?>" title="Open in email client">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <?php endif; ?>
                        <?php if (!empty($lead['phone'])): ?>
                        <a class="btn btn-xs btn-secondary" href="https://wa.me/<?php echo preg_replace('/[^0-9]/','',$lead['phone']); ?>" target="_blank" title="WhatsApp">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>

            <!-- Expand row -->
            <tr id="detail-<?php echo $lead['id']; ?>" class="lead-details-row" style="display:none;">
                <td colspan="6">
                    <div class="lead-detail-panel">
                        <div style="display:flex; flex-wrap:wrap; gap:1rem;">
                            <div style="flex:1; min-width:200px;">
                                <p><strong>Full Name:</strong> <?php echo htmlspecialchars($lead['name']); ?></p>
                                <?php if (!empty($lead['email'])): ?><p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>" style="color:var(--accent);"><?php echo htmlspecialchars($lead['email']); ?></a></p><?php endif; ?>
                                <?php if (!empty($lead['phone'])): ?><p><strong>Phone:</strong> <a href="tel:<?php echo htmlspecialchars($lead['phone']); ?>" style="color:var(--accent);"><?php echo htmlspecialchars($lead['phone']); ?></a></p><?php endif; ?>
                                <p><strong>Service:</strong> <?php echo htmlspecialchars($lead['service_interest'] ?: 'General Inquiry'); ?></p>
                                <p><strong>Received:</strong> <?php echo date('F j, Y \a\t g:i A', strtotime($lead['created_at'])); ?></p>
                            </div>
                            <?php if (!empty($lead['message'])): ?>
                            <div style="flex:2; min-width:280px;" class="msg">
                                <strong>Message:</strong><br>
                                <?php echo nl2br(htmlspecialchars($lead['message'])); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p><?php echo $statusFilter==='all' ? 'No leads yet. Your contact form submissions will appear here.' : 'No "'.ucfirst($statusFilter).'" leads.'; ?></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ── Reply Modal ── -->
<div class="reply-modal-overlay" id="replyModal">
    <div class="reply-modal">
        <div class="reply-modal-header">
            <h3><i class="fas fa-paper-plane" style="color:var(--accent);"></i> Send Email Reply</h3>
            <button onclick="closeReply()" style="background:none; border:none; cursor:pointer; font-size:1.1rem; color:var(--text-3); line-height:1;">✕</button>
        </div>
        <div class="reply-modal-body">
            <div style="display:flex; align-items:center; gap:.65rem; padding:.7rem .9rem; background:var(--page-bg); border-radius:var(--r-sm); margin-bottom:1rem;">
                <div id="replyAvatar" style="width:36px;height:36px;border-radius:10px;background:var(--accent);color:#fff;font-weight:700;font-size:.85rem;display:flex;align-items:center;justify-content:center;flex-shrink:0;">?</div>
                <div>
                    <div id="replyName" style="font-size:.8rem;font-weight:700;color:var(--text);">Lead Name</div>
                    <div id="replyEmail" style="font-size:.68rem;color:var(--text-3);">email@example.com</div>
                </div>
            </div>
            <form method="POST" id="replyForm">
                <input type="hidden" name="send_reply" value="1">
                <input type="hidden" name="reply_lead_id" id="replyLeadId" value="">
                <div class="form-group">
                    <label>Subject</label>
                    <input type="text" name="reply_subject" id="replySubject" value="Re: Your Inquiry — Mctech-hub Systems" required>
                </div>
                <div class="form-group">
                    <label>Message</label>
                    <textarea name="reply_body" id="replyBody" rows="7" required placeholder="Write your professional reply here...
                    
The email will be beautifully formatted and branded automatically."></textarea>
                </div>
                <div style="display:flex; gap:.5rem; margin-top:.25rem;">
                    <button type="submit" class="btn btn-primary" style="flex:1;"><i class="fas fa-paper-plane"></i> Send Email</button>
                    <button type="button" onclick="closeReply()" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</button>
                </div>
                <p style="font-size:.65rem; color:var(--text-3); text-align:center; margin-top:.6rem;">
                    <i class="fas fa-shield-alt"></i> Sent as Mctech-hub Systems · Beautifully formatted HTML email
                </p>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDetail(id) {
    const row = document.getElementById('detail-' + id);
    if (!row) return;
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}

function openReply(id, name, email) {
    document.getElementById('replyLeadId').value  = id;
    document.getElementById('replyName').textContent  = name;
    document.getElementById('replyEmail').textContent = email;
    document.getElementById('replyAvatar').textContent = name.charAt(0).toUpperCase();
    document.getElementById('replyModal').classList.add('open');
    setTimeout(() => document.getElementById('replyBody').focus(), 100);
}

function closeReply() {
    document.getElementById('replyModal').classList.remove('open');
    document.getElementById('replyBody').value = '';
}

document.getElementById('replyModal').addEventListener('click', function(e) {
    if (e.target === this) closeReply();
});

document.addEventListener('keydown', e => { if(e.key === 'Escape') closeReply(); });
</script>

<?php include 'includes/admin-footer.php'; ?>