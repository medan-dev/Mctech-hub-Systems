<?php 
include 'includes/config.php'; 

// Get slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

// Fetch post from database
$stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE slug = ? AND is_published = 1");
$stmt->execute([$slug]);
$post = $stmt->fetch();

// Redirect if not found
if (!$post) {
    header('Location: blog.php');
    exit;
}

$page_title = $post['title'];
$page_description = $post['excerpt'];
$page_class = 'page-blog-single';

// Fetch related posts
$stmtRelated = $pdo->prepare("SELECT * FROM blog_posts WHERE id != ? AND is_published = 1 ORDER BY created_at DESC LIMIT 3");
$stmtRelated->execute([$post['id']]);
$relatedPosts = $stmtRelated->fetchAll();

include 'includes/header.php'; 
?>

<!-- Blog Single Hero -->
<section class="blog-single-hero">
    <div class="container">
        <div class="blog-hero-content">
            <a href="blog.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Blog</a>
            <div class="post-meta-header">
                <span class="post-date"><?php echo date('F j, Y', strtotime($post['published_at'])); ?></span>
                <span class="post-author">By <?php echo htmlspecialchars($post['author']); ?></span>
            </div>
            <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        </div>
    </div>
</section>

<!-- Blog Content -->
<section class="section blog-single-section">
    <div class="container">
        <div class="blog-single-layout">
            <article class="blog-article">
                <?php if ($post['featured_image']): ?>
                <div class="featured-image">
                    <img src="assets/images/blog/<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                </div>
                <?php endif; ?>
                
                <div class="article-content">
                    <?php echo $post['content']; ?>
                </div>
                
                <div class="article-footer">
                    <div class="share-buttons">
                        <span>Share this article:</span>
                        <a href="#" class="share-btn"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="share-btn"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="share-btn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" class="share-btn"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </article>
            
            <aside class="blog-sidebar">
                <div class="sidebar-widget newsletter-widget">
                    <h3>Subscribe to our Newsletter</h3>
                    <p>Get the latest tech insights delivered to your inbox.</p>
                    <form action="#" class="sidebar-form">
                        <input type="email" placeholder="Your email address">
                        <button type="submit" class="btn btn-primary w-full">Subscribe</button>
                    </form>
                </div>
                
                <?php if ($relatedPosts): ?>
                <div class="sidebar-widget related-posts">
                    <h3>Related Articles</h3>
                    <div class="related-list">
                        <?php foreach($relatedPosts as $related): ?>
                        <a href="blog-post.php?slug=<?php echo $related['slug']; ?>" class="related-item">
                            <?php if($related['featured_image']): ?>
                            <div class="related-img">
                                <img src="assets/images/blog/<?php echo htmlspecialchars($related['featured_image']); ?>" alt="<?php echo htmlspecialchars($related['title']); ?>">
                            </div>
                            <?php endif; ?>
                            <div class="related-info">
                                <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                                <span><?php echo date('M j, Y', strtotime($related['published_at'])); ?></span>
                            </div>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </aside>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>