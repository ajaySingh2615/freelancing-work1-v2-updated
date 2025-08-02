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

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_images':
                $university_id = intval($_POST['university_id']);
                $image_urls = array_filter(array_map('trim', explode("\n", $_POST['image_urls'])));
                
                if (empty($university_id)) {
                    $errors[] = "Please select a university.";
                } elseif (empty($image_urls)) {
                    $errors[] = "Please provide at least one image URL.";
                } else {
                    try {
                        $stmt = $db->prepare("INSERT INTO university_images (university_id, image_url) VALUES (?, ?)");
                        $added_count = 0;
                        
                        foreach ($image_urls as $url) {
                            if (filter_var($url, FILTER_VALIDATE_URL)) {
                                $stmt->execute([$university_id, $url]);
                                $added_count++;
                            }
                        }
                        
                        if ($added_count > 0) {
                            $success = "Successfully added $added_count image(s)!";
                        } else {
                            $errors[] = "No valid image URLs were provided.";
                        }
                    } catch (Exception $e) {
                        $errors[] = "Error adding images: " . $e->getMessage();
                    }
                }
                break;
                
            case 'delete_image':
                $image_id = intval($_POST['image_id']);
                
                try {
                    $stmt = $db->prepare("DELETE FROM university_images WHERE id = ?");
                    $stmt->execute([$image_id]);
                    $success = "Image deleted successfully!";
                } catch (Exception $e) {
                    $errors[] = "Error deleting image: " . $e->getMessage();
                }
                break;
                
            case 'bulk_delete':
                $selected_ids = $_POST['selected_images'] ?? [];
                
                if (!empty($selected_ids)) {
                    try {
                        $stmt = $db->prepare("DELETE FROM university_images WHERE id = ?");
                        $deleted_count = 0;
                        
                        foreach ($selected_ids as $id) {
                            $stmt->execute([intval($id)]);
                            $deleted_count++;
                        }
                        
                        $success = "Successfully deleted $deleted_count image(s)!";
                    } catch (Exception $e) {
                        $errors[] = "Error performing bulk delete: " . $e->getMessage();
                    }
                } else {
                    $errors[] = "Please select images to delete.";
                }
                break;
        }
    }
}

// Get university filter
$university_filter = isset($_GET['university']) ? intval($_GET['university']) : 0;

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Build WHERE clause
$where_clause = "";
$params = [];

if (!empty($university_filter)) {
    $where_clause = "WHERE ui.university_id = ?";
    $params[] = $university_filter;
}

// Get total count
$count_query = "SELECT COUNT(*) as total FROM university_images ui $where_clause";
$count_stmt = $db->prepare($count_query);
$count_stmt->execute($params);
$total_images = $count_stmt->fetch()['total'];
$total_pages = ceil($total_images / $per_page);

// Get images with pagination
$query = "SELECT ui.*, u.name as university_name, c.name as country_name, c.flag_code
          FROM university_images ui 
          LEFT JOIN universities u ON ui.university_id = u.id
          LEFT JOIN countries c ON u.country_id = c.id
          $where_clause 
          ORDER BY ui.created_at DESC 
          LIMIT $per_page OFFSET $offset";
$stmt = $db->prepare($query);
$stmt->execute($params);
$images = $stmt->fetchAll();

// Get universities for dropdown
$universitiesStmt = $db->prepare("SELECT u.id, u.name, c.name as country_name FROM universities u LEFT JOIN countries c ON u.country_id = c.id ORDER BY c.name, u.name");
$universitiesStmt->execute();
$universities = $universitiesStmt->fetchAll();

