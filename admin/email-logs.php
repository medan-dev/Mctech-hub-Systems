<?php
include '../includes/config.php';
$page_title = 'Email Logs';

$perPage = 20;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;
$filter  = $_GET['status'] ?? 'all';

try {
    $where    = $filter !== 'all' ? "WHERE status = '{$filter}'" : '';
    $total    = (int)$pdo->query("SELECT COUNT(*) FROM email_logs {$where}")->fetchColumn();
    $logs     = $pdo->query("SELECT l.*, c.name as lead_name FROM email_logs l LEFT JOIN contacts c ON c.id=l.lead_id {$where} ORDER BY l.sent_at DESC LIMIT {$perPage} OFFSET {$offset}")->fetchAll();
    $totalSent   = (int)$pdo->query("SELECT COUNT(*) FROM email_logs WHERE status='sent'")->fetchColumn();
    $totalFailed = (int)$pdo->query("SELECT COUNT(*) FROM email_logs WHERE status='failed'")->fetchColumn();
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
</style>

<?php if (!$tableExists): ?>
<div class="alert alert-danger">
    <i class="fas fa-exclamation-triangle"></i>
    Email tables not set up. <a href="../setup-email-tables.php" style="color:var(--accent); font-weight:700;">Run setup →</a>
</div>
<?php include 'includes/admin-footer.php'; exit; ?>
<?php endif; ?>

<!-- Stats row -->
<div style="display:grid; grid-template-columns:repeat(3,1fr); gap:.85rem; margin-bottom:1.1rem;">
    <div class="card" style="padding:1rem 1.2rem;">
        <div style="display:flex; align-items:center; gap:.65rem;">
            <div style="width:38px;height:38px;border-radius:11px;background:var(--blue-soft);color:var(--blue);display:flex;align-items:center;justify-content:center;font-size:.85rem;"><i class="fas fa-paper-plane"></i></div>
            <div><div style="font-size:1.4rem;font-weight:800;color:var(--text);line-height:1;" data-count="<?php echo $total; ?>"><?php echo $total; ?></div><div style="font-size:.68rem;color:var(--text-3);">Total Emails</div></div>
        </div>
    </div>
    <div class="card" style="padding:1rem 1.2rem;">
        <div style="display:flex; align-items:center; gap:.65rem;">
            <div style="width:38px;height:38px;border-radius:11px;background:var(--green-soft);color:var(--green);display:flex;align-items:center;justify-content:center;font-size:.85rem;"><i class="fas fa-check-circle"></i></div>
            <div><div style="font-size:1.4rem;font-weight:800;color:var(--text);line-height:1;" data-count="<?php echo $totalSent; ?>"><?php echo $totalSent; ?></div><div style="font-size:.68rem;color:var(--text-3);">Sent</div></div>
        </div>
    </div>
    <div class="card" style="padding:1rem 1.2rem;">
        <div style="display:flex; align-items:center; gap:.65rem;">
            <div style="width:38px;height:38px;border-radius:11px;background:var(--accent-soft);color:var(--accent);display:flex;align-items:center;justify-content:center;font-size:.85rem;"><i class="fas fa-exclamation-circle"></i></div>
            <div><div style="font-size:1.4rem;font-weight:800;color:var(--text);line-height:1;" data-count="<?php echo $totalFailed; ?>"><?php echo $totalFailed; ?></div><div style="font-size:.68rem;color:var(--text-3);">Failed</div></div>
        </div>
    </div>
</div>

<!-- Filter tabs -->
<div style="display:flex; gap:6px; margin-bottom:1.1rem; flex-wrap:wrap; align-items:center;">
    <?php foreach (['all'=>'All', 'sent'=>'Sent', 'failed'=>'Failed'] as $k=>$v): ?>
    <a href="?status=<?php echo $k; ?>" class="filter-tab <?php echo $filter===$k?'active':''; ?>"><?php echo $v; ?></a>
    <?php endforeach; ?>
    <a href="mail-settings.php" class="link-pill" style="margin-left:auto;"><i class="fas fa-cog"></i> SMTP Settings</a>
</div>

<div class="content-card">
    <div class="content-card-header">
        <h3><i class="fas fa-history" style="color:var(--accent); font-size:.82rem;"></i> Email Log</h3>
        <span style="font-size:.72rem; color:var(--text-3);"><?php echo $total; ?> email<?php echo $total!=1?'s':''; ?></span>
    </div>
    <div class="content-card-body">
        <?php if ($logs): ?>
        <table class="admin-table">
            <thead><tr><th>Status</th><th>To</th><th>Subject</th><th>Lead</th><th>Sent</th><th>Preview</th></tr></thead>
            <tbody>
            <?php foreach ($logs as $log): ?>
            <tr>
                <td>
                    <?php if ($log['status'] === 'sent'): ?>
                    <span class="badge badge-green"><span class="status-dot active"></span> Sent</span>
                    <?php else: ?>
                    <span class="badge badge-danger"><i class="fas fa-times" style="font-size:.55rem;"></i> Failed</span>
                    <?php endif; ?>
                </td>
                <td style="font-size:.76rem; max-width:180px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?php echo htmlspecialchars($log['to_email']); ?></td>
                <td style="font-size:.76rem; max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-weight:500;"><?php echo htmlspecialchars($log['subject']); ?></td>
                <td>
                    <?php if ($log['lead_name']): ?>
                    <a href="leads.php" style="color:var(--blue); font-size:.72rem; font-weight:600;"><?php echo htmlspecialchars($log['lead_name']); ?></a>
                    <?php else: ?>
                    <span style="color:var(--text-3); font-size:.72rem;">—</span>
                    <?php endif; ?>
                </td>
                <td style="color:var(--text-3); font-size:.72rem; white-space:nowrap;"><?php echo date('M j, g:i A', strtotime($log['sent_at'])); ?></td>
                <td>
                    <button class="btn btn-xs btn-secondary" onclick="showPreview(<?php echo $log['id']; ?>)">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
            <!-- hidden body store -->
            <tr id="body-<?php echo $log['id']; ?>" style="display:none">
                <td colspan="6" style="padding:0;">
                    <div style="display:none" class="email-body-data"><?php echo htmlspecialchars($log['body']); ?></div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($total > $perPage): $pages = ceil($total/$perPage); ?>
        <div style="display:flex; justify-content:center; gap:4px; padding:1rem; border-top:1px solid var(--border-2);">
            <?php for ($i=1;$i<=$pages;$i++): ?>
            <a href="?status=<?php echo $filter; ?>&page=<?php echo $i; ?>"
               style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:8px;font-size:.72rem;font-weight:600;text-decoration:none;border:1.5px solid <?php echo $i===$page?'var(--accent)':'var(--border)'; ?>;background:<?php echo $i===$page?'var(--accent)':'var(--white)'; ?>;color:<?php echo $i===$page?'#fff':'var(--text-2)'; ?>;">
                <?php echo $i; ?>
            </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="empty-state"><i class="fas fa-inbox"></i><p>No emails logged yet.</p></div>
        <?php endif; ?>
    </div>
</div>

<!-- Preview Modal -->
<div id="previewModal" style="display:none; position:fixed; inset:0; background:rgba(11,20,55,.55); z-index:9999; align-items:center; justify-content:center; padding:1rem;">
    <div style="background:#fff; border-radius:var(--r-xl); width:100%; max-width:640px; max-height:85vh; display:flex; flex-direction:column; box-shadow:var(--sh-xl);">
        <div style="display:flex; justify-content:space-between; align-items:center; padding:1rem 1.25rem; border-bottom:1px solid var(--border);">
            <h3 style="font-size:.9rem; font-weight:700; color:var(--text); margin:0;">Email Preview</h3>
            <button onclick="document.getElementById('previewModal').style.display='none'" style="background:none; border:none; cursor:pointer; font-size:1rem; color:var(--text-3);">✕</button>
        </div>
        <div style="flex:1; overflow:auto; padding:0;">
            <iframe id="previewFrame" style="width:100%; min-height:500px; border:none;"></iframe>
        </div>
    </div>
</div>

<script>
const logRows = <?php echo json_encode(array_column($logs ?? [], 'body', 'id')); ?>;
function showPreview(id) {
    const modal = document.getElementById('previewModal');
    const frame = document.getElementById('previewFrame');
    if (logRows[id]) {
        frame.srcdoc = logRows[id];
        modal.style.display = 'flex';
    }
}
document.getElementById('previewModal')?.addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
});
</script>

<?php include 'includes/admin-footer.php'; ?>
