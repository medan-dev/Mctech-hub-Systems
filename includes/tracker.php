<?php
/**
 * Mctech-hub Systems — Visitor Tracker
 * Lightweight middleware included on every public page via header.php
 * Tracks: page, referrer, device, browser, OS, IP, geo (cached per session)
 */
function mct_trackVisit($pdo) {
    // Skip admin pages, AJAX calls, bots
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    if (preg_match('/bot|crawl|spider|slurp|curl|wget|python|scrapy|headless/i', $ua)) return;
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) return;
    if (strpos($_SERVER['REQUEST_URI'] ?? '', '/admin/') !== false) return;

    // ── Parse Browser ──
    if      (stripos($ua, 'Edg/')     !== false) $browser = 'Edge';
    elseif  (stripos($ua, 'OPR/')     !== false) $browser = 'Opera';
    elseif  (stripos($ua, 'Chrome/')  !== false) $browser = 'Chrome';
    elseif  (stripos($ua, 'Firefox/') !== false) $browser = 'Firefox';
    elseif  (stripos($ua, 'Safari/')  !== false) $browser = 'Safari';
    else                                          $browser = 'Other';

    // ── Parse OS ──
    if      (stripos($ua, 'Windows')   !== false) $os = 'Windows';
    elseif  (stripos($ua, 'Android')   !== false) $os = 'Android';
    elseif  (stripos($ua, 'iPhone')    !== false) $os = 'iOS';
    elseif  (stripos($ua, 'iPad')      !== false) $os = 'iOS';
    elseif  (stripos($ua, 'Macintosh') !== false) $os = 'macOS';
    elseif  (stripos($ua, 'Linux')     !== false) $os = 'Linux';
    else                                           $os = 'Other';

    // ── Device type ──
    if      (preg_match('/mobile|android|iphone/i', $ua)) $device = 'mobile';
    elseif  (preg_match('/tablet|ipad/i',  $ua))           $device = 'tablet';
    else                                                    $device = 'desktop';

    // ── IP Address (handles proxies + CloudFlare) ──
    $ip = $_SERVER['HTTP_CF_CONNECTING_IP']
       ?? $_SERVER['HTTP_X_FORWARDED_FOR']
       ?? $_SERVER['HTTP_X_REAL_IP']
       ?? $_SERVER['REMOTE_ADDR']
       ?? '0.0.0.0';
    $ip = trim(explode(',', $ip)[0]);

    // ── Geo (cached in session, looked up once per visitor session) ──
    $country = $_SESSION['_mct_country'] ?? null;
    $city    = $_SESSION['_mct_city']    ?? null;
    if ($country === null && !in_array($ip, ['127.0.0.1', '::1', '0.0.0.0'])) {
        try {
            $ctx = stream_context_create(['http' => ['timeout' => 1.5, 'ignore_errors' => true]]);
            $geo = @file_get_contents("http://ip-api.com/json/{$ip}?fields=country,city,status", false, $ctx);
            if ($geo) {
                $d = json_decode($geo, true);
                $country = ($d['status'] ?? '') === 'success' ? ($d['country'] ?? '') : '';
                $city    = ($d['status'] ?? '') === 'success' ? ($d['city']    ?? '') : '';
            }
        } catch (Exception $e) {}
        $_SESSION['_mct_country'] = $country ?? '';
        $_SESSION['_mct_city']    = $city    ?? '';
    }

    // ── Is new visitor this session? ──
    $isNew = empty($_SESSION['_mct_visited']);
    $_SESSION['_mct_visited'] = true;

    // ── Page data ──
    $uri      = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');  // strip query string
    $referrer = substr($_SERVER['HTTP_REFERER'] ?? '', 0, 500);

    // ── Write to DB (non-fatal) ──
    try {
        $pdo->prepare("INSERT INTO page_visits
            (session_id, page_url, referrer, ip_address, user_agent, browser, os, device_type, country, city, is_new_visitor)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([
                session_id(),
                substr($uri, 0, 500),
                $referrer,
                $ip,
                substr($ua, 0, 500),
                $browser,
                $os,
                $device,
                $country ?? '',
                $city    ?? '',
                $isNew ? 1 : 0,
            ]);
    } catch (Exception $e) { /* silently fail — tracking never breaks the site */ }
}
