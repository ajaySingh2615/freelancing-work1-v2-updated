<?php
session_start();
require_once '../config/database.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$database = new Database();
$db = $database->connect();

$errors = [];
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $name = sanitizeInput($_POST['name']);
                $description = sanitizeInput($_POST['description']);
                $color = sanitizeInput($_POST['color']);
                $status = sanitizeInput($_POST['status']);
                
                if (empty($name)) {
                    $errors[] = "Category name is required";
                } else {
                    $slug = generateSlug($name);
                    
                    // Check if slug already exists
                    $check_query = "SELECT id FROM blog_categories WHERE slug = ?";
                    $check_stmt = $db->prepare($check_query);
                    $check_stmt->execute([$slug]);
                    
                    if ($check_stmt->fetch()) {
                        $errors[] = "A category with this name already exists";
                    } else {
                        try {
                            $insert_query = "INSERT INTO blog_categories (name, slug, description, color, status) VALUES (?, ?, ?, ?, ?)";
                            $insert_stmt = $db->prepare($insert_query);
                            $insert_stmt->execute([$name, $slug, $description, $color, $status]);
                            $success = "Category added successfully!";
                        } catch (Exception $e) {
                            $errors[] = "Error adding category: " . $e->getMessage();
                        }
                    }
                }
                break;
                
            case 'edit':
                $id = intval($_POST['id']);
                $name = sanitizeInput($_POST['name']);
                $description = sanitizeInput($_POST['description']);
                $color = sanitizeInput($_POST['color']);
                $status = sanitizeInput($_POST['status']);
                
                if (empty($name)) {
                    $errors[] = "Category name is required";
                } else {
                    $slug = generateSlug($name);
                    
                    // Check if slug already exists for other categories
                    $check_query = "SELECT id FROM blog_categories WHERE slug = ? AND id != ?";
                    $check_stmt = $db->prepare($check_query);
                    $check_stmt->execute([$slug, $id]);
                    
                    if ($check_stmt->fetch()) {
                        $errors[] = "A category with this name already exists";
                    } else {
                        try {
                            $update_query = "UPDATE blog_categories SET name = ?, slug = ?, description = ?, color = ?, status = ? WHERE id = ?";
                            $update_stmt = $db->prepare($update_query);
                            $update_stmt->execute([$name, $slug, $description, $color, $status, $id]);
                            $success = "Category updated successfully!";
                        } catch (Exception $e) {
                            $errors[] = "Error updating category: " . $e->getMessage();
                        }
                    }
                }
                break;
                
            case 'delete':
                $id = intval($_POST['id']);
                
                try {
                    // Check if category is being used by any blogs
                    $usage_query = "SELECT COUNT(*) as count FROM blogs WHERE category_id = ?";
                    $usage_stmt = $db->prepare($usage_query);
                    $usage_stmt->execute([$id]);
                    $usage_count = $usage_stmt->fetch()['count'];
                    
                    if ($usage_count > 0) {
                        $errors[] = "Cannot delete category. It is being used by $usage_count blog post(s).";
                    } else {
                        $delete_query = "DELETE FROM blog_categories WHERE id = ?";
                        $delete_stmt = $db->prepare($delete_query);
                        $delete_stmt->execute([$id]);
                        $success = "Category deleted successfully!";
                    }
                } catch (Exception $e) {
                    $errors[] = "Error deleting category: " . $e->getMessage();
                }
                break;
                
            case 'toggle_status':
                $id = intval($_POST['id']);
                $status = sanitizeInput($_POST['status']);
                
                try {
                    $update_query = "UPDATE blog_categories SET status = ? WHERE id = ?";
                    $update_stmt = $db->prepare($update_query);
                    $update_stmt->execute([$status, $id]);
                    $success = "Category status updated successfully!";
                } catch (Exception $e) {
                    $errors[] = "Error updating category status: " . $e->getMessage();
                }
                break;
        }
    }
}

