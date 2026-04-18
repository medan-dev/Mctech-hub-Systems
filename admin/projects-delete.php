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

if ($_POST && isset($_POST['confirm_delete'])) {
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: projects.php');
    exit;
}

$page_title = 'Delete Project: ' . htmlspecialchars($project['title']);
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
            <h1>Delete Project</h1>
            <a href="projects.php" class="btn btn-secondary">← Back to Projects</a>
        </header>

        <div class="form-container">
            <div style="background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 8px; padding: 2rem; margin-bottom: 2rem;">
                <h3 style="color: #856404; margin-top: 0;"><i class="fas fa-exclamation-triangle"></i> Warning: This action cannot be undone!</h3>
                <p style="color: #856404; margin-bottom: 0;">You are about to delete the project "<strong><?php echo htmlspecialchars($project['title']); ?></strong>".</p>
            </div>

            <div style="background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--border-radius-lg); padding: 2rem; margin-bottom: 2rem;">
                <h3>Project Details:</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 0.5rem 0; font-weight: 600; width: 150px;">Title:</td>
                        <td style="padding: 0.5rem 0;"><?php echo htmlspecialchars($project['title']); ?></td>
                    </tr>
                    <tr style="background: var(--gray-50);">
                        <td style="padding: 0.5rem 0; font-weight: 600;">Client Type:</td>
                        <td style="padding: 0.5rem 0;"><?php echo htmlspecialchars($project['client_type']); ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 0.5rem 0; font-weight: 600;">Service:</td>
                        <td style="padding: 0.5rem 0;"><?php echo htmlspecialchars($project['service_name'] ?? 'Unknown'); ?></td>
                    </tr>
                    <tr style="background: var(--gray-50);">
                        <td style="padding: 0.5rem 0; font-weight: 600;">Featured:</td>
                        <td style="padding: 0.5rem 0;"><?php echo $project['is_featured'] ? 'Yes' : 'No'; ?></td>
                    </tr>
                    <tr>
                        <td style="padding: 0.5rem 0; font-weight: 600;">Outcome:</td>
                        <td style="padding: 0.5rem 0;"><?php echo htmlspecialchars($project['outcome']); ?></td>
                    </tr>
                    <tr style="background: var(--gray-50);">
                        <td style="padding: 0.5rem 0; font-weight: 600;">Created:</td>
                        <td style="padding: 0.5rem 0;"><?php echo date('F j, Y \a\t g:i A', strtotime($project['created_at'])); ?></td>
                    </tr>
                </table>
                <?php if ($project['image']): ?>
                    <div style="margin-top: 1rem;">
                        <strong>Project Image:</strong><br>
                        <img src="../assets/images/<?php echo htmlspecialchars($project['image']); ?>" alt="Project Image" style="max-width: 200px; max-height: 150px; border: 1px solid #ddd; border-radius: 8px; margin-top: 0.5rem;">
                    </div>
                <?php endif; ?>
            </div>

            <form method="POST">
                <button type="submit" name="confirm_delete" class="btn btn-danger" style="margin-right: 1rem;">
                    <i class="fas fa-trash"></i> Yes, Delete Project
                </button>
                <a href="projects.php" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </form>
        </div>
    </main>
</div>

<script src="../assets/js/main.js"></script>
</body>
</html>