<?php
$page_title = "MBBS Destinations - Best Countries to Study Abroad";
$page_description = "Explore the best countries for MBBS studies abroad. Compare destinations, universities, and opportunities for medical education.";
include 'includes/header.php';
include 'includes/functions.php';

// Get all countries from database
$countries = getCountriesByRegion();
$totalCountries = count($countries);
?>

<!-- Hero Section -->
<section class="destinations-hero">
    <div class="container">
        <div class="hero-content">
            <nav class="breadcrumb">
                <a href="index.php">Home</a>
                <span>/</span>
                <span>Countries</span>
            </nav>
            <h1 class="hero-title">Best Countries to Study Abroad</h1>
        </div>
    </div>
</section>

<!-- Search Section -->
<section class="search-section">
    <div class="container">
        <div class="search-wrapper">
            <div class="search-header">
                <p class="search-subtitle">Search through top MBBS destinations and find the perfect country for your medical education journey</p>
            </div>
            
            <div class="search-container">
                <div class="search-box">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" id="countrySearch" placeholder="Search countries, universities, or student info..." class="search-input">
                    <div class="search-actions">
                        <button class="search-clear" id="searchClear">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="search-btn">
                            <i class="fas fa-search"></i>
                            Search
                        </button>
                    </div>
                </div>
                
                <div class="search-suggestions" id="searchSuggestions">
                    <!-- Suggestions will be populated by JavaScript -->
                </div>
            </div>
            
            <div class="search-stats">
                <span class="search-result-count" id="resultCount"><?php echo $totalCountries; ?> Countries</span>
                <span>•</span>
                <span>Popular destinations for international students</span>
            </div>
            
            <div class="search-filters">
                <div class="filter-tag" data-filter="all">
                    <i class="fas fa-globe"></i>
                    All Countries
                </div>
                <div class="filter-tag" data-filter="europe">
                    <i class="fas fa-map-marker-alt"></i>
                    Europe
                </div>
                <div class="filter-tag" data-filter="asia">
                    <i class="fas fa-map-marker-alt"></i>
                    Asia
                </div>
                <div class="filter-tag" data-filter="popular">
                    <i class="fas fa-fire"></i>
                    Most Popular
                </div>
                <div class="filter-tag" data-filter="budget">
                    <i class="fas fa-dollar-sign"></i>
                    Budget Friendly
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Countries Grid Section -->
<section class="countries-section">
    <div class="container">
        <div class="countries-grid">
            
            <?php if (!empty($countries)): ?>
                <?php foreach ($countries as $country): 
                    $universitiesCount = getUniversitiesCountByCountry($country['id']);
                    $categoriesString = is_array($country['categories']) ? implode(',', $country['categories']) : '';
                ?>
                <div class="country-card" 
                     data-country="<?php echo htmlspecialchars($country['slug']); ?>" 
                     data-region="<?php echo htmlspecialchars($country['region']); ?>" 
                     data-category="<?php echo htmlspecialchars($categoriesString); ?>">
                    
                    <div class="country-header">
                        <div class="country-flag">
                            <span class="flag-icon flag-icon-<?php echo htmlspecialchars($country['flag_code']); ?>"></span>
                        </div>
                        <div class="country-info">
                            <h3 class="country-name"><?php echo htmlspecialchars($country['name']); ?></h3>
                            <p class="students-count">
                                <i class="fas fa-users"></i>
                                <?php echo number_format($country['student_count']); ?> Students
                            </p>
                            <p class="universities-count">
                                <i class="fas fa-university"></i>
                                <?php echo $universitiesCount; ?> <?php echo $universitiesCount === 1 ? 'University' : 'Universities'; ?>
                            </p>
                        </div>
                    </div>
                    
                    <div class="country-content">
                        <p class="country-description">
                            <?php echo htmlspecialchars(substr($country['description'], 0, 200)); ?>
                            <?php if (strlen($country['description']) > 200) echo '...'; ?>
                        </p>
                        
                        <?php if (!empty($country['categories'])): ?>
                        <div class="country-tags">
                            <?php foreach ($country['categories'] as $category): ?>
                                <span class="tag tag-<?php echo htmlspecialchars($category); ?>">
                                    <?php echo ucfirst($category); ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>
                        
                        <div class="country-actions">
                            <a href="university-partners.php?country=<?php echo urlencode($country['slug']); ?>" 
                               class="btn btn-secondary" 
                               data-track="explore_universities"
                               data-country="<?php echo htmlspecialchars($country['name']); ?>">
                                View Universities
                            </a>
                            <a href="contact.php?country=<?php echo urlencode($country['slug']); ?>" 
                               class="btn btn-accent" 
                               data-track="talk_to_counselor"
                               data-country="<?php echo htmlspecialchars($country['name']); ?>">
                                Get Consultation
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
            <?php else: ?>
                <div class="no-countries">
                    <div class="no-countries-content">
                        <i class="fas fa-globe-americas fa-3x"></i>
                        <h3>No countries available</h3>
                        <p>We're currently updating our destinations. Please check back soon!</p>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
