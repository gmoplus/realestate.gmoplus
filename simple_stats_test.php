<?php
// Simple Stats Test
require_once 'includes/config.inc.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['account']['ID'])) {
    echo json_encode(['error' => 'Not logged in']);
    exit;
}

$account_id = $_SESSION['account']['ID'];

try {
    $pdo = new PDO("mysql:host=" . RL_DBHOST . ";dbname=" . RL_DBNAME, RL_DBUSER, RL_DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get fresh statistics
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM fl_listing_refresh_history WHERE Account_ID = ? AND Status = 'success'");
    $stmt->execute([$account_id]);
    $total = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM fl_listing_refresh_history WHERE Account_ID = ? AND Status = 'success' AND MONTH(Refresh_Date) = MONTH(NOW()) AND YEAR(Refresh_Date) = YEAR(NOW())");
    $stmt->execute([$account_id]);
    $month = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM fl_listing_refresh_history WHERE Account_ID = ? AND Status = 'success' AND YEARWEEK(Refresh_Date, 1) = YEARWEEK(NOW(), 1)");
    $stmt->execute([$account_id]);
    $week = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM fl_listing_refresh_history WHERE Account_ID = ? AND Status = 'success' AND DATE(Refresh_Date) = CURDATE()");
    $stmt->execute([$account_id]);
    $today = $stmt->fetchColumn();
    
    echo json_encode([
        'success' => true,
        'account_id' => $account_id,
        'stats' => [
            'total' => (int)$total,
            'month' => (int)$month,
            'week' => (int)$week,
            'today' => (int)$today
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?> 