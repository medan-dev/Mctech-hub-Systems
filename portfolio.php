<?php 
include 'includes/config.php'; 
$page_title = 'Our Work — Best Website & App Projects Built in Uganda';
$page_description = 'Explore Mctech-hub Systems portfolio. See the best websites, web apps & AI solutions built by Uganda\'s top developer & designer team. E-commerce, corporate sites, SaaS platforms, mobile apps & more.';
$page_class = 'page-portfolio';

// Curated portfolio data with local images and details
$portfolioItems = [
    [
        'title' => 'School Management System',
        'client' => 'Kampala International School',
        'category' => 'Web Apps',
        'filter' => 'apps',
        'desc' => 'Full enrollment, grading, attendance, and parent portal for 1,400+ students.',
        'image' => 'assets/images/portfolio-school.png',
        'tags' => ['PHP', 'MySQL', 'Admin Panel'],
        'featured' => true,
    ],
    [
        'title' => 'African Fashion Marketplace',
        'client' => 'AfriBazaar Ltd',
        'category' => 'E-Commerce',
        'filter' => 'websites',
        'desc' => 'Mobile-first shopping platform with M-Pesa & Airtel Money integration.',
        'image' => 'assets/images/portfolio-ecommerce.png',
        'tags' => ['React', 'Node.js', 'Mobile Pay'],
        'featured' => false,
    ],
    [
        'title' => 'Medical Records Platform',
        'client' => 'Nairobi Health Centre',
        'category' => 'Healthcare',
        'filter' => 'apps',
        'desc' => 'Patient management, appointment scheduling, and digital prescriptions.',
        'image' => 'assets/images/portfolio-healthcare.png',
        'tags' => ['Laravel', 'REST API', 'HIPAA'],
        'featured' => false,
    ],
    [
        'title' => 'Corporate Intelligence Dashboard',
        'client' => 'TechVentures Africa',
        'category' => 'Analytics',
        'filter' => 'ai',
        'desc' => 'Real-time KPI tracking, revenue analytics, and predictive forecasting.',
        'image' => 'assets/images/portfolio-dashboard.png',
        'tags' => ['Python', 'Chart.js', 'AI/ML'],
        'featured' => true,
    ],
    [
        'title' => 'E-Learning Platform',
        'client' => 'EduPrime Uganda',
        'category' => 'Web Apps',
        'filter' => 'apps',
        'desc' => 'Online course platform with video lessons, quizzes, and certifications.',
        'image' => 'assets/images/portfolio-lms.png',
        'tags' => ['Vue.js', 'Firebase', 'Video API'],
        'featured' => false,
    ],
    [
        'title' => 'Restaurant Booking & POS',
        'client' => 'ChefPoint Group',
        'category' => 'Hospitality',
        'filter' => 'websites',
        'desc' => 'Table reservation, order management, and kitchen display system.',
        'image' => 'assets/images/portfolio-restaurant.png',
        'tags' => ['PHP', 'POS', 'Real-time'],
        'featured' => false,
    ],
    [
        'title' => 'AI Customer Support Bot',
        'client' => 'ServiCall Uganda',
        'category' => 'AI Solutions',
        'filter' => 'ai',
        'desc' => 'NLP-powered chatbot handling 80% of support queries automatically.',
        'image' => 'assets/images/portfolio-chatbot.png',
        'tags' => ['OpenAI', 'NLP', 'Python'],
        'featured' => false,
    ],
    [
        'title' => 'Enterprise CRM Dashboard',
        'client' => 'GrowthAxis Partners',
        'category' => 'Web Apps',
        'filter' => 'apps',
        'desc' => 'Sales pipeline, contact management, and revenue forecasting tool.',
        'image' => 'assets/images/portfolio-crm.png',
        'tags' => ['React', 'PostgreSQL', 'API'],
        'featured' => true,
    ],
];
?>
<?php include 'includes/header.php'; ?>

