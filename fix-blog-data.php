<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->connect();

echo "<h2>Fixing Blog Data</h2>";

try {
    // Update published blog posts that have NULL published_at
    $fix_query = "UPDATE blogs 
                  SET published_at = created_at 
                  WHERE status = 'published' AND published_at IS NULL";
    
    $fix_stmt = $db->prepare($fix_query);
    $rows_affected = $fix_stmt->execute();
    
    echo "<p>✓ Fixed published_at timestamps for blog posts</p>";
    
    // Check current status
    $check_query = "SELECT COUNT(*) as count FROM blogs WHERE status = 'published' AND published_at IS NOT NULL";
    $check_stmt = $db->prepare($check_query);
    $check_stmt->execute();
    $result = $check_stmt->fetch();
    
    echo "<p>✅ Found " . $result['count'] . " published blog posts with proper timestamps</p>";
    
    // If no published posts, let's check if there are any posts at all
    $total_query = "SELECT COUNT(*) as count FROM blogs";
    $total_stmt = $db->prepare($total_query);
    $total_stmt->execute();
    $total_result = $total_stmt->fetch();
    
    echo "<p>📊 Total blog posts in database: " . $total_result['count'] . "</p>";
    
    if ($total_result['count'] == 0) {
        echo "<p style='color: orange;'>⚠️ No blog posts found. Please run the seed data script first.</p>";
        echo "<p><a href='seed-data.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Run Seed Data</a></p>";
    } else {
        // Update any draft posts to published for testing
        $publish_query = "UPDATE blogs SET status = 'published', published_at = NOW() WHERE status = 'draft'";
        $publish_stmt = $db->prepare($publish_query);
        $publish_stmt->execute();
        
        echo "<p>✓ Updated any draft posts to published status</p>";
    }
    
    echo "<hr>";
    echo "<p><a href='debug-blog.php' style='background: #ffc107; color: black; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Debug Blog Data</a></p>";
    echo "<p><a href='blog.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Blog Page</a></p>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
}
?> 