<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$page_title = 'Add New Blog Post';

if ($_POST && isset($_POST['add_post'])) {
    $title = trim($_POST['title']);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $title));
    $excerpt = trim($_POST['excerpt']);
    $content = $_POST['content'];
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    $image_path = '';
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
        $upload_dir = '../assets/images/blog/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $filename = basename($_FILES['featured_image']['name']);
        $target_file = $upload_dir . $filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Security: Unified multi-layer image validation
        if (secureValidateImage($_FILES['featured_image'])) {
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target_file)) {
                $image_path = 'blog/' . $filename;
            }
        } else {
            // Block invalid/malicious files immediately
            header('HTTP/1.1 403 Forbidden');
            die("Security Error: Invalid image file detected.");
        }
    }

    $stmt = $pdo->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, featured_image, is_published, published_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $published_at = $is_published ? date('Y-m-d H:i:s') : null;
    $stmt->execute([$title, $slug, $excerpt, $content, $image_path, $is_published, $published_at]);
    header('Location: blog.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - Mctech-hub Systems</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-dashboard">

<div class="admin-container">
    <aside class="admin-sidebar">
        <div class="sidebar-header">
            <h2><i class="fas fa-cogs"></i> Admin</h2>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></p>
        </div>
        <nav class="admin-nav">
            <a href="index.php" class="nav-item"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
            <a href="services.php" class="nav-item"><i class="fas fa-layer-group"></i> Services</a>
            <a href="projects.php" class="nav-item"><i class="fas fa-project-diagram"></i> Projects</a>
            <a href="testimonials.php" class="nav-item"><i class="fas fa-star"></i> Testimonials</a>
            <a href="blog.php" class="nav-item"><i class="fas fa-blog"></i> Blog</a>
            <a href="leads.php" class="nav-item"><i class="fas fa-envelope"></i> Leads</a>
            <a href="login.php?logout=1" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1>Add New Blog Post</h1>
            <a href="blog.php" class="btn btn-secondary">← Back to Blog</a>
        </header>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Post Title</label>
                    <input type="text" name="title" placeholder="Blog Post Title" required>
                </div>
                <div class="form-group">
                    <label>Excerpt</label>
                    <textarea name="excerpt" placeholder="Short excerpt (150 characters max)" rows="3" maxlength="150"></textarea>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea name="content" placeholder="Full blog post content" rows="15" required></textarea>
                </div>
                <div class="form-group">
                    <label>Featured Image</label>
                    <input type="file" name="featured_image" accept="image/*" onchange="previewImage(event)">
                    <small style="color: #666;">Upload a featured image for the blog post (optional)</small>
                    <div id="image-preview" style="margin-top: 10px; display: none;">
                        <img id="preview-img" src="" alt="Image Preview" style="max-width: 300px; max-height: 200px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="is_published" checked> Publish immediately</label>
                </div>
                <button type="submit" name="add_post" class="btn btn-primary">Publish Post</button>
                <a href="blog.php" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </main>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('image-preview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
}
</script>
<script src="../assets/js/main.js"></script>
</body>
</html>