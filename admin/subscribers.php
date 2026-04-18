<?php
include '../includes/config.php';
require_once '../includes/mailer.php';
$page_title = 'Subscribers';

$success = $error = '';

/* ── Broadcast send ── */
if ($_POST && isset($_POST['send_broadcast'])) {
    $subject  = trim($_POST['bc_subject'] ?? '');
    $bodyText = trim($_POST['bc_body'] ?? '');
    if ($subject && $bodyText) {
        try {
            $subs = $pdo->query("SELECT * FROM email_subscribers WHERE status='active'")->fetchAll();
            $sent = $failed = 0;
            foreach ($subs as $sub) {
                $html = Mailer::tplBroadcast($sub['name'] ?: 'there', $subject, $bodyText);
                $ok   = Mailer::send($sub['email'], $sub['name'] ?: '', $subject, $html);
                $ok ? $sent++ : $failed++;
                usleep(50000); // 50ms pause between sends — be kind to SMTP
            }
            $success = "Broadcast sent to <strong>{$sent}</strong> subscriber(s)." . ($failed ? " <em>{$failed} failed.</em>" : '');
        } catch (Exception $e) {
            $error = 'Broadcast error: ' . $e->getMessage();
        }
    } else { $error = 'Subject and message body are required.'; }
}

/* ── Unsubscribe ── */
if (isset($_GET['unsub'])) {
    $pdo->prepare("UPDATE email_subscribers SET status='unsubscribed' WHERE id=?")->execute([(int)$_GET['unsub']]);
    header('Location: subscribers.php?msg=unsubscribed'); exit;
}
/* ── Delete ── */
if (isset($_GET['del'])) {
    $pdo->prepare("DELETE FROM email_subscribers WHERE id=?")->execute([(int)$_GET['del']]);
    header('Location: subscribers.php?msg=deleted'); exit;
}
/* ── Re-activate ── */
if (isset($_GET['activate'])) {
    $pdo->prepare("UPDATE email_subscribers SET status='active' WHERE id=?")->execute([(int)$_GET['activate']]);
    header('Location: subscribers.php?msg=activated'); exit;
}

try {
    $filter   = $_GET['status'] ?? 'active';
    $where    = $filter !== 'all' ? "WHERE status = '{$filter}'" : '';
    $subs     = $pdo->query("SELECT * FROM email_subscribers {$where} ORDER BY subscribed_at DESC")->fetchAll();
    $totActive  = (int)$pdo->query("SELECT COUNT(*) FROM email_subscribers WHERE status='active'")->fetchColumn();
    $totAll     = (int)$pdo->query("SELECT COUNT(*) FROM email_subscribers")->fetchColumn();
    $totToday   = (int)$pdo->query("SELECT COUNT(*) FROM email_subscribers WHERE DATE(subscribed_at)=CURDATE()")->fetchColumn();
    $tableExists = true;
} catch (Exception $e) {
    $tableExists = false;
}

include 'includes/admin-header.php';
?>

<style>
.content-card        { background:var(--white); border-radius:var(--r-xl); border:1px solid var(--border); box-shadow:var(--sh-sm); overflow:hidden; }
.content-card-header { display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid var(--border-2); }
.content-card-header h3 { font-size:.9rem; font-weight:700; color:var(--text); display:flex; align-items:center; gap:7px; }
.content-card-body   { padding:0; }
.action-btns { display:flex; gap:5px; }
/* Broadcast compose */
.bc-panel { background:var(--white); border-radius:var(--r-xl); border:1px solid var(--border); box-shadow:var(--sh-sm); margin-bottom:1rem; }
.bc-panel-hd { display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid var(--border); cursor:pointer; user-select:none; }
.bc-panel-hd h3 { font-size:.9rem; font-weight:700; color:var(--text); margin:0; display:flex; align-items:center; gap:8px; }
.bc-body { padding:1.25rem; display:none; }
.bc-body.open { display:block; }
</style>

