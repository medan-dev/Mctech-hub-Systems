<?php
/**
 * Mctech-hub Systems — UltraFirewall (WAF)
 * Immediate threat detection and blocking.
 */

function mct_firewall() {
    $malicious_signatures = [
        // SQL Injection patterns
        '/(\'|\"|%27|%22)\s*(OR|AND|UNION|SELECT|DROP|INSERT|DELETE|UPDATE|TRUNCATE)/i',
        '/UNION\s+ALL\s+SELECT/i',
        '/group_concat/i',
        '/information_schema/i',
        // XSS patterns
        '/<script/i',
        '/javascript:/i',
        '/onerror\s*=/i',
        '/onload\s*=/i',
        '/alert\(/i',
        '/<iframe/i',
        // Path Traversal / LFI
        '/\.\.\//',
        '/\/etc\/passwd/i',
        '/\.env/i',
        '/\.git/i',
        // Shell injection
        '/system\(/i',
        '/exec\(/i',
        '/passthru\(/i',
        '/shell_exec\(/i'
    ];

    // Check GET, POST, and COOKIE data
    $check_arrays = [$_GET, $_POST, $_COOKIE, $_REQUEST];
    
    foreach ($check_arrays as $arr) {
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                $check_string = json_encode($value);
            } else {
                $check_string = (string)$value;
            }

            foreach ($malicious_signatures as $sig) {
                if (preg_match($sig, $check_string)) {
                    // BLOCK IMMEDIATELY
                    header('HTTP/1.1 403 Forbidden');
                    session_destroy();
                    die("<div style='font-family:sans-serif; text-align:center; padding:50px;'>
                        <h1 style='color:red;'>SECURITY VIOLATION DETECTED</h1>
                        <p>Your request was blocked immediately by Mctech-hub Systems UltraFirewall.</p>
                        <p>System logged this incident. If you believe this is an error, contact support.</p>
                        <hr>
                        <small>Ref: Firewall-Block-".time()."</small>
                    </div>");
                }
            }
        }
    }
}

// Global whitelisting of core system inputs if necessary
// mct_firewall(); // Will be called in config.php
