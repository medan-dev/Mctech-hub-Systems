<?php
include '../includes/config.php';
$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch();
if (!$service) { header('Location: services.php'); exit; }
$page_title = 'Edit Service';

if ($_POST && isset($_POST['update_service'])) {
    $name = trim($_POST['name']);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));
    $short_desc = trim($_POST['short_desc']);
    $full_desc = trim($_POST['full_desc']);
    $category = $_POST['category'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $order_num = (int)($_POST['order_num'] ?? 0);
    $image_path = $service['image'];

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/images/services/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $filename = $slug . '-' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image_path = 'services/' . $filename;
        }
    }

    $stmt = $pdo->prepare("UPDATE services SET name=?, slug=?, short_desc=?, full_desc=?, category=?, image=?, is_featured=?, order_num=?, updated_at=CURRENT_TIMESTAMP WHERE id=?");
    $stmt->execute([$name, $slug, $short_desc, $full_desc, $category, $image_path, $is_featured, $order_num, $id]);
    header('Location: services.php');
    exit;
}
include 'includes/admin-header.php';
?>

<div class="form-container">
    <form method="POST" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group">
                <label>Service Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($service['name']); ?>" required>
            </div>
            <div class="form-group">
                <label>Category *</label>
                <select name="category" required>
                    <?php foreach(['websites'=>'Websites','apps'=>'Apps','ai'=>'AI','care'=>'Care'] as $k=>$v): ?>
                    <option value="<?php echo $k; ?>" <?php echo $service['category']===$k?'selected':''; ?>><?php echo $v; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group full-width">
                <label>Short Description *</label>
                <textarea name="short_desc" rows="2" maxlength="150" required><?php echo htmlspecialchars($service['short_desc']); ?></textarea>
            </div>
            <div class="form-group full-width">
                <label>Full Description *</label>
                <textarea name="full_desc" rows="5" required><?php echo htmlspecialchars($service['full_desc']); ?></textarea>
            </div>
            <div class="form-group">
                <label>Service Image</label>
                <input type="file" name="image" accept="image/*" onchange="previewImg(this)">
                <?php if ($service['image']): ?>
                <div style="margin-top:8px;">
                    <img src="../assets/images/<?php echo htmlspecialchars($service['image']); ?>" style="max-width:150px; border-radius:10px; border:1px solid var(--admin-border);">
                </div>
                <?php endif; ?>
                <div id="img-preview" style="margin-top:8px; display:none;">
                    <img id="preview" src="" style="max-width:150px; border-radius:10px; border:1px solid var(--admin-border);">
                </div>
            </div>
            <div class="form-group">
                <label>Display Order</label>
                <input type="number" name="order_num" value="<?php echo $service['order_num'] ?? 0; ?>" min="0">
            </div>
            <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_featured" <?php echo $service['is_featured']?'checked':''; ?> style="width:auto;"> Featured Service
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="update_service" class="btn btn-primary"><i class="fas fa-save"></i> Update Service</button>
            <a href="services.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
function previewImg(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { document.getElementById('preview').src = e.target.result; document.getElementById('img-preview').style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include 'includes/admin-footer.php'; ?>