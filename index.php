<?php
include 'includes/config.php';
$page_title = 'Best Website Developer, App Developer & Designer in Uganda';
$page_description = 'Mctech-hub Systems is Uganda\'s best website developer, app developer & designer. We build stunning websites, mobile apps & AI solutions for businesses. Starting from UGX 300,000. Trusted by 35+ clients in Kampala & Africa.';
$page_class = 'page-home';
$services = getServices($pdo, true);
$projects = getProjects($pdo, 6);
$testimonials = getTestimonials($pdo);
$stats = ['websites' => 20, 'logos' => 7, 'satisfaction' => 100, 'experience' => 3];
?>
<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content" style="align-items: center;">
            <div class="hero-text">
                <p class="hero-badge"><i class="fas fa-rocket" style="color: #FFB020; margin-right: 5px;"></i> Africa's Digital Partner</p>
                <h1 class="animated-headline" style="font-size: clamp(2.8rem, 5vw, 4rem) !important; margin-bottom: 1rem;">
                    We Build Strategic Tech for Africa
                </h1>
                <p class="hero-description">We understand the African market. We built Mctech-hub Systems to help local businesses compete globally with technology that actually works. From startups to established companies, we're your partner in digital transformation.</p>
                <div class="hero-buttons" style="display: flex; gap: 1rem;">
                    <a href="contact.php" class="btn btn-primary">Let's Start</a>
                    <a href="portfolio.php" class="btn btn-secondary" style="border-width: 2px !important;">See Our Work</a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-mesh-bg"></div>
                <div class="hero-image-container">
                    <img src="assets/images/hero-strategic-tech.png" alt="Strategic Tech Solutions for Africa" class="hero-main-img" style="border-radius: 20px;">
                    
                    <!-- Decorative floating elements -->
                    <div class="floating-orb orb-1"></div>
                    <div class="floating-orb orb-2"></div>
                    
                    <div class="hero-float-card card-1 glass-shadow">
                        <div class="card-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="card-info">
                            <span class="card-title">Growth</span>
                            <span class="card-val">+140%</span>
                        </div>
                    </div>
                    
                    <div class="hero-float-card card-2 glass-shadow">
                        <div class="card-icon"><i class="fas fa-shield-alt"></i></div>
                        <div class="card-info">
                            <span class="card-title">Security</span>
                            <span class="card-val">Verified</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section (Dribbble Layout) -->
<section class="services-section">
    <div class="container services-split-layout">
        <div class="services-info">
            <h2 class="section-title">Professional digital solutions for growth.</h2>
            <p class="section-desc">We tailor our expertise to solve your unique business challenges, providing high-quality development and design services tailored for your success in the modern digital age.</p>
            
            <ul style="list-style: none; padding: 0; margin-bottom: 2.5rem;">
                <li class="service-check">
                    <i class="fas fa-check-circle"></i> Custom-Built Solutions
                </li>
                <li class="service-check">
                    <i class="fas fa-check-circle"></i> Agile Development Process
                </li>
                <li class="service-check">
                    <i class="fas fa-check-circle"></i> 24/7 Technical Support
                </li>
            </ul>
            <a href="services.php" class="btn btn-primary" style="padding: 0.7rem 2rem !important; border-radius: 50px !important;">View All Services</a>
        </div>
        
        <div class="services-grid-wrapper">
            <div class="services-split-grid">
                <div class="service-item service-inquiry-btn" data-service="Website Development" data-price="Custom">
                    <div class="service-icon"><i class="fas fa-laptop-code"></i></div>
                    <h3>Website Development</h3>
                    <p>High-performance websites that convert visitors into customers.</p>
                </div>
                <div class="service-item service-inquiry-btn" data-service="App Development" data-price="Custom">
                    <div class="service-icon"><i class="fas fa-mobile-alt"></i></div>
                    <h3>App Development</h3>
                    <p>Native and cross-platform mobile apps for iOS and Android.</p>
                </div>
                <div class="service-item service-inquiry-btn" data-service="AI Integration" data-price="Custom">
                    <div class="service-icon"><i class="fas fa-brain"></i></div>
                    <h3>AI Integration</h3>
                    <p>Smart chatbots, automation tools, and data analytics.</p>
                </div>
                <div class="service-item service-inquiry-btn" data-service="UI/UX Design" data-price="Custom">
                    <div class="service-icon"><i class="fas fa-pencil-ruler"></i></div>
                    <h3>UI/UX Design</h3>
                    <p>Intuitive interfaces that provide exceptional brand experiences.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Who We Are Section (Professional Restyle) -->
