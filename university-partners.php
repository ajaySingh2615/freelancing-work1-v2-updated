<?php
$page_title = "Universities in %s - Study MBBS Abroad";
$page_description = "Explore top medical universities in %s for MBBS studies. Get detailed information about admission requirements, fees, and programs.";

include 'includes/header.php';
include 'includes/functions.php';

// Get country from URL parameter
$country_slug = $_GET['country'] ?? '';

if (empty($country_slug)) {
    header('Location: destinations.php');
    exit;
}

// Get country details
$country = getCountryBySlug($country_slug);
if (!$country) {
    header('Location: destinations.php');
    exit;
}

// Get universities for this country
$universities = getUniversitiesByCountry($country['id']);

// Update page title and description with country name
$page_title = sprintf($page_title, $country['name']);
$page_description = sprintf($page_description, $country['name']);
?>

<!-- Country Hero Section -->
<section class="university-partners-hero">
    <div class="hero-background-decoration">
        <div class="airplane-icon airplane-1">
            <i class="fas fa-plane"></i>
                            </div>
        <div class="airplane-icon airplane-2">
            <i class="fas fa-plane"></i>
                        </div>
        <div class="airplane-icon airplane-3">
            <i class="fas fa-plane"></i>
                                    </div>
                                    </div>
    <div class="container">
        <div class="hero-content">
            <nav class="hero-breadcrumb">
                <a href="index.php">Home</a>
                <span class="breadcrumb-separator">/</span>
                <a href="destinations.php"><?php echo htmlspecialchars($country['name']); ?></a>
                <span class="breadcrumb-separator">/</span>
                <span class="breadcrumb-current">Universities</span>
            </nav>
            <div class="hero-main">
                <h1 class="hero-title">Top Universities in <?php echo htmlspecialchars($country['name']); ?></h1>
                                    </div>
                                </div>
                            </div>
</section>

<!-- Enhanced Search and Filter Section -->
<section class="search-filter-section">
    <div class="container">
        <div class="search-filter-container">
            <!-- Search Section -->
            <div class="search-section">
                <div class="search-wrapper">
                    <div class="search-input-container">
                        <div class="search-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <input type="text" 
                               class="search-input" 
                               id="universitySearch" 
                               placeholder="Search universities, programs, or locations..."
                               autocomplete="off">
                        <div class="search-clear" id="searchClear">
                            <i class="fas fa-times"></i>
                    </div>
                    </div>
                    <div class="search-suggestions" id="searchSuggestions">
                        <!-- Dynamic suggestions will be populated here -->
                    </div>
                </div>
            </div>
            
            <!-- Filter Section -->
            <div class="filter-section">
                <div class="filter-tabs">
                    <button class="filter-tab active" data-filter="all">
                        <i class="fas fa-th-large"></i>
                        <span>All Universities</span>
                        <span class="tab-count"><?php echo count($universities); ?></span>
                    </button>
                    <button class="filter-tab" data-filter="popular">
                        <i class="fas fa-fire"></i>
                        <span>Popular</span>
                    </button>
                    <button class="filter-tab" data-filter="budget">
                        <i class="fas fa-dollar-sign"></i>
                        <span>Budget Friendly</span>
                    </button>
                    <button class="filter-tab" data-filter="recommended">
                        <i class="fas fa-star"></i>
                        <span>Recommended</span>
                    </button>
                            </div>
                
                <div class="filter-controls">
                    <div class="sort-dropdown">
                        <button class="sort-btn" id="sortButton">
                            <i class="fas fa-sort"></i>
                            <span>Sort by Name</span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="sort-options" id="sortOptions">
                            <div class="sort-option active" data-sort="name">
                                <i class="fas fa-font"></i>
                                <span>Alphabetical</span>
                        </div>
                            <div class="sort-option" data-sort="popular">
                                <i class="fas fa-fire"></i>
                                <span>Most Popular</span>
                            </div>
                            <div class="sort-option" data-sort="location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>By Location</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="view-toggle">
                        <button class="view-btn active" data-view="grid" title="Grid View">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button class="view-btn" data-view="list" title="List View">
                            <i class="fas fa-list"></i>
                        </button>
                </div>
            </div>
        </div>
            
            <!-- Results Info -->
            <div class="results-info">
                <div class="results-text">
                    <span class="results-count" id="universityCount"><?php echo count($universities); ?></span>
                    <span class="results-label">universities found in <?php echo htmlspecialchars($country['name']); ?></span>
                </div>
                <div class="filter-reset" id="filterReset" style="display: none;">
                    <button class="reset-btn">
                        <i class="fas fa-undo"></i>
                        <span>Clear Filters</span>
                    </button>
            </div>
            </div>
        </div>
    </div>
