<?php
// Debug Refresh Statistics
require_once 'includes/config.inc.php';

header('Content-Type: text/plain; charset=utf-8');

// Start session to get account info
session_start();

echo "=== REFRESH STATISTICS DEBUG ===\n\n";

// Check session
if (isset($_SESSION['account']['ID'])) {
    $account_id = $_SESSION['account']['ID'];
    echo "✓ Account ID: " . $account_id . "\n\n";
} else {
    echo "✗ No account ID in session\n";
    exit;
}

// Check database connection
try {
    $pdo = new PDO("mysql:host=" . RL_DBHOST . ";dbname=" . RL_DBNAME, RL_DBUSER, RL_DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ Database connection OK\n\n";
} catch (Exception $e) {
    echo "✗ Database error: " . $e->getMessage() . "\n";
    exit;
}

// Check refresh history table
echo "1. CHECKING REFRESH HISTORY TABLE:\n";
$stmt = $pdo->query("SHOW TABLES LIKE 'fl_listing_refresh_history'");
if ($stmt->rowCount() > 0) {
    echo "✓ Table exists\n";
    
    // Check table structure
    $stmt = $pdo->query("DESCRIBE fl_listing_refresh_history");
    echo "Table columns:\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "✗ Table 'fl_listing_refresh_history' does not exist!\n";
}

echo "\n2. CHECKING REFRESH RECORDS:\n";
// Get all refresh records for this user
$stmt = $pdo->prepare("SELECT * FROM fl_listing_refresh_history WHERE Account_ID = ? ORDER BY Refresh_Date DESC LIMIT 10");
$stmt->execute([$account_id]);
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($records)) {
    echo "✗ No refresh records found for Account_ID: " . $account_id . "\n";
} else {
    echo "✓ Found " . count($records) . " refresh records:\n";
    foreach ($records as $record) {
        echo "  - ID: " . $record['ID'] . 
             ", Listing_ID: " . $record['Listing_ID'] . 
             ", Date: " . $record['Refresh_Date'] . 
             ", Status: " . $record['Status'] . "\n";
    }
}

echo "\n3. RUNNING STATISTICS QUERIES:\n";

// Total refresh count
$stmt = $pdo->prepare("SELECT COUNT(*) as total_refreshes FROM fl_listing_refresh_history WHERE Account_ID = ? AND Status = 'success'");
$stmt->execute([$account_id]);
$total = $stmt->fetchColumn();
echo "Total refreshes: " . $total . "\n";

// This month
$stmt = $pdo->prepare("SELECT COUNT(*) as month_refreshes FROM fl_listing_refresh_history WHERE Account_ID = ? AND Status = 'success' AND MONTH(Refresh_Date) = MONTH(NOW()) AND YEAR(Refresh_Date) = YEAR(NOW())");
$stmt->execute([$account_id]);
$month = $stmt->fetchColumn();
echo "This month: " . $month . "\n";

// Today
$stmt = $pdo->prepare("SELECT COUNT(*) as today_refreshes FROM fl_listing_refresh_history WHERE Account_ID = ? AND Status = 'success' AND DATE(Refresh_Date) = CURDATE()");
$stmt->execute([$account_id]);
$today = $stmt->fetchColumn();
echo "Today: " . $today . "\n";

echo "\n4. CHECKING CONTROLLER DATABASE ACCESS:\n";
// Test the exact query from controller
try {
    $reefless = new Reefless();
    $reefless->connect();
    $rlDb = new rlDatabase();
    
    echo "RL_DBPREFIX: '" . RL_DBPREFIX . "'\n";
    
    $sql_total = "SELECT COUNT(*) as total_refreshes FROM `fl_listing_refresh_history` WHERE `Account_ID` = {$account_id} AND `Status` = 'success'";
    echo "Controller query: " . $sql_total . "\n";
    $total_controller = $rlDb->getOne($sql_total);
    echo "Controller result: " . $total_controller . "\n";
    
    // Also test if controller gets account info properly
    if (defined('IS_LOGIN')) {
        echo "✓ IS_LOGIN is defined\n";
    } else {
        echo "✗ IS_LOGIN not defined\n";
    }
    
} catch (Exception $e) {
    echo "✗ Controller test error: " . $e->getMessage() . "\n";
    echo "Error details: " . $e->getTraceAsString() . "\n";
}

?> 