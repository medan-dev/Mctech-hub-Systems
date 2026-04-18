<?php
include '../includes/config.php';
$page_title = 'Traffic Analytics';

try {
    // ── Summary cards ──
    $today        = date('Y-m-d');
    $weekAgo      = date('Y-m-d', strtotime('-7 days'));
    $monthAgo     = date('Y-m-d', strtotime('-30 days'));

    $totalVisits  = (int)$pdo->query("SELECT COUNT(*) FROM page_visits")->fetchColumn();
    $todayVisits  = (int)$pdo->query("SELECT COUNT(*) FROM page_visits WHERE DATE(visited_at)='{$today}'")->fetchColumn();
    $weekVisits   = (int)$pdo->query("SELECT COUNT(*) FROM page_visits WHERE visited_at>='{$weekAgo}'")->fetchColumn();
    $uniqueVisits = (int)$pdo->query("SELECT COUNT(DISTINCT session_id) FROM page_visits")->fetchColumn();
    $newVisitors  = (int)$pdo->query("SELECT COUNT(*) FROM page_visits WHERE is_new_visitor=1")->fetchColumn();

    // ── Daily visits last 14 days ──
    $daily = $pdo->query("SELECT DATE(visited_at) as day, COUNT(*) as cnt FROM page_visits WHERE visited_at >= DATE_SUB(NOW(),INTERVAL 14 DAY) GROUP BY day ORDER BY day ASC")->fetchAll();
    $dailyLabels = $dailyData = [];
    // Fill all 14 days even if no data
    for ($i = 13; $i >= 0; $i--) {
        $d = date('Y-m-d', strtotime("-{$i} days"));
        $dailyLabels[] = date('M j', strtotime($d));
        $found = false;
        foreach ($daily as $row) { if ($row['day'] === $d) { $dailyData[] = (int)$row['cnt']; $found = true; break; } }
        if (!$found) $dailyData[] = 0;
    }

    // ── Devices ──
    $devices = $pdo->query("SELECT device_type, COUNT(*) as cnt FROM page_visits GROUP BY device_type ORDER BY cnt DESC")->fetchAll();

    // ── Browsers ──
    $browsers = $pdo->query("SELECT browser, COUNT(*) as cnt FROM page_visits GROUP BY browser ORDER BY cnt DESC LIMIT 6")->fetchAll();

    // ── Top pages ──
    $topPages = $pdo->query("SELECT page_url, COUNT(*) as cnt FROM page_visits GROUP BY page_url ORDER BY cnt DESC LIMIT 10")->fetchAll();

    // ── Top referrers ──
    $referrers = $pdo->query("SELECT referrer, COUNT(*) as cnt FROM page_visits WHERE referrer != '' GROUP BY referrer ORDER BY cnt DESC LIMIT 8")->fetchAll();

    // ── Countries ──
    $countries = $pdo->query("SELECT country, COUNT(*) as cnt FROM page_visits WHERE country != '' GROUP BY country ORDER BY cnt DESC LIMIT 8")->fetchAll();

    // ── Recent visitors ──
    $recent = $pdo->query("SELECT * FROM page_visits ORDER BY visited_at DESC LIMIT 25")->fetchAll();

    // ── OS breakdown ──
    $oss = $pdo->query("SELECT os, COUNT(*) as cnt FROM page_visits GROUP BY os ORDER BY cnt DESC LIMIT 6")->fetchAll();

    $tableExists = true;
} catch (Exception $e) {
    $tableExists = false;
    $err = $e->getMessage();
}

include 'includes/admin-header.php';
?>

<style>
.analytics-grid { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem; }
.analytics-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1rem; }
.a-card { background:var(--white); border-radius:var(--r-xl); border:1px solid var(--border); box-shadow:var(--sh-sm); overflow:hidden; }
.a-card-hd { display:flex; align-items:center; justify-content:space-between; padding:.85rem 1.1rem; border-bottom:1px solid var(--border-2); }
.a-card-hd h3 { font-size:.82rem; font-weight:700; color:var(--text); margin:0; display:flex; align-items:center; gap:7px; }
.a-card-body  { padding:1.1rem; }
.a-card-body.no-pad { padding:0; }