</section>

<!-- Universities Grid Section -->
<section class="py-5 universities-grid-section">
    <div class="container">
        <?php if (!empty($universities)): ?>
            <div class="row g-4 gx-lg-5" id="universitiesGrid">
                <?php foreach ($universities as $university): ?>
                    <div class="col-12 col-md-6 col-lg-4 university-card-wrapper">
                        <div class="modern-university-card hover-lift-university">
                            <!-- University Image -->
                            <div class="university-image-section">
                                <?php if (!empty($university['featured_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($university['featured_image']); ?>" 
                                         class="university-featured-img" 
                                         alt="<?php echo htmlspecialchars($university['name']); ?>"
                                         loading="lazy">
                                <?php else: ?>
                                    <div class="university-placeholder-img">
                                        <div class="placeholder-content">
                                            <i class="fas fa-university"></i>
                                            <span>University Image</span>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- University Logo Badge -->
                                <?php if (!empty($university['logo_image'])): ?>
                                    <div class="university-logo-badge">
                                        <img src="<?php echo htmlspecialchars($university['logo_image']); ?>" 
                                             class="university-logo" 
                                             alt="<?php echo htmlspecialchars($university['name']); ?> Logo">
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Quick Info Overlay -->
                                <div class="quick-info-overlay">
                                    <?php if (!empty($university['course_duration'])): ?>
                                        <div class="duration-badge">
                                            <i class="fas fa-clock"></i>
                                            <span><?php echo htmlspecialchars($university['course_duration']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <!-- University Content -->
                            <div class="university-content">
                                <!-- Header Section -->
                                <div class="university-header">
                                    <h3 class="university-title">
                                        <?php echo htmlspecialchars($university['name']); ?>
                                    </h3>
                                    
                                    <div class="university-location">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($university['location']); ?></span>
                                    </div>
                                </div>
                                
                                <!-- Meta Information -->
                                <div class="university-meta-modern">
                                    <?php if (!empty($university['language_of_instruction'])): ?>
                                        <div class="meta-badge">
                                            <i class="fas fa-language"></i>
                                            <span><?php echo htmlspecialchars($university['language_of_instruction']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- University Description -->
                                <div class="university-description">
                                    <?php 
                                    $description = $university['about_university'];
                                    $plainText = strip_tags($description);
                                    
                                    if (strlen($plainText) > 120) {
                                        // Truncate HTML content safely
                                        $truncated = mb_substr($plainText, 0, 120);
                                        echo htmlspecialchars($truncated) . '...';
                                    } else {
                                        // Display safe HTML content
                                        echo strip_tags($description, '<p><br><strong><b><em><i><ul><ol><li>');
                                    }
                                    ?>
                                </div>
                                
                                <!-- Action Button -->
                                <div class="university-action">
                                    <a href="university-detail.php?slug=<?php echo urlencode($university['slug']); ?>" 
                                       class="view-details-btn">
                                        <span>Explore University</span>
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php else: ?>
            <!-- No Universities Found -->
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="fas fa-university fa-4x text-muted"></i>
                </div>
                <h3 class="h4 text-muted mb-3">No Universities Available</h3>
                <p class="text-muted mb-4">We're currently updating our university listings for <?php echo htmlspecialchars($country['name']); ?>. Please check back soon or contact us for more information.</p>
                <a href="contact.php?country=<?php echo urlencode($country['slug']); ?>" class="btn btn-primary">
                    <i class="fas fa-phone-alt me-2"></i>Contact Us for Information
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>



<!-- Enhanced CSS for University Partners -->
<style>
/* Hero Section - Clean Design with Background Decoration */
.university-partners-hero {
    background: linear-gradient(135deg, #fef8e7 0%, #f0f9ff 100%);
    padding: 3rem 0;
    position: relative;
    overflow: hidden;
    min-height: 250px;
    border-bottom: 1px solid #e5e7eb;
}

.hero-background-decoration {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: 1;
}

.airplane-icon {
    position: absolute;
    font-size: 2.5rem;
    transform: rotate(45deg);
    opacity: 0.1;
}

.airplane-1 {
    top: 20%;
    right: 15%;
    font-size: 3rem;
    color: #003585;
    opacity: 0.08;
}

.airplane-2 {
    top: 60%;
    right: 25%;
    font-size: 2rem;
    color: #FEBA02;
    opacity: 0.12;
}

.airplane-3 {
    top: 40%;
    right: 5%;
    font-size: 2.5rem;
    color: #149DE1;
    opacity: 0.1;
}

.hero-content {
    position: relative;
    z-index: 2;
    max-width: 100%;
}

.hero-breadcrumb {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 2rem;
    font-size: 0.9rem;
    font-weight: 500;
}

.hero-breadcrumb a {
    color: #6b7280;
    text-decoration: none;
    transition: color 0.3s ease;
}

.hero-breadcrumb a:hover {
    color: #003585;
}

.breadcrumb-separator {
    color: #9ca3af;
    margin: 0 0.25rem;
}

.breadcrumb-current {
    color: #374151;
    font-weight: 600;
}

.hero-main {
    max-width: 800px;
}

.hero-title {
    font-size: clamp(2rem, 5vw, 3.5rem);
    font-weight: 700;
    color: #111827 !important;
    margin: 0;
    line-height: 1.2;
    letter-spacing: -0.02em;
}

/* Universities Grid Section */
.universities-grid-section {
    background: #fafbfc;
    min-height: 60vh;
}

/* University Cards Grid */
#universitiesGrid {
    margin-top: 2rem;
}

.university-card-wrapper {
    margin-bottom: 2rem;
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

/* Modern University Card Styling */
.modern-university-card {
    position: relative;
    overflow: hidden;
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    border: 1px solid rgba(0, 0, 0, 0.04);
    height: 100%;
    display: flex;
    flex-direction: column;
}

/* Enhanced hover effects */
.modern-university-card:hover {
    transform: translateY(-8px);
    border: 1px solid rgba(254, 186, 2, 0.2);
    animation: modernGlow 3s ease-in-out infinite;
}

/* Modern shine effect */
.modern-university-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 50%;
    height: 100%;
    background: linear-gradient(
        120deg,
        transparent,
        rgba(254, 186, 2, 0.15),
        transparent
    );
    transform: skewX(-25deg);
    transition: 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    z-index: 1;
}

.modern-university-card:hover::before {
    animation: modernShine 3s ease-in-out infinite;
}

/* University Image Section */
.university-image-section {
    position: relative;
    height: 200px;
    overflow: hidden;
    border-radius: 16px 16px 0 0;
}

.university-featured-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.modern-university-card:hover .university-featured-img {
    transform: scale(1.05);
}

.university-placeholder-img {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
}

.placeholder-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    color: #64748b;
}

.placeholder-content i {
    font-size: 2.5rem;
}

.placeholder-content span {
    font-size: 0.875rem;
    font-weight: 500;
}

/* University Logo Badge */
.university-logo-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    padding: 8px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    backdrop-filter: blur(10px);
    z-index: 3;
}

.university-logo {
    width: 32px;
    height: 32px;
    object-fit: contain;
}

/* Quick Info Overlay */
.quick-info-overlay {
    position: absolute;
    bottom: 12px;
    left: 12px;
    z-index: 3;
}

.duration-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(0, 53, 133, 0.9);
    color: white;
    padding: 0.5rem 0.75rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    backdrop-filter: blur(10px);
}

/* University Content */
.university-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    padding: 1.5rem;
    position: relative;
    z-index: 2;
}

/* University Header */
.university-header {
    margin-bottom: 1rem;
}

.university-title {
    font-size: 1.125rem;
    font-weight: 600;
    color: #1e293b;
    line-height: 1.4;
    margin: 0 0 0.75rem 0;
    transition: color 0.3s ease;
}

.modern-university-card:hover .university-title {
    color: #003585;
}

.university-location {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 500;
}

.university-location i {
    color: #3b82f6;
    font-size: 0.75rem;
}

/* Meta Information */
.university-meta-modern {
    margin-bottom: 1rem;
}

.meta-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    background: #f8fafc;
    color: #475569;
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-size: 0.8rem;
    font-weight: 500;
    border: 1px solid #e2e8f0;
}

