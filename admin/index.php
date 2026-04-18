<?php
include '../includes/config.php';
$page_title = 'Dashboard';

/* ── Aggregate stats ── */
$svc_count  = (int)$pdo->query("SELECT COUNT(*) FROM services WHERE is_featured=1 OR 1")->fetchColumn();
$proj_count = (int)$pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn();
$total_leads= (int)$pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$new_leads  = (int)$pdo->query("SELECT COUNT(*) FROM contacts WHERE status='new'")->fetchColumn();
$testi_count= (int)$pdo->query("SELECT COUNT(*) FROM testimonials WHERE is_active=1")->fetchColumn();
$post_count = (int)$pdo->query("SELECT COUNT(*) FROM blog_posts WHERE is_published=1")->fetchColumn();
$draft_count= (int)$pdo->query("SELECT COUNT(*) FROM blog_posts WHERE is_published=0")->fetchColumn();

/* ── Lead status for donut ── */
$statuses = ['new'=>0,'contacted'=>0,'proposal'=>0,'closed'=>0];
foreach ($pdo->query("SELECT status, COUNT(*) c FROM contacts GROUP BY status") as $r)
    if (array_key_exists($r['status'], $statuses)) $statuses[$r['status']] = (int)$r['c'];
$donutTotal = array_sum($statuses) ?: 1;

/* ── Monthly leads (last 7 months) ── */
$months_raw = $pdo->query("
    SELECT DATE_FORMAT(created_at,'%b') m, COUNT(*) c
    FROM contacts
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 MONTH)
    GROUP BY YEAR(created_at), MONTH(created_at)
    ORDER BY YEAR(created_at), MONTH(created_at)
")->fetchAll();
$chart_labels = array_column($months_raw,'m');
$chart_data   = array_column($months_raw,'c');
// Pad to at least 7 months of labels
if (empty($chart_labels)) {
    $chart_labels = [];
    $chart_data   = [];
    for ($i=6; $i>=0; $i--) { $chart_labels[] = date('M', strtotime("-{$i} months")); $chart_data[] = 0; }
}

/* ── Recent leads ── */
$recent_leads = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 6")->fetchAll();

/* ── Recent projects (for cards) ── */
$recent_projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 3")->fetchAll();

/* ── Top services by project count ── */
$top_services = $pdo->query("
    SELECT s.name, s.category, COUNT(p.id) as cnt
    FROM services s
    LEFT JOIN projects p ON p.service_id = s.id
    GROUP BY s.id
    ORDER BY cnt DESC, s.id ASC
    LIMIT 5
")->fetchAll();
$maxSvcCount = max(array_column($top_services,'cnt') ?: [1]) ?: 1;

/* ── Recent blog posts ── */
$recent_posts = $pdo->query("SELECT id,title,is_published,created_at FROM blog_posts ORDER BY created_at DESC LIMIT 6")->fetchAll();

/* ── Today's date for calendar ── */
$today_day = (int)date('j');
$today_mon = (int)date('n');
$today_yr  = (int)date('Y');

include 'includes/admin-header.php';
?>

<style>
/* ─── Dashboard-specific layout ─── */
.db-wrap {
    display: grid;
    grid-template-columns: 230px 1fr 264px;
    gap: 1rem;
    align-items: start;
}

/* ─── Section heading ─── */
.section-hd {
    font-size: .6rem; font-weight: 700; letter-spacing: .14em;
    text-transform: uppercase; color: var(--text-3);
    margin-bottom: .6rem; display: flex; align-items: center; gap: 6px;
}
.section-hd::after {
    content: ''; flex: 1; height: 1px; background: var(--border);
}

/* ─── Mini stat card (2×2 grid) ─── */
.mini-stats { display: grid; grid-template-columns: 1fr 1fr; gap: .65rem; margin-bottom: .85rem; }
.mini-card {
    background: var(--white);
    border-radius: var(--r-lg);
    padding: .9rem .95rem;
    border: 1px solid var(--border);
    box-shadow: var(--sh-xs);
    cursor: pointer;
    transition: var(--t-slow);
    position: relative; overflow: hidden;
}
.mini-card:hover { transform: translateY(-2px); box-shadow: var(--sh); }
.mini-card-icon {
    width: 34px; height: 34px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; margin-bottom: .55rem;
}
.mini-card-num { font-size: 1.5rem; font-weight: 800; color: var(--text); letter-spacing: -.04em; line-height: 1; }
.mini-card-lbl { font-size: .65rem; color: var(--text-3); font-weight: 500; margin-top: 3px; }
.mini-card-badge {
    position: absolute; top: .65rem; right: .65rem;
    font-size: .55rem; font-weight: 700; padding: 2px 6px;
    border-radius: 20px;
}

/* ─── Quick actions ─── */
.quick-grid { display: grid; grid-template-columns: 1fr 1fr; gap: .5rem; margin-bottom: .85rem; }
.quick-btn {
    display: flex; flex-direction: column; align-items: center; gap: 5px;
    padding: .65rem .4rem;
    background: var(--white); border-radius: var(--r);
    border: 1.5px dashed var(--border); color: var(--text-3);
    font-size: .65rem; font-weight: 600; text-align: center;
    cursor: pointer; transition: var(--t); text-decoration: none;
}
.quick-btn i { font-size: 1rem; }
.quick-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-soft); border-style: solid; }