/* Over-arching chart bar */
.mini-bar { height:6px; border-radius:4px; background:var(--page-bg); overflow:hidden; margin-top:5px; }
.mini-bar-fill { height:100%; border-radius:4px; transition:.6s ease; }

/* Recent visitor row */
.vis-row { display:flex; align-items:center; gap:.65rem; padding:.5rem .9rem; border-bottom:1px solid var(--border-2); font-size:.72rem; }
.vis-row:last-child { border-bottom:none; }
.vis-icon { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.7rem; flex-shrink:0; }

/* Realtime pulse */
@keyframes pulse { 0%,100%{opacity:1;} 50%{opacity:.35;} }
.live-dot { width:8px; height:8px; border-radius:50%; background:var(--green); animation:pulse 1.8s infinite; display:inline-block; margin-right:4px; }
</style>

<?php if (!$tableExists): ?>
<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i>
    Tracking tables not set up yet. <a href="../setup-tracking-tables.php" style="font-weight:700;color:var(--accent);">Run setup →</a>
</div>
<?php include 'includes/admin-footer.php'; exit; ?>
<?php endif; ?>

<!-- ── Summary Stat Cards ── -->
<div style="display:grid; grid-template-columns:repeat(5,1fr); gap:.85rem; margin-bottom:1.1rem;">
    <?php
    $stats = [
        ['fas fa-eye',        'var(--blue)',   '--blue-soft',   $totalVisits,  'Total Page Views', ''],
        ['fas fa-users',      'var(--accent)', '--accent-soft', $uniqueVisits, 'Unique Sessions',  ''],
        ['fas fa-calendar-day','var(--green)', '--green-soft',  $todayVisits,  'Today',            ''],
        ['fas fa-calendar-week','var(--purple)','--purple-soft',$weekVisits,   'This Week',        ''],
        ['fas fa-star',       'var(--yellow)', '--yellow-soft', $newVisitors,  'New Visitors',     ''],
    ];
    foreach ($stats as [$icon, $col, $soft, $val, $lbl]) : ?>
    <div style="background:var(--white); border-radius:var(--r-xl); border:1px solid var(--border); box-shadow:var(--sh-sm); padding:.9rem 1.1rem;">
        <div style="display:flex; align-items:flex-start; justify-content:space-between;">
            <div style="width:38px;height:38px;border-radius:11px;background:var(<?php echo $soft;?>);color:<?php echo $col;?>;display:flex;align-items:center;justify-content:center;font-size:.8rem;"><i class="<?php echo $icon; ?>"></i></div>
        </div>
        <div style="font-size:1.55rem; font-weight:800; color:var(--text); margin:.5rem 0 2px; line-height:1;" data-count="<?php echo $val; ?>"><?php echo number_format($val); ?></div>
        <div style="font-size:.65rem; color:var(--text-3); font-weight:600; text-transform:uppercase; letter-spacing:.5px;"><?php echo $lbl; ?></div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ── Daily Visits Chart (full width) ── -->
<div class="a-card" style="margin-bottom:1rem;">
    <div class="a-card-hd">
        <h3><i class="fas fa-chart-area" style="color:var(--blue);"></i> Daily Page Views — Last 14 Days</h3>
        <span style="font-size:.7rem; color:var(--text-3);"><span class="live-dot"></span> Live tracking active</span>
    </div>
    <div class="a-card-body" style="padding:1rem 1.25rem;">
        <canvas id="dailyChart" height="80"></canvas>
    </div>
</div>