.meta-badge i {
    color: #3b82f6;
    font-size: 0.75rem;
}

/* University Description */
.university-description {
    flex: 1;
    font-size: 0.875rem;
    line-height: 1.6;
    color: #64748b;
    margin-bottom: 1.5rem;
}

/* Action Button */
.university-action {
    margin-top: auto;
}

.view-details-btn {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    background: linear-gradient(135deg, #003585 0%, #0047a3 100%);
    color: white;
    padding: 0.875rem 1.25rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
    box-shadow: 0 2px 8px rgba(0, 53, 133, 0.2);
}

.view-details-btn:hover {
    background: linear-gradient(135deg, #002968 0%, #003585 100%);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 53, 133, 0.3);
    text-decoration: none;
}

.view-details-btn i {
    transition: transform 0.3s ease;
}

.view-details-btn:hover i {
    transform: translateX(4px);
}

/* Modern Animations */
@keyframes modernShine {
    0% {
        left: -100%;
    }
    20% {
        left: 100%;
    }
    100% {
        left: 100%;
    }
}

@keyframes modernGlow {
    0% {
        box-shadow: 
            0 4px 20px rgba(0, 0, 0, 0.08),
            0 0 20px rgba(254, 186, 2, 0.1);
    }
    50% {
        box-shadow: 
            0 8px 30px rgba(0, 0, 0, 0.12),
            0 0 30px rgba(254, 186, 2, 0.2),
            0 0 40px rgba(254, 186, 2, 0.1);
    }
    100% {
        box-shadow: 
            0 4px 20px rgba(0, 0, 0, 0.08),
            0 0 20px rgba(254, 186, 2, 0.1);
    }
}

/* Staggered Animation Delays */
.university-card-wrapper:nth-child(1) { animation-delay: 0.1s; }
.university-card-wrapper:nth-child(2) { animation-delay: 0.2s; }
.university-card-wrapper:nth-child(3) { animation-delay: 0.3s; }
.university-card-wrapper:nth-child(4) { animation-delay: 0.4s; }
.university-card-wrapper:nth-child(5) { animation-delay: 0.5s; }
.university-card-wrapper:nth-child(6) { animation-delay: 0.6s; }

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.smooth-scroll {
    scroll-behavior: smooth;
}

/* Enhanced Search and Filter Section */
.search-filter-section {
    background: linear-gradient(135deg, #f8fafb 0%, #ffffff 100%);
    padding: 2rem 0;
    border-bottom: 1px solid #e5e7eb;
    position: relative;
}

.search-filter-container {
    max-width: 100%;
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Search Section */
.search-section {
    display: flex;
    justify-content: center;
}

.search-wrapper {
    position: relative;
    width: 100%;
    max-width: 600px;
}

.search-input-container {
    position: relative;
    display: flex;
    align-items: center;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.search-input-container:focus-within {
    border-color: #003585;
    box-shadow: 0 4px 20px rgba(0, 53, 133, 0.15);
    transform: translateY(-2px);
}

.search-icon {
    color: #6b7280;
    margin-right: 0.75rem;
    transition: color 0.3s ease;
}

.search-input-container:focus-within .search-icon {
    color: #003585;
}

.search-input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-size: 1rem;
    color: #374151;
    font-weight: 500;
}

.search-input::placeholder {
    color: #9ca3af;
    font-weight: 400;
}

.search-clear {
    color: #9ca3af;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: all 0.3s ease;
    opacity: 0;
    visibility: hidden;
}

.search-clear.show {
    opacity: 1;
    visibility: visible;
}

.search-clear:hover {
    color: #ef4444;
    background: rgba(239, 68, 68, 0.1);
}

.search-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-top: 0.5rem;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    z-index: 1000;
    display: none;
}

/* Filter Section */
.filter-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

/* Filter Tabs */
.filter-tabs {
    display: flex;
    gap: 0.5rem;
    overflow-x: auto;
    padding-bottom: 0.5rem;
}

.filter-tab {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    color: #6b7280;
    font-weight: 500;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.3s ease;
    white-space: nowrap;
    min-width: fit-content;
}

.filter-tab:hover {
    border-color: #d1d5db;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
}

.filter-tab.active {
    background: #003585;
    border-color: #003585;
    color: white;
    box-shadow: 0 4px 12px rgba(0, 53, 133, 0.25);
}

.tab-count {
    background: rgba(255, 255, 255, 0.2);
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.filter-tab.active .tab-count {
    background: rgba(255, 255, 255, 0.25);
}

/* Filter Controls */
.filter-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
}

/* Sort Dropdown */
.sort-dropdown {
    position: relative;
}

.sort-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    color: #374151;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
    min-width: 160px;
}

.sort-btn:hover {
    border-color: #d1d5db;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
}

.sort-options {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    margin-top: 0.5rem;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    z-index: 100;
    display: none;
}

.sort-options.show {
    display: block;
    animation: slideDown 0.2s ease-out;
}

.sort-option {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.3s ease;
}

.sort-option:hover {
    background: #f9fafb;
    color: #374151;
}

.sort-option.active {
    background: #f0f9ff;
    color: #003585;
    font-weight: 500;
}

/* View Toggle */
.view-toggle {
    display: flex;
    gap: 0.25rem;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 0.25rem;
}

.view-btn {
    padding: 0.5rem;
    background: transparent;
    border: none;
    border-radius: 6px;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.3s ease;
}

.view-btn:hover {
    background: #f3f4f6;
    color: #374151;
}

.view-btn.active {
    background: #003585;
    color: white;
    box-shadow: 0 2px 4px rgba(0, 53, 133, 0.2);
}

/* Results Info */
.results-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: white;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.results-text {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.results-count {
    font-size: 1.25rem;
    font-weight: 700;
    color: #003585;
}

.results-label {
    color: #6b7280;
    font-weight: 500;
}

.reset-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: transparent;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #6b7280;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

.reset-btn:hover {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626;
}

/* Animations */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 768px) {
    .search-filter-section {
        padding: 1.5rem 0;
    }
    
    .filter-controls {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
    }
    
    .filter-tabs {
        gap: 0.25rem;
    }
    
    .filter-tab {
        padding: 0.5rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .results-info {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
}

@media (max-width: 576px) {
    .search-filter-container {
        gap: 1rem;
    }
    
    .search-input-container {
        padding: 0.625rem 0.875rem;
    }
    
    .search-input {
        font-size: 0.9rem;
    }
    
    .filter-tab span:not(.tab-count) {
        display: none;
    }
    
         .sort-btn {
         min-width: 120px;
         font-size: 0.875rem;
     }
 }
 
  /* List View Styling */
 #universitiesGrid.list-view {
     display: flex;
     flex-direction: column;
     gap: 1rem;
 }
 
 #universitiesGrid.list-view .university-card-wrapper {
     margin-bottom: 0;
 }
 
 #universitiesGrid.list-view .modern-university-card {
     flex-direction: row;
     max-width: 100%;
     border-radius: 12px;
 }
 
 #universitiesGrid.list-view .university-image-section {
     width: 200px;
     height: 150px;
     flex-shrink: 0;
     border-radius: 12px 0 0 12px;
 }
 
 #universitiesGrid.list-view .university-content {
     flex: 1;
     padding: 1.25rem;
 }
 
 #universitiesGrid.list-view .university-action {
     width: 200px;
     flex-shrink: 0;
     display: flex;
     align-items: center;
     justify-content: center;
     padding: 1rem;
 }
 
 @media (max-width: 768px) {
     #universitiesGrid.list-view .modern-university-card {
         flex-direction: column;
         border-radius: 16px;
     }
     
     #universitiesGrid.list-view .university-image-section {
         width: 100%;
         height: 180px;
         border-radius: 16px 16px 0 0;
     }
     
     #universitiesGrid.list-view .university-action {
         width: 100%;
         padding: 0 1.5rem 1.5rem 1.5rem;
     }
 }