<?php if (!$tableExists): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i>
    Subscriber tables not set up. <a href="../setup-tracking-tables.php" style="font-weight:700;color:var(--accent);">Run setup →</a>
</div>
<?php include 'includes/admin-footer.php'; exit; ?>
<?php endif; ?>

<!-- Flash messages -->
<?php
$flashMap = [
    'unsubscribed' => ['warning', '<i class="fas fa-user-times"></i> Subscriber marked as unsubscribed.'],
    'deleted'      => ['danger',  '<i class="fas fa-trash"></i> Subscriber deleted.'],
    'activated'    => ['success', '<i class="fas fa-check-circle"></i> Subscriber re-activated.'],
];
if (isset($_GET['msg'], $flashMap[$_GET['msg']])):
    [$type, $txt] = $flashMap[$_GET['msg']];
?><div class="alert alert-<?php echo $type; ?>"><?php echo $txt; ?></div><?php endif; ?>
<?php if ($success): ?><div class="alert alert-success"><i class="fas fa-check-circle"></i> <?php echo $success; ?></div><?php endif; ?>
<?php if ($error):   ?><div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?></div><?php endif; ?>

<!-- ── Stat cards ── -->
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:.85rem; margin-bottom:1rem;">
    <?php foreach ([
        ['fas fa-users','var(--blue)','--blue-soft', $totAll,    'Total Subscribers'],
        ['fas fa-check-circle','var(--green)','--green-soft', $totActive,'Active Now'],
        ['fas fa-calendar-day','var(--accent)','--accent-soft', $totToday,'Joined Today'],
    ] as [$ico,$col,$soft,$v,$l]): ?>
    <div style="background:var(--white); border-radius:var(--r-xl); border:1px solid var(--border); box-shadow:var(--sh-sm); padding:1rem 1.2rem; display:flex; align-items:center; gap:.75rem;">
        <div style="width:40px;height:40px;border-radius:12px;background:var(<?php echo $soft;?>);color:<?php echo $col;?>;display:flex;align-items:center;justify-content:center;font-size:.85rem;flex-shrink:0;"><i class="<?php echo $ico; ?>"></i></div>
        <div><div style="font-size:1.5rem;font-weight:800;color:var(--text);line-height:1;" data-count="<?php echo $v; ?>"><?php echo number_format($v); ?></div><div style="font-size:.66rem;color:var(--text-3);text-transform:uppercase;letter-spacing:.5px;"><?php echo $l; ?></div></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Broadcast panel (collapsible) ── -->
<div class="bc-panel">
    <div class="bc-panel-hd" onclick="toggleBroadcast()">
        <h3><i class="fas fa-broadcast-tower" style="color:var(--accent);"></i> Send Broadcast Email
            <span style="font-size:.68rem; background:var(--accent-soft); color:var(--accent); padding:2px 8px; border-radius:20px; font-weight:700;"><?php echo $totActive; ?> active</span>
        </h3>
        <i class="fas fa-chevron-down" id="bcChevron" style="color:var(--text-3); font-size:.75rem; transition:.2s;"></i>
    </div>
    <div class="bc-body" id="bcBody">
        <div style="background:var(--blue-soft); border-radius:var(--r-sm); padding:.75rem 1rem; margin-bottom:1rem; border-left:3px solid var(--blue); font-size:.76rem; color:var(--text-2); line-height:1.6;">
            <strong style="color:var(--blue);">📢 Broadcast:</strong> This will send a professionally branded HTML email to all <strong><?php echo $totActive; ?></strong> active subscribers.
            Make sure your <a href="mail-settings.php" style="color:var(--blue); font-weight:700;">SMTP settings</a> are configured first.
        </div>
        <form method="POST">
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Email Subject</label>
                    <input type="text" name="bc_subject" placeholder="🚀 New: AI Workflow Tools for Your Business" required>
                </div>
                <div class="form-group full-width">
                    <label>Message Body <span style="color:var(--text-3); font-weight:400; font-size:.7rem;">(plain text — beautifully formatted automatically)</span></label>
                    <textarea name="bc_body" rows="8" placeholder="Write your newsletter content here. It will be wrapped in a professional branded HTML template automatically.