<!-- ── 3 columns: Devices, Browsers, OS ── -->
<div class="analytics-grid" style="margin-bottom:1rem;">

    <!-- Devices Donut -->
    <div class="a-card">
        <div class="a-card-hd"><h3><i class="fas fa-mobile-alt" style="color:var(--accent);"></i> Devices</h3></div>
        <div class="a-card-body" style="display:flex; flex-direction:column; align-items:center; gap:1rem;">
            <div style="position:relative; width:140px; height:140px;">
                <canvas id="deviceChart" width="140" height="140"></canvas>
                <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; flex-direction:column;">
                    <div style="font-size:1.3rem; font-weight:800; color:var(--text); line-height:1;"><?php echo number_format($totalVisits); ?></div>
                    <div style="font-size:.6rem; color:var(--text-3);">total</div>
                </div>
            </div>
            <div style="width:100%;">
                <?php
                $devColors = ['desktop'=>'#4361ee','mobile'=>'#e63946','tablet'=>'#f59e0b'];
                $devTotal  = max(1, array_sum(array_column($devices, 'cnt')));
                foreach ($devices as $d) :
                    $pct = round($d['cnt']/$devTotal*100);
                    $clr = $devColors[$d['device_type']] ?? '#7c3aed';
                ?>
                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:.4rem;">
                    <span style="font-size:.72rem; color:var(--text-2); text-transform:capitalize;"><?php echo htmlspecialchars($d['device_type']); ?></span>
                    <span style="font-size:.72rem; font-weight:700; color:var(--text);"><?php echo $pct; ?>%</span>
                </div>
                <div class="mini-bar" style="margin-bottom:.6rem;"><div class="mini-bar-fill" style="width:<?php echo $pct; ?>%; background:<?php echo $clr; ?>" data-width="<?php echo $pct; ?>"></div></div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Browsers -->
    <div class="a-card">
        <div class="a-card-hd"><h3><i class="fas fa-globe" style="color:var(--green);"></i> Browsers</h3></div>
        <div class="a-card-body no-pad">
            <?php
            $brColors = ['Chrome'=>'#4285F4','Firefox'=>'#FF7139','Safari'=>'#006CFF','Edge'=>'#0078D7','Opera'=>'#FF1B2D','Other'=>'#94a3b8'];
            $brTotal  = max(1, array_sum(array_column($browsers, 'cnt')));
            foreach ($browsers as $b) :
                $pct = round($b['cnt']/$brTotal*100);
                $clr = $brColors[$b['browser']] ?? '#94a3b8';
            ?>
            <div style="display:flex; align-items:center; gap:.65rem; padding:.6rem .9rem; border-bottom:1px solid var(--border-2);">
                <div style="width:28px; height:28px; border-radius:8px; background:<?php echo $clr; ?>22; display:flex; align-items:center; justify-content:center; font-size:.65rem; font-weight:800; color:<?php echo $clr; ?>; flex-shrink:0;"><?php echo strtoupper(substr($b['browser'],0,2)); ?></div>
                <div style="flex:1;">
                    <div style="display:flex; justify-content:space-between; font-size:.72rem; margin-bottom:3px;">
                        <span style="font-weight:600; color:var(--text);"><?php echo htmlspecialchars($b['browser']); ?></span>
                        <span style="color:var(--text-3);"><?php echo $b['cnt']; ?></span>
                    </div>
                    <div class="mini-bar"><div class="mini-bar-fill" style="width:<?php echo $pct; ?>%; background:<?php echo $clr; ?>" data-width="<?php echo $pct; ?>"></div></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- OS -->
    <div class="a-card">
        <div class="a-card-hd"><h3><i class="fas fa-desktop" style="color:var(--purple);"></i> Operating Systems</h3></div>
        <div class="a-card-body no-pad">
            <?php
            $osColors = ['Windows'=>'#0078D7','macOS'=>'#555555','Android'=>'#3DDC84','iOS'=>'#007AFF','Linux'=>'#FCC624','Other'=>'#94a3b8'];
            $osTotal  = max(1, array_sum(array_column($oss, 'cnt')));
            foreach ($oss as $o) :
                $pct = round($o['cnt']/$osTotal*100);
                $clr = $osColors[$o['os']] ?? '#94a3b8';
            ?>
            <div style="display:flex; align-items:center; gap:.65rem; padding:.6rem .9rem; border-bottom:1px solid var(--border-2);">
                <div style="width:28px; height:28px; border-radius:8px; background:<?php echo $clr; ?>22; display:flex; align-items:center; justify-content:center; font-size:.65rem; font-weight:800; color:<?php echo $clr; ?>; flex-shrink:0;"><?php echo strtoupper(substr($o['os'],0,2)); ?></div>
                <div style="flex:1;">
                    <div style="display:flex; justify-content:space-between; font-size:.72rem; margin-bottom:3px;">
                        <span style="font-weight:600; color:var(--text);"><?php echo htmlspecialchars($o['os']); ?></span>
                        <span style="color:var(--text-3);"><?php echo $o['cnt']; ?></span>
                    </div>
                    <div class="mini-bar"><div class="mini-bar-fill" style="width:<?php echo $pct; ?>%; background:<?php echo $clr; ?>" data-width="<?php echo $pct; ?>"></div></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- ── 2 columns: Top Pages + Countries ── -->
