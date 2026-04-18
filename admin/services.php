<?php 
include '../includes/config.php'; 
$page_title = 'Services';
$services = getServices($pdo);
include 'includes/admin-header.php';
?>
<style>
.content-card        { background:var(--white); border-radius:var(--r-xl); border:1px solid var(--border); box-shadow:var(--sh-sm); overflow:hidden; }
.content-card-header { display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid var(--border-2); }
.content-card-header h3 { font-size:.9rem; font-weight:700; color:var(--text); display:flex; align-items:center; gap:7px; }
.content-card-body   { padding:0; }
.action-btns { display:flex; gap:5px; }
</style>

<div class="content-card">
    <div class="content-card-header">
        <h3><i class="fas fa-layer-group" style="color: var(--admin-accent); margin-right: 8px;"></i> All Services</h3>
        <a href="services-add.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Service</a>
    </div>
    <div class="content-card-body">
        <?php if ($services): ?>
        <table class="admin-table">
            <thead><tr><th>#</th><th>Name</th><th>Category</th><th>Featured</th><th>Order</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach($services as $i => $s): ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><strong><?php echo htmlspecialchars($s['name']); ?></strong></td>
                    <td><span class="badge badge-info"><?php echo ucfirst($s['category'] ?? '—'); ?></span></td>
                    <td><?php echo $s['is_featured'] ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-warning">No</span>'; ?></td>
                    <td><?php echo $s['order_num'] ?? 0; ?></td>
                    <td>
                        <div class="action-btns">
                            <a href="services-edit.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                            <a href="services-delete.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this service?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-layer-group"></i>
            <p>No services yet. <a href="services-add.php">Add your first service</a></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
