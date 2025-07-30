<?php
/**
 * Production Database Setup
 * Run this ONCE when deploying to production server
 * Then delete this file for security
 */

// Enable error reporting for setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Production Database Setup</h1>";

try {
    // Include the database configuration
    require_once 'config/database.php';
    
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724;'>✅ Database Setup Successful!</h3>";
    
    // Show created tables
    $tables = ['countries', 'universities', 'university_images'];
    foreach ($tables as $table) {
        $stmt = $db->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "<p style='color: #155724;'>✅ Table '$table': $count records</p>";
    }
    
    echo "<p style='color: #155724; font-weight: bold;'>Production database is ready!</p>";
    echo "<p style='color: #d63384;'>⚠️ IMPORTANT: Delete this file (setup-production.php) after running for security.</p>";
    echo "</div>";
    
    // Optional: Disable automatic table creation in config/database.php
    echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h4>Optional Optimization:</h4>";
    echo "<p>To improve performance, you can comment out this line in <code>config/database.php</code>:</p>";
    echo "<pre><code>// createTables(\$db);  // Comment this line after production setup</code></pre>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #721c24;'>❌ Database Setup Failed!</h3>";
    echo "<p style='color: #721c24;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your database connection settings in <code>config/database.php</code></p>";
    echo "</div>";
}
?>

<h2>Next Steps:</h2>
<ol>
    <li>✅ Database tables are created</li>
    <li>🗑️ Delete this file: <code>setup-production.php</code></li>
    <li>🌐 Visit your site: <a href="destinations.php">destinations.php</a></li>
    <li>🔧 (Optional) Comment out automatic table creation in config/database.php</li>
</ol> 