<div class="analytics-grid-2" style="margin-bottom:1rem;">
    <!-- Top Pages -->
    <div class="a-card">
        <div class="a-card-hd"><h3><i class="fas fa-file-alt" style="color:var(--blue);"></i> Top Pages</h3></div>
        <div class="a-card-body no-pad">
            <?php foreach ($topPages as $p) :
                $label = $p['page_url'] === '/' || $p['page_url'] === '/mctech-hub/' ? 'Homepage' : ltrim(str_replace(['/mctech-hub/','.php'], ['/', ''], $p['page_url']), '/');
                $maxPV = $topPages[0]['cnt'] ?? 1;
                $pct   = round($p['cnt'] / $maxPV * 100);
            ?>
            <div style="padding:.55rem .9rem; border-bottom:1px solid var(--border-2);">
                <div style="display:flex; justify-content:space-between; align-items:baseline; font-size:.72rem; margin-bottom:3px;">
                    <span style="font-weight:600; color:var(--text); max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;" title="<?php echo htmlspecialchars($p['page_url']); ?>"><?php echo htmlspecialchars(ucwords(str_replace('-',' ',$label))); ?></span>
                    <span style="color:var(--text-3); flex-shrink:0; margin-left:.5rem;"><?php echo number_format($p['cnt']); ?> views</span>
                </div>
                <div class="mini-bar"><div class="mini-bar-fill" style="width:<?php echo $pct; ?>%; background:var(--blue);" data-width="<?php echo $pct; ?>"></div></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Top Countries -->
    <div class="a-card">
        <div class="a-card-hd"><h3><i class="fas fa-globe-africa" style="color:var(--green);"></i> Top Countries</h3></div>
        <div class="a-card-body no-pad">
            <?php if ($countries): $maxC = $countries[0]['cnt'] ?? 1;
            foreach ($countries as $c) : $pct = round($c['cnt']/$maxC*100); ?>
            <div style="padding:.55rem .9rem; border-bottom:1px solid var(--border-2);">
                <div style="display:flex; justify-content:space-between; font-size:.72rem; margin-bottom:3px;">
                    <span style="font-weight:600; color:var(--text);"><?php echo htmlspecialchars($c['country'] ?: 'Unknown'); ?></span>
                    <span style="color:var(--text-3);"><?php echo number_format($c['cnt']); ?></span>
                </div>
                <div class="mini-bar"><div class="mini-bar-fill" style="width:<?php echo $pct; ?>%; background:var(--green);" data-width="<?php echo $pct; ?>"></div></div>
            </div>
            <?php endforeach; else: ?>
            <div class="empty-state" style="padding:1.5rem;"><i class="fas fa-globe"></i><p>No geo data yet. Visitors from real IPs will show here.</p></div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- ── Referrers + Recent Visitors ── -->