/* ─── Project list cards ─── */
.proj-card {
    display: flex; align-items: center; gap: .7rem;
    padding: .7rem .85rem; margin-bottom: .5rem;
    background: var(--white); border-radius: var(--r);
    border: 1px solid var(--border); box-shadow: var(--sh-xs);
    text-decoration: none; transition: var(--t);
}
.proj-card:hover { box-shadow: var(--sh); transform: translateX(3px); border-color: rgba(230,57,70,.25); }
.proj-thumb {
    width: 48px; height: 48px; border-radius: 12px; flex-shrink: 0;
    object-fit: cover; background: var(--page-bg);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; color: var(--accent); border: 1px solid var(--border);
    overflow: hidden;
}
.proj-thumb img { width: 100%; height: 100%; object-fit: cover; }
.proj-info { flex: 1; min-width: 0; }
.proj-title { font-size: .78rem; font-weight: 700; color: var(--text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.proj-sub   { font-size: .65rem; color: var(--text-3); margin-top: 2px; }
.proj-feat  { font-size: .58rem; font-weight: 700; padding: 2px 7px; border-radius: 20px; background: var(--yellow-soft); color: #92400e; flex-shrink: 0; }

/* ─── Lead overview card ─── */
.lead-ov-card {
    background: var(--white); border-radius: var(--r-xl);
    border: 1px solid var(--border); box-shadow: var(--sh-sm);
    padding: 1.1rem 1.25rem; margin-bottom: .85rem;
}
.lead-ov-inner { display: flex; gap: 1rem; align-items: center; }
.donut-shell { position: relative; flex-shrink: 0; }
.donut-shell canvas { display: block; }
.donut-center {
    position: absolute; inset: 0;
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    pointer-events: none;
}
.donut-center .big { font-size: 1.25rem; font-weight: 800; color: var(--text); line-height: 1; }
.donut-center .sm  { font-size: .58rem; color: var(--text-3); font-weight: 500; }
.donut-legend { flex: 1; display: flex; flex-direction: column; gap: .6rem; }
.dl-row { display: flex; align-items: center; gap: 7px; }
.dl-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.dl-name { font-size: .73rem; font-weight: 600; color: var(--text); flex: 1; }
.dl-pct  { font-size: .7rem; font-weight: 700; color: var(--text-2); width: 32px; text-align: right; }
.dl-bar  { flex: 1; height: 5px; background: var(--border-2); border-radius: 5px; overflow: hidden; }
.dl-fill { height: 100%; border-radius: 5px; transition: width 1.1s var(--ease); }

/* ─── Top services ─── */
.svc-card {
    background: var(--white); border-radius: var(--r-xl);
    border: 1px solid var(--border); box-shadow: var(--sh-sm);
    padding: 1rem 1.15rem; margin-bottom: .85rem;
}
.svc-row { margin-bottom: .75rem; }
.svc-row:last-child { margin-bottom: 0; }
.svc-hd { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
.svc-name { font-size: .75rem; font-weight: 600; color: var(--text); display: flex; align-items: center; gap: 6px; }
.svc-dot  { width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0; }
.svc-cnt  { font-size: .68rem; font-weight: 700; color: var(--text-2); }
.svc-bar  { height: 5px; background: var(--border-2); border-radius: 5px; overflow: hidden; }
.svc-fill { height: 100%; border-radius: 5px; transition: width 1s var(--ease); }

/* ─── Revenue / Trend chart ─── */
.trend-card {
    background: var(--white); border-radius: var(--r-xl);
    border: 1px solid var(--border); box-shadow: var(--sh-sm);
    overflow: hidden;
}
.trend-header {
    display: flex; align-items: flex-start; justify-content: space-between;
    padding: 1.1rem 1.25rem .5rem;
}
.trend-header h3 { font-size: .9rem; font-weight: 700; color: var(--text); }
.trend-header p   { font-size: .65rem; color: var(--text-3); margin-top: 2px; }
.trend-total { font-size: 1.4rem; font-weight: 800; color: var(--text); letter-spacing: -.03em; }
.trend-total em { font-size: .68rem; font-weight: 600; font-style: normal; color: var(--green); background: var(--green-soft); padding: 2px 7px; border-radius: 20px; margin-left: 6px; }
.trend-body { padding: 0 .75rem .75rem; position: relative; height: 155px; }
.trend-foot {
    display: flex; align-items: center; gap: 1rem;
    padding: .6rem 1.25rem; border-top: 1px solid var(--border-2);
}
.trend-legend { display: flex; align-items: center; gap: 5px; font-size: .68rem; color: var(--text-3); }
.trend-legend span { width: 12px; height: 3px; border-radius: 3px; display: inline-block; }

/* ─── Calendar ─── */
.cal-card {
    background: var(--white); border-radius: var(--r-xl);
    border: 1px solid var(--border); box-shadow: var(--sh-sm);
    padding: .95rem 1rem; margin-bottom: .85rem;
}
.cal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: .7rem; }
.cal-month  { font-size: .85rem; font-weight: 700; color: var(--text); }
.cal-nav    { display: flex; gap: 3px; }
.cal-nav button {
    width: 24px; height: 24px; border-radius: 7px;
    border: 1px solid var(--border); background: var(--page-bg);
    color: var(--text-3); font-size: .6rem;
    cursor: pointer; display: flex; align-items: center; justify-content: center;
    transition: var(--t);
}
.cal-nav button:hover { background: var(--accent); color: #fff; border-color: var(--accent); }
.cal-days-hd { display: grid; grid-template-columns: repeat(7,1fr); margin-bottom: 2px; }
.cal-dh { font-size: .58rem; font-weight: 700; color: var(--text-3); text-align: center; padding: 2px 0; }
.cal-grid { display: grid; grid-template-columns: repeat(7,1fr); gap: 1px; }
.cal-d {
    font-size: .65rem; font-weight: 600; color: var(--text-2);
    text-align: center; padding: 5px 2px; border-radius: 7px;
    cursor: pointer; transition: var(--t); line-height: 1;
}
.cal-d:hover { background: var(--page-bg); }
.cal-d.today { background: var(--accent); color: #fff; font-weight: 700; box-shadow: 0 2px 8px rgba(230,57,70,.35); }
.cal-d.other { color: #c8d0e8; }

/* ─── Activity feed ─── */
.act-card {
    background: var(--white); border-radius: var(--r-xl);
    border: 1px solid var(--border); box-shadow: var(--sh-sm);
    margin-bottom: .85rem; overflow: hidden;
}
.act-card-hd {
    display: flex; justify-content: space-between; align-items: center;
    padding: .85rem 1rem .65rem; border-bottom: 1px solid var(--border-2);
}
.act-card-hd h4 { font-size: .82rem; font-weight: 700; color: var(--text); }
.act-card-hd a  { font-size: .68rem; font-weight: 600; color: var(--accent); }
.act-item {
    display: flex; align-items: center; gap: .6rem;
    padding: .58rem 1rem; border-bottom: 1px solid var(--border-2);
    transition: var(--t);
}
.act-item:last-child { border-bottom: none; }
.act-item:hover { background: #f8faff; }
.act-ico {
    width: 30px; height: 30px; border-radius: 9px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; font-size: .7rem;
}
.act-body { flex: 1; min-width: 0; }
.act-body p { font-size: .73rem; color: var(--text); font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; line-height: 1.35; }
.act-body span { font-size: .62rem; color: var(--text-3); }
.act-badge { font-size: .58rem; font-weight: 700; padding: 2px 7px; border-radius: 20px; flex-shrink: 0; white-space: nowrap; }

/* ─── Responsive ─── */
@media (max-width: 1200px) {
    .db-wrap { grid-template-columns: 200px 1fr 240px; gap: .8rem; }
}
@media (max-width: 1024px) {
    .db-wrap { grid-template-columns: 1fr 1fr; }
    .db-wrap > .db-right { grid-column: 1 / -1; display: grid; grid-template-columns: 1fr 1fr; gap: .85rem; }
}
@media (max-width: 700px) {
    .db-wrap { grid-template-columns: 1fr; }
    .db-wrap > .db-right { grid-template-columns: 1fr; }
    .mini-stats { grid-template-columns: 1fr 1fr; }
}
</style>

<?php
// Color maps
$dotColors  = ['#e63946','#4361ee','#f59e0b','#10b981','#7c3aed'];
$svcColors  = ['#e63946','#4361ee','#f59e0b','#10b981','#7c3aed'];
$statusMap  = ['new'=>['#e63946','var(--accent-soft)','var(--accent)'],'contacted'=>['#4361ee','var(--blue-soft)','var(--blue)'],'proposal'=>['#f59e0b','var(--yellow-soft)','var(--yellow)'],'closed'=>['#10b981','var(--green-soft)','var(--green)']];
$catMap     = ['websites'=>'🌐','apps'=>'📱','ai'=>'🤖','care'=>'🛠️'];
?>

<div class="db-wrap">

<!-- ════════════ LEFT COLUMN ════════════ -->
<div class="db-left">

    <p class="section-hd">Overview</p>

    <!-- 2×2 Mini Stats -->
    <div class="mini-stats">
        <div class="mini-card" onclick="location.href='leads.php'">
            <div class="mini-card-icon" style="background:var(--accent-soft);color:var(--accent);"><i class="fas fa-envelope-open-text"></i></div>
            <span class="mini-card-badge" style="background:var(--accent-soft);color:var(--accent);">NEW</span>
            <div class="mini-card-num" data-count="<?php echo $new_leads; ?>"><?php echo $new_leads; ?></div>
            <div class="mini-card-lbl">New Leads</div>
        </div>
        <div class="mini-card" onclick="location.href='projects.php'">
            <div class="mini-card-icon" style="background:var(--blue-soft);color:var(--blue);"><i class="fas fa-briefcase"></i></div>
            <span class="mini-card-badge" style="background:var(--blue-soft);color:var(--blue);">LIVE</span>
            <div class="mini-card-num" data-count="<?php echo $proj_count; ?>"><?php echo $proj_count; ?></div>
            <div class="mini-card-lbl">Projects</div>
        </div>
        <div class="mini-card" onclick="location.href='services.php'">
            <div class="mini-card-icon" style="background:var(--green-soft);color:var(--green);"><i class="fas fa-layer-group"></i></div>
            <span class="mini-card-badge" style="background:var(--green-soft);color:var(--green);">ON</span>
            <div class="mini-card-num" data-count="<?php echo $svc_count; ?>"><?php echo $svc_count; ?></div>
            <div class="mini-card-lbl">Services</div>
        </div>
        <div class="mini-card" onclick="location.href='blog.php'">
            <div class="mini-card-icon" style="background:var(--purple-soft);color:var(--purple);"><i class="fas fa-pen-nib"></i></div>
            <span class="mini-card-badge" style="background:var(--purple-soft);color:var(--purple);">PUB</span>
            <div class="mini-card-num" data-count="<?php echo $post_count; ?>"><?php echo $post_count; ?></div>
            <div class="mini-card-lbl">Blog Posts</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <p class="section-hd">Quick Add</p>
    <div class="quick-grid">
        <a class="quick-btn" href="add-project.php"><i class="fas fa-plus-circle"></i>New Project</a>
        <a class="quick-btn" href="services-add.php"><i class="fas fa-layer-group"></i>Add Service</a>
        <a class="quick-btn" href="add-blog.php"><i class="fas fa-pen-nib"></i>Write Post</a>
        <a class="quick-btn" href="add-testimonial.php"><i class="fas fa-star"></i>Add Review</a>
    </div>

    <!-- Recent Projects -->
    <p class="section-hd" style="margin-top:.3rem;">Recent Projects</p>
    <?php if ($recent_projects): foreach ($recent_projects as $p): ?>
    <a class="proj-card" href="projects-edit.php?id=<?php echo $p['id']; ?>">
        <div class="proj-thumb">
            <?php if (!empty($p['image'])): ?>
            <img src="../assets/images/<?php echo htmlspecialchars($p['image']); ?>" alt="">
            <?php else: ?><i class="fas fa-code"></i><?php endif; ?>
        </div>
        <div class="proj-info">
            <div class="proj-title"><?php echo htmlspecialchars($p['title']); ?></div>
            <div class="proj-sub"><?php echo htmlspecialchars($p['client_type'] ?: 'Project'); ?></div>
        </div>
        <?php if ($p['is_featured']): ?><span class="proj-feat">★ Featured</span><?php endif; ?>
    </a>
    <?php endforeach;
    else: ?>
    <div style="text-align:center; padding:1.25rem; background:var(--white); border-radius:var(--r-lg); border:1px solid var(--border); font-size:.78rem; color:var(--text-3);">
        No projects yet. <a href="add-project.php" style="color:var(--accent); font-weight:700;">Add one →</a>
    </div>
    <?php endif; ?>

</div><!-- /.db-left -->

<!-- ════════════ CENTER COLUMN ════════════ -->
<div class="db-center">

    <p class="section-hd">Performance</p>

    <!-- Lead Overview (Donut) -->
    <div class="lead-ov-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.8rem;">
            <div>
                <h3 style="font-size:.88rem; font-weight:700; color:var(--text);">Lead Overview</h3>
                <p style="font-size:.65rem; color:var(--text-3); margin-top:1px;">All-time breakdown by stage</p>
            </div>
            <div style="text-align:right;">
                <div style="font-size:1.3rem; font-weight:800; color:var(--text); letter-spacing:-.03em;"><?php echo $total_leads; ?></div>
                <div style="font-size:.62rem; color:var(--text-3);">total leads</div>
            </div>
        </div>
        <div class="lead-ov-inner">
            <div class="donut-shell">
                <canvas id="donutChart" width="110" height="110"></canvas>
                <div class="donut-center">
                    <span class="big"><?php echo $total_leads; ?></span>
                    <span class="sm">leads</span>
                </div>
            </div>
            <div class="donut-legend">
                <?php foreach ([['New',$statuses['new'],'#e63946'],['Contacted',$statuses['contacted'],'#4361ee'],['Proposal',$statuses['proposal'],'#f59e0b'],['Closed',$statuses['closed'],'#10b981']] as [$lbl,$val,$col]):
                    $pct = round($val / $donutTotal * 100); ?>
                <div class="dl-row">
                    <div class="dl-dot" style="background:<?php echo $col; ?>"></div>
                    <div class="dl-name"><?php echo $lbl; ?></div>
                    <div class="dl-pct"><?php echo $pct; ?>%</div>
                    <div class="dl-bar"><div class="dl-fill" data-width="<?php echo $pct; ?>" style="width:<?php echo $pct; ?>%; background:<?php echo $col; ?>;"></div></div>
                    <div style="font-size:.68rem; font-weight:700; color:var(--text-2); width:22px; text-align:right; flex-shrink:0;"><?php echo $val; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Top Services -->
    <div class="svc-card">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.7rem;">
            <h3 style="font-size:.88rem; font-weight:700; color:var(--text);">Top Services</h3>
            <a href="services.php" class="link-pill" style="font-size:.65rem; padding:.25rem .7rem;">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <?php if ($top_services): foreach ($top_services as $i => $svc):
            $col = $svcColors[$i % count($svcColors)];
            $pct = max(8, round($svc['cnt'] / $maxSvcCount * 100));
            $icon = $catMap[$svc['category']] ?? '⚙️';
        ?>
        <div class="svc-row">
            <div class="svc-hd">
                <span class="svc-name">
                    <span class="svc-dot" style="background:<?php echo $col; ?>"></span>
                    <?php echo htmlspecialchars($svc['name']); ?>
                </span>
                <span class="svc-cnt"><?php echo $svc['cnt']; ?> project<?php echo $svc['cnt']!=1?'s':''; ?></span>
            </div>
            <div class="svc-bar"><div class="svc-fill" data-width="<?php echo $pct; ?>" style="width:<?php echo $pct; ?>%; background:<?php echo $col; ?>;"></div></div>
        </div>
        <?php endforeach;
        else: ?>
        <p style="color:var(--text-3); font-size:.78rem; text-align:center; padding:.75rem 0;">Add services to see stats</p>
        <?php endif; ?>
    </div>

    <!-- Leads Trend Chart -->
    <div class="trend-card">
        <div class="trend-header">
            <div>
                <h3>Leads Trend</h3>
                <p>Monthly inquiries — last <?php echo count($chart_labels); ?> months</p>
            </div>
            <div style="text-align:right;">
                <div class="trend-total">
                    <?php echo $total_leads; ?>
                    <em>+<?php echo $new_leads; ?> new</em>
                </div>
                <div class="period-tabs" style="margin-top:5px; justify-content:flex-end;">
                    <button class="period-tab active" onclick="switchPeriod(7,this)">7M</button>
                    <button class="period-tab" onclick="switchPeriod(4,this)">4M</button>
                    <button class="period-tab" onclick="switchPeriod(2,this)">2M</button>
                </div>
            </div>
        </div>
        <div class="trend-body"><canvas id="trendChart"></canvas></div>
        <div class="trend-foot">
            <div class="trend-legend"><span style="background:var(--accent);"></span>Total Leads</div>
            <div class="trend-legend"><span style="background:var(--green);"></span>Closed</div>
            <span style="margin-left:auto; font-size:.62rem; color:var(--text-3);">Updated: <?php echo date('M j, Y'); ?></span>
        </div>
    </div>

    <!-- Recent Leads Table -->
    <div class="card" style="margin-top:.85rem;">
        <div class="card-header">
            <h3><i class="fas fa-envelope-open-text"></i>Recent Leads</h3>
            <a href="leads.php" class="link-pill">View All <i class="fas fa-arrow-right"></i></a>
        </div>
        <div class="card-body">
            <?php if ($recent_leads):
                $avatarColors = ['red','blue','green','yellow','purple','red'];
            ?>
            <table class="admin-table">
                <thead><tr><th>Contact</th><th>Service</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($recent_leads as $i => $l):
                    $c = $avatarColors[$i % count($avatarColors)];
                    $sc = ['new'=>'badge-danger','contacted'=>'badge-info','proposal'=>'badge-yellow','closed'=>'badge-green'][$l['status']] ?? 'badge-gray';
                ?>
                <tr>
                    <td>
                        <div class="cell-avatar">
                            <div class="cell-av <?php echo $c; ?>"><?php echo strtoupper(substr($l['name'],0,1)); ?></div>
                            <div>
                                <div class="cell-av-name"><?php echo htmlspecialchars($l['name']); ?></div>
                                <?php if (!empty($l['email'])): ?><div class="cell-av-sub"><?php echo htmlspecialchars($l['email']); ?></div><?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($l['service_interest'] ?: '—'); ?></td>
                    <td><span class="badge <?php echo $sc; ?>"><?php echo ucfirst($l['status']); ?></span></td>
                    <td style="color:var(--text-3); font-size:.72rem;"><?php echo date('M j, Y', strtotime($l['created_at'])); ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state"><i class="fas fa-inbox"></i><p>No leads yet. <a href="../contact.php" target="_blank">View contact page</a></p></div>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /.db-center -->

<!-- ════════════ RIGHT COLUMN ════════════ -->
<div class="db-right">

    <p class="section-hd">Schedule</p>

    <!-- Calendar -->
    <div class="cal-card">
        <div class="cal-header">
            <span class="cal-month" id="calTitle"><?php echo date('F Y'); ?></span>
            <div class="cal-nav">
                <button onclick="moveMonth(-1)" title="Previous"><i class="fas fa-chevron-left"></i></button>
                <button onclick="moveMonth(1)"  title="Next"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
        <div class="cal-days-hd"><?php foreach(['Su','Mo','Tu','We','Th','Fr','Sa'] as $d) echo "<div class='cal-dh'>$d</div>"; ?></div>
        <div class="cal-grid" id="calGrid"></div>
    </div>

    <!-- Today Summary -->
    <div style="background:var(--white); border-radius:var(--r-xl); border:1px solid var(--border); box-shadow:var(--sh-sm); padding:.85rem 1rem; margin-bottom:.85rem;">
        <div style="font-size:.7rem; font-weight:700; color:var(--text-3); margin-bottom:.6rem; text-transform:uppercase; letter-spacing:.1em;">Today — <?php echo date('D, M j'); ?></div>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:.5rem;">
            <div style="background:var(--accent-soft); border-radius:12px; padding:.6rem .75rem; cursor:pointer;" onclick="location.href='leads.php'">
                <div style="font-size:1.2rem; font-weight:800; color:var(--accent);"><?php echo $new_leads; ?></div>
                <div style="font-size:.62rem; color:var(--text-3); font-weight:500;">Pending Leads</div>
            </div>
            <div style="background:var(--blue-soft); border-radius:12px; padding:.6rem .75rem; cursor:pointer;" onclick="location.href='blog.php'">
                <div style="font-size:1.2rem; font-weight:800; color:var(--blue);"><?php echo $draft_count; ?></div>
                <div style="font-size:.62rem; color:var(--text-3); font-weight:500;">Draft Posts</div>
            </div>
            <div style="background:var(--green-soft); border-radius:12px; padding:.6rem .75rem; cursor:pointer;" onclick="location.href='testimonials.php'">
                <div style="font-size:1.2rem; font-weight:800; color:var(--green);"><?php echo $testi_count; ?></div>
                <div style="font-size:.62rem; color:var(--text-3); font-weight:500;">Active Reviews</div>
            </div>
            <div style="background:var(--purple-soft); border-radius:12px; padding:.6rem .75rem; cursor:pointer;" onclick="location.href='projects.php'">
                <div style="font-size:1.2rem; font-weight:800; color:var(--purple);"><?php echo $proj_count; ?></div>
                <div style="font-size:.62rem; color:var(--text-3); font-weight:500;">Total Projects</div>
            </div>
        </div>
    </div>

    <p class="section-hd">Activity</p>

    <!-- Latest Leads feed -->
    <div class="act-card">
        <div class="act-card-hd">
            <h4><i class="fas fa-envelope-open-text" style="color:var(--accent); margin-right:5px; font-size:.78rem;"></i>Latest Inquiries</h4>
            <a href="leads.php">View All</a>
        </div>
        <?php if ($recent_leads):
            $aColors = [['var(--accent-soft)','var(--accent)'],['var(--blue-soft)','var(--blue)'],['var(--green-soft)','var(--green)'],['var(--yellow-soft)','var(--yellow)'],['var(--purple-soft)','var(--purple)'],['var(--accent-soft)','var(--accent)']];
            $stMap   = ['new'=>['var(--accent-soft)','var(--accent)'],'contacted'=>['var(--blue-soft)','var(--blue)'],'proposal'=>['var(--yellow-soft)','var(--yellow)'],'closed'=>['var(--green-soft)','var(--green)']];
            foreach ($recent_leads as $i => $l):
                [$ibg,$ifg] = $aColors[$i % count($aColors)];
                [$sbg,$sfg] = $stMap[$l['status']] ?? ['#f1f5f9','#475569'];
        ?>
        <div class="act-item">
            <div class="act-ico" style="background:<?php echo $ibg; ?>; color:<?php echo $ifg; ?>;"><i class="fas fa-user"></i></div>
            <div class="act-body">
                <p><?php echo htmlspecialchars($l['name']); ?></p>
                <span><?php echo htmlspecialchars($l['service_interest'] ?: 'General'); ?> · <?php echo date('M j', strtotime($l['created_at'])); ?></span>
            </div>
            <span class="act-badge" style="background:<?php echo $sbg; ?>; color:<?php echo $sfg; ?>;"><?php echo ucfirst($l['status']); ?></span>
        </div>
        <?php endforeach;
        else: ?>
        <div class="empty-state" style="padding:1.25rem 0;"><i class="fas fa-inbox"></i><p>No leads yet.</p></div>
        <?php endif; ?>
    </div>

    <!-- Blog posts feed -->
    <div class="act-card">
        <div class="act-card-hd">
            <h4><i class="fas fa-pen-nib" style="color:var(--purple); margin-right:5px; font-size:.78rem;"></i>Blog Posts</h4>
            <a href="blog.php">Manage</a>
        </div>
        <?php if ($recent_posts): foreach ($recent_posts as $p): ?>
        <div class="act-item">
            <div class="act-ico" style="background:var(--purple-soft); color:var(--purple);"><i class="fas fa-pen-nib"></i></div>
            <div class="act-body">
                <p><?php echo htmlspecialchars(mb_substr($p['title'],0,40)); echo mb_strlen($p['title'])>40?'…':''; ?></p>
                <span><?php echo $p['is_published']?'Published':'Draft'; ?> · <?php echo date('M j, Y', strtotime($p['created_at'])); ?></span>
            </div>
            <?php if ($p['is_published']): ?>
            <span class="act-badge" style="background:var(--green-soft); color:var(--green);">Live</span>
            <?php else: ?>
            <span class="act-badge" style="background:var(--yellow-soft); color:var(--yellow);">Draft</span>
            <?php endif; ?>
        </div>
        <?php endforeach;
        else: ?>
        <div class="empty-state" style="padding:1.25rem 0;"><i class="fas fa-pen-nib"></i><p>No posts yet.</p></div>
        <?php endif; ?>
    </div>

</div><!-- /.db-right -->
</div><!-- /.db-wrap -->

<!-- ── SCRIPTS ── -->
<script>
Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";
Chart.defaults.color = '#7b84b0';

/* ── Donut Chart ── */
(function() {
    const ctx = document.getElementById('donutChart')?.getContext('2d');
    if (!ctx) return;
    const vals = [<?php echo implode(',', [$statuses['new'],$statuses['contacted'],$statuses['proposal'],$statuses['closed']]); ?>];
    const hasData = vals.some(v => v > 0);
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['New','Contacted','Proposal','Closed'],
            datasets: [{
                data: hasData ? vals : [1,1,1,1],
                backgroundColor: hasData ? ['#e63946','#4361ee','#f59e0b','#10b981'] : ['#e4e9f7','#e4e9f7','#e4e9f7','#e4e9f7'],
                borderWidth: 0,
                hoverOffset: 5,
                borderRadius: hasData ? 4 : 0,
                spacing: hasData ? 2 : 0,
            }]
        },
        options: {
            responsive: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: hasData,
                    backgroundColor: '#0b1437',
                    titleColor: '#fff',
                    bodyColor: 'rgba(255,255,255,.7)',
                    padding: 10,
                    cornerRadius: 10,
                    callbacks: { label: c => ' ' + c.label + ': ' + c.parsed }
                }
            }
        }
    });
})();

/* ── Trend Chart ── */
const allLabels = <?php echo json_encode($chart_labels); ?>;
const allData   = <?php echo json_encode($chart_data); ?>;
let trendChart;
(function() {
    const ctx = document.getElementById('trendChart')?.getContext('2d');
    if (!ctx) return;

    const g1 = ctx.createLinearGradient(0,0,0,150);
    g1.addColorStop(0,'rgba(230,57,70,.18)');
    g1.addColorStop(1,'rgba(230,57,70,.0)');

    const g2 = ctx.createLinearGradient(0,0,0,150);
    g2.addColorStop(0,'rgba(16,185,129,.14)');
    g2.addColorStop(1,'rgba(16,185,129,.0)');

    trendChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: allLabels,
            datasets: [
                {
                    label: 'Leads',
                    data: allData,
                    borderColor: '#e63946',
                    borderWidth: 2.5,
                    backgroundColor: g1,
                    pointBackgroundColor: '#e63946',
                    pointRadius: 3.5,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: .45,
                },
                {
                    label: 'Closed',
                    data: allData.map(v => Math.floor(v * .3)),
                    borderColor: '#10b981',
                    borderWidth: 2,
                    backgroundColor: g2,
                    pointBackgroundColor: '#10b981',
                    pointRadius: 3,
                    pointHoverRadius: 5,
                    fill: true,
                    tension: .45,
                    borderDash: [],
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0b1437',
                    titleColor: '#fff',
                    bodyColor: 'rgba(255,255,255,.7)',
                    padding: 10,
                    cornerRadius: 12,
                    displayColors: true,
                    boxWidth: 8,
                    boxHeight: 8,
                    boxPadding: 4,
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#7b84b0' }
                },
                y: {
                    grid: { color: '#f0f4ff', drawBorder: false },
                    ticks: { font: { size: 10 }, color: '#7b84b0', precision: 0 },
                    beginAtZero: true
                }
            }
        }
    });
})();

