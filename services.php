<?php 
include 'includes/config.php'; 
$page_title = 'Best Web Development & App Development Services in Uganda';
$page_description = 'Affordable, professional website development, app development, UI/UX design, SEO, branding & AI integration services by the best developer & designer in Uganda. Packages starting from UGX 200,000.';
$page_class = 'page-services';
?>
<?php include 'includes/header.php'; ?>

<!-- Cinematic Services Hero -->
<section class="cinematic-who" style="padding-top: 130px; padding-bottom: 40px;">
    <div class="container cinematic-container">
        <div class="cinematic-visual-frame">
            <img loading="lazy" src="assets/images/hero-services.png" alt="Premium Tech Services by Mctech-hub Systems" class="cinematic-img">
            
            <!-- Cutout Top Right: Navigation Helper -->
            <div class="cutout-top-right glass-shadow">
                <a href="#services-grid" class="social-btn"><i class="fas fa-arrow-down"></i></a>
            </div>
            
            <!-- Cutout Bottom Left: Hero Title & Desc -->
            <div class="cutout-bottom-left glass-shadow cinematic-hero-content">
                <p class="hero-badge"><i class="fas fa-briefcase" style="color: #FFB020; margin-right: 5px;"></i> What We Do</p>
                <h1 class="cinematic-title">Premium Services,<br>Unbeatable Prices</h1>
                <p class="cinematic-desc">Scale your business with our high-impact, budget-friendly tech packages designed for the African market.</p>
            </div>
        </div>
    </div>
</section>
<div id="services-grid"></div>

<!-- Services Grid -->
<section class="services-section bg-light">
    <div class="container">
        <div class="section-header fade-in-up">
            <h2>Our Packages</h2>
            <p>Choose the perfect plan to get your business online and growing today.</p>
        </div>
        
        <div class="services-grid">
            <!-- 1. Starter Package -->
            <div class="service-item">
                <div class="discount-badge">Hot Deal</div>
                <div class="service-icon">
                    <i class="fas fa-rocket" style="font-size: 2.5rem; color: white;"></i>
                </div>
                <h3>Starter Web Package</h3>
                <p>Perfect for small businesses. 5-page responsive website, contact form, social media integration, and 1 month free support.</p>
                <div class="price-wrapper">
                    <span class="old-price">UGX 500,000</span>
                    <span class="price">UGX 300,000</span>
                </div>
                <a href="contact.php?service=starter" class="btn btn-secondary full-width service-btn-fix service-inquiry-btn" data-service="Starter Web Package" data-price="UGX 300,000">Get Started</a>
            </div>

            <!-- 2. Corporate Branding -->
            <div class="service-item">
                <div class="service-icon">
                    <i class="fas fa-briefcase" style="font-size: 2.5rem; color: white;"></i>
                </div>
                <h3>Corporate Branding</h3>
                <p>Complete UI/UX design, logo creation, business cards, and brand identity guidelines to make you stand out.</p>
                <div class="price-wrapper">
                    <span class="price">UGX 500,000</span>
                </div>
                <a href="contact.php?service=branding" class="btn btn-secondary full-width service-btn-fix service-inquiry-btn" data-service="Corporate Branding" data-price="UGX 500,000">Order Now</a>
            </div>

            <!-- 3. E-Commerce Pro -->
            <div class="service-item">
                <div class="discount-badge">-30% OFF</div>
                <div class="service-icon">
                    <i class="fas fa-store" style="font-size: 2.5rem; color: white;"></i>
                </div>
                <h3>E-Commerce Pro</h3>
                <p>Full online store with Mobile Money & Visa integration, inventory management, admin panel, and sales analytics.</p>
                <div class="price-wrapper">
                    <span class="old-price">UGX 1,800,000</span>
                    <span class="price">UGX 1,200,000</span>
                </div>
                <a href="contact.php?service=ecommerce" class="btn btn-secondary full-width service-btn-fix service-inquiry-btn" data-service="E-Commerce Pro" data-price="UGX 1,200,000">Start Selling</a>
            </div>

            <!-- 4. SEO & Growth -->
            <div class="service-item">
                <div class="service-icon">
                    <i class="fas fa-chart-line" style="font-size: 2.5rem; color: white;"></i>
                </div>
                <h3>SEO & Growth</h3>
                <p>Rank #1 on Google. Includes keyword research, on-page optimization, Google My Business setup, and monthly reports.</p>
                <div class="price-wrapper">
                    <span class="price">UGX 350,000</span>
                </div>
                <a href="contact.php?service=seo" class="btn btn-secondary full-width service-btn-fix service-inquiry-btn" data-service="SEO & Growth" data-price="UGX 350,000">Boost Traffic</a>
            </div>

            <!-- 5. Mobile App Lite -->
            <div class="service-item">
                <div class="service-icon">
                    <i class="fas fa-mobile-alt" style="font-size: 2.5rem; color: white;"></i>
                </div>
                <h3>Mobile App Lite</h3>
                <p>Cross-platform mobile application (Android & iOS) with essential features, modern UI, and offline capabilities.</p>
                <div class="price-wrapper">
                    <span class="price">UGX 2,500,000</span>
                </div>
                <a href="contact.php?service=app" class="btn btn-secondary full-width service-btn-fix service-inquiry-btn" data-service="Mobile App Lite" data-price="UGX 2,500,000">Build App</a>
            </div>

            <!-- 6. Cloud & Hosting -->
            <div class="service-item">
                <div class="discount-badge">Yearly</div>
                <div class="service-icon">
                    <i class="fas fa-server" style="font-size: 2.5rem; color: white;"></i>
                </div>
                <h3>Cloud & Hosting</h3>
                <p>Secure domain registration (.co.ug / .com), fast SSD hosting, free SSL certificate, and 24/7 technical support.</p>
                <div class="price-wrapper">
                    <span class="price">UGX 200,000<small class="price-unit">/yr</small></span>
                </div>
                <a href="contact.php?service=hosting" class="btn btn-secondary full-width service-btn-fix service-inquiry-btn" data-service="Cloud & Hosting" data-price="UGX 200,000/yr">Subscribe</a>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section services-cta">
    <div class="container text-center">
        <h2 class="section-title">Not sure what you need?</h2>
        <p class="section-desc centered-desc">Let's have a quick chat about your business goals. We'll recommend the best package for you.</p>
        <a href="contact.php" class="btn btn-primary">Get a Free Consultation</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