<div class="analytics-grid-2">
    <!-- Referrers -->
    <div class="a-card">
        <div class="a-card-hd"><h3><i class="fas fa-share-alt" style="color:var(--yellow);"></i> Traffic Sources</h3></div>
        <div class="a-card-body no-pad">
            <?php if ($referrers): $maxR = $referrers[0]['cnt'] ?? 1;
            foreach ($referrers as $r) :
                $host = @parse_url($r['referrer'], PHP_URL_HOST) ?: 'Direct/Unknown';
                $host = str_replace('www.', '', $host);
                $pct  = round($r['cnt']/$maxR*100);
            ?>
            <div style="padding:.55rem .9rem; border-bottom:1px solid var(--border-2);">
                <div style="display:flex; justify-content:space-between; font-size:.72rem; margin-bottom:3px;">
                    <span style="font-weight:600; color:var(--text); max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;"><?php echo htmlspecialchars($host); ?></span>
                    <span style="color:var(--text-3);"><?php echo number_format($r['cnt']); ?></span>
                </div>
                <div class="mini-bar"><div class="mini-bar-fill" style="width:<?php echo $pct; ?>%; background:var(--yellow);" data-width="<?php echo $pct; ?>"></div></div>
            </div>
            <?php endforeach; else: ?>
            <div class="empty-state" style="padding:1.5rem;"><i class="fas fa-share-alt"></i><p>No referrers yet — visitors came directly or from hidden sources.</p></div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Visitors -->
    <div class="a-card">
        <div class="a-card-hd">
            <h3><i class="fas fa-users" style="color:var(--accent);"></i> Recent Visitors</h3>
            <span style="font-size:.68rem; color:var(--green); font-weight:700; display:flex; align-items:center; gap:4px;"><span class="live-dot"></span> Live Feed</span>
        </div>
        <div class="a-card-body no-pad" style="max-height:340px; overflow-y:auto;">
            <?php foreach ($recent as $v) :
                $devIcon = match($v['device_type']) { 'mobile'=>'fas fa-mobile-alt', 'tablet'=>'fas fa-tablet-alt', default=>'fas fa-desktop' };
                $devCol  = match($v['device_type']) { 'mobile'=>'var(--accent)', 'tablet'=>'var(--yellow)', default=>'var(--blue)' };
                $page = ltrim(str_replace(['/mctech-hub/', '.php'], ['/', ''], $v['page_url']), '/') ?: 'Home';
            ?>
            <div class="vis-row">
                <div class="vis-icon" style="background:<?php echo $devCol; ?>18; color:<?php echo $devCol; ?>;"><i class="<?php echo $devIcon; ?>"></i></div>
                <div style="flex:1; min-width:0;">
                    <div style="font-weight:600; color:var(--text); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"><?php echo htmlspecialchars(ucwords(str_replace('-',' ',$page))); ?></div>
                    <div style="color:var(--text-3); font-size:.65rem;"><?php echo htmlspecialchars($v['browser'] . ' · ' . $v['os'] . ($v['country'] ? ' · '.$v['country'] : '')); ?></div>
                </div>
                <div style="flex-shrink:0; text-align:right; color:var(--text-3);">
                    <?php echo $v['is_new_visitor'] ? '<span style="font-size:.6rem;background:var(--green-soft);color:var(--green);padding:1px 6px;border-radius:20px;font-weight:700;">NEW</span>' : ''; ?>
                    <div style="font-size:.65rem; margin-top:2px;"><?php echo date('g:i A', strtotime($v['visited_at'])); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
// Daily visits chart
const dCtx = document.getElementById('dailyChart');
if (dCtx) {
    new Chart(dCtx, {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($dailyLabels); ?>,
            datasets: [{
                label: 'Page Views',
                data:  <?php echo json_encode($dailyData); ?>,
                backgroundColor: (ctx) => {
                    const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 200);
                    g.addColorStop(0, 'rgba(67,97,238,0.85)');
                    g.addColorStop(1, 'rgba(67,97,238,0.1)');
                    return g;
                },
                borderColor: '#4361ee',
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: true,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y} views` } } },
            scales: {
                x: { grid: { display: false }, ticks: { font: { size: 10, family: "'Plus Jakarta Sans', sans-serif" }, color: '#7b84b0' } },
                y: { grid: { color: '#f0f4ff' }, ticks: { font: { size: 10 }, color: '#7b84b0', precision: 0 }, beginAtZero: true }
            }
        }
    });
}

// Devices donut
const devCtx = document.getElementById('deviceChart');
if (devCtx) {
    const devData  = <?php echo json_encode(array_column($devices, 'cnt')); ?>;
    const devLabels = <?php echo json_encode(array_column($devices, 'device_type')); ?>;
    new Chart(devCtx, {
        type: 'doughnut',
        data: {
            labels: devLabels,
            datasets: [{ data: devData.length ? devData : [1], backgroundColor: ['#4361ee','#e63946','#f59e0b','#10b981'], borderWidth: 3, borderColor: '#fff', hoverOffset: 6 }]
        },
        options: {
            cutout: '72%', responsive: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ` ${ctx.label}: ${ctx.parsed}` } } }
        }
    });
}

// Auto-refresh every 60 seconds
setTimeout(() => location.reload(), 60000);
</script>

<?php include 'includes/admin-footer.php'; ?>