function switchPeriod(n, btn) {
    document.querySelectorAll('.period-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    if (trendChart) {
        trendChart.data.labels              = allLabels.slice(-n);
        trendChart.data.datasets[0].data    = allData.slice(-n);
        trendChart.data.datasets[1].data    = allData.slice(-n).map(v => Math.floor(v*.3));
        trendChart.update('active');
    }
}

/* ── Calendar ── */
const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
let calY = <?php echo $today_yr; ?>, calM = <?php echo $today_mon - 1; ?>;

function buildCal() {
    const now   = new Date();
    const first = new Date(calY, calM, 1).getDay();
    const days  = new Date(calY, calM+1, 0).getDate();
    const prev  = new Date(calY, calM, 0).getDate();
    document.getElementById('calTitle').textContent = MONTHS[calM] + ' ' + calY;
    let html = '';
    for (let i=first-1; i>=0; i--)       html += `<div class="cal-d other">${prev-i}</div>`;
    for (let d=1; d<=days; d++) {
        const isToday = d===now.getDate() && calM===now.getMonth() && calY===now.getFullYear();
        html += `<div class="cal-d${isToday?' today':''}">${d}</div>`;
    }
    const remain = (first + days) % 7;
    if (remain) for (let d=1; d<=7-remain; d++) html += `<div class="cal-d other">${d}</div>`;
    document.getElementById('calGrid').innerHTML = html;
}
function moveMonth(dir) { calM += dir; if(calM<0){calM=11;calY--;} else if(calM>11){calM=0;calY++;} buildCal(); }
buildCal();
</script>

<?php include 'includes/admin-footer.php'; ?>
