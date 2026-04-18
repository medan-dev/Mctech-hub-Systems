<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$page_title = 'Add New Project';

$services = getServices($pdo);

if ($_POST && isset($_POST['add_project'])) {
    $title = trim($_POST['title']);
    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $title));
    $short_desc = trim($_POST['short_desc']);
    $full_desc = trim($_POST['full_desc']);
    $client_type = trim($_POST['client_type']);
    $outcome = trim($_POST['outcome']);
    $service_id = (int)$_POST['service_id'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../assets/images/projects/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $filename = basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES['image']['tmp_name']);
        if ($check !== false) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'projects/' . $filename;
            }
        }
    }

    $stmt = $pdo->prepare("INSERT INTO projects (title, slug, short_desc, full_desc, client_type, outcome, image, service_id, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $slug, $short_desc, $full_desc, $client_type, $outcome, $image_path, $service_id, $is_featured]);
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
            <a href="projects.php" class="nav-item"><i class="fas fa-project-diagram"></i> Projects</a>
            <a href="testimonials.php" class="nav-item"><i class="fas fa-star"></i> Testimonials</a>
            <a href="blog.php" class="nav-item"><i class="fas fa-blog"></i> Blog</a>
            <a href="leads.php" class="nav-item"><i class="fas fa-envelope"></i> Leads</a>
            <a href="login.php?logout=1" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1>Add New Project</h1>
            <a href="projects.php" class="btn btn-secondary">← Back to Projects</a>
        </header>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Project Title</label>
                    <input type="text" name="title" placeholder="Project Title" required>
                </div>
                <div class="form-group">
                    <label>Client Type</label>
                    <input type="text" name="client_type" placeholder="Client Type (e.g. Education)" required>
                </div>
                <div class="form-group">
                    <label>Select Service</label>
                    <select name="service_id" required>
                        <option value="">Select Service</option>
                        <?php foreach($services as $service): ?>
                        <option value="<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Short Description</label>
                    <textarea name="short_desc" placeholder="Short Description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Full Description</label>
                    <textarea name="full_desc" placeholder="Full Description" rows="5"></textarea>
                </div>
                <div class="form-group">
                    <label>Outcome</label>
                    <input type="text" name="outcome" placeholder="Outcome (e.g. 300% lead growth)" required>
                </div>
                <div class="form-group">
                    <label>Project Image</label>
                    <input type="file" name="image" accept="image/*" required onchange="previewImage(event)">
                    <small style="color: #666;">Upload a project image (JPG, PNG, GIF)</small>
                    <div id="image-preview" style="margin-top: 10px; display: none;">
                        <img id="preview-img" src="" alt="Image Preview" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
                <div class="form-group">
                    <label><input type="checkbox" name="is_featured"> Featured Project</label>
                </div>
                <button type="submit" name="add_project" class="btn btn-primary">Add Project</button>
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