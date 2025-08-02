<?php
session_start();
require_once '../config/database.php';

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

// Check for session success message
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_country':
                $name = trim($_POST['name']);
                $flag_code = trim($_POST['flag_code']);
                $description = trim($_POST['description']);
                $region = $_POST['region'];
                $student_count = intval($_POST['student_count']);
                $categories = isset($_POST['categories']) ? json_encode($_POST['categories']) : json_encode([]);
                $featured_image = trim($_POST['featured_image']);
                $meta_title = trim($_POST['meta_title']);
                $meta_description = trim($_POST['meta_description']);
                $sort_order = intval($_POST['sort_order']);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                // Generate slug
                $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $name)));
                
                if (empty($name) || empty($flag_code) || empty($region)) {
                    $errors[] = "Name, flag code, and region are required fields.";
                } else {
                    try {
                        $stmt = $db->prepare("INSERT INTO countries (name, slug, flag_code, description, region, student_count, categories, featured_image, meta_title, meta_description, sort_order, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$name, $slug, $flag_code, $description, $region, $student_count, $categories, $featured_image, $meta_title, $meta_description, $sort_order, $is_active]);
                        $_SESSION['success_message'] = "Country added successfully!";
                        header("Location: manage-countries.php");
                        exit();
                    } catch (Exception $e) {
                        $errors[] = "Error adding country: " . $e->getMessage();
                    }
                }
                break;
                
            case 'edit_country':
                $id = intval($_POST['country_id']);
                $name = trim($_POST['name']);
                $flag_code = trim($_POST['flag_code']);
                $description = trim($_POST['description']);
                $region = $_POST['region'];
                $student_count = intval($_POST['student_count']);
                $categories = isset($_POST['categories']) ? json_encode($_POST['categories']) : json_encode([]);
                $featured_image = trim($_POST['featured_image']);
                $meta_title = trim($_POST['meta_title']);
                $meta_description = trim($_POST['meta_description']);
                $sort_order = intval($_POST['sort_order']);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                // Generate slug
                $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $name)));
                
                if (empty($name) || empty($flag_code) || empty($region)) {
                    $errors[] = "Name, flag code, and region are required fields.";
                } else {
                    try {
                        $stmt = $db->prepare("UPDATE countries SET name = ?, slug = ?, flag_code = ?, description = ?, region = ?, student_count = ?, categories = ?, featured_image = ?, meta_title = ?, meta_description = ?, sort_order = ?, is_active = ? WHERE id = ?");
                        $stmt->execute([$name, $slug, $flag_code, $description, $region, $student_count, $categories, $featured_image, $meta_title, $meta_description, $sort_order, $is_active, $id]);
                        $_SESSION['success_message'] = "Country updated successfully!";
                        header("Location: manage-countries.php");
                        exit();
                    } catch (Exception $e) {
                        $errors[] = "Error updating country: " . $e->getMessage();
                    }
                }
                break;
                
            case 'toggle_status':
                $id = intval($_POST['country_id']);
                $new_status = intval($_POST['new_status']);
                
                try {
                    $stmt = $db->prepare("UPDATE countries SET is_active = ? WHERE id = ?");
                    $stmt->execute([$new_status, $id]);
                    $_SESSION['success_message'] = "Country status updated successfully!";
                    header("Location: manage-countries.php");
                    exit();
                } catch (Exception $e) {
                    $errors[] = "Error updating status: " . $e->getMessage();
                }
                break;
                
            case 'delete_country':
                $id = intval($_POST['country_id']);
                
                try {
                    // Check if country has universities
                    $checkStmt = $db->prepare("SELECT COUNT(*) as count FROM universities WHERE country_id = ?");
                    $checkStmt->execute([$id]);
                    $result = $checkStmt->fetch();
                    
                    if ($result['count'] > 0) {
                        $errors[] = "Cannot delete country. It has associated universities.";
                    } else {
                        $stmt = $db->prepare("DELETE FROM countries WHERE id = ?");
                        $stmt->execute([$id]);
                        $_SESSION['success_message'] = "Country deleted successfully!";
                        header("Location: manage-countries.php");
                        exit();
                    }
                } catch (Exception $e) {
                    $errors[] = "Error deleting country: " . $e->getMessage();
                }
                break;
                
            case 'bulk_action':
                $action = $_POST['bulk_action'] ?? '';
                $selected_ids = $_POST['selected_countries'] ?? [];
                

                
                if (empty($selected_ids)) {
                    $errors[] = "Please select at least one country to perform bulk action.";
                } elseif (empty($action)) {
                    $errors[] = "Please select an action to perform.";
                } else {
                    try {
                        $processed_count = 0;
                        $skipped_count = 0;
                        
                        switch ($action) {
                            case 'activate':
                                $stmt = $db->prepare("UPDATE countries SET is_active = 1 WHERE id = ?");
                                break;
                            case 'deactivate':
                                $stmt = $db->prepare("UPDATE countries SET is_active = 0 WHERE id = ?");
                                break;
                            case 'delete':
                                // For delete, check if country has universities
                                $checkStmt = $db->prepare("SELECT COUNT(*) as count FROM universities WHERE country_id = ?");
                                $stmt = $db->prepare("DELETE FROM countries WHERE id = ?");
                                break;
                            default:
                                $errors[] = "Invalid action selected.";
                                break 2; // Break out of both switch and try
                        }
                        
                        foreach ($selected_ids as $id) {
                            $id = intval($id);
                            if ($id > 0) {
                                if ($action === 'delete') {
                                    // Check if country has universities before deleting
                                    $checkStmt->execute([$id]);
                                    $result = $checkStmt->fetch();
                                    
                                    if ($result['count'] > 0) {
                                        $skipped_count++;
                                        continue; // Skip this country
                                    }
                                }
                                $stmt->execute([$id]);
                                $processed_count++;
                            }
                        }
                        
                        if ($processed_count > 0) {
                            $action_word = ucfirst($action);
                            if ($action === 'activate') $action_word = 'Activated';
                            elseif ($action === 'deactivate') $action_word = 'Deactivated';
                            elseif ($action === 'delete') $action_word = 'Deleted';
                            
                            $_SESSION['success_message'] = "{$action_word} {$processed_count} countr" . ($processed_count === 1 ? 'y' : 'ies') . " successfully!";
                            
                            if ($skipped_count > 0) {
                                $_SESSION['success_message'] .= " ({$skipped_count} countr" . ($skipped_count === 1 ? 'y' : 'ies') . " skipped - has universities)";
                            }
                        } else {
                            if ($skipped_count > 0) {
                                $errors[] = "Cannot delete selected countries. They have associated universities.";
                            } else {
                                $errors[] = "No valid countries found to process.";
                            }
                        }
                        
                        if (empty($errors)) {
                            header("Location: manage-countries.php");
                            exit();
                        }
                    } catch (Exception $e) {
                        $errors[] = "Error performing bulk action: " . $e->getMessage();
                    }
                }
                break;
        }
    }
}

