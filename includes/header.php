<?php
// Send Security Headers
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("X-XSS-Protection: 1; mode=block");
header("Referrer-Policy: strict-origin-when-cross-origin");
header("Permissions-Policy: geolocation=(), camera=(), microphone=()");
header("Content-Security-Policy: default-src 'self' https:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; img-src 'self' data: https:; font-src 'self' data: https://cdnjs.cloudflare.com https://fonts.gstatic.com; connect-src 'self' https:; frame-src 'self' https:;");

// Include SEO functions
require_once __DIR__ . '/seo.php';

// Include visitor tracker (non-blocking — silently skips if table not ready)
if (!function_exists('mct_trackVisit')) {
    @include_once __DIR__ . '/tracker.php';
}
if (function_exists('mct_trackVisit')) {
    try { mct_trackVisit($pdo); } catch(Exception $e) {}
}

// Get page-specific SEO or use defaults
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
if ($currentPage === 'index') $currentPage = 'home';
$pageSeo = getPageSeo($currentPage);

// Allow individual pages to override SEO values
if (isset($page_title))       $pageSeo['page_title']  = $page_title  ?: $pageSeo['page_title'];
if (isset($page_description)) $pageSeo['description'] = $page_description ?: $pageSeo['description'];
if (isset($page_keywords))    $pageSeo['keywords']    = $page_keywords    ?: $pageSeo['keywords'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="theme-color" content="#003d99">

    <?php renderSeoMeta($pageSeo); ?>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>assets/images/logo3.png">
    <link rel="apple-touch-icon" href="<?php echo BASE_URL; ?>assets/images/logo3.png">

    <!-- Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://images.unsplash.com">
    <link rel="dns-prefetch" href="https://cdnjs.cloudflare.com">

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Core Styles -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <!-- Dribbble Theme Upgrade -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/dribbble-theme.css">
    
    <?php if (basename($_SERVER['PHP_SELF']) === 'admin/login.php' || (isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], '/admin/') === 0)): ?>
        <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/admin.css">
    <?php endif; ?>

    <!-- JSON-LD Structured Data (for Google Rich Results) -->
    <?php renderAllSchemas($pageSeo); ?>

    <script>
        // Check for saved theme or system preference
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            document.documentElement.setAttribute('data-theme', theme);
        })();

        document.addEventListener('DOMContentLoaded', () => {
            const themeToggle = document.getElementById('themeToggle');
            if (!themeToggle) return;

            const darkIcon = themeToggle.querySelector('.dark-icon');
            const lightIcon = themeToggle.querySelector('.light-icon');

            const updateIcons = (theme) => {
                if (theme === 'dark') {
                    darkIcon.style.display = 'none';
                    lightIcon.style.display = 'inline-block';
                } else {
                    darkIcon.style.display = 'inline-block';
                    lightIcon.style.display = 'none';
                }
            };

            // Sync icons on load
            updateIcons(document.documentElement.getAttribute('data-theme'));

            themeToggle.addEventListener('click', () => {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateIcons(newTheme);
            });
        });
    </script>
</head>
<body>
    <!-- Preloader -->
    <div class="preloader">
        <div class="loader">
            <div></div><div></div><div></div><div></div>
        </div>
    </div>

    <!-- Floating WhatsApp -->
    <a href="https://wa.me/256758611414?text=Hello%20Mctech-hub%20Systems%2C%20I%27m%20interested%20in%20your%20services" 
       class="whatsapp-float" target="_blank" title="WhatsApp">
        <i class="fab fa-whatsapp"></i>
        <span class="whatsapp-label">Chat Now</span>
    </a>

    <!-- Header Container -->
    <div class="header-container">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="container">
                <div class="top-contact">
                    <a href="tel:+256758611414"><i class="fas fa-phone-volume"></i> +256 758 611 414</a>
                    <a href="mailto:mctechhubsystems@gmail.com"><i class="fas fa-envelope-circle-check"></i> mctechhubsystems@gmail.com</a>
                </div>
                <div class="top-social">
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://wa.me/256758611414?text=Hello%20Mctech-hub%20Systems%2C%20I%27m%20interested%20in%20your%20services" aria-label="WhatsApp" target="_blank"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </div>

        <!-- Scroll Progress Bar -->
        <div id="scrollProgressBar" style="position: fixed; top: 0; left: 0; height: 4px; background: var(--accent); z-index: 10000; width: 0%; border-radius: 0 4px 4px 0;"></div>
        
        <!-- Navigation -->
    <header class="header">
        <nav class="navbar container">
            <!-- Logo -->
            <div class="nav-brand">
                <a href="<?php echo BASE_URL; ?>" class="logo">
                    <img src="<?php echo BASE_URL; ?>assets/images/logo3.png" alt="Mctech-hub Systems — Best Developer & Designer in Uganda" class="logo-img">
                </a>
            </div>
            
            <!-- Main Menu -->
            <ul class="nav-menu">
                <li><a href="<?php echo BASE_URL; ?>" class="nav-link">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>services.php" class="nav-link">Services</a></li>
                <li><a href="<?php echo BASE_URL; ?>portfolio.php" class="nav-link">Portfolio</a></li>
                <li><a href="<?php echo BASE_URL; ?>about.php" class="nav-link">About</a></li>
                <li><a href="<?php echo BASE_URL; ?>blog.php" class="nav-link">Blog</a></li>
                <li><a href="<?php echo BASE_URL; ?>contact.php" class="nav-link btn-primary">Contact</a></li>
            </ul>
            
            <!-- Mobile Toggle -->
            <div class="header-actions" style="display: flex; align-items: center; gap: 1rem;">
                <!-- Theme Toggle -->
                <button id="themeToggle" class="theme-toggle" aria-label="Toggle Theme" style="background: none; border: none; cursor: pointer; color: var(--text-primary); font-size: 1.2rem; transition: color var(--transition); display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%;">
                    <i class="fas fa-moon dark-icon"></i>
                    <i class="fas fa-sun light-icon" style="display: none;"></i>
                </button>

                <div class="hamburger" onclick="document.querySelector('.nav-menu').classList.toggle('active'); this.classList.toggle('active');">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
        </nav>
    </header>
    </div>

    <!-- Main Content Wrapper -->
    <main class="main-content">
