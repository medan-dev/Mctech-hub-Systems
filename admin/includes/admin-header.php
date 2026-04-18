<?php
if (!isset($_SESSION['admin_id'])) { header('Location: login.php'); exit; }
$current_page = basename($_SERVER['PHP_SELF']);

// Pre-fetch new leads count for sidebar badge + topbar dot
try {
    $newLeadsCount = (int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE status='new'")->fetchColumn();
} catch(Exception $e) { $newLeadsCount = 0; }

$adminInitial = strtoupper(substr($_SESSION['admin_username'] ?? 'A', 0, 1));
$adminName    = htmlspecialchars($_SESSION['admin_username'] ?? 'Admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? 'Admin'); ?> — Mctech-hub Systems</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="icon" href="../assets/images/logo.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js" defer></script>
</head>
<body>

<button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">
    <i class="fas fa-bars"></i>
</button>

<div class="admin-container">

    <!-- ═══════════ SIDEBAR ═══════════ -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-header">
            <img src="../assets/images/logo.png" alt="Mctech-hub" class="sidebar-logo" onerror="this.style.display='none'">
            <div class="sidebar-brand">
                <h2>Mctech-hub</h2>
                <p>Admin Portal</p>
            </div>
        </div>

        <span class="nav-label">Main Menu</span>
        <nav class="admin-nav">
            <a href="index.php" class="nav-item <?php echo $current_page==='index.php'?'active':''; ?>">
                <span class="ni"><i class="fas fa-th-large"></i></span>
                <span class="label">Dashboard</span>
            </a>
            <a href="leads.php" class="nav-item <?php echo $current_page==='leads.php'?'active':''; ?>">
                <span class="ni"><i class="fas fa-envelope-open-text"></i></span>
                <span class="label">Leads</span>
                <?php if ($newLeadsCount > 0): ?>
                <span class="nav-badge"><?php echo $newLeadsCount; ?></span>
                <?php endif; ?>
            </a>

            <span class="nav-label" style="margin-top:.25rem;">Content</span>
            <a href="services.php" class="nav-item <?php echo in_array($current_page,['services.php','services-add.php','services-edit.php','services-delete.php'])?'active':''; ?>">
                <span class="ni"><i class="fas fa-layer-group"></i></span>
                <span class="label">Services</span>
            </a>
            <a href="projects.php" class="nav-item <?php echo in_array($current_page,['projects.php','add-project.php','projects-edit.php','projects-delete.php'])?'active':''; ?>">
                <span class="ni"><i class="fas fa-briefcase"></i></span>
                <span class="label">Projects</span>
            </a>
            <a href="testimonials.php" class="nav-item <?php echo in_array($current_page,['testimonials.php','add-testimonial.php','testimonials-edit.php','testimonials-delete.php'])?'active':''; ?>">
                <span class="ni"><i class="fas fa-star"></i></span>
                <span class="label">Testimonials</span>
            </a>
            <a href="blog.php" class="nav-item <?php echo in_array($current_page,['blog.php','add-blog.php','blog-edit.php','blog-delete.php'])?'active':''; ?>">
                <span class="ni"><i class="fas fa-pen-nib"></i></span>
                <span class="label">Blog Posts</span>
            </a>

            <span class="nav-label" style="margin-top:.25rem;">Traffic & Growth</span>
            <a href="analytics.php" class="nav-item <?php echo $current_page==='analytics.php'?'active':''; ?>">
                <span class="ni"><i class="fas fa-chart-line"></i></span>
                <span class="label">Analytics</span>
            </a>
            <a href="subscribers.php" class="nav-item <?php echo in_array($current_page,['subscribers.php']) ? 'active' : ''; ?>">
                <span class="ni"><i class="fas fa-users"></i></span>
                <span class="label">Subscribers</span>
                <?php
                try {
                    $subCount = (int)$pdo->query("SELECT COUNT(*) FROM email_subscribers WHERE status='active'")->fetchColumn();
                    if ($subCount > 0) echo '<span class="nav-badge" style="background:rgba(16,185,129,.3);">' . $subCount . '</span>';
                } catch(Exception $e) {}
                ?>
            </a>

            <span class="nav-label" style="margin-top:.25rem;">Email</span>
            <a href="mail-settings.php" class="nav-item <?php echo in_array($current_page,['mail-settings.php']) ? 'active' : ''; ?>">
                <span class="ni"><i class="fas fa-cog"></i></span>
                <span class="label">Mail Settings</span>
            </a>
            <a href="email-logs.php" class="nav-item <?php echo in_array($current_page,['email-logs.php']) ? 'active' : ''; ?>">
                <span class="ni"><i class="fas fa-history"></i></span>
                <span class="label">Email Logs</span>
                <?php
                try {
                    $failedCount = (int)$pdo->query("SELECT COUNT(*) FROM email_logs WHERE status='failed'")->fetchColumn();
                    if ($failedCount > 0) echo '<span class="nav-badge" style="background:rgba(230,57,70,.3);">' . $failedCount . '</span>';
                } catch(Exception $e) {}
                ?>
            </a>

            <div class="nav-divider"></div>
            <a href="login.php?logout=1" class="nav-item nav-logout">
                <span class="ni"><i class="fas fa-sign-out-alt"></i></span>
                <span class="label">Logout</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <div class="admin-user">
                <div class="admin-avatar"><?php echo $adminInitial; ?></div>
                <div>
                    <strong><?php echo $adminName; ?></strong>
                    <small>Administrator</small>
                </div>
            </div>
        </div>
    </aside>

    <!-- ═══════════ MAIN ═══════════ -->
    <main class="admin-main">

        <!-- Top Bar -->
        <header class="admin-topbar">
            <div class="topbar-left">
                <h1><?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?></h1>
                <div class="topbar-breadcrumb">
                    <a href="index.php">Home</a>
                    <?php if ($current_page !== 'index.php'): ?>
                    <i class="fas fa-chevron-right"></i>
                    <span><?php echo htmlspecialchars($page_title ?? ''); ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="topbar-right">
                <!-- Search -->
                <div class="topbar-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="adminSearch" placeholder="Search anything…" autocomplete="off">
                </div>
                <!-- View site -->
                <a href="../" target="_blank" class="tb-btn" title="View Website">
                    <i class="fas fa-external-link-alt"></i>
                </a>
                <!-- Notification -->
                <a href="leads.php" class="tb-btn" title="New Leads" style="<?php echo $newLeadsCount>0?'color:var(--accent); border-color:var(--accent); background:var(--accent-soft);':''; ?>">
                    <i class="fas fa-bell"></i>
                    <?php if ($newLeadsCount > 0): ?><span class="tb-notif-dot"></span><?php endif; ?>
                </a>
                <!-- User pill -->
                <div class="tb-user">
                    <div class="tb-user-av"><?php echo $adminInitial; ?></div>
                    <span class="tb-user-name"><?php echo $adminName; ?></span>
                    <i class="fas fa-chevron-down" style="font-size:.55rem; color:var(--text-3); margin-left:2px;"></i>
                </div>
            </div>
        </header>

        <!-- Page Content Opens Here -->
        <div class="admin-content">
