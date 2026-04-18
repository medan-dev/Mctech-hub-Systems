<?php 
include 'includes/config.php'; 
$page_title = 'Tech Blog — Web Development, App & Design Tips from Uganda\'s Best Developer';
$page_description = 'Expert insights on web development, app development, UI/UX design, AI, SEO & digital transformation from Uganda\'s best tech team. Learn, grow & stay ahead with Mctech-hub Systems blog.';
$page_class = 'page-blog';
$posts = getBlogPosts($pdo, 20); // Fetch more posts for the grid
?>
<?php include 'includes/header.php'; ?>

<!-- Cinematic Blog Hero -->
<section class="cinematic-who" style="padding-top: 130px; padding-bottom: 40px;">
    <div class="container cinematic-container">
        <div class="cinematic-visual-frame">
            <img loading="lazy" src="assets/images/hero-blog.png" alt="Tech Insights & Innovation — Mctech-hub Systems Blog" class="cinematic-img">
            
            <div class="cutout-top-right glass-shadow">
                <a href="#featured-post" class="social-btn"><i class="fas fa-arrow-down"></i></a>
            </div>
            
            <div class="cutout-bottom-left glass-shadow" style="max-width: 85%;">
                <p style="color: var(--accent); font-weight: 800; font-size: 0.95rem; text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 1px;"><i class="fas fa-newspaper" style="color: #FFB020; margin-right: 5px;"></i> Our Latest Thinking</p>
                <h1 style="font-size: clamp(2.3rem, 4vw, 3.5rem); font-weight: 800; color: var(--text-dark); margin-bottom: 15px; line-height: 1.1;">Insights &<br>Innovation</h1>
                <p style="font-size: 1.05rem; color: #475569; max-width: 600px; line-height: 1.6; font-weight: 500;">Expert perspectives on technology, digital transformation, and business growth in Africa. Explore our latest articles and case studies.</p>
            </div>
        </div>
    </div>
</section>
<div id="featured-post"></div>

<!-- Featured Post Section -->
<?php if ($posts && count($posts) > 0): 
    $featured = $posts[0];
    $remaining_posts = array_slice($posts, 1);
?>
<section class="section featured-post-section">
    <div class="container">
        <div class="featured-card fade-in-up">
            <div class="featured-image">
                <img src="assets/images/<?php echo $featured['featured_image']; ?>" alt="<?php echo htmlspecialchars($featured['title']); ?>">
            </div>
            <div class="featured-content">
                <span class="post-category">Featured Article</span>
                <h2><?php echo htmlspecialchars($featured['title']); ?></h2>
                <p><?php echo htmlspecialchars($featured['excerpt']); ?></p>
                <div class="meta">
                    <span><i class="far fa-calendar-alt"></i> <?php echo date('M j, Y', strtotime($featured['published_at'])); ?></span>
                </div>
                <a href="blog-post.php?slug=<?php echo $featured['slug']; ?>" class="btn btn-primary">Read Article</a>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Blog Grid -->
<section class="section blog-listing-section">
    <div class="container">
        <div class="section-header">
            <h2>Latest <span class="text-accent">Insights</span></h2>
        </div>
        <div class="blog-grid-layout">
            <?php 
            foreach ($remaining_posts as $index => $post): 
                $isHidden = $index >= 6 ? 'hidden' : ''; 
            ?>
            <article class="blog-post fade-in-up <?php echo $isHidden; ?>">
                <div class="image">
                    <img src="assets/images/<?php echo $post['featured_image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
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
                    <p><?php echo htmlspecialchars(substr($post['excerpt'], 0, 100)) . '...'; ?></p>
                    <a href="blog-post.php?slug=<?php echo $post['slug']; ?>" class="read-more">Read Article <i class="fas fa-arrow-right"></i></a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
        
        <!-- Load More Button -->
        <div class="load-more-container fade-in-up">
            <button id="loadMoreBlogBtn" class="btn btn-secondary">Load More Articles</button>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
