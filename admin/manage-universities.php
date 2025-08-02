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
            case 'add_university':
                $country_id = intval($_POST['country_id']);
                $name = trim($_POST['name']);
                $featured_image = trim($_POST['featured_image']);
                $logo_image = trim($_POST['logo_image']);
                $about_university = trim($_POST['about_university']);
                $course_duration = trim($_POST['course_duration']);
                $language_of_instruction = trim($_POST['language_of_instruction']);
                $annual_fees = floatval($_POST['annual_fees']);
                $location = trim($_POST['location']);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                // Generate slug
                $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $name)));
                
                if (empty($name) || empty($country_id) || empty($location)) {
                    $errors[] = "Name, country, and location are required fields.";
                } else {
                    try {
                        $stmt = $db->prepare("INSERT INTO universities (country_id, name, slug, featured_image, logo_image, about_university, course_duration, language_of_instruction, annual_fees, location, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$country_id, $name, $slug, $featured_image, $logo_image, $about_university, $course_duration, $language_of_instruction, $annual_fees, $location, $is_active]);
                        $_SESSION['success_message'] = "University added successfully!";
                        header("Location: manage-universities.php");
                        exit();
                    } catch (Exception $e) {
                        $errors[] = "Error adding university: " . $e->getMessage();
                    }
                }
                break;
                
            case 'edit_university':
                $id = intval($_POST['university_id']);
                $country_id = intval($_POST['country_id']);
                $name = trim($_POST['name']);
                $featured_image = trim($_POST['featured_image']);
                $logo_image = trim($_POST['logo_image']);
                $about_university = trim($_POST['about_university']);
                $course_duration = trim($_POST['course_duration']);
                $language_of_instruction = trim($_POST['language_of_instruction']);
                $annual_fees = floatval($_POST['annual_fees']);
                $location = trim($_POST['location']);
                $is_active = isset($_POST['is_active']) ? 1 : 0;
                
                // Generate slug
                $slug = strtolower(str_replace(' ', '-', preg_replace('/[^A-Za-z0-9 ]/', '', $name)));
                
                if (empty($name) || empty($country_id) || empty($location)) {
                    $errors[] = "Name, country, and location are required fields.";
                } else {
                    try {
                        $stmt = $db->prepare("UPDATE universities SET country_id = ?, name = ?, slug = ?, featured_image = ?, logo_image = ?, about_university = ?, course_duration = ?, language_of_instruction = ?, annual_fees = ?, location = ?, is_active = ? WHERE id = ?");
                        $stmt->execute([$country_id, $name, $slug, $featured_image, $logo_image, $about_university, $course_duration, $language_of_instruction, $annual_fees, $location, $is_active, $id]);
                        $_SESSION['success_message'] = "University updated successfully!";
                        header("Location: manage-universities.php");
                        exit();
                    } catch (Exception $e) {
                        $errors[] = "Error updating university: " . $e->getMessage();
                    }
                }
                break;
                
            case 'toggle_status':
                $id = intval($_POST['university_id']);
                $new_status = intval($_POST['new_status']);
                
                try {
                    $stmt = $db->prepare("UPDATE universities SET is_active = ? WHERE id = ?");
                    $stmt->execute([$new_status, $id]);
                    $_SESSION['success_message'] = "University status updated successfully!";
                    header("Location: manage-universities.php");
                    exit();
                } catch (Exception $e) {
                    $errors[] = "Error updating status: " . $e->getMessage();
                }
                break;
                
            case 'delete_university':
                $id = intval($_POST['university_id']);
                
                try {
                    // Delete associated images first
                    $deleteImagesStmt = $db->prepare("DELETE FROM university_images WHERE university_id = ?");
                    $deleteImagesStmt->execute([$id]);
                    
                    // Delete university
                    $stmt = $db->prepare("DELETE FROM universities WHERE id = ?");
                    $stmt->execute([$id]);
                    $_SESSION['success_message'] = "University deleted successfully!";
                    header("Location: manage-universities.php");
                    exit();
                } catch (Exception $e) {
                    $errors[] = "Error deleting university: " . $e->getMessage();
                }
                break;
                
            case 'bulk_action':
                $action = $_POST['bulk_action'] ?? '';
                $selected_ids = $_POST['selected_universities'] ?? [];
                

                
                if (empty($selected_ids)) {
                    $errors[] = "Please select at least one university to perform bulk action.";
                } elseif (empty($action)) {
                    $errors[] = "Please select an action to perform.";
                } else {
                    try {
                        $processed_count = 0;
                        
                        switch ($action) {
                            case 'activate':
                                $stmt = $db->prepare("UPDATE universities SET is_active = 1 WHERE id = ?");
                                break;
                            case 'deactivate':
                                $stmt = $db->prepare("UPDATE universities SET is_active = 0 WHERE id = ?");
                                break;
                            case 'delete':
                                // Delete images first
                                $deleteImagesStmt = $db->prepare("DELETE FROM university_images WHERE university_id = ?");
                                $stmt = $db->prepare("DELETE FROM universities WHERE id = ?");
                                break;
                            default:
                                $errors[] = "Invalid action selected.";
                                break 2; // Break out of both switch and try
                        }
                        
                        foreach ($selected_ids as $id) {
                            $id = intval($id);
                            if ($id > 0) {
                                if ($action === 'delete') {
                                    $deleteImagesStmt->execute([$id]);
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
                            
                            $_SESSION['success_message'] = "{$action_word} {$processed_count} universit" . ($processed_count === 1 ? 'y' : 'ies') . " successfully!";
                        } else {
                            $errors[] = "No valid universities found to process.";
                        }
                        
                        if (empty($errors)) {
                            header("Location: manage-universities.php");
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
$country_filter = isset($_GET['country']) ? intval($_GET['country']) : 0;
$status_filter = isset($_GET['status']) ? trim($_GET['status']) : '';

// Build WHERE clause
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(u.name LIKE ? OR u.location LIKE ? OR u.about_university LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($country_filter)) {
    $where_conditions[] = "u.country_id = ?";
    $params[] = $country_filter;
}

if ($status_filter !== '') {
    $where_conditions[] = "u.is_active = ?";
    $params[] = intval($status_filter);
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Get total count
$count_query = "SELECT COUNT(*) as total FROM universities u $where_clause";
$count_stmt = $db->prepare($count_query);
$count_stmt->execute($params);
$total_universities = $count_stmt->fetch()['total'];
$total_pages = ceil($total_universities / $per_page);

// Get universities with pagination
$query = "SELECT u.*, c.name as country_name, c.flag_code,
          (SELECT COUNT(*) FROM university_images ui WHERE ui.university_id = u.id) as images_count
          FROM universities u 
          LEFT JOIN countries c ON u.country_id = c.id
          $where_clause 
          ORDER BY u.name ASC 
          LIMIT $per_page OFFSET $offset";
$stmt = $db->prepare($query);
$stmt->execute($params);
$universities = $stmt->fetchAll();

// Get countries for dropdown
$countriesStmt = $db->prepare("SELECT id, name, flag_code FROM countries ORDER BY name ASC");
$countriesStmt->execute();
$countries = $countriesStmt->fetchAll();

// Get university for editing if requested
$editing_university = null;
if (isset($_GET['edit']) && !empty($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $edit_stmt = $db->prepare("SELECT * FROM universities WHERE id = ?");
    $edit_stmt->execute([$edit_id]);
    $editing_university = $edit_stmt->fetch();
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
    <title>Manage Universities - MedStudy Global Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css" rel="stylesheet">
    
    <!-- TinyMCE (Rich Text Editor) -->
    <script src="https://cdn.tiny.cloud/1/ckhdla67dgiuczihylz9vgm24qocra38y6d17t4zfaad8v8b/tinymce/7/tinymce.min.js" referrerpolicy="strict-origin-when-cross-origin"></script>
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
            justify-content: space-between;
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

        /* University Logo */
        .university-logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            object-fit: contain;
            background: #f8f9fa;
            padding: 4px;
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
                <li><a href="manage-countries.php"><i class="fas fa-globe"></i> Countries</a></li>
                <li class="active"><a href="manage-universities.php"><i class="fas fa-university"></i> Universities</a></li>
                <li><a href="../blog.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Blog</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-content">
                <div class="admin-header">
                    <h1><i class="fas fa-university"></i> Manage Universities</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUniversityModal">
                        <i class="fas fa-plus"></i> Add New University
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
                                    <input type="text" name="search" class="form-control" placeholder="Search universities..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                
                                <select name="country" class="form-control" style="width: auto;">
                                    <option value="">All Countries</option>
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?php echo $country['id']; ?>" <?php echo $country_filter == $country['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($country['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <select name="status" class="form-control" style="width: auto;">
                                    <option value="">All Status</option>
                                    <option value="1" <?php echo $status_filter === '1' ? 'selected' : ''; ?>>Active</option>
                                    <option value="0" <?php echo $status_filter === '0' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                                
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Filter
                                </button>
                                
                                <a href="manage-universities.php" class="btn btn-secondary">
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
                                    Total: <?php echo $total_universities; ?> universities
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Universities Table -->
                <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="50">
                                        <input type="checkbox" class="form-check-input" id="selectAllTable">
                                    </th>
                                    <th width="60">Logo</th>
                                    <th>University</th>
                                    <th>Country</th>
                                    <th>Location</th>
                                    <th>Duration</th>
                                    <th>Language</th>
                                    <th>Images</th>
                                    <th>Status</th>
                                    <th width="200">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($universities)): ?>
                                    <tr>
                                        <td colspan="10" class="text-center py-5">
                                            <i class="fas fa-university fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No universities found</h5>
                                            <p class="text-muted">Add your first university to get started.</p>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($universities as $university): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_universities[]" value="<?php echo $university['id']; ?>" class="form-check-input university-checkbox">
                                            </td>
                                            <td>
                                                <?php if (!empty($university['logo_image'])): ?>
                                                    <img src="<?php echo htmlspecialchars($university['logo_image']); ?>" 
                                                         alt="Logo" class="university-logo">
                                                <?php else: ?>
                                                    <div class="university-logo d-flex align-items-center justify-content-center">
                                                        <i class="fas fa-university text-muted"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($university['name']); ?></strong>
                                                <br>
                                                <small class="text-muted"><?php echo htmlspecialchars($university['slug']); ?></small>
                                            </td>
                                            <td>
                                                <span class="flag-icon flag-icon-<?php echo strtolower($university['flag_code']); ?>"></span>
                                                <?php echo htmlspecialchars($university['country_name']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($university['location']); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($university['course_duration'] ?: 'N/A'); ?>
                                            </td>
                                            <td>
                                                <?php echo htmlspecialchars($university['language_of_instruction'] ?: 'N/A'); ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-info"><?php echo $university['images_count']; ?></span>
                                            </td>
                                            <td>
                                                <span class="status-badge <?php echo $university['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                                    <?php echo $university['is_active'] ? 'Active' : 'Inactive'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="?edit=<?php echo $university['id']; ?>" class="btn btn-warning btn-sm" title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?')">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="university_id" value="<?php echo $university['id']; ?>">
                                                        <input type="hidden" name="new_status" value="<?php echo $university['is_active'] ? 0 : 1; ?>">
                                                        <button type="submit" class="btn btn-<?php echo $university['is_active'] ? 'secondary' : 'success'; ?> btn-sm" title="<?php echo $university['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                                            <i class="fas fa-<?php echo $university['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this university? This action cannot be undone.')">
                                                        <input type="hidden" name="action" value="delete_university">
                                                        <input type="hidden" name="university_id" value="<?php echo $university['id']; ?>">
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
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&country=<?php echo $country_filter; ?>&status=<?php echo urlencode($status_filter); ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&country=<?php echo $country_filter; ?>&status=<?php echo urlencode($status_filter); ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&country=<?php echo $country_filter; ?>&status=<?php echo urlencode($status_filter); ?>">
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

    <!-- Add/Edit University Modal -->
    <div class="modal fade" id="addUniversityModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <?php echo $editing_university ? 'Edit University' : 'Add New University'; ?>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="<?php echo $editing_university ? 'edit_university' : 'add_university'; ?>">
                        <?php if ($editing_university): ?>
                            <input type="hidden" name="university_id" value="<?php echo $editing_university['id']; ?>">
                        <?php endif; ?>
                        
                        <!-- Basic Information Section -->
                        <h6 class="text-primary mb-3"><i class="fas fa-info-circle"></i> Basic Information</h6>
                        
                        <div class="form-row">
                            <div class="form-group half">
                                <label class="form-label">University Name *</label>
                                <input type="text" name="name" class="form-control" required 
                                       value="<?php echo $editing_university ? htmlspecialchars($editing_university['name']) : ''; ?>">
                            </div>
                            <div class="form-group half">
                                <label class="form-label">Country *</label>
                                <select name="country_id" class="form-control" required>
                                    <option value="">Select Country</option>
                                    <?php foreach ($countries as $country): ?>
                                        <option value="<?php echo $country['id']; ?>" 
                                                <?php echo ($editing_university && $editing_university['country_id'] == $country['id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($country['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Location *</label>
                            <input type="text" name="location" class="form-control" required placeholder="City, Country"
                                   value="<?php echo $editing_university ? htmlspecialchars($editing_university['location']) : ''; ?>">
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group half">
                                <label class="form-label">Featured Image URL (Cloudinary)</label>
                                <input type="url" name="featured_image" class="form-control"
                                       value="<?php echo $editing_university ? htmlspecialchars($editing_university['featured_image']) : ''; ?>">
                            </div>
                            <!-- <div class="form-group half">
                                <label class="form-label">Logo Image URL (Cloudinary)</label>
                                <input type="url" name="logo_image" class="form-control"
                                       value="<?php echo $editing_university ? htmlspecialchars($editing_university['logo_image']) : ''; ?>">
                            </div> -->
                        </div>
                        
                        <!-- Academic Information Section -->
                        <hr class="my-4">
                        <h6 class="text-primary mb-3"><i class="fas fa-graduation-cap"></i> Academic Information</h6>
                        
                        <div class="form-group">
                            <label class="form-label">About University</label>
                            <div class="alert alert-warning tinymce-error" style="display: none;">
                                <i class="fas fa-exclamation-triangle"></i>
                                Rich text editor failed to load. You can still use the basic text area below.
                            </div>
                            <textarea name="about_university" class="form-control" id="about_university" rows="6" 
                                      placeholder="Detailed description about the university..."><?php echo $editing_university ? htmlspecialchars($editing_university['about_university']) : ''; ?></textarea>
                            <small class="form-text text-muted">Use the rich text editor to format your content with headings, lists, links, and more.</small>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group half">
                                <label class="form-label">Course Duration</label>
                                <input type="text" name="course_duration" class="form-control" placeholder="e.g., 6 years"
                                       value="<?php echo $editing_university ? htmlspecialchars($editing_university['course_duration']) : ''; ?>">
                            </div>
                            <div class="form-group half">
                                <label class="form-label">Language of Instruction</label>
                                <input type="text" name="language_of_instruction" class="form-control" placeholder="e.g., English"
                                       value="<?php echo $editing_university ? htmlspecialchars($editing_university['language_of_instruction']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <!-- <div class="form-group half">
                                <label class="form-label">Annual Fees (USD)</label>
                                <input type="number" name="annual_fees" class="form-control" min="0" step="0.01"
                                       value="<?php echo $editing_university ? $editing_university['annual_fees'] : ''; ?>">
                            </div> -->
                            <div class="form-group half">
                                <label class="form-label">Status</label>
                                <div class="form-check mt-2">
                                    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                                           <?php echo (!$editing_university || $editing_university['is_active']) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="is_active">
                                        Active
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <?php echo $editing_university ? 'Update University' : 'Add University'; ?>
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
            const checkboxes = document.querySelectorAll('.university-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectAllState();
        });

        document.getElementById('selectAllTable').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.university-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            document.getElementById('selectAll').checked = this.checked;
            updateSelectAllState();
        });

        // Update select all state when individual checkboxes are clicked
        document.addEventListener('change', function(e) {
            if (e.target.classList.contains('university-checkbox')) {
                updateSelectAllState();
            }
        });



        function updateSelectAllState() {
            const checkboxes = document.querySelectorAll('.university-checkbox');
            const checkedBoxes = document.querySelectorAll('.university-checkbox:checked');
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
            const checkedBoxes = document.querySelectorAll('.university-checkbox:checked');
            const bulkAction = document.querySelector('select[name="bulk_action"]').value;
            const form = document.getElementById('bulkActionForm');
            
            if (checkedBoxes.length === 0) {
                alert('Please select at least one university to perform bulk action.');
                return false;
            }
            
            if (!bulkAction) {
                alert('Please select an action to perform.');
                return false;
            }
            
            // Clear any existing selected_universities inputs
            const existingInputs = form.querySelectorAll('input[name="selected_universities[]"]');
            existingInputs.forEach(input => input.remove());
            
            // Add selected IDs to the form
            checkedBoxes.forEach((box) => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'selected_universities[]';
                hiddenInput.value = box.value;
                form.appendChild(hiddenInput);
            });
            
            let confirmMessage = `Are you sure you want to ${bulkAction} ${checkedBoxes.length} selected universit${checkedBoxes.length === 1 ? 'y' : 'ies'}?`;
            
            if (bulkAction === 'delete') {
                confirmMessage = `⚠️ WARNING: This will permanently delete ${checkedBoxes.length} universit${checkedBoxes.length === 1 ? 'y' : 'ies'} and all associated data!\n\nThis action cannot be undone. Are you sure you want to continue?`;
            }
            
            return confirm(confirmMessage);
        }

        // Auto-open modal if editing
        <?php if ($editing_university): ?>
            const editModal = new bootstrap.Modal(document.getElementById('addUniversityModal'));
            editModal.show();
        <?php endif; ?>

        // Initialize TinyMCE for About University textarea
        document.addEventListener('DOMContentLoaded', function() {
            // Debug: Log current origin for TinyMCE troubleshooting
            console.log('Current origin for TinyMCE:', window.location.origin);
            console.log('Current hostname:', window.location.hostname);
            
            // Initialize TinyMCE for about_university
            tinymce.init({
                selector: '#about_university',
                height: 300,
                menubar: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | ' +
                        'bold italic forecolor | alignleft aligncenter ' +
                        'alignright alignjustify | bullist numlist outdent indent | ' +
                        'link unlink | removeformat | help',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; font-size: 14px }',
                // Link configuration
                link_assume_external_targets: true,
                link_context_toolbar: true,
                link_default_target: '_blank',
                link_default_protocol: 'https',
                link_title: false,
                target_list: [
                    {title: 'None', value: ''},
                    {title: 'New window', value: '_blank'},
                    {title: 'Same window', value: '_self'}
                ],
                branding: false,
                promotion: false,
                // Privacy settings to disable analytics and telemetry
                analytics: false,
                telemetry: false,
                statistics: false,
                privacy_policy_url: '',
                // Additional privacy settings
                referrer_policy: 'no-referrer',
                skin: 'oxide',
                content_css: 'default',
                // Domain configuration for localhost development
                document_base_url: window.location.origin,
                // Additional settings for better UX
                help_accessibility: false,
                statusbar: false,
                convert_urls: false,
                remove_script_host: false,
                relative_urls: false,
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                    
                    // Handle initialization errors
                    editor.on('init', function() {
                        console.log('TinyMCE initialized successfully for:', editor.id);
                        // Hide any error messages
                        const errorElements = document.querySelectorAll('.tinymce-error');
                        errorElements.forEach(function(el) {
                            el.style.display = 'none';
                        });
                    });
                    
                    editor.on('LoadError', function(e) {
                        console.error('TinyMCE Load Error:', e);
                        const errorElements = document.querySelectorAll('.tinymce-error');
                        errorElements.forEach(function(el) {
                            el.style.display = 'block';
                        });
                    });
                },
                // Error handling
                init_instance_callback: function(editor) {
                    console.log('TinyMCE instance callback:', editor.id);
                }
            }).then(function(editors) {
                console.log('TinyMCE initialized successfully');
            }).catch(function(error) {
                console.error('TinyMCE initialization failed:', error);
                // Show error message and fallback to basic textarea
                document.querySelector('.tinymce-error').style.display = 'block';
                document.getElementById('about_university').style.display = 'block';
            });
        });
    </script>
</body>
</html> 