<section class="who-we-are-section">
    <div class="container">
        <div class="who-layout" style="display: flex; flex-wrap: wrap; gap: 4rem; align-items: center;">
            
            <!-- Left: Visual -->
            <div class="who-visual" style="flex: 1; min-width: 340px; position: relative;">
                <div style="position: relative; border-radius: 24px; overflow: hidden; box-shadow: 0 30px 60px rgba(21,26,48,0.12);">
                    <img loading="lazy" src="assets/images/who-we-are.png" alt="Mctech-hub Systems Team in Kampala, Uganda" style="width: 100%; height: 480px; object-fit: cover; display: block;">
                </div>
                
                <!-- Floating Stats Card -->
                <div class="who-stats-card">
                    <div class="who-stats-flex">
                        <div class="who-stat">
                            <div class="who-stat-num">50+</div>
                            <div class="who-stat-label">Projects</div>
                        </div>
                        <div class="who-stat-divider"></div>
                        <div class="who-stat">
                            <div class="who-stat-num success">100%</div>
                            <div class="who-stat-label">Satisfaction</div>
                        </div>
                        <div class="who-stat-divider"></div>
                        <div class="who-stat">
                            <div class="who-stat-num info">3+</div>
                            <div class="who-stat-label">Years</div>
                        </div>
                    </div>
                </div>

                <!-- Accent corner decoration -->
                <div style="position: absolute; top: -12px; left: -12px; width: 80px; height: 80px; border-top: 4px solid var(--accent); border-left: 4px solid var(--accent); border-radius: 8px 0 0 0; z-index: 2;"></div>
            </div>
            
            <!-- Right: Content -->
            <div class="who-content">
                <p class="who-badge">
                    <span></span>
                    Who We Are
                </p>
                <h2>
                    From Kampala,<br>Building Africa's<br>Digital Future
                </h2>
                <p class="who-text">
                    Founded in <strong>2023</strong>, Mctech‑hub Systems began when three developers in Kampala noticed African businesses were underserved by global technology. Solutions were too expensive, too complex, or disconnected from local realities.
                </p>
                <p class="who-text">
                    We set out to change that — delivering <strong>world‑class websites, mobile apps, and AI solutions</strong> that are accessible, affordable, and tailored for the African market.
                </p>

                <!-- Mission Mini Card -->
                <div class="mission-mini-card">
                    <p class="mission-title">
                        <i class="fas fa-bullseye"></i>Our Mission
                    </p>
                    <p class="mission-desc">
                        To empower African businesses with scalable, secure, and innovative digital solutions — bridging the gap between local potential and global standards.
                    </p>
                </div>

                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <a href="about.php" class="btn btn-primary" style="padding: 0.8rem 2rem !important; border-radius: 50px !important;">Learn More About Us</a>
                    <a href="contact.php" class="btn btn-secondary" style="padding: 0.8rem 2rem !important; border-radius: 50px !important; border-width: 2px !important;">Get In Touch</a>
                </div>
            </div>
            
        </div>
    </div>
