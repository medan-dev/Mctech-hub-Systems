        </div><!-- /.admin-content -->
    </main>
</div><!-- /.admin-container -->

<script>
document.addEventListener('DOMContentLoaded', () => {

    /* ── Sidebar Toggle ── */
    const toggle  = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    if (toggle && sidebar) {
        toggle.addEventListener('click', () => {
            const open = sidebar.classList.toggle('open');
            toggle.classList.toggle('open', open);
            toggle.innerHTML = open ? '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
        });
        document.addEventListener('click', e => {
            if (window.innerWidth <= 1024 && !sidebar.contains(e.target) && !toggle.contains(e.target)) {
                sidebar.classList.remove('open');
                toggle.classList.remove('open');
                toggle.innerHTML = '<i class="fas fa-bars"></i>';
            }
        });
    }

    /* ── Auto-dismiss alerts ── */
    document.querySelectorAll('.alert, .success, .error').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity .4s, transform .4s';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-8px)';
            setTimeout(() => el.remove(), 420);
        }, 5500);
    });

    /* ── Client-side search (filters table rows) ── */
    const searchInput = document.getElementById('adminSearch');
    if (searchInput) {
        searchInput.addEventListener('input', () => {
            const q = searchInput.value.toLowerCase().trim();
            document.querySelectorAll('.admin-table tbody tr:not(.lead-detail-row)').forEach(row => {
                row.style.display = (!q || row.textContent.toLowerCase().includes(q)) ? '' : 'none';
            });
        });
    }

    /* ── Count-up animation for stat numbers ── */
    const countEls = document.querySelectorAll('[data-count]');
    if (countEls.length) {
        const observer = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                const target = parseInt(el.getAttribute('data-count'), 10);
                let current = 0;
                const step = Math.max(1, Math.ceil(target / 35));
                const timer = setInterval(() => {
                    current = Math.min(current + step, target);
                    el.textContent = current.toLocaleString();
                    if (current >= target) clearInterval(timer);
                }, 28);
                observer.unobserve(el);
            });
        }, { threshold: 0.3 });
        countEls.forEach(el => observer.observe(el));
    }

    /* ── Animate progress bars (dl-fill, svc-fill, etc.) ── */
    const barEls = document.querySelectorAll('[data-width]');
    if (barEls.length) {
        const barObserver = new IntersectionObserver(entries => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;
                const el = entry.target;
                el.style.width = '0%';
                requestAnimationFrame(() => {
                    setTimeout(() => {
                        el.style.width = el.getAttribute('data-width') + '%';
                    }, 80);
                });
                barObserver.unobserve(el);
            });
        }, { threshold: 0.1 });
        barEls.forEach(el => { el.style.width = '0%'; barObserver.observe(el); });
    }
});
</script>
</body>
</html>
