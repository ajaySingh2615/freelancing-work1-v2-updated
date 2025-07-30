<?php 
require_once 'config/database.php';

// Database connection
$database = new Database();
$db = $database->connect();

// Get blog slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    header("Location: blog.php");
    exit();
}

// Get blog post
try {
    $post_query = "SELECT b.*, c.name as category_name, c.slug as category_slug, c.color as category_color 
                   FROM blogs b 
                   LEFT JOIN blog_categories c ON b.category_id = c.id 
                   WHERE b.slug = ? AND b.status = 'published'";
    $post_stmt = $db->prepare($post_query);
    $post_stmt->execute([$slug]);
    $post = $post_stmt->fetch();
    
    if (!$post) {
        header("Location: blog.php");
        exit();
    }
    
    // Update view count
    $update_views = "UPDATE blogs SET views = views + 1 WHERE id = ?";
    $update_stmt = $db->prepare($update_views);
    $update_stmt->execute([$post['id']]);
    
} catch (Exception $e) {
    header("Location: blog.php");
    exit();
}

// Get related posts (same category, excluding current post)
try {
    $related_query = "SELECT b.*, c.name as category_name, c.slug as category_slug, c.color as category_color 
                     FROM blogs b 
                     LEFT JOIN blog_categories c ON b.category_id = c.id 
                     WHERE b.category_id = ? AND b.id != ? AND b.status = 'published' 
                     ORDER BY b.published_at DESC 
                     LIMIT 6";
    $related_stmt = $db->prepare($related_query);
    $related_stmt->execute([$post['category_id'], $post['id']]);
    $related_posts = $related_stmt->fetchAll();
} catch (Exception $e) {
    $related_posts = [];
}

// Get previous and next posts
try {
    $prev_query = "SELECT id, title, slug FROM blogs 
                   WHERE published_at < ? AND status = 'published' 
                   ORDER BY published_at DESC LIMIT 1";
    $prev_stmt = $db->prepare($prev_query);
    $prev_stmt->execute([$post['published_at']]);
    $prev_post = $prev_stmt->fetch();
    
    $next_query = "SELECT id, title, slug FROM blogs 
                   WHERE published_at > ? AND status = 'published' 
                   ORDER BY published_at ASC LIMIT 1";
    $next_stmt = $db->prepare($next_query);
    $next_stmt->execute([$post['published_at']]);
    $next_post = $next_stmt->fetch();
} catch (Exception $e) {
    $prev_post = null;
    $next_post = null;
}

include 'includes/header.php';
?>

<!-- Include Blog Detail Page CSS -->
<link rel="stylesheet" href="assets/css/variables.css">
<link rel="stylesheet" href="assets/css/blog-detail.css">
    <div class="blog-detail-page">
        <div class="blog-container">
            <!-- Back Button -->
            <a href="blog.php" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Back to Blog
            </a>
            
            <!-- Article Header -->
            <header class="article-header">
                <div class="article-category" style="background-color: <?php echo htmlspecialchars($post['category_color'] ?? '#003585'); ?>">
                    <?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?>
                </div>
                <h1 class="article-title"><?php echo htmlspecialchars($post['title']); ?></h1>
                <div class="article-meta">
                    <div class="author-info">
                        <div class="author-avatar">
                            <img src="<?php echo $post['author_avatar'] ? htmlspecialchars($post['author_avatar']) : 'assets/images/media/about-page/our-team/mohd irshad.webp'; ?>" 
                                 alt="<?php echo htmlspecialchars($post['author_name']); ?>">
                        </div>
                        <span><?php echo htmlspecialchars($post['author_name']); ?></span>
                    </div>
                    <div class="publish-date">
                        <i class="fas fa-calendar"></i>
                        <?php echo formatDate($post['published_at']); ?>
                    </div>
                    <div class="read-time">
                        <i class="fas fa-clock"></i>
                        <?php 
                        $read_time = isset($post['read_time']) && $post['read_time'] 
                            ? $post['read_time'] 
                            : ceil(str_word_count(strip_tags($post['content'])) / 200); // 200 words per minute
                        echo $read_time . ' min read'; 
                        ?>
                    </div>
                </div>
            </header>
            
            <!-- Featured Image -->
            <?php if ($post['featured_image']): ?>
            <div class="featured-image-container">
                <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                     alt="<?php echo htmlspecialchars($post['title']); ?>" 
                     class="featured-image">
            </div>
            <?php endif; ?>
            
            <!-- Article Content -->
            <article class="article-content">
                <?php echo $post['content']; ?>
            </article>
            
            <!-- Navigation -->
            <nav class="article-navigation">
                <?php if ($prev_post): ?>
                <a href="blog-detail.php?slug=<?php echo urlencode($prev_post['slug']); ?>" class="nav-post prev">
                    <i class="fas fa-arrow-left"></i>
                    <div class="nav-post-content">
                        <h4>Previous Post</h4>
                        <p><?php echo htmlspecialchars($prev_post['title']); ?></p>
                    </div>
                </a>
                <?php else: ?>
                <div></div>
                <?php endif; ?>
                
                <?php if ($next_post): ?>
                <a href="blog-detail.php?slug=<?php echo urlencode($next_post['slug']); ?>" class="nav-post next">
                    <div class="nav-post-content">
                        <h4>Next Post</h4>
                        <p><?php echo htmlspecialchars($next_post['title']); ?></p>
                    </div>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <?php else: ?>
                <div></div>
                <?php endif; ?>
            </nav>
            
            <!-- Related Posts -->
            <?php if (!empty($related_posts)): ?>
            <section class="related-posts">
                <h2>Related Articles</h2>
                <div class="related-grid">
                    <?php foreach ($related_posts as $related): ?>
                    <article class="related-card">
                        <?php if ($related['featured_image']): ?>
                        <div class="related-card-image">
                            <img src="<?php echo htmlspecialchars($related['featured_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($related['title']); ?>">
                        </div>
                        <?php endif; ?>
                        <div class="related-card-content">
                            <div class="related-card-category" style="color: <?php echo htmlspecialchars($related['category_color'] ?? '#003585'); ?>">
                                <?php echo htmlspecialchars($related['category_name'] ?? 'Uncategorized'); ?>
                            </div>
                            <h3 class="related-card-title">
                                <a href="blog-detail.php?slug=<?php echo urlencode($related['slug']); ?>">
                                    <?php echo htmlspecialchars($related['title']); ?>
                                </a>
                            </h3>
                            <div class="related-card-meta">
                                <span><?php echo htmlspecialchars($related['author_name']); ?></span>
                                <span><?php echo timeAgo($related['published_at']); ?></span>
                            </div>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            </section>
            <?php endif; ?>
                 </div>
     </div>

<?php include 'includes/footer.php'; ?> 