<?php 
include '../includes/config.php'; 
$page_title = 'Testimonials';
$testimonials = $pdo->query("SELECT * FROM testimonials ORDER BY created_at DESC")->fetchAll();
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
        <h3><i class="fas fa-star" style="color:var(--accent); margin-right:8px;"></i> All Testimonials</h3>
        <a href="add-testimonial.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Testimonial</a>
    </div>
    <div class="content-card-body">
        <?php if ($testimonials): ?>
        <table class="admin-table">
            <thead><tr><th>#</th><th>Client</th><th>Company</th><th>Rating</th><th>Active</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach($testimonials as $i => $t): ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><strong><?php echo htmlspecialchars($t['client_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($t['company'] ?? '—'); ?></td>
                    <td><?php echo str_repeat('★', $t['rating'] ?? 5); ?></td>
                    <td><?php echo $t['is_active'] ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Hidden</span>'; ?></td>
                    <td>
                        <div class="action-btns">
                            <a href="testimonials-edit.php?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                            <a href="testimonials-delete.php?id=<?php echo $t['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this testimonial?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-star"></i>
            <p>No testimonials yet. <a href="add-testimonial.php">Add your first testimonial</a></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
