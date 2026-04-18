<?php
include '../includes/config.php';
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$page_title = 'Delete Testimonial';

$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM testimonials WHERE id = ?");
$stmt->execute([$id]);
$testimonial = $stmt->fetch();

if (!$testimonial) {
    header('Location: testimonials.php');
    exit;
}

if ($_POST && isset($_POST['confirm_delete'])) {
    $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->execute([$id]);
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
            <a href="testimonials.php" class="nav-item active"><i class="fas fa-star"></i> Testimonials</a>
            <a href="blog.php" class="nav-item"><i class="fas fa-blog"></i> Blog</a>
            <a href="leads.php" class="nav-item"><i class="fas fa-envelope"></i> Leads</a>
            <a href="login.php?logout=1" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </nav>
    </aside>

    <main class="admin-main">
        <header class="admin-header">
            <h1>Delete Testimonial</h1>
            <a href="testimonials.php" class="btn btn-secondary">← Back to Testimonials</a>
        </header>

        <div class="card">
            <div class="delete-confirmation">
                <h3>Are you sure you want to delete this testimonial?</h3>
                <div class="testimonial-details">
                    <div class="testimonial-info">
                        <h4><?php echo htmlspecialchars($testimonial['client_name']); ?></h4>
                        <p><strong>Company:</strong> <?php echo htmlspecialchars($testimonial['company']); ?></p>
                        <p><strong>Rating:</strong> <?php echo str_repeat('★', $testimonial['rating']); ?></p>
                        <p><strong>Message:</strong> <?php echo htmlspecialchars(substr($testimonial['message'], 0, 100)) . (strlen($testimonial['message']) > 100 ? '...' : ''); ?></p>
                        <p><strong>Active:</strong> <?php echo $testimonial['is_active'] ? 'Yes' : 'No'; ?></p>
                        <?php if ($testimonial['image']): ?>
                            <div class="testimonial-image">
                                <img src="../assets/images/<?php echo htmlspecialchars($testimonial['image']); ?>" alt="Client Image" style="max-width: 100px; max-height: 100px; border-radius: 50%;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="delete-actions">
                    <form method="POST" style="display: inline;">
                        <button type="submit" name="confirm_delete" class="btn btn-danger">Yes, Delete Testimonial</button>
                    </form>
                    <a href="testimonials.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="../assets/js/main.js"></script>
</body>
</html>