// Native smooth scroll (no external library for performance)
document.documentElement.style.scrollBehavior = "smooth";

// Inject Logo into Preloader
(function injectPreloaderLogo() {
  const preloader = document.querySelector(".preloader");
  if (preloader && !preloader.querySelector("img")) {
    const logo = document.createElement("img");
    // Determine path based on CSS link to handle root vs admin pages
    const cssLink = document.querySelector(
      'link[href*="assets/css/style.css"]',
    );
    const prefix = cssLink
      ? cssLink.getAttribute("href").replace("assets/css/style.css", "")
      : "";

    logo.src = prefix + "assets/images/logo3.png";
    logo.alt = "Loading Mctech-hub Systems...";
    logo.style.maxWidth = "200px";
    logo.style.marginBottom = "30px";
    logo.style.animation = "logo-pulse 2.5s infinite ease-in-out";

    // Ensure preloader styles support centered logo
    preloader.style.display = "flex";
    preloader.style.flexDirection = "column";
    preloader.style.alignItems = "center";
    preloader.style.justifyContent = "center";
    preloader.style.backgroundColor = "var(--bg-primary)"; // adapt to theme
    preloader.style.zIndex = "999999";

    preloader.insertBefore(logo, preloader.firstChild);

    // Add animation keyframes
    const style = document.createElement("style");
    style.textContent = `
            @keyframes logo-pulse { 
                0% { transform: scale(0.95); opacity: 0.6; filter: drop-shadow(0 0 10px rgba(255,140,0,0.2)); }
                50% { transform: scale(1.05); opacity: 1; filter: drop-shadow(0 0 25px rgba(255,140,0,0.6)); }
                100% { transform: scale(0.95); opacity: 0.6; filter: drop-shadow(0 0 10px rgba(255,140,0,0.2)); }
            }
        `;
    document.head.appendChild(style);
  }
})();

// Scroll Progress Bar Logic
window.addEventListener("scroll", () => {
  const progressBar = document.getElementById("scrollProgressBar");
  if (progressBar) {
    const scrollTop = document.documentElement.scrollTop || document.body.scrollTop;
    const scrollHeight = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    // Prevent divide by zero error on very short pages
    const scrolled = scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;
    progressBar.style.width = scrolled + "%";
  }
});

function hidePreloader() {
  const preloader = document.querySelector(".preloader");
  if (preloader) {
    preloader.classList.add("hidden");
    setTimeout(() => {
      preloader.style.display = "none";
    }, 600);
  }
}

// Hide preloader when window loads (including images)
window.addEventListener("load", hidePreloader);

// Fallback: Hide preloader after 3 seconds max
setTimeout(hidePreloader, 3000);