// Pagination and filtering
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$region_filter = isset($_GET['region']) ? trim($_GET['region']) : '';
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build WHERE clause
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($region_filter)) {
    $where_conditions[] = "region = ?";
    $params[] = $region_filter;
}

if ($status_filter !== '') {
    $where_conditions[] = "is_active = ?";
    $params[] = intval($status_filter);
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get total count
$count_query = "SELECT COUNT(*) as total FROM countries $where_clause";
$count_stmt = $db->prepare($count_query);
$count_stmt->execute($params);
$total_countries = $count_stmt->fetch()['total'];
$total_pages = ceil($total_countries / $per_page);

// Get countries with pagination
$query = "SELECT c.*, (SELECT COUNT(*) FROM universities u WHERE u.country_id = c.id) as universities_count 
          FROM countries c 
          $where_clause 
          ORDER BY c.sort_order ASC, c.name ASC 
          LIMIT $per_page OFFSET $offset";
$stmt = $db->prepare($query);
$stmt->execute($params);
$countries = $stmt->fetchAll();

// Get country for editing if requested
$editing_country = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_stmt = $db->prepare("SELECT * FROM countries WHERE id = ?");
    $edit_stmt->execute([$edit_id]);
    $editing_country = $edit_stmt->fetch();
}

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Countries - MedStudy Global Admin</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/webp" href="../assets/images/media/logo/sunrise-logo.webp">
    <link rel="shortcut icon" type="image/webp" href="../assets/images/media/logo/sunrise-logo.webp">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #003585;
            --primary-dark: #002a6a;
            --accent-color: #FEBA02;
            --light-bg: #f8f9fa;
            --border-color: #e9e9e9;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--light-bg);
            color: #333;
        }

        .admin-container {
            display: flex;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .admin-sidebar {
            width: 250px;
            background: var(--primary-color);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 600;
        }

        .sidebar-menu {
            list-style: none;
            padding: 1rem 0;
        }

        .sidebar-menu li {
            margin: 0;
        }

        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
        }

        .sidebar-menu a:hover,
        .sidebar-menu .active a {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-menu i {
            margin-right: 0.75rem;
            width: 20px;
        }

        /* Main Content Styles */
        .admin-main {
            flex: 1;
            margin-left: 250px;
            min-height: 100vh;
        }

        .admin-content {
            padding: 2rem;
        }

        .admin-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .admin-header h1 {
            color: var(--primary-color);
            font-weight: 600;
            margin: 0;
        }

        /* Card Styles */
        .admin-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: white;
            padding: 1.5rem;
            border: none;
        }

        .card-header h5 {
            margin: 0;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Form Styles */
        .form-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-group.half {
            flex: 1;
        }

        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(0, 53, 133, 0.25);
        }

        /* Button Styles */
        .btn {
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s;
            border: none;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        .btn-success {
            background: #28a745;
        }

        .btn-warning {
            background: var(--accent-color);
            color: #333;
        }

        .btn-danger {
            background: #dc3545;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        /* Table Styles */
        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .table {
            margin: 0;
        }

        .table th {
            background: var(--light-bg);
            color: var(--primary-color);
            font-weight: 600;
            border: none;
            padding: 1rem;
        }

        .table td {
            padding: 1rem;
            vertical-align: middle;
            border-color: var(--border-color);
        }

        .table tbody tr:hover {
            background: rgba(0, 53, 133, 0.05);
        }

        /* Filter Styles */
        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .filters-left {
            display: flex;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-left: 2.5rem;
            width: 250px;
        }

        .search-box i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        /* Status Badge */
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        /* Flag Icon */
        .flag-icon {
            width: 24px;
            height: auto;
            border-radius: 3px;
        }

        /* Categories Display */
        .categories-display {
            display: flex;
            gap: 0.25rem;
            flex-wrap: wrap;
        }

        .category-tag {
            background: var(--accent-color);
            color: #333;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Pagination */
        .pagination {
            margin: 0;
        }

        .pagination .page-link {
            border-color: var(--border-color);
            color: var(--primary-color);
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

        /* Alert Styles */
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

            .form-row {
                flex-direction: column;
            }

            .filters-header {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box input {
                width: 100%;
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
                <li><a href="manage-categories.php"><i class="fas fa-tags"></i> Categories</a></li>
                <li class="active"><a href="manage-countries.php"><i class="fas fa-globe"></i> Countries</a></li>
                <li><a href="manage-universities.php"><i class="fas fa-university"></i> Universities</a></li>
                <li><a href="../blog.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Blog</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-content">
                <div class="admin-header">
                    <h1><i class="fas fa-globe"></i> Manage Countries</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCountryModal">
                        <i class="fas fa-plus"></i> Add New Country
                    </button>
                </div>

                <!-- Success/Error Messages -->
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <?php foreach ($errors as $error): ?>
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Filters and Search -->
                <div class="admin-card">
                    <div class="card-body">
                        <form method="GET" class="filters-header">
                            <div class="filters-left">
                                <div class="search-box">
                                    <i class="fas fa-search"></i>
                                    <input type="text" name="search" class="form-control" placeholder="Search countries..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                
                                <select name="region" class="form-control" style="width: auto;">
                                    <option value="">All Regions</option>
                                    <option value="asia" <?php echo $region_filter === 'asia' ? 'selected' : ''; ?>>Asia</option>
                                    <option value="europe" <?php echo $region_filter === 'europe' ? 'selected' : ''; ?>>Europe</option>
                                    <option value="africa" <?php echo $region_filter === 'africa' ? 'selected' : ''; ?>>Africa</option>
                                    <option value="americas" <?php echo $region_filter === 'americas' ? 'selected' : ''; ?>>Americas</option>
                                    <option value="oceania" <?php echo $region_filter === 'oceania' ? 'selected' : ''; ?>>Oceania</option>
                                </select>
                                
                                <select name="status" class="form-control" style="width: auto;">
                                    <option value="">All Status</option>
                                    <option value="1" <?php echo $status_filter === '1' ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo $status_filter === '0' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                
                                <a href="manage-countries.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Bulk Actions Bar -->
                <div class="admin-card">
                    <div class="card-body">
                        <form method="POST" id="bulkActionForm">
                            <input type="hidden" name="action" value="bulk_action">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                    <label for="selectAll" class="form-check-label">Select All</label>
                                    
                                    <select name="bulk_action" class="form-control" style="width: auto;">
                                        <option value="">Bulk Actions</option>
                                        <option value="activate">Activate</option>
                                        <option value="deactivate">Deactivate</option>
                                        <option value="delete">Delete</option>
                                    </select>
                                    
                                    <button type="submit" class="btn btn-warning btn-sm" onclick="return validateBulkAction()">
                                        Apply
                                    </button>
                                </div>
                                
                                <div class="text-muted">
                                    Total: <?php echo $total_countries; ?> countries
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Countries Table -->
                <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input" id="selectAllTable">
                                    </th>
                                    <th width="60">Flag</th>
                                    <th>Name</th>
                                    <th>Region</th>
                                    <th>Universities</th>
                                    <th>Students</th>
                                    <th>Categories</th>
                                    <th>Status</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($countries)): ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-5">
                                            <i class="fas fa-globe fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No countries found</h5>
                                            <p class="text-muted">Add your first country to get started.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($countries as $country): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_countries[]" value="<?php echo $country['id']; ?>" class="form-check-input country-checkbox">
                                            </td>
                                            <td>
                                                <span class="flag-icon flag-icon-<?php echo strtolower($country['flag_code']); ?>"></span>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($country['name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($country['slug']); ?></small>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo ucfirst($country['region']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-primary"><?php echo $country['universities_count']; ?></span>
                                            </td>
                                            <td>
                                                <?php echo number_format($country['student_count']); ?>
                                            </td>
                                            <td>
                                                <div class="categories-display">
                                                    <?php 
                                                    $categories = json_decode($country['categories'], true) ?: [];
                                                    foreach ($categories as $category): 
                                                    ?>
                                                        <span class="category-tag"><?php echo ucfirst($category); ?></span>
                                                    <?php endforeach; ?>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="status-badge <?php echo $country['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                                    <?php echo $country['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="?edit=<?php echo $country['id']; ?>" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="country_id" value="<?php echo $country['id']; ?>">
                                                        <input type="hidden" name="new_status" value="<?php echo $country['is_active'] ? 0 : 1; ?>">
                                                        <button type="submit" class="btn btn-<?php echo $country['is_active'] ? 'secondary' : 'success'; ?> btn-sm" title="<?php echo $country['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                                            <i class="fas fa-<?php echo $country['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this country? This action cannot be undone.')">
                                                        <input type="hidden" name="action" value="delete_country">
                                                        <input type="hidden" name="country_id" value="<?php echo $country['id']; ?>">
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Delete">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <nav>
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&region=<?php echo urlencode($region_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&region=<?php echo urlencode($region_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&region=<?php echo urlencode($region_filter); ?>&status=<?php echo urlencode($status_filter); ?>">
                                            <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Add/Edit Country Modal -->
    <div class="modal fade" id="addCountryModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?php echo $editing_country ? 'Edit Country' : 'Add New Country'; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $editing_country ? 'edit_country' : 'add_country'; ?>">
                        <?php if ($editing_country): ?>
                            <input type="hidden" name="country_id" value="<?php echo $editing_country['id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-row">
                            <div class="form-group half">
                                <label class="form-label">Country Name *</label>
                                <input type="text" name="name" class="form-control" required 
                                       value="<?php echo $editing_country ? htmlspecialchars($editing_country['name']) : ''; ?>">
                            </div>
                            <div class="form-group half">
                                <label class="form-label">Flag Code *</label>
                                <input type="text" name="flag_code" class="form-control" required placeholder="e.g., us, ru, in"
                                       value="<?php echo $editing_country ? htmlspecialchars($editing_country['flag_code']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group half">
                                <label class="form-label">Region *</label>
                                <select name="region" class="form-control" required>
                                    <option value="">Select Region</option>
                                    <option value="asia" <?php echo ($editing_country && $editing_country['region'] === 'asia') ? 'selected' : ''; ?>>Asia</option>
                                    <option value="europe" <?php echo ($editing_country && $editing_country['region'] === 'europe') ? 'selected' : ''; ?>>Europe</option>
                                    <option value="africa" <?php echo ($editing_country && $editing_country['region'] === 'africa') ? 'selected' : ''; ?>>Africa</option>
                                    <option value="americas" <?php echo ($editing_country && $editing_country['region'] === 'americas') ? 'selected' : ''; ?>>Americas</option>
                                    <option value="oceania" <?php echo ($editing_country && $editing_country['region'] === 'oceania') ? 'selected' : ''; ?>>Oceania</option>
                                </select>
                            </div>
                            <div class="form-group half">
                                <label class="form-label">Student Count</label>
                                <input type="number" name="student_count" class="form-control" min="0"
                                       value="<?php echo $editing_country ? $editing_country['student_count'] : '0'; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"><?php echo $editing_country ? htmlspecialchars($editing_country['description']) : ''; ?></textarea>
                        </div>
                        
                        <!-- <div class="form-group">
                            <label class="form-label">Featured Image URL (Cloudinary)</label>
                            <input type="url" name="featured_image" class="form-control"
                                   value="<?php echo $editing_country ? htmlspecialchars($editing_country['featured_image']) : ''; ?>">
                        </div> -->
                        
                        <div class="form-row">
                            <div class="form-group half">
                                <label class="form-label">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control"
                                       value="<?php echo $editing_country ? htmlspecialchars($editing_country['meta_title']) : ''; ?>">
                            </div>
                            <div class="form-group half">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control" min="0"
                                       value="<?php echo $editing_country ? $editing_country['sort_order'] : '0'; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="2"><?php echo $editing_country ? htmlspecialchars($editing_country['meta_description']) : ''; ?></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Categories</label>
                            <div class="d-flex gap-3">
                                <?php 
                                $existing_categories = $editing_country ? json_decode($editing_country['categories'], true) ?: [] : [];
                                $available_categories = ['popular', 'budget', 'premium'];
                                foreach ($available_categories as $category): 
                                ?>
                                    <div class="form-check">
                                        <input type="checkbox" name="categories[]" value="<?php echo $category; ?>" 
                                               class="form-check-input" id="cat_<?php echo $category; ?>"
                                               <?php echo in_array($category, $existing_categories) ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="cat_<?php echo $category; ?>">
                                            <?php echo ucfirst($category); ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                                       <?php echo (!$editing_country || $editing_country['is_active']) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="is_active">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <?php echo $editing_country ? 'Update Country' : 'Add Country'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.country-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectAllState();
        });

        document.getElementById('selectAllTable').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.country-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            document.getElementById('selectAll').checked = this.checked;
            updateSelectAllState();
        });

        // Update select all state when individual checkboxes are clicked
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('country-checkbox')) {
                updateSelectAllState();
            }
        });



        function updateSelectAllState() {
            const checkboxes = document.querySelectorAll('.country-checkbox');
            const checkedBoxes = document.querySelectorAll('.country-checkbox:checked');
            const selectAllCheckbox = document.getElementById('selectAll');
            const selectAllTableCheckbox = document.getElementById('selectAllTable');
            
            if (checkedBoxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
                selectAllTableCheckbox.indeterminate = false;
                selectAllTableCheckbox.checked = false;
            } else if (checkedBoxes.length === checkboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
                selectAllTableCheckbox.indeterminate = false;
                selectAllTableCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
                selectAllCheckbox.checked = false;
                selectAllTableCheckbox.indeterminate = true;
                selectAllTableCheckbox.checked = false;
            }
        }

        // Bulk action validation
        function validateBulkAction() {
            const checkedBoxes = document.querySelectorAll('.country-checkbox:checked');
            const bulkAction = document.querySelector('select[name="bulk_action"]').value;
            const form = document.getElementById('bulkActionForm');
            
            if (checkedBoxes.length === 0) {
                alert('Please select at least one country to perform bulk action.');
                return false;
            }
            
            if (!bulkAction) {
                alert('Please select an action to perform.');
                return false;
            }
            
            // Clear any existing selected_countries inputs
            const existingInputs = form.querySelectorAll('input[name="selected_countries[]"]');
            existingInputs.forEach(input => input.remove());
            
            // Add selected IDs to the form
            checkedBoxes.forEach((box) => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'selected_countries[]';
                hiddenInput.value = box.value;
                form.appendChild(hiddenInput);
            });
            
            let confirmMessage = `Are you sure you want to ${bulkAction} ${checkedBoxes.length} selected countr${checkedBoxes.length === 1 ? 'y' : 'ies'}?`;
            
            if (bulkAction === 'delete') {
                confirmMessage = `⚠️ WARNING: This will permanently delete ${checkedBoxes.length} countr${checkedBoxes.length === 1 ? 'y' : 'ies'} and may affect associated universities!\n\nThis action cannot be undone. Are you sure you want to continue?`;
            }
            
            return confirm(confirmMessage);
        }

        // Auto-open modal if editing
        <?php if ($editing_country): ?>
            const editModal = new bootstrap.Modal(document.getElementById('addCountryModal'));
            editModal.show();
        <?php endif; ?>
    </script>
</body>
</html> 