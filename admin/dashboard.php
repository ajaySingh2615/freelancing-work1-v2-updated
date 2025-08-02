<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Initialize database connection
$database = new Database();
$db = $database->connect();

$errors = [];
$success = '';

// Handle AJAX requests
if (isset($_GET['ajax']) && $_GET['ajax'] === 'stats') {
    try {
        $statsQuery = $db->prepare("
            SELECT 
                COUNT(CASE WHEN status = 'published' THEN 1 END) as published_blogs,
                COUNT(CASE WHEN status = 'draft' THEN 1 END) as draft_blogs,
                COUNT(CASE WHEN status = 'archived' THEN 1 END) as archived_blogs,
                SUM(views) as total_views,
                COUNT(*) as total_blogs
            FROM blogs
        ");
        $statsQuery->execute();
        $stats = $statsQuery->fetch();
        
        header('Content-Type: application/json');
        echo json_encode(['stats' => $stats]);
        exit();
    } catch (Exception $e) {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
        exit();
    }
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'toggle_status':
                $blogId = intval($_POST['blog_id']);
                $newStatus = sanitizeInput($_POST['new_status']);
                
                try {
                    $updateQuery = $db->prepare("UPDATE blogs SET status = ? WHERE id = ?");
                    $updateQuery->execute([$newStatus, $blogId]);
                    $success = "Blog status updated successfully!";
                } catch (Exception $e) {
                    $errors[] = "Error updating blog status: " . $e->getMessage();
                }
                break;
                
            case 'delete_blog':
                $blogId = intval($_POST['blog_id']);
                
                try {
                    $deleteQuery = $db->prepare("DELETE FROM blogs WHERE id = ?");
                    $deleteQuery->execute([$blogId]);
                    $success = "Blog deleted successfully!";
                } catch (Exception $e) {
                    $errors[] = "Error deleting blog: " . $e->getMessage();
                }
                break;
                
            case 'bulk_action':
                $action = sanitizeInput($_POST['bulk_action']);
                $selectedIds = $_POST['selected_blogs'] ?? [];
                
                if (!empty($selectedIds) && !empty($action)) {
                    try {
                        switch ($action) {
                            case 'publish':
                                $query = $db->prepare("UPDATE blogs SET status = 'published' WHERE id = ?");
                                break;
                            case 'unpublish':
                                $query = $db->prepare("UPDATE blogs SET status = 'draft' WHERE id = ?");
                                break;
                            case 'archive':
                                $query = $db->prepare("UPDATE blogs SET status = 'archived' WHERE id = ?");
                                break;
                            case 'delete':
                                $query = $db->prepare("DELETE FROM blogs WHERE id = ?");
                                break;
                        }
                        
                        foreach ($selectedIds as $id) {
                            $query->execute([intval($id)]);
                        }
                        
                        $success = "Bulk action completed successfully!";
                    } catch (Exception $e) {
                        $errors[] = "Error performing bulk action: " . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Get filter parameters
$statusFilter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$categoryFilter = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
$searchQuery = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
// Date filter parameters removed

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Get statistics
try {
    $statsQuery = $db->prepare("
        SELECT 
            COUNT(CASE WHEN status = 'published' THEN 1 END) as published_blogs,
            COUNT(CASE WHEN status = 'draft' THEN 1 END) as draft_blogs,
            COUNT(CASE WHEN status = 'archived' THEN 1 END) as archived_blogs,
            SUM(views) as total_views,
            COUNT(*) as total_blogs
        FROM blogs
    ");
    $statsQuery->execute();
    $stats = $statsQuery->fetch();
} catch (Exception $e) {
    $errors[] = "Error loading statistics: " . $e->getMessage();
    $stats = ['published_blogs' => 0, 'draft_blogs' => 0, 'archived_blogs' => 0, 'total_views' => 0, 'total_blogs' => 0];
}

// Get countries and universities statistics
try {
    $countriesStatsQuery = $db->prepare("
        SELECT 
            COUNT(*) as total_countries,
            COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_countries,
            SUM(student_count) as total_students
        FROM countries
    ");
    $countriesStatsQuery->execute();
    $countriesStats = $countriesStatsQuery->fetch();
    
    $universitiesStatsQuery = $db->prepare("
        SELECT 
            COUNT(*) as total_universities,
            COUNT(CASE WHEN is_active = 1 THEN 1 END) as active_universities
        FROM universities
    ");
    $universitiesStatsQuery->execute();
    $universitiesStats = $universitiesStatsQuery->fetch();
    
    $imagesStatsQuery = $db->prepare("SELECT COUNT(*) as total_images FROM university_images");
    $imagesStatsQuery->execute();
    $imagesStats = $imagesStatsQuery->fetch();
} catch (Exception $e) {
    $countriesStats = ['total_countries' => 0, 'active_countries' => 0, 'total_students' => 0];
    $universitiesStats = ['total_universities' => 0, 'active_universities' => 0];
    $imagesStats = ['total_images' => 0];
}

// Build WHERE clause for filters
$whereConditions = [];
$params = [];

if (!empty($statusFilter)) {
    $whereConditions[] = "b.status = ?";
    $params[] = $statusFilter;
}

if (!empty($categoryFilter)) {
    $whereConditions[] = "b.category_id = ?";
    $params[] = $categoryFilter;
}

if (!empty($searchQuery)) {
    $whereConditions[] = "(b.title LIKE ? OR b.content LIKE ? OR b.excerpt LIKE ?)";
    $searchParam = '%' . $searchQuery . '%';
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
}

// Date filters removed as requested

$whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

// Get total count for pagination
try {
    $countQuery = $db->prepare("
        SELECT COUNT(*) as total
        FROM blogs b
        LEFT JOIN blog_categories c ON b.category_id = c.id
        $whereClause
    ");
    $countQuery->execute($params);
    $totalBlogs = $countQuery->fetch()['total'];
    $totalPages = ceil($totalBlogs / $limit);
} catch (Exception $e) {
    $totalBlogs = 0;
    $totalPages = 1;
}

// Get blogs with filters
try {
    $blogsQuery = $db->prepare("
        SELECT b.*, c.name as category_name, c.color as category_color
        FROM blogs b
        LEFT JOIN blog_categories c ON b.category_id = c.id
        $whereClause
        ORDER BY b.created_at DESC
        LIMIT $limit OFFSET $offset
    ");
    $blogsQuery->execute($params);
    $blogs = $blogsQuery->fetchAll();
} catch (Exception $e) {
    $errors[] = "Error loading blogs: " . $e->getMessage();
    $blogs = [];
}

// Get categories for filter dropdown
try {
    $categoriesQuery = $db->prepare("SELECT * FROM blog_categories WHERE status = 'active' ORDER BY name");
    $categoriesQuery->execute();
    $categories = $categoriesQuery->fetchAll();
} catch (Exception $e) {
    $categories = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - MedStudy Global</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Admin Styles -->
    <link rel="stylesheet" href="admin-styles.css">
    
    <style>
        :root {
            --primary-color: #003585;
            --secondary-color: #feba02;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --info-color: #17a2b8;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }

        body {
            background-color: #f4f6f9;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #002366 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            text-align: center;
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
            color: var(--secondary-color);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 1rem 0;
        }

        .sidebar-menu li a {
            display: block;
            padding: 0.875rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .sidebar-menu li a:hover,
        .sidebar-menu li.active a {
            background: rgba(254, 186, 2, 0.1);
            color: var(--secondary-color);
            border-left-color: var(--secondary-color);
        }

        .sidebar-menu li a i {
            width: 20px;
            margin-right: 0.75rem;
        }

        /* Main Content */
        .admin-main {
            flex: 1;
            margin-left: 280px;
            padding: 0;
            background-color: #f4f6f9;
        }

        .admin-header {
            background: white;
            padding: 1.5rem 2rem;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .admin-header h1 {
            margin: 0;
            color: var(--primary-color);
            font-size: 1.8rem;
            font-weight: 600;
        }

        .admin-content {
            padding: 2rem;
        }

        /* Statistics Cards */
        .stats-row {
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            height: 100%;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card.published {
            border-left: 4px solid var(--success-color);
        }

        .stat-card.draft {
            border-left: 4px solid var(--warning-color);
        }

        .stat-card.archived {
            border-left: 4px solid var(--dark-color);
        }

        .stat-card.views {
            border-left: 4px solid var(--info-color);
        }

        .stat-card.countries {
            border-left: 4px solid #28a745;
        }

        .stat-card.universities {
            border-left: 4px solid var(--primary-color);
        }

        .stat-card.students {
            border-left: 4px solid var(--secondary-color);
        }

        .stat-card.images {
            border-left: 4px solid #6f42c1;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 0.95rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-sublabel {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .stat-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 2.5rem;
            opacity: 0.1;
        }

        /* Filters Section */
        .filters-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .filters-header h5 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(0, 53, 133, 0.1);
        }

        .btn-filter {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-filter:hover {
            background: #002366;
            color: white;
        }

        .btn-clear {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-clear:hover {
            background: #545b62;
            color: white;
        }

        /* Table Section */
        .table-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h5 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
        }

        .bulk-actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }

        .table {
            margin: 0;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: var(--primary-color);
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f3f4;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-published {
            background: #d4edda;
            color: #155724;
        }

        .status-draft {
            background: #fff3cd;
            color: #856404;
        }

        .status-archived {
            background: #f8d7da;
            color: #721c24;
        }

        .category-badge {
            color: white;
            padding: 0.3rem 0.7rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 0.4rem 0.8rem;
            border-radius: 6px;
            font-size: 0.85rem;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .btn-edit {
            background: #e3f2fd;
            color: #1976d2;
        }

        .btn-edit:hover {
            background: #1976d2;
            color: white;
        }

        .btn-publish {
            background: #d4edda;
            color: #155724;
        }

        .btn-publish:hover {
            background: #155724;
            color: white;
        }

        .btn-unpublish {
            background: #fff3cd;
            color: #856404;
        }

        .btn-unpublish:hover {
            background: #856404;
            color: white;
        }

        .btn-delete {
            background: #ffebee;
            color: #c62828;
        }

        .btn-delete:hover {
            background: #c62828;
            color: white;
        }

        /* Pagination */
        .pagination {
            justify-content: center;
            margin-top: 2rem;
        }

        .pagination .page-link {
            color: var(--primary-color);
            border: 1px solid #dee2e6;
            padding: 0.5rem 0.75rem;
            margin: 0 0.25rem;
            border-radius: 6px;
        }

        .pagination .page-link:hover {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .pagination .page-item.active .page-link {
            background: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            color: #dee2e6;
        }

        .empty-state h5 {
            color: #495057;
            margin-bottom: 1rem;
        }

        /* Alerts */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .admin-sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .admin-main {
                margin-left: 0;
            }

            .admin-content {
                padding: 1rem;
            }

            .admin-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .filters-header {
                flex-direction: column;
                gap: 1rem;
            }

            .table-header {
                flex-direction: column;
                gap: 1rem;
            }

            .action-buttons {
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="admin-sidebar">
            <div class="sidebar-header">
                <h4><i class="fas fa-graduation-cap"></i> MedStudy Admin</h4>
            </div>
            <ul class="sidebar-menu">
                <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="add-blog.php"><i class="fas fa-plus"></i> Add Blog</a></li>
                <li><a href="manage-categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="manage-countries.php"><i class="fas fa-globe"></i> Countries</a></li>
                <li><a href="manage-universities.php"><i class="fas fa-university"></i> Universities</a></li>
                <li><a href="manage-university-images.php"><i class="fas fa-images"></i> University Images</a></li>
                <li><a href="../blog.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Blog</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                <div>
                    <span class="me-3">Welcome, <?php echo htmlspecialchars($_SESSION['admin_name'] ?? 'Admin'); ?>!</span>
                    <a href="add-blog.php" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Add Blog
                    </a>
                </div>
            </div>

            <div class="admin-content">
                <!-- Messages -->
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0 ps-3">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row stats-row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card published">
                            <i class="fas fa-check-circle stat-icon"></i>
                            <div class="stat-number"><?php echo number_format($stats['published_blogs']); ?></div>
                            <div class="stat-label">Published Blogs</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card draft">
                            <i class="fas fa-edit stat-icon"></i>
                            <div class="stat-number"><?php echo number_format($stats['draft_blogs']); ?></div>
                            <div class="stat-label">Draft Blogs</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card archived">
                            <i class="fas fa-archive stat-icon"></i>
                            <div class="stat-number"><?php echo number_format($stats['archived_blogs']); ?></div>
                            <div class="stat-label">Archived Blogs</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card views">
                            <i class="fas fa-eye stat-icon"></i>
                            <div class="stat-number"><?php echo number_format($stats['total_views']); ?></div>
                            <div class="stat-label">Total Views</div>
                        </div>
                    </div>
                </div>

                <!-- Countries & Universities Statistics -->
                <div class="row stats-row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card countries">
                            <i class="fas fa-globe stat-icon"></i>
                            <div class="stat-number"><?php echo number_format($countriesStats['total_countries']); ?></div>
                            <div class="stat-label">Total Countries</div>
                            <div class="stat-sublabel"><?php echo number_format($countriesStats['active_countries']); ?> Active</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card universities">
                            <i class="fas fa-university stat-icon"></i>
                            <div class="stat-number"><?php echo number_format($universitiesStats['total_universities']); ?></div>
                            <div class="stat-label">Total Universities</div>
                            <div class="stat-sublabel"><?php echo number_format($universitiesStats['active_universities']); ?> Active</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card students">
                            <i class="fas fa-graduation-cap stat-icon"></i>
                            <div class="stat-number"><?php echo number_format($countriesStats['total_students']); ?></div>
                            <div class="stat-label">Total Students</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stat-card images">
                            <i class="fas fa-images stat-icon"></i>
                            <div class="stat-number"><?php echo number_format($imagesStats['total_images']); ?></div>
                            <div class="stat-label">University Images</div>
                        </div>
                    </div>
                </div>

                <!-- Filters Section -->
                <div class="filters-section">
                    <div class="filters-header">
                        <h5><i class="fas fa-filter me-2"></i>Filter & Search</h5>
                        <div>
                            <small class="text-muted">Showing <?php echo number_format($totalBlogs); ?> blogs</small>
                        </div>
                    </div>
                    
                    <form method="GET" action="dashboard.php">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="published" <?php echo $statusFilter === 'published' ? 'selected' : ''; ?>>Published</option>
                                    <option value="draft" <?php echo $statusFilter === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    <option value="archived" <?php echo $statusFilter === 'archived' ? 'selected' : ''; ?>>Archived</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-control">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $categoryFilter == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Date filters removed as requested -->
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Search by title, content, or excerpt..." value="<?php echo htmlspecialchars($searchQuery); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <div class="d-flex gap-2">
                                    <button type="submit" class="btn btn-filter">
                                        <i class="fas fa-search"></i> Filter
                                    </button>
                                    <a href="dashboard.php" class="btn btn-clear">
                                        <i class="fas fa-times"></i> Clear
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Blogs Table -->
                <div class="table-section">
                    <div class="table-header">
                        <h5><i class="fas fa-list me-2"></i>All Blogs</h5>
                        <div class="bulk-actions">
                            <form method="POST" id="bulkForm" style="display: flex; gap: 0.5rem; align-items: center;">
                                <input type="hidden" name="action" value="bulk_action">
                                <select name="bulk_action" class="form-control form-control-sm">
                                    <option value="">Bulk Actions</option>
                                    <option value="publish">Publish</option>
                                    <option value="unpublish">Unpublish</option>
                                    <option value="archive">Archive</option>
                                    <option value="delete">Delete</option>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Apply</button>
                            </form>
                        </div>
                    </div>
                    
                    <?php if (!empty($blogs)): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="selectAll">
                                    </th>
                                    <th>Title</th>
                                    <th width="150">Category</th>
                                    <th width="100">Status</th>
                                    <th width="80">Views</th>
                                    <th width="120">Created</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blogs as $blog): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_blogs[]" value="<?php echo $blog['id']; ?>" form="bulkForm">
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($blog['title']); ?></strong>
                                            <div class="mt-1">
                                                <?php if ($blog['is_featured']): ?>
                                                    <span class="badge bg-warning text-dark">Featured</span>
                                                <?php endif; ?>
                                                <?php if ($blog['is_editors_pick']): ?>
                                                    <span class="badge bg-info">Editor's Pick</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($blog['category_name']): ?>
                                            <span class="category-badge" style="background-color: <?php echo $blog['category_color']; ?>">
                                                <?php echo htmlspecialchars($blog['category_name']); ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">No Category</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $blog['status']; ?>">
                                            <?php echo ucfirst($blog['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($blog['views']); ?></td>
                                    <td>
                                        <small class="text-muted"><?php echo timeAgo($blog['created_at']); ?></small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="add-blog.php?id=<?php echo $blog['id']; ?>" class="btn-action btn-edit" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                                                        <?php if ($blog['status'] === 'published'): ?>
                                <form method="POST" style="display: inline;" class="status-form">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                    <input type="hidden" name="new_status" value="draft">
                                    <button type="submit" class="btn-action btn-publish" title="Currently Published - Click to Unpublish">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="POST" style="display: inline;" class="status-form">
                                    <input type="hidden" name="action" value="toggle_status">
                                    <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                    <input type="hidden" name="new_status" value="published">
                                    <button type="submit" class="btn-action btn-unpublish" title="Currently Draft - Click to Publish">
                                        <i class="fas fa-eye-slash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                                            
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this blog?');">
                                                <input type="hidden" name="action" value="delete_blog">
                                                <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                                <button type="submit" class="btn-action btn-delete" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                    <div class="d-flex justify-content-center mt-3">
                        <nav>
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($_SERVER['QUERY_STRING']) ? '&' . str_replace('page=' . $page, '', $_SERVER['QUERY_STRING']) : ''; ?>">Previous</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($_SERVER['QUERY_STRING']) ? '&' . str_replace('page=' . $page, '', $_SERVER['QUERY_STRING']) : ''; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($_SERVER['QUERY_STRING']) ? '&' . str_replace('page=' . $page, '', $_SERVER['QUERY_STRING']) : ''; ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                    <?php endif; ?>
                    
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-blog"></i>
                        <h5>No blogs found</h5>
                        <p>Start by creating your first blog post or adjust your filters!</p>
                        <a href="add-blog.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Create First Blog
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Select all checkbox functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="selected_blogs[]"]');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });

        // AJAX for status toggle forms
        document.addEventListener('DOMContentLoaded', function() {
            // Handle status toggle forms with AJAX
            document.querySelectorAll('.status-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const button = this.querySelector('button');
                    const originalHtml = button.innerHTML;
                    
                    // Show loading state
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    button.disabled = true;
                    
                    fetch('dashboard.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Parse the response to get the updated row
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(data, 'text/html');
                        
                        // Find the corresponding row and update it
                        const blogId = formData.get('blog_id');
                        const currentRow = document.querySelector(`input[value="${blogId}"]`).closest('tr');
                        const newRow = doc.querySelector(`input[value="${blogId}"]`).closest('tr');
                        
                        if (newRow) {
                            currentRow.innerHTML = newRow.innerHTML;
                            
                            // Re-attach event listeners to the new elements
                            attachStatusFormListeners(currentRow);
                            
                            // Show success message
                            showAlert('Status updated successfully!', 'success');
                            
                            // Update statistics
                            updateStatistics();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        button.innerHTML = originalHtml;
                        button.disabled = false;
                        showAlert('Error updating status. Please try again.', 'danger');
                    });
                });
            });
            
            // Handle delete forms with AJAX
            document.querySelectorAll('form[onsubmit*="confirm"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    if (!confirm('Are you sure you want to delete this blog?')) {
                        return;
                    }
                    
                    const formData = new FormData(this);
                    const button = this.querySelector('button');
                    const originalHtml = button.innerHTML;
                    
                    // Show loading state
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                    button.disabled = true;
                    
                    fetch('dashboard.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.text())
                    .then(data => {
                        // Remove the row
                        const blogId = formData.get('blog_id');
                        const currentRow = document.querySelector(`input[value="${blogId}"]`).closest('tr');
                        currentRow.remove();
                        
                        // Show success message
                        showAlert('Blog deleted successfully!', 'success');
                        
                        // Update statistics
                        updateStatistics();
                        
                        // Check if no blogs left
                        const remainingRows = document.querySelectorAll('tbody tr');
                        if (remainingRows.length === 0) {
                            location.reload(); // Reload to show empty state
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        button.innerHTML = originalHtml;
                        button.disabled = false;
                        showAlert('Error deleting blog. Please try again.', 'danger');
                    });
                });
            });
        });

        // Function to attach status form listeners to new elements
        function attachStatusFormListeners(container) {
            container.querySelectorAll('.status-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    // Same logic as above - could be refactored into a function
                });
            });
        }

        // Function to show alert messages
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;
            alertDiv.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>${message}`;
            
            const content = document.querySelector('.admin-content');
            content.insertBefore(alertDiv, content.firstChild);
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                alertDiv.style.transition = 'opacity 0.5s ease';
                alertDiv.style.opacity = '0';
                setTimeout(() => alertDiv.remove(), 500);
            }, 5000);
        }

        // Function to update statistics via AJAX
        function updateStatistics() {
            fetch('dashboard.php?ajax=stats')
            .then(response => response.json())
            .then(data => {
                if (data.stats) {
                    document.querySelector('.stat-card.published .stat-number').textContent = data.stats.published_blogs;
                    document.querySelector('.stat-card.draft .stat-number').textContent = data.stats.draft_blogs;
                    document.querySelector('.stat-card.archived .stat-number').textContent = data.stats.archived_blogs;
                    document.querySelector('.stat-card.views .stat-number').textContent = data.stats.total_views;
                }
            })
            .catch(error => console.error('Error updating stats:', error));
        }

        // Bulk actions form validation
        document.getElementById('bulkForm').addEventListener('submit', function(e) {
            const action = this.bulk_action.value;
            const checked = document.querySelectorAll('input[name="selected_blogs[]"]:checked');
            
            if (!action) {
                e.preventDefault();
                alert('Please select a bulk action.');
                return;
            }
            
            if (checked.length === 0) {
                e.preventDefault();
                alert('Please select at least one blog.');
                return;
            }
            
            if (action === 'delete') {
                if (!confirm('Are you sure you want to delete ' + checked.length + ' blog(s)?')) {
                    e.preventDefault();
                    return;
                }
            }
        });

        // Auto-hide alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    alert.style.transition = 'opacity 0.5s ease';
                    alert.style.opacity = '0';
                    setTimeout(function() {
                        alert.remove();
                    }, 500);
                }, 5000);
            });
        });
    </script>
</body>
</html> 