/* Form Styling */
.form-label {
    font-weight: 500;
    color: var(--text-color);
    margin-bottom: 0.5rem;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(0, 53, 133, 0.25);
}

.text-danger {
    color: #dc3545 !important;
}

/* Consultation Card Styling */
.consultation-card {
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.1);
    border: none;
    border-radius: 12px;
    overflow: hidden;
}

/* Alert Styling */
.alert {
    border: none;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
}

.alert-success {
    background: linear-gradient(135deg, rgba(40, 167, 69, 0.1) 0%, rgba(40, 167, 69, 0.05) 100%);
    color: #155724;
    border-left: 4px solid #28a745;
}

.alert-danger {
    background: linear-gradient(135deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
    color: #721c24;
    border-left: 4px solid #dc3545;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .university-card-wrapper {
        margin-bottom: 1.5rem;
    }
    
    .university-image-section {
        height: 190px;
    }
}

@media (max-width: 992px) {
    #universitiesGrid {
        margin-top: 1.5rem;
    }
    
    .university-card-wrapper {
        margin-bottom: 1.5rem;
    }
    
    .university-image-section {
        height: 180px;
    }
    
    .university-content {
        padding: 1.25rem;
    }
    
    .view-details-btn {
        padding: 0.75rem 1rem;
        font-size: 0.85rem;
    }
}

