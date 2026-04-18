<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', '/mctech-hub/');

$DB_HOST = 'localhost';
$DB_USER = 'root';        // CHANGE THIS to your DB username
$DB_PASS = '';            // CHANGE THIS to your DB password  
$DB_NAME = 'if0_41690432_mctech_hub';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Core Functions
function getServices($pdo, $featured = null) {
    $sql = "SELECT * FROM services";
    $params = [];
    
    if ($featured !== null) {
        $sql .= " WHERE is_featured = ?";
        $params[] = $featured;
    }
    $sql .= " ORDER BY order_num ASC, id ASC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getProjects($pdo, $limit = null, $featured = null) {
    $sql = "SELECT p.*, s.name as service_name FROM projects p 
            LEFT JOIN services s ON p.service_id = s.id";
    $params = [];
    
    if ($featured !== null) {
        $sql .= " WHERE p.is_featured = ?";
        $params[] = $featured;
    }
    $sql .= " ORDER BY p.created_at DESC";
    
    if ($limit) $sql .= " LIMIT $limit";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getTestimonials($pdo, $limit = 6) {
    $sql = "SELECT * FROM testimonials WHERE is_active = 1 ORDER BY created_at DESC LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

function getBlogPosts($pdo, $limit = 6, $published = true) {
    $sql = "SELECT * FROM blog_posts";
    $params = [];
    
    if ($published) {
        $sql .= " WHERE is_published = 1";
    }
    $sql .= " ORDER BY published_at DESC, created_at DESC";
    
    if ($limit) $sql .= " LIMIT $limit";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function getRecentLeads($pdo, $limit = 5) {
    $sql = "SELECT * FROM contacts WHERE status = 'new' ORDER BY created_at DESC LIMIT ?";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
}

// Security helper
function sanitize($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
