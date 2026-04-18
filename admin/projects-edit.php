<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
$stmt->execute([$id]);
$project = $stmt->fetch();

if (!$project) {
    header('Location: projects.php');
    exit;
}

$page_title = 'Edit Project: ' . htmlspecialchars($project['title']);
$services = getServices($pdo);

if ($_POST && isset($_POST['update_project'])) {
    $title = trim($_POST['title']);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $title));
    $short_desc = trim($_POST['short_desc']);
    $full_desc = trim($_POST['full_desc']);
    $client_type = trim($_POST['client_type']);
    $outcome = trim($_POST['outcome']);
    $service_id = (int)$_POST['service_id'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    $image_path = $project['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/images/projects/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $filename = basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'projects/' . $filename;
            }
        }
    }

    $stmt = $pdo->prepare("UPDATE projects SET title = ?, slug = ?, short_desc = ?, full_desc = ?, client_type = ?, outcome = ?, image = ?, service_id = ?, is_featured = ? WHERE id = ?");
    $stmt->execute([$title, $slug, $short_desc, $full_desc, $client_type, $outcome, $image_path, $service_id, $is_featured, $id]);
    header('Location: projects.php');
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
            <a href="projects.php" class="nav-item active"><i class="fas fa-project-diagram"></i> Projects</a>
            <a href="testimonials.php" class="nav-item"><i class="fas fa-star"></i> Testimonials</a>
            <a href="blog.php" class="nav-item"><i class="fas fa-blog"></i> Blog</a>
            <a href="leads.php" class="nav-item"><i class="fas fa-envelope"></i> Leads</a>
            <a href="login.php?logout=1" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1>Edit Project</h1>
            <a href="projects.php" class="btn btn-secondary">← Back to Projects</a>
        </header>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Project Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Client Type</label>
                    <select name="client_type" required>
                        <option value="Business" <?php echo $project['client_type'] === 'Business' ? 'selected' : ''; ?>>Business</option>
                        <option value="Education" <?php echo $project['client_type'] === 'Education' ? 'selected' : ''; ?>>Education</option>
                        <option value="Healthcare" <?php echo $project['client_type'] === 'Healthcare' ? 'selected' : ''; ?>>Healthcare</option>
                        <option value="Agriculture" <?php echo $project['client_type'] === 'Agriculture' ? 'selected' : ''; ?>>Agriculture</option>
                        <option value="NGO" <?php echo $project['client_type'] === 'NGO' ? 'selected' : ''; ?>>NGO</option>
                        <option value="Other" <?php echo $project['client_type'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Service</label>
                    <select name="service_id" required>
                        <option value="">Select Service</option>
                        <?php foreach($services as $service): ?>
                            <option value="<?php echo $service['id']; ?>" <?php echo $project['service_id'] == $service['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($service['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Short Description</label>
                    <textarea name="short_desc" rows="3" required><?php echo htmlspecialchars($project['short_desc']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Full Description</label>
                    <textarea name="full_desc" rows="6" required><?php echo htmlspecialchars($project['full_desc']); ?></textarea>
                </div>
                <div class="form-group">
                    <label>Outcome/Result</label>
                    <input type="text" name="outcome" value="<?php echo htmlspecialchars($project['outcome']); ?>" placeholder="e.g., 300% increase in leads" required>
                </div>
                <div class="form-group">
                    <label>Project Image</label>
                    <input type="file" name="image" accept="image/*" onchange="previewImage(event)">
                    <small style="color: #666;">Upload a new image to replace the current one (optional)</small>
                    <?php if ($project['image']): ?>
                        <div id="current-image" style="margin-top: 10px;">
                            <p><strong>Current Image:</strong></p>
                            <img src="../assets/images/<?php echo htmlspecialchars($project['image']); ?>" alt="Current Image" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; border-radius: 8px;">
                        </div>
                    <?php endif; ?>
                    <div id="image-preview" style="margin-top: 10px; display: none;">
                        <p><strong>New Image Preview:</strong></p>
                        <img id="preview-img" src="" alt="Image Preview" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; border-radius: 8px;">
                    </div>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="is_featured" <?php echo $project['is_featured'] ? 'checked' : ''; ?>> Featured Project</label>
                </div>
                <button type="submit" name="update_project" class="btn btn-primary">Update Project</button>
                <a href="projects.php" class="btn btn-secondary">Cancel</a>
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