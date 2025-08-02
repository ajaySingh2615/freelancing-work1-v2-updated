<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
require_once 'cloudinary.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$errors = [];
$success = '';
$showSuccessModal = false;
$isEdit = false;
$editBlog = null;

// Check if this is an edit operation
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $isEdit = true;
    $blogId = intval($_GET['id']);
    
    try {
        $editQuery = $db->prepare("SELECT * FROM blogs WHERE id = ?");
        $editQuery->execute([$blogId]);
        $editBlog = $editQuery->fetch();
        
        if (!$editBlog) {
            $errors[] = "Blog not found.";
            $isEdit = false;
        }
    } catch (Exception $e) {
        $errors[] = "Error loading blog for editing: " . $e->getMessage();
        $isEdit = false;
    }
}

// Get categories for dropdown
try {
    $categoriesQuery = $db->prepare("SELECT * FROM blog_categories WHERE status = 'active' ORDER BY name");
    $categoriesQuery->execute();
    $categories = $categoriesQuery->fetchAll();
} catch (Exception $e) {
    $errors[] = "Error loading categories: " . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Basic validation
    $title = sanitizeInput($_POST['title']);
    $slug = sanitizeInput($_POST['slug']);
    $excerpt = sanitizeInput($_POST['excerpt']);
    $content = $_POST['content']; // Don't sanitize content as it may contain HTML
    $category_id = !empty($_POST['category_id']) ? intval($_POST['category_id']) : null;
    $author_name = sanitizeInput($_POST['author_name']);
    $status = sanitizeInput($_POST['status']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_editors_pick = isset($_POST['is_editors_pick']) ? 1 : 0;
    
    // SEO fields
    $meta_title = sanitizeInput($_POST['meta_title']);
    $meta_description = sanitizeInput($_POST['meta_description']);
    $meta_keywords = sanitizeInput($_POST['meta_keywords']);
    $canonical_url = sanitizeInput($_POST['canonical_url']);
    
    // Open Graph fields
    $og_title = sanitizeInput($_POST['og_title']);
    $og_description = sanitizeInput($_POST['og_description']);
    
    // Twitter Card fields
    $twitter_title = sanitizeInput($_POST['twitter_title']);
    $twitter_description = sanitizeInput($_POST['twitter_description']);
    
    // Schema.org fields
    $schema_type = sanitizeInput($_POST['schema_type']);
    
    // Validation
    if (empty($title)) $errors[] = "Title is required";
    if (empty($slug)) $errors[] = "Slug is required";
    if (empty($content)) $errors[] = "Content is required";
    if (empty($author_name)) $errors[] = "Author name is required";
    
    // Check if slug already exists (skip check if editing the same blog)
    if (!empty($slug)) {
        if ($isEdit) {
            $slugCheck = $db->prepare("SELECT id FROM blogs WHERE slug = ? AND id != ?");
            $slugCheck->execute([$slug, $blogId]);
        } else {
            $slugCheck = $db->prepare("SELECT id FROM blogs WHERE slug = ?");
            $slugCheck->execute([$slug]);
        }
        
        if ($slugCheck->fetch()) {
            $errors[] = "Slug already exists. Please choose a different slug.";
        }
    }
    
    // Handle featured image upload
    $featured_image = $isEdit ? $editBlog['featured_image'] : '';
    $author_avatar = $isEdit ? $editBlog['author_avatar'] : '';
    $og_image = $isEdit ? $editBlog['og_image'] : '';
    $twitter_image = $isEdit ? $editBlog['twitter_image'] : '';
    
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
        $uploadResult = uploadToCloudinary($_FILES['featured_image'], 'blog-featured');
        if (isset($uploadResult['error'])) {
            $errors[] = "Featured image upload failed: " . $uploadResult['error'];
        } else {
            $featured_image = $uploadResult['url'];
            $og_image = $featured_image;
            $twitter_image = $featured_image;
        }
    }
    
    if (isset($_FILES['author_avatar']) && $_FILES['author_avatar']['error'] === 0) {
        $uploadResult = uploadToCloudinary($_FILES['author_avatar'], 'blog-authors');
        if (isset($uploadResult['error'])) {
            $errors[] = "Author avatar upload failed: " . $uploadResult['error'];
        } else {
            $author_avatar = $uploadResult['url'];
        }
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        try {
            // Prepare schema data
            $schema_data = json_encode([
                '@context' => 'https://schema.org',
                '@type' => $schema_type,
                'headline' => $title,
                'description' => $excerpt,
                'image' => $featured_image,
                'author' => [
                    '@type' => 'Person',
                    'name' => $author_name
                ],
                'publisher' => [
                    '@type' => 'Organization',
                    'name' => 'MedStudy Global'
                ],
                'datePublished' => date('c'),
                'dateModified' => date('c')
            ]);
            
            $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;
            
            if ($isEdit) {
                // Update existing blog
                $updateQuery = $db->prepare("
                    UPDATE blogs SET
                        title = ?, slug = ?, excerpt = ?, content = ?, featured_image = ?, category_id = ?, 
                        author_name = ?, author_avatar = ?, status = ?, is_featured = ?, is_editors_pick = ?,
                        meta_title = ?, meta_description = ?, meta_keywords = ?, canonical_url = ?,
                        og_title = ?, og_description = ?, og_image = ?,
                        twitter_title = ?, twitter_description = ?, twitter_image = ?,
                        schema_type = ?, schema_data = ?, published_at = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                
                $updateQuery->execute([
                    $title, $slug, $excerpt, $content, $featured_image, $category_id,
                    $author_name, $author_avatar, $status, $is_featured, $is_editors_pick,
                    $meta_title, $meta_description, $meta_keywords, $canonical_url,
                    $og_title, $og_description, $og_image,
                    $twitter_title, $twitter_description, $twitter_image,
                    $schema_type, $schema_data, $published_at, $blogId
                ]);
                
                $success = "Blog post updated successfully!";
            } else {
                // Insert new blog
                $insertQuery = $db->prepare("
                    INSERT INTO blogs (
                        title, slug, excerpt, content, featured_image, category_id, 
                        author_id, author_name, author_avatar, status, is_featured, is_editors_pick,
                        meta_title, meta_description, meta_keywords, canonical_url,
                        og_title, og_description, og_image,
                        twitter_title, twitter_description, twitter_image,
                        schema_type, schema_data,
                        published_at, created_at
                    ) VALUES (
                        ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW()
                    )
                ");
                
                $insertQuery->execute([
                    $title, $slug, $excerpt, $content, $featured_image, $category_id,
                    $_SESSION['admin_id'], $author_name, $author_avatar, $status, $is_featured, $is_editors_pick,
                    $meta_title, $meta_description, $meta_keywords, $canonical_url,
                    $og_title, $og_description, $og_image,
                    $twitter_title, $twitter_description, $twitter_image,
                    $schema_type, $schema_data,
                    $published_at
                ]);
                
                $success = "Blog post created successfully!";
            }
            
            // Set flag for showing success modal
            $showSuccessModal = true;
            
        } catch (Exception $e) {
            $errors[] = "Error saving blog post: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="referrer" content="strict-origin-when-cross-origin">
    <title><?php echo $isEdit ? 'Edit Blog' : 'Add New Blog'; ?> - MedStudy Global</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- TinyMCE (Rich Text Editor) -->
    <script src="https://cdn.tiny.cloud/1/ckhdla67dgiuczihylz9vgm24qocra38y6d17t4zfaad8v8b/tinymce/7/tinymce.min.js" referrerpolicy="strict-origin-when-cross-origin"></script>
    
    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="admin-styles.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
            color: #333;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #003585 0%, #002a6a 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .admin-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0;
        }
        
        .form-container {
            background: white;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin: 2rem 0;
        }
        
        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #eee;
        }
        
        .form-section:last-child {
            border-bottom: none;
        }
        
        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #003585;
            margin-bottom: 1rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #003585;
        }
        
        .form-group label {
            font-weight: 500;
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: #003585;
            box-shadow: 0 0 0 2px rgba(0, 53, 133, 0.1);
        }
        
        .slug-input {
            position: relative;
        }
        
        .slug-prefix {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 0.9rem;
        }
        
        .slug-prefix + .form-control {
            padding-left: 120px;
        }
        
        .image-upload {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }
        
        .image-upload:hover {
            border-color: #003585;
            background: #f8f9fa;
        }
        
        .image-upload input[type="file"] {
            display: none;
        }
        
        .image-preview {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .btn-primary {
            background: #003585;
            border-color: #003585;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .btn-primary:hover {
            background: #002a6a;
            border-color: #002a6a;
        }
        
        .btn-secondary {
            background: #6c757d;
            border-color: #6c757d;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 500;
        }
        
        .nav-pills .nav-link {
            color: #666;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .nav-pills .nav-link:hover {
            background: #f8f9fa;
            color: #003585;
        }
        
        .nav-pills .nav-link.active {
            background: #003585;
            color: white;
        }
        
        .seo-tabs {
            margin-bottom: 2rem;
        }
        
        .tab-content {
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin-top: 1rem;
        }
        
        .character-count {
            font-size: 0.8rem;
            color: #666;
            margin-top: 0.25rem;
        }
        
        .character-count.warning {
            color: #ffc107;
        }
        
        .character-count.error {
            color: #dc3545;
        }
        
        .help-text {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.25rem;
        }
        
        .form-check-label {
            font-weight: 500;
            color: #333;
        }
        
        .note-editor {
            border-radius: 8px;
        }
        
        .note-toolbar {
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        
        .note-editing-area {
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 1rem;
                margin: 1rem 0;
            }
            
            .slug-prefix + .form-control {
                padding-left: 10px;
            }
            
            .slug-prefix {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <div class="admin-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="fas fa-<?php echo $isEdit ? 'edit' : 'plus'; ?>"></i> <?php echo $isEdit ? 'Edit Blog' : 'Add New Blog'; ?></h1>
                </div>
                <div class="col-md-6 text-right">
                    <a href="dashboard.php" class="btn btn-light">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger mt-3">
                <h5><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</h5>
                <ul class="mb-0">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="alert alert-success mt-3">
                <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="form-container">
            <!-- Basic Information -->
            <div class="form-section">
                <h2 class="section-title">Basic Information</h2>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label for="title">Blog Title *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="title" 
                                   name="title" 
                                   value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ($isEdit ? htmlspecialchars($editBlog['title']) : ''); ?>"
                                   required>
                            <div class="help-text">This will be the main heading of your blog post</div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="status">Status *</label>
                            <select class="form-control" id="status" name="status" required>
                                <?php 
                                $currentStatus = isset($_POST['status']) ? $_POST['status'] : ($isEdit ? $editBlog['status'] : 'draft');
                                ?>
                                <option value="draft" <?php echo $currentStatus === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                <option value="published" <?php echo $currentStatus === 'published' ? 'selected' : ''; ?>>Published</option>
                                <option value="archived" <?php echo $currentStatus === 'archived' ? 'selected' : ''; ?>>Archived</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="slug">URL Slug *</label>
                    <div class="slug-input">
                        <span class="slug-prefix">../blog/</span>
                        <input type="text" 
                               class="form-control" 
                               id="slug" 
                               name="slug" 
                               value="<?php echo isset($_POST['slug']) ? htmlspecialchars($_POST['slug']) : ($isEdit ? htmlspecialchars($editBlog['slug']) : ''); ?>"
                               required>
                    </div>
                    <div class="help-text">URL-friendly version of the title. Only lowercase letters, numbers, and hyphens.</div>
                </div>
                
                <div class="form-group">
                    <label for="excerpt">Excerpt</label>
                    <textarea class="form-control" 
                              id="excerpt" 
                              name="excerpt" 
                              rows="3" 
                              maxlength="300"><?php echo isset($_POST['excerpt']) ? htmlspecialchars($_POST['excerpt']) : ($isEdit ? htmlspecialchars($editBlog['excerpt']) : ''); ?></textarea>
                    <div class="character-count" id="excerpt-count">0/300 characters</div>
                    <div class="help-text">Brief summary of the blog post (optional but recommended)</div>
                </div>
                
                <div class="form-group">
                    <label for="content">Content *</label>
                    <div class="alert alert-warning tinymce-error" style="display: none;">
                        <i class="fas fa-exclamation-triangle"></i>
                        Rich text editor failed to load. You can still use the basic text area below.
                    </div>
                    <textarea class="form-control" 
                              id="content" 
                              name="content" 
                              rows="10" 
                              required><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ($isEdit ? htmlspecialchars($editBlog['content']) : ''); ?></textarea>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control" id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                <?php 
                                $currentCategoryId = isset($_POST['category_id']) ? $_POST['category_id'] : ($isEdit ? $editBlog['category_id'] : '');
                                foreach ($categories as $category): 
                                ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                            <?php echo $currentCategoryId == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="author_name">Author Name *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="author_name" 
                                   name="author_name" 
                                   value="<?php echo isset($_POST['author_name']) ? htmlspecialchars($_POST['author_name']) : ($isEdit ? htmlspecialchars($editBlog['author_name']) : $_SESSION['admin_name']); ?>"
                                   required>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_featured" 
                                   name="is_featured" 
                                   value="1" 
                                   <?php echo (isset($_POST['is_featured']) || ($isEdit && $editBlog['is_featured'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_featured">
                                <i class="fas fa-star"></i> Featured Post
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   id="is_editors_pick" 
                                   name="is_editors_pick" 
                                   value="1" 
                                   <?php echo (isset($_POST['is_editors_pick']) || ($isEdit && $editBlog['is_editors_pick'])) ? 'checked' : ''; ?>>
                            <label class="form-check-label" for="is_editors_pick">
                                <i class="fas fa-heart"></i> Editor's Pick
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Images -->
            <div class="form-section">
                <h2 class="section-title">Images</h2>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Featured Image</label>
                            <div class="image-upload" onclick="document.getElementById('featured_image').click();">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                <p class="mt-2 mb-0">Click to upload featured image</p>
                                <small class="text-muted">Recommended: 1200x675px (16:9 ratio)</small>
                                <input type="file" id="featured_image" name="featured_image" accept="image/*">
                                <img id="featured_preview" class="image-preview" style="display: none;">
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Author Avatar</label>
                            <div class="image-upload" onclick="document.getElementById('author_avatar').click();">
                                <i class="fas fa-user-circle fa-2x text-muted"></i>
                                <p class="mt-2 mb-0">Click to upload author avatar</p>
                                <small class="text-muted">Recommended: 200x200px (square)</small>
                                <input type="file" id="author_avatar" name="author_avatar" accept="image/*">
                                <img id="avatar_preview" class="image-preview" style="display: none;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- SEO Settings -->
            <div class="form-section">
                <h2 class="section-title">SEO Settings</h2>
                
                <ul class="nav nav-pills seo-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-toggle="pill" href="#basic-seo">Basic SEO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#social-media">Social Media</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="pill" href="#structured-data">Structured Data</a>
                    </li>
                </ul>
                
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="basic-seo">
                        <div class="form-group">
                            <label for="meta_title">Meta Title</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="meta_title" 
                                   name="meta_title" 
                                   maxlength="60"
                                   value="<?php echo isset($_POST['meta_title']) ? htmlspecialchars($_POST['meta_title']) : ''; ?>">
                            <div class="character-count" id="meta-title-count">0/60 characters</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_description">Meta Description</label>
                            <textarea class="form-control" 
                                      id="meta_description" 
                                      name="meta_description" 
                                      rows="3" 
                                      maxlength="160"><?php echo isset($_POST['meta_description']) ? htmlspecialchars($_POST['meta_description']) : ''; ?></textarea>
                            <div class="character-count" id="meta-description-count">0/160 characters</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="meta_keywords">Meta Keywords</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="meta_keywords" 
                                   name="meta_keywords" 
                                   value="<?php echo isset($_POST['meta_keywords']) ? htmlspecialchars($_POST['meta_keywords']) : ''; ?>">
                            <div class="help-text">Separate keywords with commas</div>
                        </div>
                        
                        <div class="form-group">
                            <label for="canonical_url">Canonical URL</label>
                            <input type="url" 
                                   class="form-control" 
                                   id="canonical_url" 
                                   name="canonical_url" 
                                   value="<?php echo isset($_POST['canonical_url']) ? htmlspecialchars($_POST['canonical_url']) : ''; ?>">
                            <div class="help-text">Leave blank to use default URL</div>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="social-media">
                        <h5>Open Graph (Facebook)</h5>
                        <div class="form-group">
                            <label for="og_title">OG Title</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="og_title" 
                                   name="og_title" 
                                   value="<?php echo isset($_POST['og_title']) ? htmlspecialchars($_POST['og_title']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="og_description">OG Description</label>
                            <textarea class="form-control" 
                                      id="og_description" 
                                      name="og_description" 
                                      rows="3"><?php echo isset($_POST['og_description']) ? htmlspecialchars($_POST['og_description']) : ''; ?></textarea>
                        </div>
                        
                        <hr>
                        
                        <h5>Twitter Cards</h5>
                        <div class="form-group">
                            <label for="twitter_title">Twitter Title</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="twitter_title" 
                                   name="twitter_title" 
                                   value="<?php echo isset($_POST['twitter_title']) ? htmlspecialchars($_POST['twitter_title']) : ''; ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="twitter_description">Twitter Description</label>
                            <textarea class="form-control" 
                                      id="twitter_description" 
                                      name="twitter_description" 
                                      rows="3"><?php echo isset($_POST['twitter_description']) ? htmlspecialchars($_POST['twitter_description']) : ''; ?></textarea>
                        </div>
                    </div>
                    
                    <div class="tab-pane fade" id="structured-data">
                        <div class="form-group">
                            <label for="schema_type">Schema Type</label>
                            <select class="form-control" id="schema_type" name="schema_type">
                                <option value="Article" <?php echo (isset($_POST['schema_type']) && $_POST['schema_type'] === 'Article') ? 'selected' : ''; ?>>Article</option>
                                <option value="BlogPosting" <?php echo (isset($_POST['schema_type']) && $_POST['schema_type'] === 'BlogPosting') ? 'selected' : ''; ?>>Blog Posting</option>
                                <option value="NewsArticle" <?php echo (isset($_POST['schema_type']) && $_POST['schema_type'] === 'NewsArticle') ? 'selected' : ''; ?>>News Article</option>
                                <option value="WebPage" <?php echo (isset($_POST['schema_type']) && $_POST['schema_type'] === 'WebPage') ? 'selected' : ''; ?>>Web Page</option>
                            </select>
                            <div class="help-text">Choose the most appropriate schema type for your content</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Blog Post
                </button>
                <a href="dashboard.php" class="btn btn-secondary ml-2">
                    <i class="fas fa-times"></i> Cancel
                </a>
            </div>
        </form>
    </div>
    
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

    
    <script>
        $(document).ready(function() {
            // Debug: Log current origin for TinyMCE troubleshooting
            console.log('Current origin for TinyMCE:', window.location.origin);
            console.log('Current hostname:', window.location.hostname);
            
            // Initialize TinyMCE for content
            tinymce.init({
                selector: '#content',
                height: 400,
                menubar: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
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
                        $('.tinymce-error').hide();
                    });
                    
                    editor.on('LoadError', function(e) {
                        console.error('TinyMCE Load Error:', e);
                        $('.tinymce-error').show();
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
                $('.tinymce-error').show();
                $('#content').show();
            });
            
            // Auto-generate slug from title
            $('#title').on('input', function() {
                var title = $(this).val();
                var slug = title.toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .replace(/^-|-$/g, '');
                $('#slug').val(slug);
            });
            
            // Character counting
            function updateCharacterCount(input, counter, maxLength) {
                var count = $(input).val().length;
                var remaining = maxLength - count;
                $(counter).text(count + '/' + maxLength + ' characters');
                
                if (remaining < 10) {
                    $(counter).addClass('error').removeClass('warning');
                } else if (remaining < 20) {
                    $(counter).addClass('warning').removeClass('error');
                } else {
                    $(counter).removeClass('warning error');
                }
            }
            
            $('#excerpt').on('input', function() {
                updateCharacterCount(this, '#excerpt-count', 300);
            });
            
            $('#meta_title').on('input', function() {
                updateCharacterCount(this, '#meta-title-count', 60);
            });
            
            $('#meta_description').on('input', function() {
                updateCharacterCount(this, '#meta-description-count', 160);
            });
            
            // Auto-fill SEO fields from basic fields
            $('#title').on('input', function() {
                var title = $(this).val();
                if (!$('#meta_title').val()) {
                    $('#meta_title').val(title);
                    updateCharacterCount('#meta_title', '#meta-title-count', 60);
                }
                if (!$('#og_title').val()) {
                    $('#og_title').val(title);
                }
                if (!$('#twitter_title').val()) {
                    $('#twitter_title').val(title);
                }
            });
            
            $('#excerpt').on('input', function() {
                var excerpt = $(this).val();
                if (!$('#meta_description').val()) {
                    $('#meta_description').val(excerpt);
                    updateCharacterCount('#meta_description', '#meta-description-count', 160);
                }
                if (!$('#og_description').val()) {
                    $('#og_description').val(excerpt);
                }
                if (!$('#twitter_description').val()) {
                    $('#twitter_description').val(excerpt);
                }
            });
            
            // Image preview
            function previewImage(input, previewId) {
                if (input.files && input.files[0]) {
                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $(previewId).attr('src', e.target.result).show();
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
            
            $('#featured_image').change(function() {
                previewImage(this, '#featured_preview');
            });
            
            $('#author_avatar').change(function() {
                previewImage(this, '#avatar_preview');
            });
            
            // Initialize character counts
            updateCharacterCount('#excerpt', '#excerpt-count', 300);
            updateCharacterCount('#meta_title', '#meta-title-count', 60);
            updateCharacterCount('#meta_description', '#meta-description-count', 160);
            
            // Form validation
            $('form').on('submit', function(e) {
                var title = $('#title').val().trim();
                var slug = $('#slug').val().trim();
                var content = '';
                if (tinymce.get('content')) {
                    content = tinymce.get('content').getContent().trim();
                } else {
                    content = $('#content').val().trim();
                }
                
                if (!title) {
                    alert('Please enter a title');
                    e.preventDefault();
                    return false;
                }
                
                if (!slug) {
                    alert('Please enter a slug');
                    e.preventDefault();
                    return false;
                }
                
                if (!content || content === '<p><br></p>') {
                    alert('Please enter content');
                    e.preventDefault();
                    return false;
                }
                
                return true;
            });

            <?php if ($showSuccessModal): ?>
            // Show success modal
            $('#successModal').modal('show');
            <?php endif; ?>
        });
    </script>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" role="dialog" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">
                        <i class="fas fa-check-circle mr-2"></i>Blog Post Created Successfully!
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h6 class="mb-3">Your blog post has been saved successfully!</h6>
                    <p class="text-muted">What would you like to do next?</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary mr-3" onclick="createAnother()">
                        <i class="fas fa-plus mr-1"></i>Create Another Blog
                    </button>
                    <button type="button" class="btn btn-primary" onclick="goToDashboard()">
                        <i class="fas fa-tachometer-alt mr-1"></i>View Dashboard
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function createAnother() {
            // Reload the page to create another blog
            window.location.href = 'add-blog.php';
        }

        function goToDashboard() {
            // Navigate to dashboard
            window.location.href = 'dashboard.php';
        }
    </script>
</body>
</html> 