</section>
<!-- Portfolio Section (Premium Dribbble Showcase) -->
<section class="portfolio-section">
    <div class="container">
        
        <!-- Split Header -->
        <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; margin-bottom: 3.5rem; gap: 1.5rem;">
            <div>
                <p style="color: var(--accent); font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 0.8rem; display: flex; align-items: center; gap: 8px;">
                    <span style="width: 30px; height: 2px; background: var(--accent); display: inline-block;"></span>
                    Our Work
                </p>
                <h2 style="font-size: clamp(2rem, 4vw, 2.8rem); font-weight: 800; color: var(--text-dark); line-height: 1.15; margin: 0;">
                    Selected<br>Projects
                </h2>
            </div>
            <div style="max-width: 420px;">
                <p style="color: var(--text-light); font-size: 0.95rem; line-height: 1.7; margin: 0;">
                    From custom web applications to AI-powered platforms — here's a glimpse of how we've helped African businesses scale digitally.
                </p>
            </div>
        </div>

        <!-- Category Filter Pills -->
        <div style="display: flex; flex-wrap: wrap; gap: 0.6rem; margin-bottom: 2.5rem;">
            <span style="background: var(--primary); color: #fff; padding: 0.45rem 1.2rem; border-radius: 50px; font-size: 0.82rem; font-weight: 600; cursor: pointer;">All Projects</span>
            <span style="background: var(--light); color: var(--text-dark); padding: 0.45rem 1.2rem; border-radius: 50px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">Web Apps</span>
            <span style="background: var(--light); color: var(--text-dark); padding: 0.45rem 1.2rem; border-radius: 50px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">Mobile</span>
            <span style="background: var(--light); color: var(--text-dark); padding: 0.45rem 1.2rem; border-radius: 50px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">AI Solutions</span>
            <span style="background: var(--light); color: var(--text-dark); padding: 0.45rem 1.2rem; border-radius: 50px; font-size: 0.82rem; font-weight: 600; cursor: pointer; transition: all 0.3s;">UI/UX</span>
        </div>

        <!-- Bento Grid -->
        <div class="portfolio-bento">

            <!-- Card 1: Featured (spans 2 columns) -->
            <div class="pf-card pf-featured">
                <img loading="lazy" src="assets/images/portfolio-school.png" alt="School Management System by Mctech-hub Systems">
                <div class="pf-overlay">
                    <div class="pf-overlay-content">
                        <span class="pf-category">Web Application</span>
                        <h3>School Management System</h3>
                        <p>Complete student enrollment, attendance tracking, grade management, and parent portal for a leading institution in Kampala.</p>
                        <div class="pf-tags">
                            <span>PHP</span><span>MySQL</span><span>Dashboard</span>
                        </div>
                    </div>
                    <a href="portfolio.php" class="pf-view-btn"><i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="pf-card">
                <img loading="lazy" src="assets/images/portfolio-ecommerce.png" alt="E-Commerce Platform by Mctech-hub Systems">
                <div class="pf-overlay">
                    <div class="pf-overlay-content">
                        <span class="pf-category">E-Commerce</span>
                        <h3>African Fashion Marketplace</h3>
                        <p>Mobile-first shopping platform with M-Pesa & Airtel Money integration.</p>
                        <div class="pf-tags">
                            <span>React</span><span>Node.js</span><span>Mobile Pay</span>
                        </div>
                    </div>
                    <a href="portfolio.php" class="pf-view-btn"><i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="pf-card">
                <img loading="lazy" src="assets/images/portfolio-healthcare.png" alt="Healthcare Platform by Mctech-hub Systems">
                <div class="pf-overlay">
                    <div class="pf-overlay-content">
                        <span class="pf-category">Healthcare</span>
                        <h3>Medical Records Platform</h3>
                        <p>Patient management, appointment scheduling, and digital prescriptions.</p>
                        <div class="pf-tags">
                            <span>Laravel</span><span>REST API</span><span>HIPAA</span>
                        </div>
                    </div>
                    <a href="portfolio.php" class="pf-view-btn"><i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="pf-card pf-featured">
                <img loading="lazy" src="assets/images/portfolio-dashboard.png" alt="Corporate Analytics Dashboard by Mctech-hub Systems">
                <div class="pf-overlay">
                    <div class="pf-overlay-content">
                        <span class="pf-category">Analytics</span>
                        <h3>Corporate Intelligence Dashboard</h3>
                        <p>Real-time KPI tracking, revenue analytics, and data visualization for enterprise decision-making.</p>
                        <div class="pf-tags">
                            <span>Python</span><span>Chart.js</span><span>AI/ML</span>
                        </div>
                    </div>
                    <a href="portfolio.php" class="pf-view-btn"><i class="fas fa-arrow-right"></i></a>
                </div>
            </div>

        </div>

        <!-- CTA -->
        <div style="text-align: center; margin-top: 3.5rem;">
            <a href="portfolio.php" class="btn btn-primary" style="padding: 0.9rem 2.5rem !important; border-radius: 50px !important; font-size: 1rem;">
                View All Projects <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </a>
        </div>
        
    </div>
