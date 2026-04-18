<?php
include '../includes/config.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch();
if (!$service) { header('Location: services.php'); exit; }
$page_title = 'Delete Service';

if ($_POST && isset($_POST['confirm_delete'])) {
    $check = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE service_id = ?");
    $check->execute([$id]);
    if ($check->fetchColumn() > 0) {
        $error = "Cannot delete — this service is linked to projects. Remove or reassign them first.";
    } else {
        $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$id]);
        header('Location: services.php');
        exit;
    }
}
include 'includes/admin-header.php';
?>

<?php if (isset($error)): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
<?php endif; ?>

<div class="form-container" style="max-width: 600px;">
    <div style="text-align:center; padding: 1.5rem 0 1rem;">
        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: var(--admin-danger); margin-bottom: 1rem;"></i>
        <h2 style="font-size: 1.25rem; margin-bottom: 0.5rem;">Delete Service?</h2>
        <p style="color: var(--admin-text-light); font-size: 0.9rem;">You are about to permanently delete <strong>"<?php echo htmlspecialchars($service['name']); ?>"</strong>. This action cannot be undone.</p>
    </div>
    <div class="form-actions" style="justify-content: center;">
        <form method="POST" style="display:inline;">
            <button type="submit" name="confirm_delete" class="btn btn-danger"><i class="fas fa-trash"></i> Yes, Delete</button>
        </form>
        <a href="services.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
    </div>
</div>

<?php include 'includes/admin-footer.php'; ?>