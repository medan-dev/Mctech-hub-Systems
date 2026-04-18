    </main>

    <!-- Back to Top Button -->
    <button class="back-to-top" id="backToTop" title="Back to Top">
        <i class="fas fa-arrow-up"></i>
    </button>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <!-- Top Footer -->
            <div class="footer-grid">
                <!-- Brand / Address -->
                <div class="footer-brand">
                    <a href="<?php echo BASE_URL; ?>" class="logo">
                        <img loading="lazy" src="<?php echo BASE_URL; ?>assets/images/logo3.png" alt="Mctech-hub Systems" class="logo-img" style="filter: brightness(0) invert(1);"> <!-- Ensure logo acts like white graphic -->
                    </a>
                    <h4 style="margin-top: 1.5rem; color: #fff; font-size: 1.1rem; font-weight: 600;">Address</h4>
                    <p style="color: #fff; font-size: 0.9rem; line-height: 1.6;">Globally relevant bricks-and-clicks portals whereas functionalized applications based in Uganda, serving businesses across Africa and worldwide.</p>
                </div>
                
                <!-- Product Menu -->
                <div class="footer-section">
                    <h4>Product</h4>
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>services.php">Product</a></li>
                        <li><a href="#">Pricing</a></li>
                        <li><a href="#">Enterprise</a></li>
                        <li><a href="#">Partners</a></li>
                        <li><a href="#">Affiliate</a></li>
                    </ul>
                </div>

                <!-- Team Menu -->
                <div class="footer-section">
                    <h4>Team</h4>
                    <ul>
                        <li><a href="<?php echo BASE_URL; ?>about.php">About Us</a></li>
                        <li><a href="<?php echo BASE_URL; ?>contact.php">Contact Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Find a Partner</a></li>
                        <li><a href="#">In the News</a></li>
                    </ul>
                </div>
                
                <!-- Get in touch -->
                <div class="footer-section get-in-touch">
                    <h4>Get in touch</h4>
                    <ul>
                        <li><a href="mailto:contact@mctech-hub.com">contact@mctech-hub.com</a></li>
                        <li>Call: +256 700 000 000</li>
                    </ul>
                    <div class="social-links" style="margin-top: 1rem; margin-bottom: 1.5rem;">
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    </div>
                    <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-secondary footer-btn">Make a Schedule</a>
                </div>
            </div>
            
            <!-- Bottom Bar -->
            <div class="footer-bottom">
                <p>Copyright &copy; 2026 All Rights Reserved</p>
                <div class="bottom-contact" style="display: flex; gap: 2rem; color: #fff; font-size: 0.85rem;">
                    <span><i class="fas fa-phone" style="margin-right: 5px;"></i> Call: +256 700 000 000</span>
                    <span><i class="fas fa-envelope" style="margin-right: 5px;"></i> Email: contact@mctech-hub.com</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Global AI Widget -->
    <?php include_once __DIR__ . '/ai-widget.php'; ?>

    <!-- ══════════ SUBSCRIBER POPUP ══════════ -->
    <?php if (!isset($_COOKIE['mct_sub'])): ?>
    <div id="subPopup" style="
        position:fixed; bottom:-420px; right:24px; z-index:9000;
        width:340px; border-radius:24px; overflow:hidden;
        box-shadow:0 24px 64px rgba(11,20,55,.35);
        transition:bottom .55s cubic-bezier(.4,0,.2,1);
        font-family:'Poppins',sans-serif;
    " role="dialog" aria-label="Newsletter signup">

        <!-- Top dark section -->
        <div style="background:#0b1437; padding:22px 22px 16px; position:relative;">
            <button onclick="mctDismiss()" aria-label="Close"
                style="position:absolute;top:12px;right:14px;background:rgba(255,255,255,.1);border:none;cursor:pointer;width:28px;height:28px;border-radius:50%;color:rgba(255,255,255,.7);font-size:.85rem;display:flex;align-items:center;justify-content:center;line-height:1;">✕</button>

            <div style="display:flex; align-items:center; gap:10px; margin-bottom:10px;">
                <div style="width:36px;height:36px;border-radius:10px;background:rgba(230,57,70,.2);display:flex;align-items:center;justify-content:center;color:#e63946;font-size:.9rem;flex-shrink:0;">
                    <i class="fas fa-bolt"></i>
                </div>
                <div>
                    <p style="margin:0;font-size:.65rem;color:rgba(255,255,255,.5);text-transform:uppercase;letter-spacing:2px;font-weight:600;">Mctech-hub Insider</p>
                    <h3 style="margin:0;color:#fff;font-size:1rem;font-weight:800;line-height:1.2;">Grow your business with AI & Web</h3>
                </div>
            </div>
            <p style="margin:0;color:rgba(255,255,255,.55);font-size:.75rem;line-height:1.5;">Free tips on websites, AI agents, and digital strategy for African businesses. No spam, ever.</p>

            <!-- Value pills -->
            <div style="display:flex; gap:5px; flex-wrap:wrap; margin-top:10px;">
                <span style="background:rgba(67,97,238,.3);color:#93c5fd;font-size:.62rem;padding:3px 9px;border-radius:20px;font-weight:600;">💡 Tech Tips</span>
                <span style="background:rgba(16,185,129,.3);color:#6ee7b7;font-size:.62rem;padding:3px 9px;border-radius:20px;font-weight:600;">🌍 Africa Focus</span>
                <span style="background:rgba(124,58,237,.3);color:#c4b5fd;font-size:.62rem;padding:3px 9px;border-radius:20px;font-weight:600;">🤖 AI Insights</span>
            </div>
        </div>

        <!-- Gradient bar -->
        <div style="height:3px; background:linear-gradient(90deg,#e63946,#4361ee,#10b981);"></div>

        <!-- Form section -->
        <div style="background:#fff; padding:18px 22px 20px;">
            <!-- Success state (hidden initially) -->
            <div id="subSuccess" style="display:none; text-align:center; padding:10px 0;">
                <div style="font-size:2rem; margin-bottom:8px;">🎉</div>
                <p style="font-weight:800; color:#0b1437; margin:0 0 4px; font-size:.95rem;">You're subscribed!</p>
                <p style="color:#7b84b0; font-size:.75rem; margin:0;">Check your inbox for a welcome email.</p>
            </div>

            <!-- Form (visible initially) -->
            <form id="subForm" onsubmit="mctSubscribe(event)">
                <div style="margin-bottom:10px;">
                    <input type="text" id="subName" placeholder="Your first name (optional)"
                        style="width:100%;padding:9px 12px;border:1.5px solid #e4e9f7;border-radius:10px;font-family:inherit;font-size:.78rem;color:#0b1437;outline:none;box-sizing:border-box;transition:.2s;"
                        onfocus="this.style.borderColor='#e63946'" onblur="this.style.borderColor='#e4e9f7'">
                </div>
                <div style="margin-bottom:12px;">
                    <input type="email" id="subEmail" placeholder="Your email address *" required
                        style="width:100%;padding:9px 12px;border:1.5px solid #e4e9f7;border-radius:10px;font-family:inherit;font-size:.78rem;color:#0b1437;outline:none;box-sizing:border-box;transition:.2s;"
                        onfocus="this.style.borderColor='#e63946'" onblur="this.style.borderColor='#e4e9f7'">
                </div>
                <button type="submit" id="subBtn"
                    style="width:100%;background:linear-gradient(135deg,#e63946,#c1121f);color:#fff;border:none;padding:11px;border-radius:10px;font-weight:700;font-size:.82rem;cursor:pointer;font-family:inherit;transition:.2s;letter-spacing:.3px;">
                    <span id="subBtnTxt">🚀 Get Free Insights</span>
                </button>
                <p id="subMsg" style="margin:8px 0 0; font-size:.68rem; text-align:center; color:#7b84b0; display:none;"></p>
                <p style="margin:10px 0 0; color:#c8d0e8; font-size:.62rem; text-align:center;">
                    By subscribing you agree to receive emails from Mctech-hub. Unsubscribe anytime.
                </p>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- Scripts -->
    <script src="<?php echo BASE_URL; ?>assets/js/main.js"></script>

    <?php if (!isset($_COOKIE['mct_sub'])): ?>
    <script>
    (function() {
        const popup  = document.getElementById('subPopup');
        if (!popup) return;
        let shown    = false;
        let dismissed = false;

        function showPopup() {
            if (shown || dismissed) return;
            shown = true;
            popup.style.bottom = '24px';
        }

        // Trigger: 9 seconds after load
        setTimeout(showPopup, 9000);

        // Trigger: 45% scroll depth
        window.addEventListener('scroll', function() {
            const scrollPct = (window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100;
            if (scrollPct > 45) showPopup();
        }, { passive: true });
    })();

    function mctDismiss() {
        const popup = document.getElementById('subPopup');
        if (popup) { popup.style.bottom = '-420px'; }
        // Set dismissed cookie for 3 days
        document.cookie = 'mct_popup_dismissed=1; max-age=' + (86400*3) + '; path=/';
    }

    function mctSubscribe(e) {
        e.preventDefault();
        const email  = document.getElementById('subEmail').value.trim();
        const name   = document.getElementById('subName').value.trim();
        const btn    = document.getElementById('subBtn');
        const btnTxt = document.getElementById('subBtnTxt');
        const msg    = document.getElementById('subMsg');

        btnTxt.textContent = 'Sending…';
        btn.disabled = true;
        btn.style.opacity = '.7';

        fetch('<?php echo BASE_URL; ?>subscribe.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'email=' + encodeURIComponent(email) + '&name=' + encodeURIComponent(name) + '&source=popup'
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                document.getElementById('subForm').style.display = 'none';
                document.getElementById('subSuccess').style.display = 'block';
                document.cookie = 'mct_sub=1; max-age=' + (86400*30) + '; path=/';
                setTimeout(() => {
                    const p = document.getElementById('subPopup');
                    if (p) p.style.bottom = '-420px';
                }, 4000);
            } else {
                msg.textContent = data.msg || 'Please try again.';
                msg.style.color = '#e63946';
                msg.style.display = 'block';
                btnTxt.textContent = '🚀 Get Free Insights';
                btn.disabled = false;
                btn.style.opacity = '1';
            }
        })
        .catch(() => {
            msg.textContent = 'Network error. Please try again.';
            msg.style.color = '#e63946';
            msg.style.display = 'block';
            btnTxt.textContent = '🚀 Get Free Insights';
            btn.disabled = false;
            btn.style.opacity = '1';
        });
    }
    </script>
    <?php endif; ?>
</body>
</html>
