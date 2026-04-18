<?php 
include '../includes/config.php'; 
$page_title = 'Blog Posts';
$posts = getBlogPosts($pdo, 100, false);
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
        <h3><i class="fas fa-pen-nib" style="color: var(--admin-accent); margin-right: 8px;"></i> All Blog Posts</h3>
        <a href="add-blog.php" class="btn btn-primary"><i class="fas fa-plus"></i> New Post</a>
    </div>
    <div class="content-card-body">
        <?php if ($posts): ?>
        <table class="admin-table">
            <thead><tr><th>#</th><th>Title</th><th>Status</th><th>Published</th><th>Actions</th></tr></thead>
            <tbody>
                <?php foreach($posts as $i => $post): ?>
                <tr>
                    <td><?php echo $i + 1; ?></td>
                    <td><strong><?php echo htmlspecialchars($post['title']); ?></strong></td>
                    <td>
                        <?php if ($post['is_published']): ?>
                        <span class="badge badge-success">Published</span>
                        <?php else: ?>
                        <span class="badge badge-warning">Draft</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo date('M j, Y', strtotime($post['published_at'] ?: $post['created_at'])); ?></td>
                    <td>
                        <div class="action-btns">
                            <a href="blog-edit.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-secondary"><i class="fas fa-edit"></i></a>
                            <a href="blog-delete.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this post?')"><i class="fas fa-trash"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="empty-state">
            <i class="fas fa-pen-nib"></i>
            <p>No blog posts yet. <a href="add-blog.php">Write your first article</a></p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>
