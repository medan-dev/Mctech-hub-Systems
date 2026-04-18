<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$page_title = 'Add New Testimonial';

if ($_POST && isset($_POST['add_testimonial'])) {
    $client_name = trim($_POST['client_name']);
    $company = trim($_POST['company']);
    $message = trim($_POST['message']);
    $rating = (int)$_POST['rating'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/images/testimonials/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $filename = basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'testimonials/' . $filename;
            }
        }
    }

    $stmt = $pdo->prepare("INSERT INTO testimonials (client_name, company, message, rating, image, is_active) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$client_name, $company, $message, $rating, $image_path, $is_active]);
    header('Location: testimonials.php');
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
            <h1>Add New Testimonial</h1>
            <a href="testimonials.php" class="btn btn-secondary">← Back to Testimonials</a>
        </header>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Client Name</label>
                    <input type="text" name="client_name" placeholder="Client Name" required>
                </div>
                <div class="form-group">
                    <label>Company</label>
                    <input type="text" name="company" placeholder="Company" required>
                </div>
                <div class="form-group">
                    <label>Testimonial Message</label>
                    <textarea name="message" placeholder="Testimonial Message" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label>Rating</label>
                    <select name="rating" required>
                        <option value="">Select Rating</option>
                        <option value="5">★★★★★ (5 Stars)</option>
                        <option value="4">★★★★☆ (4 Stars)</option>
                        <option value="3">★★★☆☆ (3 Stars)</option>
                        <option value="2">★★☆☆☆ (2 Stars)</option>
                        <option value="1">★☆☆☆☆ (1 Star)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Client Image</label>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(event)">
                    <small style="color: #666;">Upload a client photo (optional)</small>
                    <div id="image-preview" style="margin-top: 10px; display: none;">
                        <img id="preview-img" src="" alt="Image Preview" style="max-width: 150px; max-height: 150px; border: 1px solid #ddd; border-radius: 50%;">
                    </div>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="is_active" checked> Active</label>
                </div>
                <button type="submit" name="add_testimonial" class="btn btn-primary">Add Testimonial</button>
                <a href="testimonials.php" class="btn btn-secondary">Cancel</a>
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