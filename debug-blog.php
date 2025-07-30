<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->connect();

echo "<h2>Blog Debug Information</h2>";

try {
    // Check if there are any blogs in the database
    $all_blogs_query = "SELECT id, title, status, published_at, created_at FROM blogs ORDER BY created_at DESC";
    $all_blogs_stmt = $db->prepare($all_blogs_query);
    $all_blogs_stmt->execute();
    $all_blogs = $all_blogs_stmt->fetchAll();
    
    echo "<h3>All Blog Posts in Database (" . count($all_blogs) . " total):</h3>";
    if (!empty($all_blogs)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 2rem;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Published At</th><th>Created At</th></tr>";
        
        foreach ($all_blogs as $blog) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($blog['id']) . "</td>";
            echo "<td>" . htmlspecialchars($blog['title']) . "</td>";
            echo "<td><strong>" . htmlspecialchars($blog['status']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($blog['published_at'] ?: 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($blog['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>No blog posts found in database!</p>";
    }
    
    // Check published blogs specifically
    $published_query = "SELECT id, title, status, published_at FROM blogs WHERE status = 'published'";
    $published_stmt = $db->prepare($published_query);
    $published_stmt->execute();
    $published_blogs = $published_stmt->fetchAll();
    
    echo "<h3>Published Blog Posts (" . count($published_blogs) . " total):</h3>";
    if (!empty($published_blogs)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 2rem;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Published At</th></tr>";
        
        foreach ($published_blogs as $blog) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($blog['id']) . "</td>";
            echo "<td>" . htmlspecialchars($blog['title']) . "</td>";
            echo "<td>" . htmlspecialchars($blog['status']) . "</td>";
            echo "<td>" . htmlspecialchars($blog['published_at'] ?: 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>No published blog posts found!</p>";
    }
    
    // Check categories
    $categories_query = "SELECT id, name, status FROM blog_categories";
    $categories_stmt = $db->prepare($categories_query);
    $categories_stmt->execute();
    $categories = $categories_stmt->fetchAll();
    
    echo "<h3>Blog Categories (" . count($categories) . " total):</h3>";
    if (!empty($categories)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%; margin-bottom: 2rem;'>";
        echo "<tr><th>ID</th><th>Name</th><th>Status</th></tr>";
        
        foreach ($categories as $category) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($category['id']) . "</td>";
            echo "<td>" . htmlspecialchars($category['name']) . "</td>";
            echo "<td>" . htmlspecialchars($category['status']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    // Test the exact query from blog.php
    echo "<h3>Testing Blog Page Query:</h3>";
    $test_query = "SELECT b.*, c.name as category_name, c.slug as category_slug, c.color as category_color 
                   FROM blogs b 
                   LEFT JOIN blog_categories c ON b.category_id = c.id 
                   WHERE status = 'published' 
                   ORDER BY b.published_at DESC 
                   LIMIT 6";
    $test_stmt = $db->prepare($test_query);
    $test_stmt->execute();
    $test_results = $test_stmt->fetchAll();
    
    echo "<p>Query found: " . count($test_results) . " results</p>";
    
    if (!empty($test_results)) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Title</th><th>Category</th><th>Published At</th></tr>";
        
        foreach ($test_results as $result) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($result['id']) . "</td>";
            echo "<td>" . htmlspecialchars($result['title']) . "</td>";
            echo "<td>" . htmlspecialchars($result['category_name'] ?: 'No category') . "</td>";
            echo "<td>" . htmlspecialchars($result['published_at'] ?: 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
    echo "<hr><h3>Actions:</h3>";
    echo "<p><a href='seed-data.php' style='background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Re-run Seed Data</a></p>";
    echo "<p><a href='blog.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>View Blog Page</a></p>";
    
} catch (Exception $e) {
    echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
}
?> 