</section>

<!-- Call to Action Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">Ready to Start Your Medical Journey?</h2>
            <p class="cta-description">Get personalized guidance from our expert counselors and find the perfect destination for your MBBS studies.</p>
            <div class="cta-buttons">
                <a href="contact.php" class="btn btn-warning">Get Free Consultation</a>
            </div>
        </div>
    </div>
</section>

<!-- External CSS -->
<link rel="stylesheet" href="assets/css/destinations.css">

<!-- Flag Icons CDN -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">

<!-- Font Awesome for Icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- JavaScript for Enhanced Functionality -->
<script src="assets/js/destinations.js"></script>
<script>
// Enhanced tracking and dynamic functionality
document.addEventListener('DOMContentLoaded', function() {
    // Track button clicks with additional country context
    const buttons = document.querySelectorAll('.country-actions .btn');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            const country = this.getAttribute('data-country');
            const action = this.getAttribute('data-track');
            
            // Analytics tracking (if available)
            if (typeof gtag !== 'undefined') {
                gtag('event', action, {
                    'event_category': 'country_interaction',
                    'event_label': country,
                    'value': 1
                });
            }
            
            // Console log for development
            console.log('Country Action:', {action, country, url: this.href});
        });
    });
    
    // Update search functionality for database-driven content
    const searchInput = document.getElementById('countrySearch');
    const countryCards = document.querySelectorAll('.country-card');
    const resultCount = document.getElementById('resultCount');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            countryCards.forEach(card => {
                const countryName = card.querySelector('.country-name').textContent.toLowerCase();
                const description = card.querySelector('.country-description').textContent.toLowerCase();
                const region = card.getAttribute('data-region');
                
                const matches = countryName.includes(query) || 
                              description.includes(query) || 
                              region.includes(query);
                
                if (matches || query === '') {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Update result count
            if (resultCount) {
                resultCount.textContent = `${visibleCount} Countries`;
            }
        });
    }
    
    // Filter functionality
    const filterTags = document.querySelectorAll('.filter-tag');
    filterTags.forEach(tag => {
        tag.addEventListener('click', function() {
            // Remove active class from all tags
            filterTags.forEach(t => t.classList.remove('active'));
            // Add active class to clicked tag
            this.classList.add('active');
            
            const filter = this.getAttribute('data-filter');
            let visibleCount = 0;
            
            countryCards.forEach(card => {
                const region = card.getAttribute('data-region');
                const categories = card.getAttribute('data-category');
                
                let shouldShow = false;
                
                if (filter === 'all') {
                    shouldShow = true;
                } else if (filter === 'europe' || filter === 'asia') {
                    shouldShow = region === filter;
                } else {
                    shouldShow = categories.includes(filter);
                }
                
                if (shouldShow) {
                    card.style.display = 'block';
                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Update result count
            if (resultCount) {
                resultCount.textContent = `${visibleCount} Countries`;
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?> 