// Get all categories with blog count
try {
    $categories_query = "SELECT c.*, COUNT(b.id) as blog_count 
                        FROM blog_categories c 
                        LEFT JOIN blogs b ON c.id = b.category_id 
                        GROUP BY c.id 
                        ORDER BY c.created_at DESC";
    $categories_stmt = $db->prepare($categories_query);
    $categories_stmt->execute();
    $categories = $categories_stmt->fetchAll();
} catch (Exception $e) {
    $categories = [];
    $errors[] = "Error fetching categories: " . $e->getMessage();
}

// Note: Helper functions (sanitizeInput, generateSlug, formatDate) are declared in config/database.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories - Admin Panel</title>
    
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
            padding: 2rem;
            border-bottom: 1px solid #e9ecef;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .admin-header h1 {
            margin: 0;
            color: var(--primary-color);
            font-size: 2rem;
            font-weight: 600;
        }

        .admin-header p {
            margin: 0.5rem 0 0 0;
            color: #6c757d;
            font-size: 1.1rem;
        }

        .admin-content {
            padding: 2rem;
        }

        /* Statistics Cards */
        .stats-row {
            margin-bottom: 2rem;
        }

        .stats-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            color: white;
            border-radius: 12px;
            padding: 1.5rem;
            height: 100%;
            position: relative;
            overflow: hidden;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
            transform: translate(20px, -20px);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 2;
        }

        .stats-label {
            font-size: 0.95rem;
            opacity: 0.9;
            position: relative;
            z-index: 2;
        }

        .stats-icon {
            position: absolute;
            top: 1rem;
            right: 1rem;
            font-size: 2rem;
            opacity: 0.3;
        }

        /* Action Header */
        .action-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 2rem;
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }

        .btn-add-category {
            background: linear-gradient(135deg, var(--secondary-color) 0%, #e6a502 100%);
            border: none;
            color: var(--primary-color);
            font-weight: 600;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-add-category:hover {
            color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(254, 186, 2, 0.4);
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        .table-header {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #dee2e6;
        }

        .table-header h5 {
            margin: 0;
            color: var(--primary-color);
            font-weight: 600;
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

        .category-color {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            display: inline-block;
        }

        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .blog-count-badge {
            background: linear-gradient(135deg, var(--info-color) 0%, #138496 100%);
            color: white;
            padding: 0.3rem 0.7rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Action Buttons */
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

        .btn-toggle {
            background: #fff3cd;
            color: #856404;
        }

        .btn-toggle:hover {
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

        /* Modals */
        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #0056b3 100%);
            color: white;
            border-radius: 12px 12px 0 0;
            border-bottom: none;
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 600;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 1.5rem;
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

        .color-picker {
            height: 45px;
            border-radius: 8px;
            cursor: pointer;
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

            .action-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .action-buttons {
                justify-content: center;
            }

            .table-responsive {
                font-size: 0.85rem;
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
                <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="add-blog.php"><i class="fas fa-plus"></i> Add Blog</a></li>
                <li class="active"><a href="manage-categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-header">
                <h1><i class="fas fa-tags"></i> Manage Blog Categories</h1>
                <p>Create, edit, and manage blog categories for better content organization</p>
            </div>

            <div class="admin-content">
                <!-- Statistics Cards -->
                <div class="row stats-row">
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <i class="fas fa-tags stats-icon"></i>
                            <div class="stats-number"><?php echo count($categories); ?></div>
                            <div class="stats-label">Total Categories</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <i class="fas fa-check-circle stats-icon"></i>
                            <div class="stats-number"><?php echo count(array_filter($categories, function($c) { return $c['status'] === 'active'; })); ?></div>
                            <div class="stats-label">Active Categories</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <i class="fas fa-pause-circle stats-icon"></i>
                            <div class="stats-number"><?php echo count(array_filter($categories, function($c) { return $c['status'] === 'inactive'; })); ?></div>
                            <div class="stats-label">Inactive Categories</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="stats-card">
                            <i class="fas fa-blog stats-icon"></i>
                            <div class="stats-number"><?php echo array_sum(array_column($categories, 'blog_count')); ?></div>
                            <div class="stats-label">Total Blog Posts</div>
                        </div>
                    </div>
                </div>

                <!-- Action Header -->
                <div class="action-header">
                    <div>
                        <h5 class="mb-0">Category Management</h5>
                        <small class="text-muted">Organize your blog content with categories</small>
                    </div>
                    <button type="button" class="btn btn-add-category" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="fas fa-plus"></i> Add New Category
                    </button>
                </div>

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

                <!-- Categories Table -->
                <div class="table-container">
                    <div class="table-header">
                        <h5><i class="fas fa-list me-2"></i>All Categories</h5>
                    </div>
                    
                    <?php if (!empty($categories)): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="60">Color</th>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th width="120">Status</th>
                                    <th width="100">Blog Posts</th>
                                    <th width="120">Created</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $category): ?>
                                <tr>
                                    <td>
                                        <div class="category-color" style="background-color: <?php echo htmlspecialchars($category['color']); ?>"></div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong><?php echo htmlspecialchars($category['name']); ?></strong>
                                            <br><small class="text-muted">Slug: <?php echo htmlspecialchars($category['slug']); ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-muted"><?php echo htmlspecialchars($category['description'] ?: 'No description'); ?></span>
                                    </td>
                                    <td>
                                        <span class="status-badge <?php echo $category['status'] === 'active' ? 'status-active' : 'status-inactive'; ?>">
                                            <?php echo ucfirst($category['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="blog-count-badge"><?php echo $category['blog_count']; ?></span>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo formatDate($category['created_at']); ?></small>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="btn-action btn-edit" 
                                                    onclick="editCategory(<?php echo htmlspecialchars(json_encode($category)); ?>)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            
                                            <form method="POST" style="display: inline-block;">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="id" value="<?php echo $category['id']; ?>">
                                                <input type="hidden" name="status" value="<?php echo $category['status'] === 'active' ? 'inactive' : 'active'; ?>">
                                                <button type="submit" class="btn-action btn-toggle">
                                                    <i class="fas <?php echo $category['status'] === 'active' ? 'fa-pause' : 'fa-play'; ?>"></i> 
                                                    <?php echo $category['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>
                                                </button>
                                            </form>
                                            
                                            <?php if ($category['blog_count'] == 0): ?>
                                            <button type="button" class="btn-action btn-delete" 
                                                    onclick="deleteCategory(<?php echo $category['id']; ?>, '<?php echo htmlspecialchars($category['name']); ?>')">
                                                <i class="fas fa-trash"></i> Delete
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-tags"></i>
                        <h5>No categories found</h5>
                        <p>Create your first category to get started organizing your blog content</p>
                        <button type="button" class="btn btn-add-category" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                            <i class="fas fa-plus"></i> Add Category
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

    <!-- Add Category Modal -->
    <div class="modal fade" id="addCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Add New Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add">
                        
                        <div class="mb-3">
                            <label for="add_name" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="add_name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="add_description" class="form-label">Description</label>
                            <textarea class="form-control" id="add_description" name="description" rows="3" placeholder="Brief description of this category"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_color" class="form-label">Category Color</label>
                                    <input type="color" class="form-control color-picker" id="add_color" name="color" value="#007bff">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="add_status" class="form-label">Status</label>
                                    <select class="form-control" id="add_status" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Add Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Category Modal -->
    <div class="modal fade" id="editCategoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Category</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" id="edit_id">
                        
                        <div class="mb-3">
                            <label for="edit_name" class="form-label">Category Name *</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_color" class="form-label">Category Color</label>
                                    <input type="color" class="form-control color-picker" id="edit_color" name="color">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">Status</label>
                                    <select class="form-control" id="edit_status" name="status">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i>Confirm Delete</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete the category "<span id="deleteCategoryName"></span>"?</p>
                    <p class="text-muted"><small>This action cannot be undone.</small></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteCategoryId">
                        <button type="submit" class="btn btn-danger">Delete Category</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function editCategory(category) {
            document.getElementById('edit_id').value = category.id;
            document.getElementById('edit_name').value = category.name;
            document.getElementById('edit_description').value = category.description || '';
            document.getElementById('edit_color').value = category.color;
            document.getElementById('edit_status').value = category.status;
            
            var editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            editModal.show();
        }

        function deleteCategory(id, name) {
            document.getElementById('deleteCategoryId').value = id;
            document.getElementById('deleteCategoryName').textContent = name;
            
            var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

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