function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage University Images - MedStudy Global Admin</title>
    
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
        .form-group {
            margin-bottom: 1rem;
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

        /* Image Gallery Styles */
        .image-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .image-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            position: relative;
        }

        .image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
        }

        .image-preview {
            width: 100%;
            height: 200px;
            object-fit: cover;
            cursor: pointer;
        }

        .image-info {
            padding: 1rem;
        }

        .image-university {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .image-country {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .image-url {
            font-size: 0.75rem;
            color: #6c757d;
            word-break: break-all;
            margin-bottom: 1rem;
        }

        .image-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .image-checkbox {
            position: absolute;
            top: 10px;
            left: 10px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 4px;
            padding: 4px;
        }

        /* Modal Styles */
        .image-modal {
            max-width: 90vw;
            max-height: 90vh;
        }

        .image-modal img {
            width: 100%;
            height: auto;
            border-radius: 8px;
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

        /* Stats Cards */
        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .stat-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
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

            .image-gallery {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
                gap: 1rem;
            }

            .filters-header {
                flex-direction: column;
                align-items: stretch;
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
                <li><a href="manage-universities.php"><i class="fas fa-university"></i> Universities</a></li>
                <li class="active"><a href="manage-university-images.php"><i class="fas fa-images"></i> University Images</a></li>
                <li><a href="../blog.php" target="_blank"><i class="fas fa-external-link-alt"></i> View Blog</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="admin-main">
            <div class="admin-content">
                <div class="admin-header">
                    <h1><i class="fas fa-images"></i> University Images</h1>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addImagesModal">
                        <i class="fas fa-plus"></i> Add Images
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

                <!-- Statistics Cards -->
                <div class="stats-cards">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-images"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_images; ?></div>
                        <div class="stat-label">Total Images</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="stat-number"><?php echo count($universities); ?></div>
                        <div class="stat-label">Universities</div>
                    </div>
                    <?php if ($university_filter): ?>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-filter"></i>
                        </div>
                        <div class="stat-number"><?php echo $total_images; ?></div>
                        <div class="stat-label">Filtered Images</div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Filters -->
                <div class="admin-card">
                    <div class="card-body">
                        <form method="GET" class="filters-header">
                            <div class="filters-left">
                                <select name="university" class="form-control" style="width: auto;" onchange="this.form.submit()">
                                    <option value="">All Universities</option>
                                    <?php foreach ($universities as $university): ?>
                                        <option value="<?php echo $university['id']; ?>" <?php echo $university_filter == $university['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($university['country_name'] . ' - ' . $university['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <a href="manage-university-images.php" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Clear Filter
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Bulk Actions Form -->
                <form method="POST" id="bulkActionForm">
                    <input type="hidden" name="action" value="bulk_delete">
                    
                    <!-- Bulk Actions Bar -->
                    <div class="admin-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                    <label for="selectAll" class="form-check-label">Select All</label>
                                    
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete selected images? This action cannot be undone.')">
                                        <i class="fas fa-trash"></i> Delete Selected
                                    </button>
                                </div>
                                
                                <div class="text-muted">
                                    Showing <?php echo count($images); ?> of <?php echo $total_images; ?> images
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Images Gallery -->
                    <?php if (empty($images)): ?>
                        <div class="admin-card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-images fa-4x text-muted mb-3"></i>
                                <h4 class="text-muted">No images found</h4>
                                <p class="text-muted">Upload your first university images to get started.</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addImagesModal">
                                    <i class="fas fa-plus"></i> Add Images
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="image-gallery">
                            <?php foreach ($images as $image): ?>
                                <div class="image-card">
                                    <div class="image-checkbox">
                                        <input type="checkbox" name="selected_images[]" value="<?php echo $image['id']; ?>" class="form-check-input image-checkbox-input">
                                    </div>
                                    
                                    <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                                         alt="University Image" 
                                         class="image-preview"
                                         onclick="showImageModal('<?php echo htmlspecialchars($image['image_url']); ?>', '<?php echo htmlspecialchars($image['university_name']); ?>')">
                                    
                                    <div class="image-info">
                                        <div class="image-university">
                                            <span class="flag-icon flag-icon-<?php echo strtolower($image['flag_code']); ?>"></span>
                                            <?php echo htmlspecialchars($image['university_name']); ?>
                                        </div>
                                        <div class="image-country">
                                            <?php echo htmlspecialchars($image['country_name']); ?>
                                        </div>
                                        <div class="image-url">
                                            <?php echo htmlspecialchars(substr($image['image_url'], 0, 50)) . (strlen($image['image_url']) > 50 ? '...' : ''); ?>
                                        </div>
                                        <div class="image-actions">
                                            <small class="text-muted">
                                                <?php echo date('M j, Y', strtotime($image['created_at'])); ?>
                                            </small>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this image?')">
                                                <input type="hidden" name="action" value="delete_image">
                                                <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </form>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="d-flex justify-content-center mt-4">
                        <nav>
                            <ul class="pagination">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page - 1; ?>&university=<?php echo $university_filter; ?>">
                                            <i class="fas fa-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&university=<?php echo $university_filter; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page + 1; ?>&university=<?php echo $university_filter; ?>">
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

    <!-- Add Images Modal -->
    <div class="modal fade" id="addImagesModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Add University Images</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="action" value="add_images">
                        
                        <div class="form-group">
                            <label class="form-label">Select University *</label>
                            <select name="university_id" class="form-control" required>
                                <option value="">Choose University</option>
                                <?php foreach ($universities as $university): ?>
                                    <option value="<?php echo $university['id']; ?>" <?php echo $university_filter == $university['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($university['country_name'] . ' - ' . $university['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Image URLs (Cloudinary) *</label>
                            <textarea name="image_urls" class="form-control" rows="8" required 
                                      placeholder="Enter one image URL per line:&#10;https://res.cloudinary.com/...&#10;https://res.cloudinary.com/..."></textarea>
                            <small class="form-text text-muted">
                                Enter one Cloudinary image URL per line. Each URL will be added as a separate image.
                            </small>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Tip:</strong> Use Cloudinary's media library to upload and get URLs for your university images. 
                            Recommended image size: 800x600px or larger for best quality.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-upload"></i> Add Images
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalTitle">Image Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="modalImage" src="" alt="Preview" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.image-checkbox-input');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Show image modal
        function showImageModal(imageUrl, universityName) {
            document.getElementById('modalImage').src = imageUrl;
            document.getElementById('imageModalTitle').textContent = universityName;
            const modal = new bootstrap.Modal(document.getElementById('imagePreviewModal'));
            modal.show();
        }

        // Prevent image loading errors from breaking the layout
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('.image-preview');
            images.forEach(img => {
                img.addEventListener('error', function() {
                    this.src = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjUwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZGRkIi8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJBcmlhbCwgc2Fucy1zZXJpZiIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzk5OSIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZHk9Ii4zZW0iPkltYWdlIG5vdCBmb3VuZDwvdGV4dD48L3N2Zz4=';
                });
            });
        });
    </script>
</body>
</html> 