<!-- Cinematic Portfolio Hero -->
<section class="cinematic-who" style="padding-top: 130px; padding-bottom: 40px;">
    <div class="container cinematic-container">
        <div class="cinematic-visual-frame">
            <img loading="lazy" src="assets/images/portfolio-hero.png" alt="Mctech-hub Systems Portfolio Showcase" class="cinematic-img">
            
            <!-- Cutout Top Right: Scroll Down -->
            <div class="cutout-top-right glass-shadow">
                <a href="#portfolio-grid" class="social-btn"><i class="fas fa-arrow-down"></i></a>
            </div>
            
            <!-- Cutout Bottom Left: Hero Content -->
            <div class="cutout-bottom-left glass-shadow cinematic-hero-content">
                <p class="hero-badge"><i class="fas fa-folder-open" style="color: #FFB020; margin-right: 5px;"></i> Our Portfolio</p>
                <h1 class="cinematic-title">Projects That<br>Drive Growth</h1>
                <p class="cinematic-desc">From concept to launch — we build digital products that solve real problems for African businesses. Every project is crafted with precision and purpose.</p>
            </div>
            
            <!-- Cutout Bottom Right: Quick Stats -->
            <div class="cutout-bottom-right glass-shadow">
                <div class="success-rate">
                    <span class="percentage">50+</span>
                    <span class="text-lines">Projects<br>Delivered</span>
                </div>
                <p class="rating"><span class="stars">★★★★★</span> 100% Satisfaction</p>
            </div>
        </div>
    </div>
</section>

<!-- Filter + Grid Section -->
<section id="portfolio-grid" style="background: var(--white); padding: 80px 0 120px;">
    <div class="container">
        
        <!-- Filter Pills -->
        <div class="filter-pills-container" style="display: flex; flex-wrap: wrap; gap: 0.6rem; margin-bottom: 3rem; justify-content: center;">
            <button class="pf-filter active" data-filter="all">All Projects</button>
            <button class="pf-filter" data-filter="websites">Websites</button>
            <button class="pf-filter" data-filter="apps">Web Apps</button>
            <button class="pf-filter" data-filter="ai">AI Solutions</button>
        </div>
        
        <!-- Bento Grid -->
        <div class="portfolio-bento" id="pf-grid">
            <?php foreach($portfolioItems as $i => $p): ?>
            <div class="pf-card <?php echo $p['featured'] ? 'pf-featured' : ''; ?>" data-cat="<?php echo $p['filter']; ?>" style="animation-delay: <?php echo $i * 0.08; ?>s;">
                <img loading="lazy" src="<?php echo $p['image']; ?>" alt="<?php echo htmlspecialchars($p['title']); ?> by Mctech-hub Systems">
                <div class="pf-overlay">
                    <div class="pf-overlay-content">
                        <span class="pf-category"><?php echo htmlspecialchars($p['category']); ?></span>
                        <h3><?php echo htmlspecialchars($p['title']); ?></h3>
                        <p><?php echo htmlspecialchars($p['desc']); ?></p>
                        <div class="pf-tags">
                            <?php foreach($p['tags'] as $tag): ?>
                            <span><?php echo $tag; ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <a href="#" class="pf-view-btn"><i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
    </div>
</section>

<!-- CTA Banner -->
<section class="portfolio-cta-section">
    <div class="portfolio-cta-grid"></div>
    <div class="container" style="position: relative; z-index: 2; text-align: center;">
        <h2 class="cta-title-light">
            Have a Project in Mind?
        </h2>
        <p class="cta-desc-muted">
            Let's discuss how we can build something exceptional for your business. From idea to launch, we've got you covered.
        </p>
        <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
            <a href="contact.php" class="btn btn-primary" style="padding: 0.9rem 2.5rem !important; border-radius: 50px !important; font-size: 1rem;">
                Start a Project <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </a>
            <a href="services.php" class="btn btn-outline-white">
                View Services
            </a>
        </div>
    </div>
</section>

<!-- Portfolio Filter JS -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const filters = document.querySelectorAll('.pf-filter');
    const cards = document.querySelectorAll('#pf-grid .pf-card');
    
    filters.forEach(btn => {
        btn.addEventListener('click', () => {
            // Update active state
            filters.forEach(f => {
                f.classList.remove('active');
            });
            btn.classList.add('active');
            
            const filter = btn.dataset.filter;
            
            cards.forEach((card, i) => {
                if (filter === 'all' || card.dataset.cat === filter) {
                    card.style.display = '';
                    card.style.animation = `fadeInScale 0.5s ${i * 0.06}s ease forwards`;
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

<style>
@keyframes fadeInScale {
    from { opacity: 0; transform: scale(0.95) translateY(10px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
</style>

<?php include 'includes/footer.php'; ?>