// DOM Ready
document.addEventListener("DOMContentLoaded", function () {
  // Animated Headline
  function initAnimatedHeadline() {
    const headlineDynamic = document.querySelector(".headline-dynamic");
    if (!headlineDynamic) return;

    const words = ["Amazing", "Modern", "Professional", "Functional"];
    let currentWordIndex = 0;

    function updateWord() {
      // Fade out current word
      const currentWord = headlineDynamic.querySelector(".word");
      if (currentWord) {
        currentWord.style.opacity = "0";
        setTimeout(() => {
          if (currentWord) currentWord.remove();
        }, 300);
      }

      // Add new word after a brief delay
      setTimeout(() => {
        const newWord = document.createElement("span");
        newWord.className = "word";
        newWord.textContent = words[currentWordIndex];
        newWord.style.opacity = "0";
        headlineDynamic.appendChild(newWord);

        // Fade in new word
        setTimeout(() => {
          newWord.style.opacity = "1";
        }, 50);

        // Update index
        currentWordIndex = (currentWordIndex + 1) % words.length;
      }, 300);
    }

    // Start animation
    updateWord();
    setInterval(updateWord, 3000);
  }

  // Testimonials Carousel
  function initTestimonialsCarousel() {
    // CSS Animation handles the infinite scroll now
  }

  // Counter Animation
  function animateCounters() {
    const counters = document.querySelectorAll(".stat-number[data-target]");
    counters.forEach((counter) => {
      const target = parseInt(counter.getAttribute("data-target"));
      const increment = target / 100;
      let current = 0;

      const updateCounter = () => {
        if (current < target) {
          current += increment;
          counter.textContent = Math.floor(current) + "+";
          requestAnimationFrame(updateCounter);
        } else {
          counter.textContent = target + "+";
        }
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            updateCounter();
            observer.unobserve(entry.target);
          }
        });
      });
      observer.observe(counter);
    });
  }

  // Smooth Scrolling
  document.querySelectorAll('a[href^="#"]').forEach((anchor) => {
    anchor.addEventListener("click", function (e) {
      e.preventDefault();
      const target = document.querySelector(this.getAttribute("href"));
      if (target) {
        target.scrollIntoView({ behavior: "smooth", block: "start" });
      }
    });
  });

  // Navbar Scroll Effect with Smooth Transition
  const header = document.querySelector(".header");
  let lastScrollY = 0;

  window.addEventListener("scroll", () => {
    const currentScrollY = window.scrollY;
    if (currentScrollY > 100) {
      header?.classList.add("header-scrolled");
    } else {
      header?.classList.remove("header-scrolled");
    }
    lastScrollY = currentScrollY;
  });

  // Mobile Menu Toggle
  const hamburger = document.querySelector(".hamburger");
  const navMenu = document.querySelector(".nav-menu");

  if (hamburger && navMenu) {
    hamburger.addEventListener("click", () => {
      navMenu.classList.toggle("active");
      hamburger.classList.toggle("active");
    });
  }

  // Back to Top Button
  const backToTop = document.getElementById("backToTop");
  if (backToTop) {
    window.addEventListener("scroll", () => {
      if (window.scrollY > 300) {
        backToTop.style.opacity = "1";
        backToTop.style.visibility = "visible";
      } else {
        backToTop.style.opacity = "0";
        backToTop.style.visibility = "hidden";
      }
    });

    backToTop.addEventListener("click", (e) => {
      e.preventDefault();
      window.scrollTo({ top: 0, behavior: "smooth" });
    });
  }

  // ═══════════════════════════════════════════════════════════════
  //  CINEMATIC SCENE-BASED SCROLL SYSTEM
  //  Clean, professional, smooth — like watching a film
  // ═══════════════════════════════════════════════════════════════
  function init3DAnimations() {
    const css = `
            /* ── Progress Bar ── */
            .scroll-progress {
                position: fixed; top: 0; left: 0; height: 2px;
                background: linear-gradient(90deg, var(--accent), #FFB020);
                z-index: 100000; width: 0%;
                transition: width 0.15s linear;
            }

            /* ── Scene Reveal (sections) ── */
            .scene {
                opacity: 0;
                transform: translateY(60px);
                transition: opacity 0.9s cubic-bezier(0.22, 1, 0.36, 1),
                            transform 0.9s cubic-bezier(0.22, 1, 0.36, 1);
                will-change: opacity, transform;
            }
            .scene.visible {
                opacity: 1;
                transform: translateY(0);
            }

            /* ── Scene Items (children inside grids) ── */
            .scene-item {
                opacity: 0;
                transform: translateY(40px) scale(0.97);
                transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1),
                            transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
                will-change: opacity, transform;
            }
            .scene-item.visible {
                opacity: 1;
                transform: translateY(0) scale(1);
            }

            /* ── Left Slide ── */
            .slide-left {
                opacity: 0;
                transform: translateX(-60px);
                transition: opacity 0.9s cubic-bezier(0.22, 1, 0.36, 1),
                            transform 0.9s cubic-bezier(0.22, 1, 0.36, 1);
            }
            .slide-left.visible {
                opacity: 1;
                transform: translateX(0);
            }

            /* ── Right Slide ── */
            .slide-right {
                opacity: 0;
                transform: translateX(60px);
                transition: opacity 0.9s cubic-bezier(0.22, 1, 0.36, 1),
                            transform 0.9s cubic-bezier(0.22, 1, 0.36, 1);
            }
            .slide-right.visible {
                opacity: 1;
                transform: translateX(0);
            }

            /* ── Scale Up ── */
            .scale-up {
                opacity: 0;
                transform: scale(0.92);
                transition: opacity 1s cubic-bezier(0.22, 1, 0.36, 1),
                            transform 1s cubic-bezier(0.22, 1, 0.36, 1);
            }
            .scale-up.visible {
                opacity: 1;
                transform: scale(1);
            }

            /* ── Parallax Image Layer ── */
            .parallax-img {
                transition: transform 0.05s linear;
                will-change: transform;
            }

            /* ── Smooth Page Enter ── */
            @keyframes page-curtain {
                from { opacity: 0; transform: translateY(30px); }
                to   { opacity: 1; transform: translateY(0); }
            }
            body > *:not(.preloader):not(.scroll-progress):not(script):not(style):not(link) {
                animation: page-curtain 0.8s cubic-bezier(0.22, 1, 0.36, 1) both;
            }

            /* ── Subtle Vignette (very light, cinematic) ── */
            .cinematic-vignette {
                position: fixed; inset: 0;
                background: radial-gradient(ellipse at center, transparent 60%, rgba(0,0,0,0.08) 100%);
                pointer-events: none;
                z-index: 99990;
            }
        `;
    const styleEl = document.createElement("style");
    styleEl.textContent = css;
    document.head.appendChild(styleEl);

    // Very subtle cinematic vignette
    const vignette = document.createElement("div");
    vignette.className = "cinematic-vignette";
    document.body.appendChild(vignette);

    // Scroll progress bar
    const progressBar = document.createElement("div");
    progressBar.className = "scroll-progress";
    document.body.appendChild(progressBar);

    window.addEventListener(
      "scroll",
      () => {
        const scrolled =
          (window.scrollY /
            (document.documentElement.scrollHeight - window.innerHeight)) *
          100;
        progressBar.style.width = scrolled + "%";
      },
      { passive: true },
    );

    // ─── SCENE OBSERVER (sections appear on scroll, reset when scrolled past) ───
    const sceneObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            entry.target.classList.add("visible");
          } else if (entry.boundingClientRect.top > 0) {
            entry.target.classList.remove("visible");
          }
        });
      },
      { threshold: 0.08, rootMargin: "0px 0px -80px 0px" },
    );

    // Tag each section as a "scene"
    document
      .querySelectorAll(
        "section, .section-header, .featured-card, .cta-wrapper",
      )
      .forEach((el) => {
        el.classList.add("scene");
        sceneObserver.observe(el);
      });

    // ─── DIRECTIONAL SLIDES ───
    // Hero text slides from left, hero visual from right
    document
      .querySelectorAll(
        ".hero-text, .services-info, .cta-content, .about-content",
      )
      .forEach((el) => {
        el.classList.remove("scene");
        el.classList.add("slide-left");
        sceneObserver.observe(el);
      });
    document.querySelectorAll(".hero-visual, .cta-image").forEach((el) => {
      el.classList.remove("scene");
      el.classList.add("slide-right");
      sceneObserver.observe(el);
    });

    // Cinematic frames scale up
    document.querySelectorAll(".cinematic-visual-frame").forEach((el) => {
      el.classList.add("scale-up");
      sceneObserver.observe(el);
    });

    // ─── STAGGERED GRID CHILDREN ───
    const gridObserver = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          const items = entry.target.querySelectorAll(".scene-item");
          if (entry.isIntersecting) {
            items.forEach((item, i) => {
              item.style.transitionDelay = `${i * 0.08}s`;
              requestAnimationFrame(() => item.classList.add("visible"));
            });
          } else if (entry.boundingClientRect.top > 0) {
            items.forEach((item) => {
              item.style.transitionDelay = "0s";
              item.classList.remove("visible");
            });
          }
        });
      },
      { threshold: 0.05, rootMargin: "0px 0px -60px 0px" },
    );

    const gridSelectors =
      ".services-grid, .portfolio-grid, .testimonials-grid, .blog-posts, .team-flex, .values-grid, .stats-grid, .mission-grid, .blog-grid-layout, .service-cards-grid";
    document.querySelectorAll(gridSelectors).forEach((grid) => {
      Array.from(grid.children).forEach((child) =>
        child.classList.add("scene-item"),
      );
      gridObserver.observe(grid);
    });

    // ─── SMOOTH PARALLAX (images only, no section bending) ───
    const parallaxImages = document.querySelectorAll(
      ".hero-image img, .cinematic-img, .cta-image img",
    );
    parallaxImages.forEach((img) => img.classList.add("parallax-img"));

    function tickParallax() {
      const wh = window.innerHeight;
      parallaxImages.forEach((img) => {
        const rect = (
          img.closest(".cinematic-visual-frame") || img.parentElement
        ).getBoundingClientRect();
        if (rect.bottom > 0 && rect.top < wh) {
          const progress = (rect.top + rect.height / 2 - wh / 2) / wh; // -0.5 → 0.5
          img.style.transform = `translateY(${progress * -30}px) scale(1.04)`;
        }
      });
      requestAnimationFrame(tickParallax);
    }
    requestAnimationFrame(tickParallax);
  }

  init3DAnimations();

  // Hamburger Menu Toggle
  function initHamburgerMenu() {
    const hamburger = document.querySelector(".hamburger");
    const navMenu = document.querySelector(".nav-menu");
    const navLinks = document.querySelectorAll(".nav-menu a");

    if (!hamburger || !navMenu) return;

    // Toggle menu on hamburger click with visual feedback
    hamburger.addEventListener("click", (e) => {
      e.stopPropagation();
      hamburger.classList.toggle("active");
      navMenu.classList.toggle("active");

      // Add visual feedback
      if (hamburger.classList.contains("active")) {
        document.body.style.overflow = "hidden";
      } else {
        document.body.style.overflow = "auto";
      }
    });

    // Close menu when a link is clicked
    navLinks.forEach((link, index) => {
      link.addEventListener("click", () => {
        hamburger.classList.remove("active");
        navMenu.classList.remove("active");
        document.body.style.overflow = "auto";
      });
    });

    // Close menu when clicking outside
    document.addEventListener("click", (e) => {
      if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
        if (hamburger.classList.contains("active")) {
          hamburger.classList.remove("active");
          navMenu.classList.remove("active");
          document.body.style.overflow = "auto";
        }
      }
    });

    // Close menu on escape key
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && hamburger.classList.contains("active")) {
        hamburger.classList.remove("active");
        navMenu.classList.remove("active");
        document.body.style.overflow = "auto";
      }
    });
  }

  // Subtle Hover Tilt for Cards (professional — NOT aggressive)
  function initTiltEffect() {
    const cards = document.querySelectorAll(
      ".service-item, .portfolio-card, .stat-item, .testimonial-card, .blog-post, .blog-card-modern, .featured-card, .team-member",
    );

    cards.forEach((card) => {
      if (window.matchMedia("(min-width: 992px)").matches) {
        card.style.transition = "transform 0.4s ease, box-shadow 0.4s ease";

        card.addEventListener("mousemove", (e) => {
          const rect = card.getBoundingClientRect();
          const x = (e.clientX - rect.left) / rect.width - 0.5; // -0.5 to 0.5
          const y = (e.clientY - rect.top) / rect.height - 0.5;

          card.style.transform = `perspective(800px) rotateX(${y * -5}deg) rotateY(${x * 5}deg) translateY(-4px)`;
          card.style.boxShadow = `0 14px 28px rgba(0,0,0,0.12), 0 4px 10px rgba(0,0,0,0.08)`;
        });

        card.addEventListener("mouseleave", () => {
          card.style.transform = "";
          card.style.boxShadow = "";
        });
      }
    });
  }

  // Portfolio Filtering
  const filterBtns = document.querySelectorAll(".filter-btn");
  const portfolioItems = document.querySelectorAll(".portfolio-item");
  const loadMoreBtn = document.getElementById("loadMoreBtn");

  if (filterBtns.length > 0) {
    let itemsToShow = 6;
    let currentFilter = "all";

    const renderProjects = () => {
      let shownCount = 0;
      let totalMatch = 0;

      portfolioItems.forEach((item) => {
        const category = item.getAttribute("data-category");
        const matches = currentFilter === "all" || category === currentFilter;

        if (matches) {
          totalMatch++;
          if (shownCount < itemsToShow) {
            item.classList.remove("hidden");
            setTimeout(() => item.classList.add("visible"), 10);
            shownCount++;
          } else {
            item.classList.add("hidden");
            item.classList.remove("visible");
          }
        } else {
          item.classList.add("hidden");
          item.classList.remove("visible");
        }
      });

      if (loadMoreBtn) {
        loadMoreBtn.style.display =
          totalMatch > itemsToShow ? "inline-flex" : "none";
      }
    };

    // Initial render
    renderProjects();

    filterBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        filterBtns.forEach((b) => b.classList.remove("active"));
        btn.classList.add("active");

        currentFilter = btn.getAttribute("data-filter");
        itemsToShow = 6; // Reset to 6 when changing filter
        renderProjects();
      });
    });

    if (loadMoreBtn) {
      loadMoreBtn.addEventListener("click", () => {
        itemsToShow = 10000; // Load all
        renderProjects();
      });
    }
  }

  // Blog Load More Functionality
  const loadMoreBlogBtn = document.getElementById("loadMoreBlogBtn");
  if (loadMoreBlogBtn) {
    // Check if there are any hidden posts initially
    if (document.querySelectorAll(".blog-post.hidden").length === 0) {
      loadMoreBlogBtn.style.display = "none";
    }

    loadMoreBlogBtn.addEventListener("click", (e) => {
      e.preventDefault();
      const hiddenPosts = document.querySelectorAll(".blog-post.hidden");

      // Reveal up to 6 posts
      for (let i = 0; i < 6 && i < hiddenPosts.length; i++) {
        hiddenPosts[i].classList.remove("hidden");
        // Trigger animation reflow if needed, or rely on CSS transition
        hiddenPosts[i].style.animation =
          "fadeInUp 0.6s cubic-bezier(0.23, 1, 0.320, 1) forwards";
      }

      // Hide button if no more posts
      if (document.querySelectorAll(".blog-post.hidden").length === 0) {
        loadMoreBlogBtn.style.display = "none";
      }
    });
  }

  // Initialize all functions
  initAnimatedHeadline();
  initTestimonialsCarousel();
  animateCounters();
  initHamburgerMenu();
  initTiltEffect();

  /* ==================== Sticky CTA + Lead Modal ==================== */
  (function initLeadModal() {
    const openBtn = document.getElementById("openLeadModal");
    const modal = document.getElementById("leadModal");
    const backdrop = document.getElementById("leadModalBackdrop");
    const closeBtn = document.getElementById("leadModalClose");
    const leadForm = document.getElementById("leadForm");
    const feedback = document.getElementById("leadFeedback");
    const sticky = document.getElementById("stickyCta");
    const stickyClose = document.getElementById("closeStickyCta");

    const DISMISS_KEY = "lead_cta_dismissed";

    function showModal() {
      if (!modal) return;
      modal.setAttribute("aria-hidden", "false");
      document.body.style.overflow = "hidden";
    }

    function hideModal() {
      if (!modal) return;
      modal.setAttribute("aria-hidden", "true");
      document.body.style.overflow = "auto";
    }

    function hideSticky(days = 7) {
      if (sticky) sticky.style.display = "none";
      const until = Date.now() + days * 24 * 60 * 60 * 1000;
      try {
        localStorage.setItem(DISMISS_KEY, String(until));
      } catch (e) {}
    }

    // Open handlers
    openBtn?.addEventListener("click", (e) => {
      e.preventDefault();
      showModal();
    });

    // Close handlers
    closeBtn?.addEventListener("click", hideModal);
    backdrop?.addEventListener("click", hideModal);
    stickyClose?.addEventListener("click", () => hideSticky(30));

    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape") hideModal();
    });

    // Auto show after delay if not dismissed
    try {
      const value = localStorage.getItem(DISMISS_KEY);
      if (!value || Number(value) < Date.now()) {
        setTimeout(() => {
          if (sticky) sticky.style.transform = "translateY(0)";
          showModal();
        }, 8000);
      } else if (sticky) {
        sticky.style.display = "none";
      }
    } catch (e) {}

    // Form submit: basic validation and attempt POST to contact endpoint; fallback to mailto
    leadForm?.addEventListener("submit", async (ev) => {
      ev.preventDefault();
      feedback.textContent = "";
      const name = document.getElementById("leadName")?.value.trim();
      const email = document.getElementById("leadEmail")?.value.trim();
      const phone = document.getElementById("leadPhone")?.value.trim();
      const message = document.getElementById("leadMessage")?.value.trim();

      if (!name || !email || !/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
        feedback.style.color = "#b91c1c";
        feedback.textContent = "Please enter a valid name and email.";
        return;
      }

      feedback.style.color = "#065f46";
      feedback.textContent = "Sending...";

      // Try POST to contact.php (relative). If server doesn't accept, fallback to mailto.
      try {
        const payload = new FormData();
        payload.append("name", name);
        payload.append("email", email);
        payload.append("phone", phone);
        payload.append("message", message || "Requesting free strategy call");

        const res = await fetch("contact.php", {
          method: "POST",
          body: payload,
        });
        if (res.ok) {
          feedback.textContent = "Thanks! We will contact you shortly.";
          leadForm.reset();
          hideSticky(30);
          setTimeout(hideModal, 1500);
          return;
        }
      } catch (e) {
        // ignore, fallback
      }

      // Fallback to mailto for browsers where fetch failed
      try {
        const subject = encodeURIComponent("Free strategy call request");
        const body = encodeURIComponent(
          `Name: ${name}%0AEmail: ${email}%0APhone: ${phone}%0A%0A${message}`,
        );
        window.location.href = `mailto:contact@mctech-hub.com?subject=${subject}&body=${body}`;
      } catch (e) {
        feedback.style.color = "#b91c1c";
        feedback.textContent =
          "Unable to submit — please email contact@mctech-hub.com";
      }
    });

    // Maybe later button
    document.getElementById("leadLater")?.addEventListener("click", () => {
      hideModal();
      hideSticky(3);
    });
  })();

  // === Advanced AI Agent Logic ===

  // Move the widget to the absolute roof of the DOM (<html>) to bypass any body transforms breaking fixed positioning
  const aiWidgetContainer = document.getElementById("ai-chat-widget");
  if (aiWidgetContainer && document.documentElement) {
    document.documentElement.appendChild(aiWidgetContainer);
  }

  const chatToggle = document.getElementById("ai-chat-toggle");
  const chatWindow = document.getElementById("ai-chat-window");
  const chatClose = document.getElementById("ai-chat-close");
  const chatInput = document.getElementById("ai-chat-input");
  const chatMessages = document.getElementById("ai-chat-messages");
  let hasOpenedWidget = false;

  // Auto-open ONLY ONCE ever per user (uses localStorage)
  if (chatToggle && chatWindow) {
    const alreadyShown = localStorage.getItem("mctech_ai_shown");
    if (!alreadyShown) {
      setTimeout(() => {
        if (!hasOpenedWidget && !chatWindow.classList.contains("active")) {
          chatWindow.classList.add("active");
          hasOpenedWidget = true;
          localStorage.setItem("mctech_ai_shown", "1");
        }
      }, 20000);
    }
  }

  if (chatToggle && chatWindow && chatClose) {
    chatToggle.addEventListener("click", () => {
      hasOpenedWidget = true;
      chatWindow.classList.toggle("active");
      if (chatWindow.classList.contains("active")) chatInput.focus();
    });

    chatClose.addEventListener("click", () => {
      chatWindow.classList.remove("active");
    });

    // Add event listener directly for enter key fallback
    chatInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        window.sendChatMessage();
      }
    });

    // Add event listener for the neon send button
    document
      .getElementById("ai-chat-send")
      ?.addEventListener("click", function () {
        window.sendChatMessage();
      });
  }

  // Extensive mock knowledge base for fully independent customer care and sales
  const aiKnowledgeBase = {
    greeting: [
      "Hello! I am N.E.O., your Mctech-hub Systems digital assistant. How can I provide you with exceptional customer care today?",
      "Welcome to Mctech-hub Systems! Looking to upgrade your digital presence or need technical support?",
      "Greetings! I'm here to provide premium customer care and answer all your questions about our agency.",
    ],
    about: [
      "Mctech-hub Systems is a premier digital agency focused on building strategic tech for Africa and the world. We specialize in custom websites, mobile apps, and integrating advanced AI solutions to help businesses dominate their markets.",
      "We are globally relevant digital experts. We build everything from high-conversion landing pages to complex enterprise web applications, bringing world-class tech standards to local and global businesses.",
    ],
    pricing: [
      "Our pricing is highly competitive for global-standard engineering. Professional business websites start at $500. Advanced web apps, mobile apps, or custom AI integrations start closer to $1,500. To give you an exact number, I'd love to set up a quick strategy call. Does tomorrow work for you?",
      "We believe in transparent pricing based on scope. Simple projects begin around $500, while enterprise systems are custom quoted. Let's get your project estimated—would you like me to connect you with our lead developer?",
    ],
    services: [
      "We offer a full suite of digital solutions: 1. Custom Web Development, 2. Mobile App Development (iOS/Android), 3. Elite UI/UX Design, and 4. Custom AI Agent Integrations (like me!). Which of those are you looking to explore?",
      "Our core services include building high-conversion websites, powerful mobile apps, and integrating advanced AI tools to automate your business. We handle everything from the initial design to the final server deployment.",
    ],
    location: [
      "We are proudly based in Uganda, but our reach is global! We serve ambitious businesses across Africa and worldwide with cutting-edge digital solutions.",
      "Our headquarters is in Uganda. However, as a digital-first agency, our clientele and team span the globe!",
    ],
    contact: [
      "You can reach our human team directly at contact@mctech-hub.com or call/WhatsApp us at +256 700 000 000. Alternatively, you can click the 'Contact' page in the top menu!",
      "To get in touch immediately, email contact@mctech-hub.com or give us a call at +256 700 000 000. Our team responds lightning fast.",
    ],
    support: [
      "I am here to provide 24/7 customer care! Please describe your technical issue in detail, and I'll ensure it gets routed to our engineering team immediately.",
      "Customer care is our absolute top priority. If you are an existing client experiencing an issue, please tell me what's wrong, or email technical support directly at contact@mctech-hub.com so we can resolve it ASAP.",
    ],
    ai: [
      "I am N.E.O., a custom-built AI agent engineered by the Mctech-hub Systems team. I utilize advanced neural logic to answer your questions instantly and provide 24/7 customer care. We can build a powerful AI just like me for your own company's website to boost your sales!",
      "I am an artificial intelligence built specifically to assist Mctech-hub clients. If you want an AI chatbot for your own platform to handle customer support while you sleep, we can absolutely develop that for you.",
    ],
    portfolio: [
      "We have built numerous high-performing projects: from dynamic E-Commerce platforms to custom CRM systems. You can view our recent case studies by clicking 'Portfolio' in the top menu. We'd love to add your business to our success stories!",
      "Our portfolio includes complex web applications, modern corporate websites, and cutting-edge mobile apps. Check out the Portfolio page to see our live work.",
    ],
    close_deal: [
      "It sounds like you're ready to take your business to the next level. Let's make this happen. Should I have our lead strategist email you to finalize the details today?",
      "You've come to the perfect place for this project. We have the exact expertise you need. Shall we set up a 15-minute onboarding call to get the contract started?",
      "I am confident Mctech-hub Systems is the best partner for this. Click 'Make a Schedule' in our footer, and let's formalize our partnership!",
    ],
    thanks: [
      "You are very welcome! It is my absolute pleasure to serve you. Let me know if you need anything else to secure your digital future.",
      "My pleasure! I'm here 24/7 if you have more questions or if you're ready to start building.",
    ],
    default: [
      "That's exceptionally interesting. To ensure you get the absolute best strategy for that specific request, I recommend speaking directly with our senior team. Would you like me to provide our email or phone number?",
      "I understand completely. Our expert human engineers would be the perfect fit to discuss that nuance in detail. While I specialize in our core services, pricing, and support, they can handle the rest. Shall we get in touch?",
      "Thank you for sharing that. As an AI, I want to make sure you get 100% accurate information. For highly specific technical inquiries, please email contact@mctech-hub.com and our lead dev will reply today!",
    ],
  };

  function analyzeIntent(text) {
    text = text.toLowerCase();

    // Sales / Closing Intent (High Priority)
    if (
      text.match(
        /\b(buy|start|hire|contract|proposal|ready|let's go|begin|onboard|sign|deal)\b/,
      )
    )
      return "close_deal";

    // General Intents
    if (text.match(/\b(hi|hello|hey|greetings|howdy|morning|afternoon)\b/))
      return "greeting";
    if (
      text.match(
        /\b(who are you|about|company|mctech|mctech-hub|what is mctech|agency|what do you do)\b/,
      )
    )
      return "about";
    if (
      text.match(
        /\b(price|cost|how much|pricing|quote|money|budget|pay|fee|charge)\b/,
      )
    )
      return "pricing";
    if (
      text.match(
        /\b(service|services|build|create|apps|website|design|develop|offer|capabilities)\b/,
      )
    )
      return "services";
    if (
      text.match(
        /\b(where|location|based|city|country|uganda|africa|headquarters)\b/,
      )
    )
      return "location";
    if (
      text.match(
        /\b(contact|email|phone|whatsapp|call|touch|reach|number|message)\b/,
      )
    )
      return "contact";
    if (
      text.match(
        /\b(support|help|broken|issue|bug|fix|customer care|assistance|problem|error)\b/,
      )
    )
      return "support";
    if (
      text.match(
        /\b(ai|artificial intelligence|bot|robot|how do you work|chat bot)\b/,
      )
    )
      return "ai";
    if (
      text.match(
        /\b(portfolio|work|examples|projects|case studies|past work|show me)\b/,
      )
    )
      return "portfolio";
    if (text.match(/\b(thanks|thank you|awesome|great|cool|perfect|good)\b/))
      return "thanks";

    return "default";
  }

  function getRandomResponse(intent) {
    const responses = aiKnowledgeBase[intent];
    return responses[Math.floor(Math.random() * responses.length)];
  }

  window.sendChatMessage = function () {
    if (!chatInput) return;
    const text = chatInput.value.trim();
    if (!text) return;

    // Add User Message
    const userMsg = document.createElement("div");
    userMsg.className = "message user-message";
    userMsg.innerHTML = `
            <div class="avatar"><i class="fas fa-user"></i></div>
            <div class="message-content">${escapeHTML(text)}</div>
        `;
    chatMessages.appendChild(userMsg);
    chatInput.value = "";
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Show Typing Indicator
    const typingIndicator = document.createElement("div");
    typingIndicator.className = "message ai-message";
    typingIndicator.id = "ai-typing-indicator";
    typingIndicator.innerHTML = `
            <div class="avatar glow"><i class="fas fa-microchip"></i></div>
            <div class="message-content futuristic-bubble" style="padding: 18px 25px;">
                <div class="typing-indicator">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        `;
    chatMessages.appendChild(typingIndicator);
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Simulate AI Processing (Dynamic delay based on text length)
    const delay = Math.max(800, Math.min(2500, text.length * 40));

    setTimeout(() => {
      const indicator = document.getElementById("ai-typing-indicator");
      if (indicator) indicator.remove();

      const intent = analyzeIntent(text);
      const responseText = getRandomResponse(intent);

      const aiMsg = document.createElement("div");
      aiMsg.className = "message ai-message";
      aiMsg.innerHTML = `
                <div class="avatar glow"><i class="fas fa-microchip"></i></div>
                <div class="message-content futuristic-bubble">${responseText}</div>
            `;
      chatMessages.appendChild(aiMsg);
      chatMessages.scrollTop = chatMessages.scrollHeight;
    }, delay);
  };

  // Helper to prevent XSS
  function escapeHTML(str) {
    return str.replace(
      /[&<>'"]/g,
      (tag) =>
        ({
          "&": "&amp;",
          "<": "&lt;",
          ">": "&gt;",
          "'": "&#39;",
          '"': "&quot;",
        })[tag],
    );
  }

  // ════════════════════════════════════════
  // 3D SWIVEL ENGINE (Cinematic 3D Effect)
  // ════════════════════════════════════════
  function init3DSwivel() {
    const tiltElements = document.querySelectorAll(
      ".service-item, .project-card, .hero-image",
    );

    tiltElements.forEach((el) => {
      el.addEventListener("mousemove", (e) => {
        const rect = el.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const centerX = rect.width / 2;
        const centerY = rect.height / 2;

        const rotateX = ((y - centerY) / centerY) * -10; // Max 10 deg
        const rotateY = ((x - centerX) / centerX) * 10;

        el.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;

        // If it has children to pop out
        const children = el.querySelectorAll("i, h3, img, .service-icon");
        children.forEach((child) => {
          child.style.transform = "translateZ(30px)";
          child.style.transition = "transform 0.1s ease-out";
        });
      });

      el.addEventListener("mouseleave", () => {
        el.style.transform =
          "perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)";
        const children = el.querySelectorAll("i, h3, img, .service-icon");
        children.forEach((child) => {
          child.style.transform = "translateZ(0px)";
        });
      });

      // Add transition for smoothness
      el.style.transition = "transform 0.1s ease-out, box-shadow 0.3s ease";
      el.style.transformStyle = "preserve-3d";
    });
  }

  // ════════════════════════════════════════
  // THREE.JS CINEMATIC HERO BACKGROUND
  // ════════════════════════════════════════
  function initHero3D() {
    const canvas = document.getElementById("hero-3d-canvas");
    if (!canvas || window.innerWidth < 768) return;

    // Load Three.js dynamically if not present
    if (typeof THREE === "undefined") {
      const script = document.createElement("script");
      script.src =
        "https://cdnjs.cloudflare.com/ajax/libs/three.js/0.160.0/three.min.js";
      script.onload = () => startThreeJS(canvas);
      document.head.appendChild(script);
    } else {
      startThreeJS(canvas);
    }
  }

  function startThreeJS(canvas) {
    const renderer = new THREE.WebGLRenderer({
      canvas,
      antialias: true,
      alpha: true,
    });
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.setSize(canvas.clientWidth, canvas.clientHeight);

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(
      75,
      canvas.clientWidth / canvas.clientHeight,
      0.1,
      1000,
    );
    camera.position.z = 5;

    // Particles / Tech Core
    const particlesGeometry = new THREE.BufferGeometry();
    const count = 1500;
    const positions = new Float32Array(count * 3);
    const colors = new Float32Array(count * 3);

    for (let i = 0; i < count * 3; i++) {
      positions[i] = (Math.random() - 0.5) * 10;
      colors[i] = Math.random();
    }

    particlesGeometry.setAttribute(
      "position",
      new THREE.BufferAttribute(positions, 3),
    );
    particlesGeometry.setAttribute(
      "color",
      new THREE.BufferAttribute(colors, 3),
    );

    const particlesMaterial = new THREE.PointsMaterial({
      size: 0.05,
      sizeAttenuation: true,
      vertexColors: true,
      transparent: true,
      opacity: 0.8,
      blending: THREE.AdditiveBlending,
    });

    const points = new THREE.Points(particlesGeometry, particlesMaterial);
    scene.add(points);

    // Mouse interaction
    let mouseX = 0,
      mouseY = 0;
    document.addEventListener("mousemove", (e) => {
      mouseX = (e.clientX - window.innerWidth / 2) / 100;
      mouseY = (e.clientY - window.innerHeight / 2) / 100;
    });

    function animate() {
      requestAnimationFrame(animate);
      points.rotation.y += 0.002;
      points.rotation.x += 0.001;

      // Subtle swivel based on mouse
      points.rotation.y += (mouseX - points.rotation.y) * 0.05;
      points.rotation.x += (mouseY - points.rotation.x) * 0.05;

      renderer.render(scene, camera);
    }
    animate();

    window.addEventListener("resize", () => {
      camera.aspect = canvas.clientWidth / canvas.clientHeight;
      camera.updateProjectionMatrix();
      renderer.setSize(canvas.clientWidth, canvas.clientHeight);
    });
  }

    init3DSwivel();
    initHero3D();

    /* =========================================
       WhatsApp Service Inquiry Workflow
       ========================================= */
    const ServiceInquiryManager = {
        init: function() {
            const inquiryBtns = document.querySelectorAll('.service-inquiry-btn');
            inquiryBtns.forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const service = btn.getAttribute('data-service') || 'Tech Service';
                    const price = btn.getAttribute('data-price') || 'Custom';
                    this.handleInquiry(service, price);
                });
            });
        },

        handleInquiry: function(service, price) {
            const phone = "256758611414";
            const message = `Hello Mctech-hub Systems, I am interested in the *${service}* package (${price}). Please guide me on how to get started.`;
            const waUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;

            // 1. Show Success Popup (Premium UI)
            this.showPopup(service);

            // 2. Open WhatsApp in new tab
            window.open(waUrl, '_blank');

            // 3. Redirect main window to contact form with delay
            setTimeout(() => {
                window.location.href = `contact.php?service=${encodeURIComponent(service)}`;
            }, 3500);
        },

        showPopup: function(service) {
            const popup = document.createElement('div');
            popup.className = 'premium-inquiry-popup';
            popup.innerHTML = `
                <div class="popup-content glass-shadow">
                    <div class="popup-icon"><i class="fas fa-check-circle"></i></div>
                    <h3>Excellent Choice!</h3>
                    <p>Redirecting you to <strong>WhatsApp</strong> to discuss the <strong>${service}</strong> package.</p>
                    <div class="popup-loader"><span></span></div>
                    <p class="popup-note">Followed by our contact form for more details.</p>
                </div>
            `;
            document.body.appendChild(popup);
            
            // Trigger animation
            setTimeout(() => popup.classList.add('active'), 10);
        }
    };

    ServiceInquiryManager.init();
});