@media (max-width: 768px) {
    .university-partners-hero {
        padding: var(--xl) 0;
        min-height: 200px;
    }
    
    .hero-breadcrumb {
        margin-bottom: var(--lg);
        font-size: 0.8rem;
    }
    
    .airplane-1 {
        font-size: 2.5rem;
        top: 15%;
        right: 10%;
    }
    
    .airplane-2 {
        font-size: 1.5rem;
        top: 70%;
        right: 20%;
    }
    
    .airplane-3 {
        font-size: 2rem;
        top: 45%;
        right: 2%;
    }
    
    /* Card adjustments for tablet */
    .university-card-wrapper {
        margin-bottom: 1.5rem;
    }
    
    .university-image-section {
        height: 170px;
    }
    
    .university-title {
        font-size: 1rem;
    }
    
    .university-description {
        font-size: 0.8rem;
    }
    
    .duration-badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.625rem;
    }
}

@media (max-width: 576px) {
    .university-partners-hero {
        padding: var(--lg) 0;
        min-height: 180px;
    }
    
    .hero-breadcrumb {
        margin-bottom: var(--md);
        font-size: 0.75rem;
        flex-wrap: wrap;
        gap: var(--xs);
    }
    
    .breadcrumb-separator {
        margin: 0 0.25rem;
    }
    
    .airplane-1 {
        font-size: 2rem;
        top: 10%;
        right: 5%;
    }
    
    .airplane-2 {
        font-size: 1.2rem;
        top: 75%;
        right: 15%;
    }
    
    .airplane-3 {
        display: none; /* Hide third airplane on mobile for cleaner look */
    }
    
    /* Mobile card adjustments */
    .university-card-wrapper {
        margin-bottom: 1.25rem;
    }
    
    .university-image-section {
        height: 160px;
    }
    
    .university-content {
        padding: 1rem;
    }
    
    .university-title {
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
    }
    
    .university-location {
        font-size: 0.8rem;
        margin-bottom: 0.75rem;
    }
    
    .university-meta-modern {
        margin-bottom: 0.75rem;
    }
    
    .meta-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    .university-description {
        font-size: 0.8rem;
        margin-bottom: 1rem;
    }
    
    .view-details-btn {
        font-size: 0.8rem;
        padding: 0.75rem 1rem;
    }
    
    .university-logo-badge {
        top: 8px;
        right: 8px;
        padding: 6px;
    }
    
    .university-logo {
        width: 28px;
        height: 28px;
    }
    
    .duration-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
}