Tips:
• Keep it personal and valuable  
• Include a clear call to action
• Mention your website or latest work

Unsubscribe links are added automatically." required></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" name="send_broadcast" value="1" class="btn btn-primary" onclick="return confirm('Send broadcast to <?php echo $totActive; ?> subscribers?')">
                    <i class="fas fa-broadcast-tower"></i> Send to <?php echo $totActive; ?> Subscribers
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ── Subscribers list ── -->
<div style="display:flex; gap:6px; margin-bottom:.85rem; align-items:center;">
    <?php foreach (['active'=>'Active','all'=>'All','unsubscribed'=>'Unsubscribed'] as $k=>$v): ?>
    <a href="?status=<?php echo $k; ?>" class="filter-tab <?php echo $filter===$k?'active':''; ?>"><?php echo $v; ?></a>
    <?php endforeach; ?>
</div>

<div class="content-card">
    <div class="content-card-header">
        <h3><i class="fas fa-users" style="color:var(--blue); font-size:.82rem;"></i> Subscribers</h3>
        <span class="badge badge-blue"><?php echo count($subs); ?> shown</span>
    </div>
    <div class="content-card-body">
        <?php if ($subs): ?>
        <table class="admin-table">
            <thead><tr><th>#</th><th>Email</th><th>Name</th><th>Source</th><th>Status</th><th>Joined</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($subs as $i => $sub): ?>
            <tr>
                <td><?php echo $i+1; ?></td>
                <td><strong style="font-size:.76rem;"><?php echo htmlspecialchars($sub['email']); ?></strong></td>
                <td style="color:var(--text-2); font-size:.73rem;"><?php echo htmlspecialchars($sub['name'] ?: '—'); ?></td>
                <td><span class="badge badge-info" style="font-size:.62rem;"><?php echo htmlspecialchars(ucfirst($sub['source'] ?? 'popup')); ?></span></td>
                <td>
                    <?php echo $sub['status'] === 'active'
                        ? '<span class="badge badge-green"><span class="status-dot active"></span> Active</span>'
                        : '<span class="badge badge-gray">Unsubscribed</span>'; ?>
                </td>
                <td style="color:var(--text-3); font-size:.72rem;"><?php echo date('M j, Y', strtotime($sub['subscribed_at'])); ?></td>
                <td>
                    <div class="action-btns">
                        <?php if ($sub['status'] === 'active'): ?>
                        <a href="?status=<?php echo $filter; ?>&unsub=<?php echo $sub['id']; ?>" class="btn btn-xs btn-yellow" title="Unsubscribe" onclick="return confirm('Unsubscribe this email?')"><i class="fas fa-user-times"></i></a>
                        <?php else: ?>
                        <a href="?status=<?php echo $filter; ?>&activate=<?php echo $sub['id']; ?>" class="btn btn-xs btn-green" title="Re-activate"><i class="fas fa-user-check"></i></a>
                        <?php endif; ?>
                        <a href="?status=<?php echo $filter; ?>&del=<?php echo $sub['id']; ?>" class="btn btn-xs btn-danger" title="Delete" onclick="return confirm('Permanently delete subscriber?')"><i class="fas fa-trash"></i></a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state"><i class="fas fa-users"></i><p>No subscribers yet. The pop-up widget on your website will capture them.</p></div>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleBroadcast() {
    const body  = document.getElementById('bcBody');
    const chev  = document.getElementById('bcChevron');
    const open  = body.classList.toggle('open');
    chev.style.transform = open ? 'rotate(180deg)' : '';
}
</script>

<?php include 'includes/admin-footer.php'; ?>
