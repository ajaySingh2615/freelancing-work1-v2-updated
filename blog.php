<?php 
require_once 'config/database.php';
require_once 'includes/functions.php';
include 'includes/header.php'; 

// Database connection
$database = new Database();
$db = $database->connect();

// Pagination settings
$posts_per_page = 6;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $posts_per_page;

// Filter parameters
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Build WHERE clause for filtering
$where_conditions = ["b.status = 'published'"];
$params = [];

if (!empty($category_filter)) {
    $where_conditions[] = "c.slug = ?";
    $params[] = $category_filter;
}

if (!empty($search_query)) {
    $where_conditions[] = "(b.title LIKE ? OR b.excerpt LIKE ? OR b.content LIKE ?)";
    $search_param = '%' . $search_query . '%';
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = implode(' AND ', $where_conditions);

// Get latest featured post
try {
    $latest_query = "SELECT b.*, c.name as category_name, c.slug as category_slug, c.color as category_color 
                    FROM blogs b 
                    LEFT JOIN blog_categories c ON b.category_id = c.id 
                    WHERE b.status = 'published' AND b.is_featured = 1 
                    ORDER BY b.published_at DESC 
                    LIMIT 1";
    $latest_stmt = $db->prepare($latest_query);
    $latest_stmt->execute();
    $latest_post = $latest_stmt->fetch();
} catch (Exception $e) {
    $latest_post = null;
}

// Get editor's picks
try {
    $picks_query = "SELECT b.*, c.name as category_name, c.slug as category_slug, c.color as category_color 
                   FROM blogs b 
                   LEFT JOIN blog_categories c ON b.category_id = c.id 
                   WHERE b.status = 'published' AND b.is_editors_pick = 1 
                   ORDER BY b.published_at DESC 
                   LIMIT 3";
    $picks_stmt = $db->prepare($picks_query);
    $picks_stmt->execute();
    $editors_picks = $picks_stmt->fetchAll();
} catch (Exception $e) {
    $editors_picks = [];
}

// Get total count for pagination
try {
    $count_query = "SELECT COUNT(*) as total 
                   FROM blogs b 
                   LEFT JOIN blog_categories c ON b.category_id = c.id 
                   WHERE $where_clause";
    $count_stmt = $db->prepare($count_query);
    $count_stmt->execute($params);
    $total_posts = $count_stmt->fetch()['total'];
    $total_pages = ceil($total_posts / $posts_per_page);
} catch (Exception $e) {
    $total_posts = 0;
    $total_pages = 1;
}

// Get blog posts with pagination
try {
    $posts_query = "SELECT b.*, c.name as category_name, c.slug as category_slug, c.color as category_color 
                   FROM blogs b 
                   LEFT JOIN blog_categories c ON b.category_id = c.id 
                   WHERE $where_clause 
                   ORDER BY b.published_at DESC 
                   LIMIT $posts_per_page OFFSET $offset";
    $posts_stmt = $db->prepare($posts_query);
    $posts_stmt->execute($params);
    $blog_posts = $posts_stmt->fetchAll();
} catch (Exception $e) {
    $blog_posts = [];
}

// Get all categories for filter
try {
    $categories_query = "SELECT * FROM blog_categories WHERE status = 'active' ORDER BY name";
    $categories_stmt = $db->prepare($categories_query);
    $categories_stmt->execute();
    $categories = $categories_stmt->fetchAll();
} catch (Exception $e) {
    $categories = [];
}
?>

<!-- Include Blog Page Specific CSS -->
<link rel="stylesheet" href="assets/css/blog-page.css">

<div class="blog-page">
    <!-- Blog Hero Section -->
    <section class="blog-hero-section section-padding">
        <div class="container">
            <div class="blog-hero-content">
                <!-- Left: Latest Post -->
                <div class="latest-post">
                    <?php if ($latest_post): ?>
                    <div class="latest-post-card">
                        <div class="post-category">
                            <span class="category-tag" style="background-color: <?php echo htmlspecialchars($latest_post['category_color'] ?? '#003585'); ?>">
                                <?php echo htmlspecialchars($latest_post['category_name'] ?? 'Uncategorized'); ?>
                            </span>
                        </div>
                        <h1 class="latest-post-title"><?php echo htmlspecialchars($latest_post['title']); ?></h1>
                        <div class="post-meta">
                            <div class="author-info">
                                <div class="author-avatar">
                                    <img src="<?php echo $latest_post['author_avatar'] ? htmlspecialchars($latest_post['author_avatar']) : 'assets/images/media/about-page/our-team/mohd irshad.webp'; ?>" 
                                         alt="<?php echo htmlspecialchars($latest_post['author_name']); ?>">
                                </div>
                                <div class="author-details">
                                    <span class="author-name"><?php echo htmlspecialchars($latest_post['author_name']); ?></span>
                                    <span class="post-date"><?php echo timeAgo($latest_post['published_at']); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php if ($latest_post['featured_image']): ?>
                        <div class="latest-post-image">
                            <img src="<?php echo htmlspecialchars($latest_post['featured_image']); ?>" alt="<?php echo htmlspecialchars($latest_post['title']); ?>">
                        </div>
                        <?php endif; ?>
                        <p class="latest-post-excerpt">
                            <?php echo htmlspecialchars($latest_post['excerpt'] ?: substr(strip_tags($latest_post['content']), 0, 200) . '...'); ?>
                        </p>
                        <a href="blog-detail.php?slug=<?php echo urlencode($latest_post['slug']); ?>" class="read-more-btn">Read Full Article</a>
                    </div>
                    <?php else: ?>
                    <div class="latest-post-card">
                        <h1 class="latest-post-title">Welcome to Our Blog</h1>
                        <p class="latest-post-excerpt">Stay updated with the latest insights on medical education, university guides, and career opportunities.</p>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Right: Editor's Picks -->
                <div class="editors-picks">
                    <h2 class="section-title">Editor's Picks</h2>
                    <div class="picks-list">
                        <?php if (!empty($editors_picks)): ?>
                            <?php foreach ($editors_picks as $pick): ?>
                            <article class="pick-card">
                                <?php if ($pick['featured_image']): ?>
                                <div class="pick-image">
                                    <img src="<?php echo htmlspecialchars($pick['featured_image']); ?>" alt="<?php echo htmlspecialchars($pick['title']); ?>">
                                </div>
                                <?php endif; ?>
                                <div class="pick-content">
                                    <span class="pick-category" style="color: <?php echo htmlspecialchars($pick['category_color'] ?? '#003585'); ?>">
                                        <?php echo htmlspecialchars($pick['category_name'] ?? 'Uncategorized'); ?>
                                    </span>
                                    <h3 class="pick-title">
                                        <a href="blog-detail.php?slug=<?php echo urlencode($pick['slug']); ?>">
                                            <?php echo htmlspecialchars($pick['title']); ?>
                                        </a>
                                    </h3>
                                    <div class="pick-meta">
                                        <span class="pick-author"><?php echo htmlspecialchars($pick['author_name']); ?></span>
                                        <span class="pick-date"><?php echo timeAgo($pick['published_at']); ?></span>
                                    </div>
                                </div>
                            </article>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-picks">No editor's picks available at the moment.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Blog Grid Section -->
    <section class="blog-grid-section section-padding">
        <div class="container">
            <!-- Filter Bar -->
            <div class="filter-bar">
                <div class="filter-left">
                    <div class="category-filter">
                        <form method="GET" id="filterForm">
                            <select id="categoryFilter" name="category" class="filter-select" onchange="document.getElementById('filterForm').submit();">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $category): ?>
                                <option value="<?php echo htmlspecialchars($category['slug']); ?>" 
                                        <?php echo ($category_filter === $category['slug']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (!empty($search_query)): ?>
                            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search_query); ?>">
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
                <div class="filter-right">
                    <div class="search-filter">
                        <form method="GET" id="searchForm">
                            <input type="text" name="search" id="searchInput" placeholder="Search articles..." 
                                   class="search-input" value="<?php echo htmlspecialchars($search_query); ?>">
                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>
                            <?php if (!empty($category_filter)): ?>
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($category_filter); ?>">
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Results Info -->
            <div class="results-info">
                <p>Showing <?php echo count($blog_posts); ?> of <?php echo $total_posts; ?> articles
                <?php if (!empty($category_filter) || !empty($search_query)): ?>
                    <?php if (!empty($category_filter)): ?>
                        in category "<?php echo htmlspecialchars($category_filter); ?>"
                    <?php endif; ?>
                    <?php if (!empty($search_query)): ?>
                        for "<?php echo htmlspecialchars($search_query); ?>"
                    <?php endif; ?>
                <?php endif; ?>
                </p>
                <?php if (!empty($category_filter) || !empty($search_query)): ?>
                <a href="blog.php" class="clear-filters">Clear all filters</a>
                <?php endif; ?>
            </div>

            <!-- Blog Grid -->
            <div class="blog-grid">
                <?php if (!empty($blog_posts)): ?>
                    <?php foreach ($blog_posts as $post): ?>
                    <article class="blog-card">
                        <?php if ($post['featured_image']): ?>
                        <div class="blog-card-image">
                            <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                        </div>
                        <?php endif; ?>
                        <div class="blog-card-content">
                            <span class="blog-category" style="color: <?php echo htmlspecialchars($post['category_color'] ?? '#003585'); ?>">
                                <?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?>
                            </span>
                            <h3 class="blog-card-title">
                                <a href="blog-detail.php?slug=<?php echo urlencode($post['slug']); ?>">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h3>
                            <div class="blog-card-meta">
                                <span class="blog-author"><?php echo htmlspecialchars($post['author_name']); ?></span>
                                <span class="blog-date"><?php echo formatDate($post['published_at']); ?></span>
                            </div>
                            <p class="blog-excerpt">
                                <?php echo htmlspecialchars($post['excerpt'] ?: substr(strip_tags($post['content']), 0, 150) . '...'); ?>
                            </p>
                        </div>
                    </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-posts">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4>No articles found</h4>
                        <p>Try adjusting your search or filter criteria.</p>
                        <a href="blog.php" class="btn btn-primary">View All Articles</a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination-container">
                <nav aria-label="Blog pagination">
                    <ul class="pagination">
                        <?php if ($current_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        
                        if ($start_page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                        <li class="page-item disabled"><span class="page-link" aria-disabled="true">...</span></li>
                        <?php endif; ?>
                        <?php endif; ?>

                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?php echo ($i === $current_page) ? 'active' : ''; ?>">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                        <?php endfor; ?>

                        <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                        <li class="page-item disabled"><span class="page-link" aria-disabled="true">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>">
                                <?php echo $total_pages; ?>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if ($current_page < $total_pages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
            <?php endif; ?>
        </div>
    </section>
</div>



<?php include 'includes/footer.php'; ?> 