/* Accessibility and Motion Preferences */
.btn:focus,
.form-control:focus,
.form-select:focus,
.view-details-btn:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}

@media (prefers-reduced-motion: reduce) {
    *,
    *::before,
    *::after {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
        scroll-behavior: auto !important;
    }
    
    .modern-university-card:hover {
        transform: none;
    }
    
    .university-featured-img {
        transition: none;
    }
    
    .view-details-btn:hover {
        transform: none;
    }
}
</style>

<!-- External CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">

<!-- JavaScript -->
<script src="assets/js/university-partners.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced search and filter functionality
    const searchInput = document.getElementById('universitySearch');
    const searchClear = document.getElementById('searchClear');
    const searchSuggestions = document.getElementById('searchSuggestions');
    const filterTabs = document.querySelectorAll('.filter-tab');
    const sortButton = document.getElementById('sortButton');
    const sortOptions = document.getElementById('sortOptions');
    const sortOptionItems = document.querySelectorAll('.sort-option');
    const viewButtons = document.querySelectorAll('.view-btn');
    const resetButton = document.querySelector('.reset-btn');
    const filterReset = document.getElementById('filterReset');
    const universityCards = document.querySelectorAll('.university-card-wrapper');
    const universityCount = document.getElementById('universityCount');
    const universitiesGrid = document.getElementById('universitiesGrid');
    
    let currentFilter = 'all';
    let currentSort = 'name';
    let currentView = 'grid';
    
    // Enhanced search functionality with clear button
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            // Show/hide clear button
            if (query.length > 0) {
                searchClear.classList.add('show');
                } else {
                searchClear.classList.remove('show');
            }
            
            performSearch(query);
        });
        
        // Clear search functionality
        searchClear.addEventListener('click', function() {
            searchInput.value = '';
            searchClear.classList.remove('show');
            performSearch('');
        });
    }
    
    // Filter tabs functionality
    filterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            // Remove active class from all tabs
            filterTabs.forEach(t => t.classList.remove('active'));
            // Add active class to clicked tab
            this.classList.add('active');
            
            currentFilter = this.getAttribute('data-filter');
            applyFilters();
            updateResetButton();
        });
    });
    
    // Sort dropdown functionality
    if (sortButton && sortOptions) {
        sortButton.addEventListener('click', function(e) {
            e.stopPropagation();
            sortOptions.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            sortOptions.classList.remove('show');
        });
        
        sortOptionItems.forEach(option => {
            option.addEventListener('click', function() {
                // Remove active class from all options
                sortOptionItems.forEach(opt => opt.classList.remove('active'));
                // Add active to clicked option
                this.classList.add('active');
                
                // Update button text
                const sortText = this.querySelector('span').textContent;
                sortButton.querySelector('span').textContent = sortText;
                
                currentSort = this.getAttribute('data-sort');
                sortOptions.classList.remove('show');
                applySorting();
                updateResetButton();
            });
        });
    }
    
    // View toggle functionality
    viewButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            viewButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            currentView = this.getAttribute('data-view');
            toggleView();
        });
    });
    
    // Reset filters functionality
    if (resetButton) {
        resetButton.addEventListener('click', function() {
            // Reset search
            searchInput.value = '';
            searchClear.classList.remove('show');
            
            // Reset filter tabs
            filterTabs.forEach(tab => tab.classList.remove('active'));
            filterTabs[0].classList.add('active'); // Set "All" as active
            
            // Reset sort
            sortOptionItems.forEach(opt => opt.classList.remove('active'));
            sortOptionItems[0].classList.add('active'); // Set first option as active
            sortButton.querySelector('span').textContent = 'Sort by Name';
            
            // Reset variables
            currentFilter = 'all';
            currentSort = 'name';
            
            // Apply reset
            performSearch('');
            applyFilters();
            applySorting();
            updateResetButton();
        });
    }
    
    // Search function
    function performSearch(query) {
        let visibleCount = 0;
        
        universityCards.forEach(card => {
            const universityName = card.querySelector('.university-name').textContent.toLowerCase();
            const cardText = card.querySelector('.card-text').textContent.toLowerCase();
            const location = card.querySelector('.university-meta').textContent.toLowerCase();
            
            const matches = universityName.includes(query) || 
                          cardText.includes(query) || 
                          location.includes(query) || 
                          query === '';
            
            if (matches) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        updateCount(visibleCount);
        updateResetButton();
    }
    
    // Filter function
    function applyFilters() {
        let visibleCount = 0;
        
        universityCards.forEach(card => {
            let shouldShow = true;
            
            if (currentFilter !== 'all') {
                // Add your filter logic here based on university data
                // For now, showing all as we don't have category data in the cards
                shouldShow = true;
            }
            
            if (shouldShow && card.style.display !== 'none') {
                card.style.display = 'block';
                visibleCount++;
            } else if (!shouldShow) {
                card.style.display = 'none';
            }
        });
        
        // Update tab counts
        const activeTab = document.querySelector('.filter-tab.active .tab-count');
        if (activeTab) {
            activeTab.textContent = visibleCount;
        }
        
        updateCount(visibleCount);
    }
    
    // Sort function
    function applySorting() {
        const cardsArray = Array.from(universityCards);
        
        cardsArray.sort((a, b) => {
            if (currentSort === 'name') {
                const nameA = a.querySelector('.university-name').textContent.toLowerCase();
                const nameB = b.querySelector('.university-name').textContent.toLowerCase();
                return nameA.localeCompare(nameB);
            } else if (currentSort === 'location') {
                const locationA = a.querySelector('.university-meta').textContent.toLowerCase();
                const locationB = b.querySelector('.university-meta').textContent.toLowerCase();
                return locationA.localeCompare(locationB);
            }
            // Add more sorting options as needed
            return 0;
        });
        
        // Reorder DOM elements
        cardsArray.forEach(card => {
            universitiesGrid.appendChild(card);
        });
    }
    
    // Toggle view function
    function toggleView() {
        if (currentView === 'list') {
            universitiesGrid.classList.add('list-view');
            universitiesGrid.classList.remove('row');
        } else {
            universitiesGrid.classList.remove('list-view');
            universitiesGrid.classList.add('row');
        }
    }
    
    // Update count function
    function updateCount(count) {
        if (universityCount) {
            universityCount.textContent = count;
        }
    }
    
    // Update reset button visibility
    function updateResetButton() {
        const hasActiveFilters = currentFilter !== 'all' || 
                                currentSort !== 'name' || 
                                searchInput.value.trim() !== '';
        
        if (hasActiveFilters) {
            filterReset.style.display = 'block';
        } else {
            filterReset.style.display = 'none';
        }
    }
    
    // Initialize
    updateResetButton();

});
</script>

<?php include 'includes/footer.php'; ?> 