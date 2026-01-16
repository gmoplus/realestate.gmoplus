<?php
// Check table structure for realestate site
require_once 'includes/config.inc.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    $pdo = new PDO("mysql:host=" . RL_DBHOST . ";dbname=" . RL_DBNAME, RL_DBUSER, RL_DBPASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== REALESTATE TABLE STRUCTURE CHECK ===\n";
    echo "Database: " . RL_DBNAME . "\n\n";
    
    // Check listings table structure
    echo "1. LISTINGS TABLE COLUMNS:\n";
    $stmt = $pdo->query("DESCRIBE fl_listings");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    // Check if there's a similar column
    echo "\n2. COLUMNS CONTAINING 'type':\n";
    $stmt = $pdo->query("SHOW COLUMNS FROM fl_listings LIKE '%type%'");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    
    // Check actual data
    echo "\n3. SAMPLE LISTING DATA:\n";
    $stmt = $pdo->query("SELECT ID, Category_ID, Status FROM fl_listings WHERE ID = 65 LIMIT 1");
    $listing = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($listing) {
        echo "  Listing 65 exists:\n";
        foreach ($listing as $key => $value) {
            echo "    $key: $value\n";
        }
    } else {
        echo "  Listing 65 not found\n";
    }
    
    // Check refresh tables
    echo "\n4. REFRESH TABLES CHECK:\n";
    
    // Check if refresh tables exist
    $stmt = $pdo->query("SHOW TABLES LIKE '%refresh%'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (empty($tables)) {
        echo "  ⚠️  Refresh tables NOT found! Need to create them.\n";
    } else {
        echo "  ✓ Refresh tables found:\n";
        foreach ($tables as $table) {
            echo "    - $table\n";
        }
    }
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?> 