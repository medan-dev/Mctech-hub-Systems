<?php
include '../includes/config.php';
$page_title = 'Add Service';

if ($_POST && isset($_POST['add_service'])) {
    $name = trim($_POST['name']);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $name));
    $short_desc = trim($_POST['short_desc']);
    $full_desc = trim($_POST['full_desc']);
    $category = $_POST['category'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $order_num = (int)($_POST['order_num'] ?? 0);

    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/images/services/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $filename = $slug . '-' . time() . '.' . $ext;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_dir . $filename)) {
            $image_path = 'services/' . $filename;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO services (name, slug, short_desc, full_desc, category, image, is_featured, order_num) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$name, $slug, $short_desc, $full_desc, $category, $image_path, $is_featured, $order_num]);
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
                <input type="text" name="name" placeholder="e.g. Web Development" required>
            </div>
            <div class="form-group">
                <label>Category *</label>
                <select name="category" required>
                    <option value="">Select Category</option>
                    <option value="websites">Websites</option>
                    <option value="apps">Apps</option>
                    <option value="ai">AI</option>
                    <option value="care">Care</option>
                </select>
            </div>
            <div class="form-group full-width">
                <label>Short Description *</label>
                <textarea name="short_desc" placeholder="Brief description (max 150 chars)" rows="2" maxlength="150" required></textarea>
            </div>
            <div class="form-group full-width">
                <label>Full Description *</label>
                <textarea name="full_desc" placeholder="Detailed service description..." rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label>Service Image</label>
                <input type="file" name="image" accept="image/*" onchange="previewImg(this)">
                <div id="img-preview" style="margin-top:8px; display:none;">
                    <img id="preview" src="" style="max-width:180px; border-radius:10px; border:1px solid var(--admin-border);">
                </div>
            </div>
            <div class="form-group">
                <label>Display Order</label>
                <input type="number" name="order_num" value="0" min="0">
            </div>
            <div class="form-group">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_featured" checked style="width:auto;"> Featured Service
                </label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" name="add_service" class="btn btn-primary"><i class="fas fa-plus"></i> Add Service</button>
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