</section>

<!-- Featured Testimonial Section (Dribbble Layout) -->
<section class="testimonial-dribbble">
    <div class="container">
        <?php 
        $testimonialData = [
            ['name' => 'Sarah Johnson', 'company' => 'CEO, TechStart', 'message' => 'Mctech-hub transformed our online presence. Modern, fast website that increased inquiries significantly.', 'image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=600&h=700&q=80'],
            ['name' => 'David Okello', 'company' => 'Director, Green Valley', 'message' => 'The clinic system revolutionized operations. Efficiency improved by 40%. Excellent support.', 'image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=600&h=700&fit=crop&crop=face']
        ];
        $featured = $testimonialData[0]; 
        ?>
        <div class="testimonial-split" style="display: flex; flex-wrap: wrap; gap: 4rem; align-items: center;">
            
            <!-- Left Side: Visuals -->
            <div class="testimonial-visual" style="flex: 1; min-width: 320px; position: relative; padding: 20px 20px 0 20px;">
                <!-- Red Frame Border -->
                <div style="position: absolute; top: 0; left: 0; width: 50%; height: 50%; border-top: 8px solid var(--accent); border-left: 8px solid var(--accent); z-index: 0;"></div>
                
                <!-- Person Image -->
                <img loading="lazy" src="<?php echo $featured['image']; ?>" alt="Client" style="width: 85%; height: auto; max-height: 500px; object-fit: cover; display: block; position: relative; z-index: 1; box-shadow: 0 20px 50px rgba(0,0,0,0.1);">
                
                <!-- Overlapping Quote Card -->
                <div class="quote-card">
                    <p class="quote-text">
                        <!-- Small quote icon -->
                        <span class="quote-mark">"</span>
                        <?php echo htmlspecialchars($featured['message']); ?>
                    </p>
                    <div class="quote-author-meta">
                        <div>
                            <h4 class="author-name"><?php echo htmlspecialchars($featured['name']); ?></h4>
                            <span class="author-company"><?php echo htmlspecialchars($featured['company']); ?></span>
                        </div>
                        <div class="google-verify-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20"><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.12C7.97 6.38 9.82 5.38 12 5.38z"/><path fill="#4285F4" d="M23.49 12.28c0-.79-.07-1.54-.19-2.28H12v4.51h6.47c-.29 1.48-1.13 2.74-2.39 3.59l3.87 3c2.26-2.09 3.54-5.18 3.54-8.82z"/><path fill="#FBBC05" d="M5.26 14.29c-.25-.72-.38-1.5-.38-2.29s.14-1.57.38-2.29l-3.99-3.1C.46 8.23 0 10.06 0 12c0 1.94.46 3.77 1.27 5.39l3.99-3.1z"/><path fill="#34A853" d="M12 23c3.24 0 5.97-1.07 7.95-2.9l-3.87-3c-1.08.72-2.46 1.15-4.08 1.15-3.13 0-5.78-2.11-6.73-4.96l-3.99 3.1C3.25 19.62 7.31 23 12 23z"/><path fill="none" d="M0 0h24v24H0z"/></svg>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Side: Content -->
            <div class="testimonial-text">
                <h2>24/7 Customer Support</h2>
                <p>Our dedicated team is here to give you personalized support within the hour available 24/7. In accordance with our commitment to providing superior and professional service. Let us handle the technical complexities so you can focus on scale.</p>
                <a href="contact.php" class="btn btn-primary">Contact Me</a>
            </div>
            
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number" data-target="<?php echo $stats['websites']; ?>">0</div>
                <div class="stat-label">Websites Developed</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="<?php echo $stats['logos']; ?>">0</div>
                <div class="stat-label">Logos Designed</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="<?php echo $stats['satisfaction']; ?>">0</div>
                <div class="stat-label">Customer Satisfaction</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" data-target="<?php echo $stats['experience']; ?>">0</div>
                <div class="stat-label">Years Experience</div>
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section class="blog-section">
    <div class="container">
        <div class="blog-header">
            <div class="blog-text">
                <h2>Our <span>Blog</span></h2>
                <h3>Latest From Blog</h3>
            </div>
            <div class="newsletter-signup">
                <form action="#" method="post" class="newsletter-form">
                    <input type="email" name="email" placeholder="Email Address" required>
                    <button type="submit" class="btn btn-primary">Join Now</button>
                </form>
            </div>
        </div>
        <div class="blog-wrapper">
        <div class="blog-posts">
            <?php 
            // Fetch posts from database to match Admin/Blog Page
            $blogPosts = getBlogPosts($pdo, 6);
            
            // Duplicate posts for seamless infinite scroll
            $displayPosts = !empty($blogPosts) ? array_merge($blogPosts, $blogPosts) : [];
            
            foreach($displayPosts as $post):
            ?>
            <article class="blog-post">
                <div class="image">
                    <img loading="lazy" src="assets/images/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                    <div class="post-badges">
                        <span class="post-badge">Technology</span>
                    </div>
                </div>
                <div class="content">
                    <div class="meta">
                        <i class="far fa-calendar-alt"></i>
                        <span><?php echo date('M j, Y', strtotime($post['published_at'])); ?></span>
                    </div>
                    <h4><a href="blog-post.php?slug=<?php echo $post['slug']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h4>
                    <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                    <a href="blog-post.php?slug=<?php echo $post['slug']; ?>" class="read-more">Read Article <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-wrapper">
            <div class="cta-content">
                <h2>Ready to Transform Your Business?</h2>
                <p>Join hundreds of satisfied clients who have elevated their digital presence with Mctech-hub Systems. Let's discuss your project and create something amazing together.</p>
                <div class="cta-buttons">
                    <a href="contact.php" class="btn btn-primary">Start Your Project Today</a>
                </div>
            </div>
            <div class="cta-image" style="position: relative; padding: 30px 0 0 30px;">
                <div style="position: absolute; top: 0; left: 0; width: 50%; height: 50%; border-top: 8px solid var(--accent); border-left: 8px solid var(--accent); z-index: 0;"></div>
                <img loading="lazy" src="https://images.unsplash.com/photo-1552664730-d307ca884978?w=600&h=450&fit=crop&crop=center" alt="Business Growth" style="position: relative; z-index: 1; border-radius: 0; box-shadow: 0 20px 50px rgba(0,0,0,0.1);">
            </div>
        </div>
    </div>
</section>

<section class="newsletter-section">
    <div class="container">
        <h2 class="newsletter-title">Subscribe Newsletter & get</h2>
        <h3 class="newsletter-subtitle">Company News</h3>
        
        <form action="#" method="post" class="newsletter-card">
            <i class="far fa-envelope"></i>
            <input type="email" name="email" placeholder="Your email" required>
            <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane" style="margin-right: 8px;"></i> Subscribe</button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
