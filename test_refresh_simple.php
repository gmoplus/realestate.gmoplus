<?php
// Simple Refresh Test - Direct without framework
session_start();

echo "Content-Type: application/json\n\n";

try {
    // Simulate POST data
    $_POST['mode'] = 'refreshListing';
    $_POST['listing_id'] = 65;
    $_POST['listing_type'] = 'konut';
    
    echo "=== SIMPLE REFRESH TEST ===\n";
    echo "Session ID: " . session_id() . "\n";
    echo "Session account: " . (isset($_SESSION['account']) ? 'EXISTS' : 'NO') . "\n";
    
    if (isset($_SESSION['account'])) {
        echo "Account ID: " . $_SESSION['account']['ID'] . "\n";
        echo "Username: " . $_SESSION['account']['Username'] . "\n";
    }
    
    // Try to include config
    if (file_exists('includes/config.inc.php')) {
        include_once('includes/config.inc.php');
        echo "Config loaded: YES\n";
        echo "Database: " . RL_DBNAME . "\n";
    } else {
        echo "Config loaded: NO\n";
    }
    
    // Try basic database connection
    if (defined('RL_DBHOST')) {
        try {
            $pdo = new PDO("mysql:host=" . RL_DBHOST . ";dbname=" . RL_DBNAME, RL_DBUSER, RL_DBPASS);
            echo "Database connection: SUCCESS\n";
            
            // Test query
            $stmt = $pdo->query("SELECT COUNT(*) FROM fl_listing_refresh_rules WHERE Status = 'active'");
            $count = $stmt->fetchColumn();
            echo "Refresh rules count: " . $count . "\n";
            
        } catch (Exception $e) {
            echo "Database connection: FAILED - " . $e->getMessage() . "\n";
        }
    }
    
    echo "\n=== TEST COMPLETED ===\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "File: " . $e->getFile() . "\n";
}
?> 