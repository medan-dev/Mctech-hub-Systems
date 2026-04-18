<?php 
include '../includes/config.php'; 
$page_title = 'Projects';
$projects = getProjects($pdo);
include 'includes/admin-header.php';
?>
<style>
.content-card        { background:var(--white); border-radius:var(--r-xl); border:1px solid var(--border); box-shadow:var(--sh-sm); overflow:hidden; }
.content-card-header { display:flex; align-items:center; justify-content:space-between; padding:1rem 1.25rem; border-bottom:1px solid var(--border-2); }
.content-card-header h3 { font-size:.9rem; font-weight:700; color:var(--text); display:flex; align-items:center; gap:7px; }
.content-card-body   { padding:0; }
.action-btns { display:flex; gap:5px; }
.table-thumb { width:40px; height:40px; border-radius:10px; object-fit:cover; border:1px solid var(--border); background:var(--page-bg); }
</style>

<div class="content-card">
    <div class="content-card-header">
        <h3><i class="fas fa-briefcase" style="color: var(--admin-accent); margin-right: 8px;"></i> All Projects</h3>
        <a href="add-project.php" class="btn btn-primary"><i class="fas fa-plus"></i> Add Project</a>
    </div>
    <div class="content-card-body">
        <?php if ($projects): ?>
        <table class="admin-table">
            <thead><tr><th>#</th><th>Image</th><th>Title</th><th>Client</th><th>Service</th><th>Featured</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach($projects as $i => $p): ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td>
                        <?php if (!empty($p['image'])): ?>
                        <img src="../assets/images/projects/<?php echo $p['image']; ?>" class="table-thumb" alt="">
                        <?php else: ?>
                        <div class="table-thumb" style="background: var(--admin-bg); display:flex; align-items:center; justify-content:center;"><i class="fas fa-image" style="color: var(--admin-text-muted);"></i></div>
                        <?php endif; ?>
                    </td>
                    <td><strong><?php echo htmlspecialchars($p['title']); ?></strong></td>
                    <td><?php echo htmlspecialchars($p['client_name'] ?? $p['client_type'] ?? '—'); ?></td>
                    <td><span class="badge badge-info"><?php echo htmlspecialchars($p['service_name'] ?? '—'); ?></span></td>
                    <td><?php echo ($p['is_featured'] ?? 0) ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-warning">No</span>'; ?></td>
                    <td>
                        <div class="action-btns">
                            <a href="projects-edit.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                            <a href="projects-delete.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this project?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-briefcase"></i>
            <p>No projects yet. <a href="add-project.php">Add your first project</a></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
