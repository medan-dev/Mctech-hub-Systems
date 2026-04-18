<?php 
include 'includes/config.php'; 
require_once 'includes/mailer.php';

$page_title = 'Contact the Best Website Developer & Designer in Uganda';
$page_description = 'Get in touch with Mctech-hub Systems — Uganda\'s best web developer & designer. Free consultation. Call +256758611414 or WhatsApp us. We respond within 24 hours. Based in Kampala, serving worldwide.';
$page_class = 'page-contact';
$message = '';
$form_data = ['name'=>'','email'=>'','phone'=>'','message'=>'','service'=>''];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name         = trim($_POST['name']    ?? '');
    $email        = trim($_POST['email']   ?? '');
    $phone        = trim($_POST['phone']   ?? '');
    $message_text = trim($_POST['message'] ?? '');
    $service      = trim($_POST['service'] ?? '');

    // Preserve submitted values
    $form_data = compact('name','email','phone','message_text','service');

    if (!empty($name) && !empty($email) && !empty($phone) && !empty($message_text)) {

        // 1. Save lead to database
        $lead_id = null;
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, message, service_interest, status) VALUES (?,?,?,?,?,'new')");
            $stmt->execute([$name, $email, $phone, $message_text, $service]);
            $lead_id = $pdo->lastInsertId();
        } catch(Exception $e) {}

        // 2. Load mail settings
        $mailSettings = [];
        try { $mailSettings = $pdo->query("SELECT * FROM mail_settings LIMIT 1")->fetch() ?: []; } catch(Exception $e) {}

        // 3. Auto-reply to visitor
        if (!empty($mailSettings['auto_reply_enabled']) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $html = Mailer::tplAutoReply($name, $service, $message_text);
            Mailer::send($email, $name, "We've received your message — Mctech-hub Systems", $html, $lead_id);
        }

        // 4. Alert admin
        if (!empty($mailSettings['admin_alert_enabled']) && !empty($mailSettings['admin_email'])) {
            $html = Mailer::tplAdminAlert($name, $email, $phone, $service, $message_text);
            Mailer::send($mailSettings['admin_email'], 'Admin', "🔴 New Lead: {$name} — {$service}", $html, $lead_id);
        }

        $message = 'success';
        $form_data = ['name'=>'','email'=>'','phone'=>'','message'=>'','service'=>''];

    } else {
        $message = 'error';
    }
}
?>
<?php include 'includes/header.php'; ?>

<!-- Cinematic Contact Hero -->
<section class="cinematic-who" style="padding-top: 130px; padding-bottom: 40px;">
    <div class="container cinematic-container">
        <div class="cinematic-visual-frame">
            <img loading="lazy" src="assets/images/ceo.jpg" alt="Contact Mctech-hub Systems" class="cinematic-img" style="object-position: center 30%;">
            
            <div class="cutout-top-right glass-shadow">
                <a href="#contact-form" class="social-btn"><i class="fas fa-arrow-down"></i></a>
            </div>
            
            <div class="cutout-bottom-left glass-shadow cinematic-hero-content">
                <p class="hero-badge"><i class="fas fa-envelope-open-text" style="color: #FFB020; margin-right: 5px;"></i> Contact Us</p>
                <h1 class="cinematic-title">Let's Talk About<br>Your Project</h1>
                <p class="cinematic-desc">We respond to all inquiries within 24 hours. Tell us about your business, your challenge, and your timeline.</p>
            </div>
        </div>
    </div>
</section>
<div id="contact-form"></div>

<!-- Contact Section -->
<section class="section contact-form-section">
    <div class="container contact-grid">
            <!-- Contact Form -->
            <div>
                <?php if ($message === 'success'): ?>
                <div class="alert alert-success">
                    <p><strong>✓ Thank you!</strong> We've received your message and will get back to you within 24 hours.</p>
                </div>
                <?php endif; ?>
                
                <?php if ($message === 'error'): ?>
                <div class="alert alert-error">
                    <p><strong>!</strong> Please fill in all required fields.</p>
                </div>
                <?php endif; ?>
                
                <form method="POST" id="contactForm" class="contact-form-p">
                    <input type="hidden" name="service" value="<?php echo htmlspecialchars($_GET['service'] ?? ''); ?>">
                    
                    <div class="form-group">
                        <label>Your Name *</label>
                        <input type="text" name="name" required class="form-control">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" name="email" required class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Phone/WhatsApp *</label>
                            <input type="tel" name="phone" required class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Message *</label>
                        <textarea name="message" id="contactMessage" rows="6" required class="form-control"><?php 
                            if(isset($_GET['service'])) {
                                echo "Hello Mctech-hub Systems, I am interested in your " . htmlspecialchars($_GET['service']) . " package and would like to learn more. ";
                            }
                        ?></textarea>
                    </div>
                    
                    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                        <button type="submit" class="btn btn-primary submit-btn" style="flex: 2;">Send Message</button>
                        <a href="https://wa.me/256758611414?text=Hello%20Mctech-hub%20Systems" 
                           target="_blank" 
                           class="btn btn-secondary" 
                           style="flex: 1; min-width: 200px; background: #25D366; border-color: #25D366; color: #fff;">
                            <i class="fab fa-whatsapp"></i> Chat via WhatsApp
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Contact Info -->
            <div class="contact-info-list">
                <h2 class="section-title">Get in Touch</h2>
                
                <div class="info-item">
                    <div class="info-flex">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-text-c">
                            <h4>Call or WhatsApp</h4>
                            <p class="main-val">
                                <a href="tel:+256700000000">+256 700 000 000</a>
                            </p>
                            <p class="sub-val">Available Monday - Friday, 9AM - 5PM Uganda Time</p>
                        </div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-flex">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-text-c">
                            <h4>Email</h4>
                            <p class="main-val">
                                <a href="mailto:contact@mctech-hub.com">contact@mctech-hub.com</a>
                            </p>
                            <p class="sub-val">We'll respond within 24 hours</p>
                        </div>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-flex">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-text-c">
                            <h4>Location</h4>
                            <p class="main-val">Kampala, Uganda</p>
                            <p class="sub-val">But we work with clients across Africa & worldwide</p>
                        </div>
                    </div>
                </div>

                <div class="info-response-card">
                    <h4>Quick Response Times</h4>
                    <ul>
                        <li>WhatsApp: Usually within 2 hours</li>
                        <li>Email: Within 24 hours</li>
                        <li>Urgent calls: Same day callback</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
