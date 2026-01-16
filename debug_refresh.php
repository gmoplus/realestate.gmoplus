<?php
// DEBUG: Realestate Refresh System Test
include_once('includes/config.inc.php');

// Debug before control.inc.php
echo "BEFORE control.inc.php:\n";
echo "  Session account: " . (isset($_SESSION['account']) ? 'EXISTS' : 'NO') . "\n";
echo "  IS_LOGIN defined: " . (defined('IS_LOGIN') ? 'YES' : 'NO') . "\n";

include_once('includes/control.inc.php');

// Debug after control.inc.php
echo "AFTER control.inc.php:\n";
echo "  IS_LOGIN defined: " . (defined('IS_LOGIN') ? 'YES' : 'NO') . "\n";
echo "  account_info exists: " . (isset($account_info) ? 'YES' : 'NO') . "\n";
if (isset($account_info)) {
    echo "  Account ID: " . $account_info['ID'] . "\n";
}

header('Content-Type: text/plain; charset=utf-8');

echo "=== REALESTATE REFRESH DEBUG ===\n";
echo "Time: " . date('Y-m-d H:i:s') . "\n\n";

// Check if user is logged in
echo "1. LOGIN CHECK:\n";
echo "  IS_LOGIN defined: " . (defined('IS_LOGIN') ? 'YES' : 'NO') . "\n";
echo "  Session ID: " . session_id() . "\n";
echo "  Session data: " . print_r($_SESSION, true) . "\n";
echo "  Account info: " . print_r($account_info, true) . "\n";

if (defined('IS_LOGIN')) {
    echo "✓ User is logged in\n";
    echo "  Account ID: " . $account_info['ID'] . "\n";
    echo "  Username: " . $account_info['Username'] . "\n";
} else {
    echo "✗ User is NOT logged in\n";
    echo "  This is the problem! Refresh only works for logged in users.\n";
    echo "  Debugging session issue...\n";
    
    // Check if login info exists in session
    if (isset($_SESSION['login']) && $_SESSION['login']) {
        echo "  But session shows login=true, checking account_info...\n";
        if (isset($_SESSION['account_info'])) {
            echo "  Account info in session: " . print_r($_SESSION['account_info'], true) . "\n";
        }
    }
    
    // Don't exit, continue debugging
}

// Check if class exists
echo "\n2. CLASS CHECK:\n";
if (class_exists('rlListingRefresh')) {
    echo "✓ rlListingRefresh class loaded\n";
} else {
    echo "✗ rlListingRefresh class NOT loaded\n";
    echo "  Trying to load manually...\n";
    if (file_exists('includes/classes/rlListingRefresh.class.php')) {
        include_once('includes/classes/rlListingRefresh.class.php');
        if (class_exists('rlListingRefresh')) {
            echo "✓ rlListingRefresh class loaded manually\n";
        } else {
            echo "✗ Still failed to load class\n";
        }
    } else {
        echo "✗ rlListingRefresh.class.php file not found\n";
    }
}

// Check refresh rules
echo "\n3. REFRESH RULES CHECK:\n";
try {
    $sql = "SELECT * FROM fl_listing_refresh_rules WHERE Status = 'active' ORDER BY Listing_Type";
    $rules = $rlDb->getAll($sql);
    echo "✓ Found " . count($rules) . " active refresh rules:\n";
    foreach ($rules as $rule) {
        echo "  - {$rule['Listing_Type']}: {$rule['Max_Refreshes']} times per {$rule['Period_Days']} days\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Check user's listings
echo "\n4. USER LISTINGS CHECK:\n";
try {
    $sql = "SELECT ID, Listing_type, Category_ID, Status FROM fl_listings 
            WHERE Account_ID = {$account_info['ID']} 
            ORDER BY Date_edit DESC LIMIT 5";
    $listings = $rlDb->getAll($sql);
    echo "✓ Found " . count($listings) . " listings for this user:\n";
    foreach ($listings as $listing) {
        echo "  - ID: {$listing['ID']}, Type: {$listing['Listing_type']}, Status: {$listing['Status']}\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test refresh system
echo "\n5. REFRESH SYSTEM TEST:\n";
try {
    $refreshSystem = new rlListingRefresh();
    echo "✓ Refresh system initialized\n";
    
    // Test with first active listing
    $sql = "SELECT ID, Listing_type FROM fl_listings 
            WHERE Account_ID = {$account_info['ID']} AND Status = 'active' 
            LIMIT 1";
    $testListing = $rlDb->getRow($sql);
    
    if ($testListing) {
        echo "✓ Testing with listing ID: {$testListing['ID']}, Type: {$testListing['Listing_type']}\n";
        $canRefresh = $refreshSystem->canRefresh($testListing['ID'], $testListing['Listing_type']);
        echo "  - Can refresh: " . ($canRefresh['allowed'] ? 'YES' : 'NO') . "\n";
        echo "  - Message: " . $canRefresh['message'] . "\n";
        if (isset($canRefresh['remaining'])) {
            echo "  - Remaining: " . $canRefresh['remaining'] . "\n";
        }
    } else {
        echo "! No active listings found for testing\n";
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}

// Test POST handler simulation
echo "\n6. POST HANDLER TEST:\n";
echo "To test AJAX handler, try this in browser console:\n";
echo "------------------------------------------------------\n";
echo "$.post('', {\n";
echo "    mode: 'refreshListing',\n";
echo "    listing_id: 65,\n";
echo "    listing_type: 'konut'\n";
echo "}, function(data) {\n";
echo "    console.log('Response:', data);\n";
echo "}).fail(function(xhr) {\n";
echo "    console.log('Error:', xhr.responseText);\n";
echo "});\n";
echo "------------------------------------------------------\n";

echo "\n=== DEBUG COMPLETED ===\n";
echo "Visit: http://realestate.gmoplus.com/debug_refresh.php\n";
?> 