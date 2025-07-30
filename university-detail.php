<?php
$page_title = "%s - Medical University Details | Study MBBS Abroad";
$page_description = "Get detailed information about %s including admission requirements, fees, programs, and application process for MBBS studies.";

include 'includes/header.php';
include 'includes/functions.php';

// Get university slug from URL
$university_slug = $_GET['slug'] ?? '';

if (empty($university_slug)) {
    header('Location: destinations.php');
    exit;
}

// Get university details
$university = getUniversityBySlug($university_slug);
if (!$university) {
    header('Location: destinations.php');
    exit;
}

// Get country details
$country = getCountryById($university['country_id']);

// Get university images
$university_images = getUniversityImages($university['id']);

// Update page title and description
$page_title = sprintf($page_title, $university['name']);
$page_description = sprintf($page_description, $university['name']);
?>

<!-- University Detail Hero Section -->
<section class="university-detail-hero position-relative overflow-hidden">
    <div class="hero-background"></div>
    <div class="container position-relative">
        <div class="row">
            <div class="col-12">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="hero-breadcrumb">
                    <ol class="breadcrumb hero-breadcrumb-list">
                        <li class="breadcrumb-item">
                            <a href="index.php" class="hero-breadcrumb-link">
                                Home
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="destinations.php" class="hero-breadcrumb-link">Countries</a>
                        </li>
                        <?php if ($country): ?>
                        <li class="breadcrumb-item">
                            <a href="university-partners.php?country=<?php echo urlencode($country['slug']); ?>" class="hero-breadcrumb-link">
                                <?php echo htmlspecialchars($country['name']); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active hero-breadcrumb-active" aria-current="page">
                            <?php echo htmlspecialchars($university['name']); ?>
                        </li>
                    </ol>
                </nav>
                
                <!-- University Header -->
                <div class="hero-content">
                    <div class="hero-main-info">
                        <?php if (!empty($university['logo_image'])): ?>
                        <div class="hero-university-logo">
                            <img src="<?php echo htmlspecialchars($university['logo_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($university['name']); ?> Logo" 
                                 class="hero-logo-image">
                        </div>
                        <?php endif; ?>
                        <div class="hero-university-details">
                            <h1 class="hero-university-name"><?php echo htmlspecialchars($university['name']); ?></h1>
                            <div class="hero-university-meta">
                                <div class="hero-meta-item">
                                    <i class="fas fa-map-marker-alt hero-meta-icon"></i>
                                    <span class="hero-meta-text"><?php echo htmlspecialchars($university['location']); ?></span>
                                </div>
                                <?php if ($country): ?>
                                <div class="hero-meta-item">
                                    <i class="fas fa-flag hero-meta-icon"></i>
                                    <span class="hero-meta-text"><?php echo htmlspecialchars($country['name']); ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="hero-actions">
                        <a href="#consultation-form" class="hero-action-btn hero-action-primary smooth-scroll">
                            
                            <span> Get Info</span>
                        </a>
                        <button class="hero-action-btn hero-action-secondary" onclick="window.print()">
                            <span>Print</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content Section -->
<section class="main-content-section">
    <div class="container">
        <div class="row g-4">
            <!-- Left Column - Main Content (70%) -->
            <div class="col-lg-8">
                <!-- University Image Gallery -->
                <div class="modern-card gallery-card">
                    <div class="card-body p-0">
                        <div class="university-gallery">
                            <!-- Main Image -->
                            <div class="main-image-container position-relative">
                                <?php if (!empty($university['featured_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($university['featured_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($university['name']); ?>" 
                                         class="main-university-image img-fluid w-100" 
                                         id="mainUniversityImage">
                                <?php else: ?>
                                    <div class="placeholder-image d-flex align-items-center justify-content-center">
                                        <i class="fas fa-university fa-4x text-muted"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Gallery Navigation -->
                                <?php if (count($university_images) > 0): ?>
                                <div class="gallery-nav">
                                    <button class="gallery-nav-btn gallery-prev" onclick="changeGalleryImage(-1)">
                                        <i class="fas fa-chevron-left"></i>
                                    </button>
                                    <button class="gallery-nav-btn gallery-next" onclick="changeGalleryImage(1)">
                                        <i class="fas fa-chevron-right"></i>
                                    </button>
                                </div>
                                <?php endif; ?>
                                
                                <!-- Image Counter -->
                                <?php if (count($university_images) > 0): ?>
                                <div class="image-counter">
                                    <span id="currentImageIndex">1</span> / <span id="totalImages"><?php echo count($university_images) + 1; ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Thumbnail Gallery -->
                            <?php if (!empty($university_images)): ?>
                            <div class="thumbnail-gallery mt-3">
                                <div class="row g-2">
                                    <!-- Featured Image Thumbnail -->
                                    <div class="col-auto">
                                        <img src="<?php echo htmlspecialchars($university['featured_image']); ?>" 
                                             alt="<?php echo htmlspecialchars($university['name']); ?>" 
                                             class="thumbnail-image active" 
                                             onclick="setMainImage('<?php echo htmlspecialchars($university['featured_image']); ?>', 0)">
                                    </div>
                                    
                                    <!-- Additional Images -->
                                    <?php foreach ($university_images as $index => $image): ?>
                                    <div class="col-auto">
                                        <img src="<?php echo htmlspecialchars($image['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($university['name']); ?> Image <?php echo $index + 2; ?>" 
                                             class="thumbnail-image" 
                                             onclick="setMainImage('<?php echo htmlspecialchars($image['image_url']); ?>', <?php echo $index + 1; ?>)">
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- About The University -->
                <div class="modern-card content-card">
                    <div class="modern-card-header">
                        <h3 class="modern-card-title">
                            <i class="fas fa-university card-icon"></i>About The University
                        </h3>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($university['about_university'])): ?>
                            <div class="university-content">
                                <?php 
                                // Display safe HTML content from TinyMCE
                                echo strip_tags($university['about_university'], '<p><br><strong><b><em><i><ul><ol><li><h1><h2><h3><h4><h5><h6><a><img><blockquote><table><tr><td><th><thead><tbody>');
                                ?>
                            </div>
                        <?php else: ?>
                            <p class="text-muted">University description will be updated soon. Please contact us for more information.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- University Information -->
                <div class="modern-card info-card">
                    <div class="modern-card-header">
                        <h3 class="modern-card-title">
                            <i class="fas fa-info-circle card-icon"></i>University Information
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <?php if (!empty($university['course_duration'])): ?>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-clock text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6 class="info-title">Course Duration</h6>
                                        <p class="info-value"><?php echo htmlspecialchars($university['course_duration']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($university['language_of_instruction'])): ?>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-language text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6 class="info-title">Language of Instruction</h6>
                                        <p class="info-value"><?php echo htmlspecialchars($university['language_of_instruction']); ?></p>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                            

                            
                            <div class="col-md-6">
                                <div class="info-item">
                                    <div class="info-icon">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                    </div>
                                    <div class="info-content">
                                        <h6 class="info-title">Location</h6>
                                        <p class="info-value"><?php echo htmlspecialchars($university['location']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Features and Facilities (if available) -->
                <div class="modern-card features-card">
                    <div class="modern-card-header">
                        <h3 class="modern-card-title">
                            <i class="fas fa-star card-icon"></i>Why Choose This University?
                        </h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>WHO & NMC Approved</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>International Recognition</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>Modern Infrastructure</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>Experienced Faculty</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>Student Support Services</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <span>Affordable Fees Structure</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar (30%) -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <!-- University Quick Info Card -->
                    <div class="modern-card sidebar-info-card">
                        <div class="modern-card-header accent-header">
                            <h4 class="sidebar-card-title">
                                <i class="fas fa-info-circle card-icon"></i>Quick Information
                            </h4>
                        </div>
                        <div class="card-body p-3">
                            <?php if (!empty($university['logo_image'])): ?>
                            <div class="text-center mb-3">
                                <img src="<?php echo htmlspecialchars($university['logo_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($university['name']); ?> Logo" 
                                     class="university-sidebar-logo">
                            </div>
                            <?php endif; ?>
                            
                            <h6 class="university-sidebar-name"><?php echo htmlspecialchars($university['name']); ?></h6>
                            
                            <div class="quick-info-list">
                                <div class="quick-info-item">
                                    <i class="fas fa-map-marker-alt text-primary"></i>
                                    <span><?php echo htmlspecialchars($university['location']); ?></span>
                                </div>
                                
                                <?php if (!empty($university['course_duration'])): ?>
                                <div class="quick-info-item">
                                    <i class="fas fa-clock text-primary"></i>
                                    <span><?php echo htmlspecialchars($university['course_duration']); ?></span>
                                </div>
                                <?php endif; ?>
                                
                                <?php if (!empty($university['language_of_instruction'])): ?>
                                <div class="quick-info-item">
                                    <i class="fas fa-language text-primary"></i>
                                    <span><?php echo htmlspecialchars($university['language_of_instruction']); ?></span>
                                </div>
                                <?php endif; ?>
                                

                            </div>
                        </div>
                    </div>

                    <!-- Get Free Counselling Form -->
                    <div class="modern-card consultation-form-card" id="consultation-form">
                        <div class="modern-card-header accent-header">
                            <h4 class="sidebar-card-title">
                                <i class="fas fa-phone-alt card-icon"></i>Get Free Counselling
                            </h4>
                        </div>
                        <div class="modern-card-body form-body">
                            <form id="universityDetailForm" action="process-university-inquiry.php" method="post">
                                <input type="hidden" name="university_id" value="<?php echo $university['id']; ?>">
                                <input type="hidden" name="university_name" value="<?php echo htmlspecialchars($university['name']); ?>">
                                <input type="hidden" name="country_id" value="<?php echo $university['country_id']; ?>">
                                <?php if ($country): ?>
                                <input type="hidden" name="country_name" value="<?php echo htmlspecialchars($country['name']); ?>">
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="student_name" class="modern-form-label">Full Name <span class="required-star">*</span></label>
                                    <input type="text" class="modern-form-input" id="student_name" name="student_name" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="email" class="modern-form-label">Email Address <span class="required-star">*</span></label>
                                    <input type="email" class="modern-form-input" id="email" name="email" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phone" class="modern-form-label">Phone Number <span class="required-star">*</span></label>
                                    <input type="tel" class="modern-form-input" id="phone" name="phone" required>
                                </div>
                                
                                <div class="form-row">
                                    <div class="col-half">
                                        <label for="country" class="modern-form-label">Country <span class="required-star">*</span></label>
                                        <select class="modern-form-select" id="country" name="country" required>
                                            <option value="">Select Country</option>
                                            <option value="India" selected>India</option>
                                        </select>
                                    </div>
                                    <div class="col-half">
                                        <label for="state" class="modern-form-label">State <span class="required-star">*</span></label>
                                        <select class="modern-form-select" id="state" name="state" required>
                                            <option value="">Select State</option>
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="city" class="modern-form-label">City <span class="required-star">*</span></label>
                                    <select class="modern-form-select" id="city" name="city" required>
                                        <option value="">Select City</option>
                                    </select>
                                    <div class="city-not-found mt-2" id="cityNotFound" style="display: none;">
                                        <small class="text-muted">City not found? 
                                            <a href="#" id="addCustomCity" class="text-primary">Add your city manually</a>
                                        </small>
                                    </div>
                                    <input type="text" class="modern-form-input mt-2" id="customCity" name="custom_city" 
                                           placeholder="Enter your city name" style="display: none;">
                                </div>
                                
                                <div class="form-group">
                                    <label for="message" class="modern-form-label">Message (Optional)</label>
                                    <textarea class="modern-form-textarea" id="message" name="message" rows="3" 
                                              placeholder="Tell us about your educational background or any specific questions..."></textarea>
                                </div>
                                
                                <div class="form-submit">
                                    <button type="submit" class="modern-submit-btn">
                                        <i class="fas fa-paper-plane submit-icon"></i>Submit Inquiry
                                    </button>
                                </div>
                            </form>

                            <!-- EmailJS Integration Script -->
                            <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
                            <script>
                                // Initialize EmailJS
                                (function() {
                                    emailjs.init("Xl2-rb_v5qwA8iJpI"); // Your EmailJS public key
                                })();

                                // Load Location Data from JSON
                                let locationData = null;
                                
                                // Function to load states for a country
                                function loadStatesForCountry(countryName) {
                                    const stateSelect = document.getElementById('state');
                                    const citySelect = document.getElementById('city');
                                    
                                    // Reset dropdowns
                                    stateSelect.innerHTML = '<option value="">Select State</option>';
                                    citySelect.innerHTML = '<option value="">Select City</option>';
                                    document.getElementById('cityNotFound').style.display = 'none';
                                    
                                    if (countryName && locationData) {
                                        console.log('Loading states for:', countryName);
                                        const country = locationData.countries.find(c => c.name === countryName);
                                        console.log('Found country:', country);
                                        
                                        if (country && country.states) {
                                            console.log('Number of states:', country.states.length);
                                            country.states.forEach(state => {
                                                const option = document.createElement('option');
                                                option.value = state.name;
                                                option.textContent = state.name;
                                                stateSelect.appendChild(option);
                                            });
                                        } else {
                                            console.log('No states found for country');
                                        }
                                    }
                                }
                                
                                // Function to load cities for a state
                                function loadCitiesForState(countryName, stateName) {
                                    const citySelect = document.getElementById('city');
                                    
                                    // Reset city dropdown
                                    citySelect.innerHTML = '<option value="">Select City</option>';
                                    document.getElementById('cityNotFound').style.display = 'none';
                                    
                                    if (countryName && stateName && locationData) {
                                        console.log('Loading cities for:', stateName, 'in', countryName);
                                        const country = locationData.countries.find(c => c.name === countryName);
                                        if (country && country.states) {
                                            const state = country.states.find(s => s.name === stateName);
                                            console.log('Found state:', state);
                                            
                                            if (state && state.cities) {
                                                console.log('Number of cities:', state.cities.length);
                                                state.cities.forEach(city => {
                                                    const option = document.createElement('option');
                                                    option.value = city;
                                                    option.textContent = city;
                                                    citySelect.appendChild(option);
                                                });
                                                
                                                // Show "city not found" option after loading cities
                                                setTimeout(() => {
                                                    document.getElementById('cityNotFound').style.display = 'block';
                                                }, 100);
                                            } else {
                                                console.log('No cities found for state');
                                            }
                                        }
                                    }
                                }
                                
                                // Function to load location data with multiple attempts
                                function loadLocationData() {
                                    const possiblePaths = [
                                        './Location.json',
                                        '/Location.json',
                                        'Location.json',
                                        '../Location.json'
                                    ];
                                    
                                    let attemptIndex = 0;
                                    
                                    function tryPath(pathIndex) {
                                        if (pathIndex >= possiblePaths.length) {
                                            console.error('All paths failed, using fallback data');
                                            useStaticLocationData();
                                            return;
                                        }
                                        
                                        const currentPath = possiblePaths[pathIndex];
                                        console.log('Trying path:', currentPath);
                                        
                                        fetch(currentPath)
                                            .then(response => {
                                                console.log('Response for', currentPath, '- Status:', response.status);
                                                if (!response.ok) {
                                                    throw new Error('HTTP ' + response.status);
                                                }
                                                return response.json();
                                            })
                                            .then(data => {
                                                console.log('✅ Successfully loaded location data from:', currentPath);
                                                locationData = data;
                                                console.log('Countries available:', locationData.countries.map(c => c.name));
                                                
                                                // Auto-load states for India (since it's pre-selected)
                                                const countrySelect = document.getElementById('country');
                                                if (countrySelect.value === 'India') {
                                                    loadStatesForCountry('India');
                                                }
                                            })
                                            .catch(error => {
                                                console.log('❌ Failed to load from', currentPath, ':', error.message);
                                                tryPath(pathIndex + 1);
                                            });
                                    }
                                    
                                    tryPath(0);
                                }
                                
                                // Fallback static data (basic states) if JSON fails to load
                                function useStaticLocationData() {
                                    console.log('Using fallback static location data');
                                    locationData = {
                                        "countries": [
                                            {
                                                "name": "India",
                                                "states": [
                                                    {"name": "Andhra Pradesh", "cities": ["Visakhapatnam", "Vijayawada", "Guntur", "Nellore", "Kurnool", "Rajahmundry", "Tirupati", "Kadapa", "Anantapur", "Eluru"]},
                                                    {"name": "Arunachal Pradesh", "cities": ["Itanagar", "Naharlagun", "Pasighat", "Tawang", "Ziro"]},
                                                    {"name": "Assam", "cities": ["Guwahati", "Silchar", "Dibrugarh", "Jorhat", "Nagaon", "Tinsukia", "Tezpur"]},
                                                    {"name": "Bihar", "cities": ["Patna", "Gaya", "Bhagalpur", "Muzaffarpur", "Purnia", "Darbhanga", "Bihar Sharif", "Arrah", "Begusarai", "Katihar"]},
                                                    {"name": "Chhattisgarh", "cities": ["Raipur", "Bhilai", "Bilaspur", "Korba", "Durg", "Rajnandgaon"]},
                                                    {"name": "Goa", "cities": ["Panaji", "Margao", "Vasco da Gama", "Mapusa", "Ponda"]},
                                                    {"name": "Gujarat", "cities": ["Ahmedabad", "Surat", "Vadodara", "Rajkot", "Bhavnagar", "Jamnagar", "Gandhinagar", "Junagadh", "Anand", "Bharuch"]},
                                                    {"name": "Haryana", "cities": ["Gurugram", "Faridabad", "Panipat", "Ambala", "Yamunanagar", "Rohtak", "Hisar", "Karnal", "Sonipat", "Panchkula"]},
                                                    {"name": "Himachal Pradesh", "cities": ["Shimla", "Dharamshala", "Solan", "Mandi", "Kullu", "Bilaspur", "Hamirpur"]},
                                                    {"name": "Jharkhand", "cities": ["Ranchi", "Jamshedpur", "Dhanbad", "Bokaro", "Deoghar", "Phusro", "Hazaribagh"]},
                                                    {"name": "Karnataka", "cities": ["Bengaluru", "Mysuru", "Hubballi", "Mangaluru", "Belagavi", "Davanagere", "Bellary", "Bijapur", "Shimoga", "Tumkur"]},
                                                    {"name": "Kerala", "cities": ["Thiruvananthapuram", "Kochi", "Kozhikode", "Thrissur", "Kollam", "Palakkad", "Alappuzha", "Malappuram", "Kannur"]},
                                                    {"name": "Madhya Pradesh", "cities": ["Bhopal", "Indore", "Jabalpur", "Gwalior", "Ujjain", "Sagar", "Dewas", "Satna", "Ratlam", "Rewa"]},
                                                    {"name": "Maharashtra", "cities": ["Mumbai", "Pune", "Nagpur", "Thane", "Nashik", "Aurangabad", "Solapur", "Amravati", "Kolhapur", "Sangli"]},
                                                    {"name": "Manipur", "cities": ["Imphal", "Thoubal", "Bishnupur", "Churachandpur"]},
                                                    {"name": "Meghalaya", "cities": ["Shillong", "Tura", "Jowai"]},
                                                    {"name": "Mizoram", "cities": ["Aizawl", "Lunglei", "Champhai"]},
                                                    {"name": "Nagaland", "cities": ["Kohima", "Dimapur", "Mokokchung"]},
                                                    {"name": "Odisha", "cities": ["Bhubaneswar", "Cuttack", "Rourkela", "Berhampur", "Sambalpur", "Puri", "Balasore", "Bhadrak"]},
                                                    {"name": "Punjab", "cities": ["Ludhiana", "Amritsar", "Jalandhar", "Patiala", "Bathinda", "Mohali", "Pathankot", "Hoshiarpur", "Batala", "Moga"]},
                                                    {"name": "Rajasthan", "cities": ["Jaipur", "Jodhpur", "Udaipur", "Kota", "Ajmer", "Bikaner", "Bhilwara", "Alwar", "Bharatpur", "Sikar"]},
                                                    {"name": "Sikkim", "cities": ["Gangtok", "Namchi", "Geyzing", "Mangan"]},
                                                    {"name": "Tamil Nadu", "cities": ["Chennai", "Coimbatore", "Madurai", "Tiruchirappalli", "Salem", "Tirunelveli", "Tiruppur", "Vellore", "Erode", "Thoothukkudi"]},
                                                    {"name": "Telangana", "cities": ["Hyderabad", "Warangal", "Nizamabad", "Khammam", "Karimnagar", "Ramagundam", "Mahbubnagar", "Nalgonda", "Adilabad", "Suryapet"]},
                                                    {"name": "Tripura", "cities": ["Agartala", "Dharmanagar", "Udaipur", "Kailasahar"]},
                                                    {"name": "Uttar Pradesh", "cities": ["Lucknow", "Kanpur", "Ghaziabad", "Agra", "Varanasi", "Meerut", "Allahabad", "Bareilly", "Moradabad", "Aligarh", "Gorakhpur", "Saharanpur", "Noida", "Firozabad", "Jhansi", "Muzaffarnagar"]},
                                                    {"name": "Uttarakhand", "cities": ["Dehradun", "Haridwar", "Roorkee", "Haldwani", "Rudrapur", "Kashipur", "Rishikesh"]},
                                                    {"name": "West Bengal", "cities": ["Kolkata", "Howrah", "Durgapur", "Asansol", "Siliguri", "Malda", "Bardhaman", "Baharampur", "Habra", "Kharagpur"]},
                                                    {"name": "Delhi", "cities": ["New Delhi", "Central Delhi", "South Delhi", "East Delhi", "West Delhi", "North Delhi", "North East Delhi", "North West Delhi", "South East Delhi", "South West Delhi", "Shahdara"]},
                                                    {"name": "Andaman and Nicobar Islands", "cities": ["Port Blair", "Diglipur", "Mayabunder"]},
                                                    {"name": "Chandigarh", "cities": ["Chandigarh"]},
                                                    {"name": "Dadra and Nagar Haveli and Daman and Diu", "cities": ["Daman", "Diu", "Silvassa"]},
                                                    {"name": "Jammu and Kashmir", "cities": ["Srinagar", "Jammu", "Baramulla", "Anantnag", "Udhampur", "Kathua"]},
                                                    {"name": "Ladakh", "cities": ["Leh", "Kargil"]},
                                                    {"name": "Lakshadweep", "cities": ["Kavaratti"]},
                                                    {"name": "Puducherry", "cities": ["Puducherry", "Karaikal", "Mahe", "Yanam"]}
                                                ]
                                            }
                                        ]
                                    };
                                    
                                    console.log('✅ Fallback data loaded with', locationData.countries[0].states.length, 'states');
                                    
                                    // Auto-load states for India
                                    const countrySelect = document.getElementById('country');
                                    if (countrySelect.value === 'India') {
                                        loadStatesForCountry('India');
                                    }
                                }
                                
                                // Start loading location data
                                loadLocationData();

                                // University Detail Form EmailJS Handler
                                document.getElementById('universityDetailForm').addEventListener('submit', function(event) {
                                    event.preventDefault();
                                    
                                    const form = event.target;
                                    const submitBtn = form.querySelector('button[type="submit"]');
                                    const originalText = submitBtn.innerHTML;
                                    
                                    // Show loading state
                                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                                    submitBtn.disabled = true;
                                    
                                    // Get university name from page
                                    const universityName = form.university_name.value || '<?php echo addslashes($university['name']); ?>';
                                    const universityLocation = '<?php echo addslashes($university['location']); ?>';
                                    const countryName = '<?php echo addslashes($country['name'] ?? ''); ?>';
                                    
                                    // Get city value (either selected or custom)
                                    const selectedCity = form.city.value;
                                    const customCity = form.custom_city.value;
                                    const finalCity = customCity || selectedCity;
                                    
                                    // Prepare template parameters
                                    const templateParams = {
                                        student_name: form.student_name.value,
                                        from_name: form.student_name.value,
                                        from_email: form.email.value,
                                        phone: form.phone.value,
                                        country: form.country.value,
                                        state: form.state.value,
                                        city: finalCity,
                                        message: form.message.value || 'No additional message provided.',
                                        university_name: universityName,
                                        university_location: universityLocation,
                                        university_country: countryName,
                                        to_email: 'ajaysingh261526@gmail.com',
                                        date: new Date().toLocaleString(),
                                        subject: 'New University Inquiry: ' + universityName + ' - ' + form.student_name.value,
                                        inquiry_type: 'University Detail Page Inquiry'
                                    };
                                    
                                    // Send via EmailJS
                                    emailjs.send('service_igiat6d', 'template_kxu5e1d', templateParams)
                                        .then(function() {
                                            alert('Thank you! Your inquiry about ' + universityName + ' has been sent successfully. We will contact you soon with detailed information.');
                                            form.reset();
                                            // Reset custom city functionality
                                            document.getElementById('customCity').style.display = 'none';
                                            document.getElementById('cityNotFound').style.display = 'none';
                                            // Reset dropdowns
                                            document.getElementById('state').innerHTML = '<option value="">Select State</option>';
                                            document.getElementById('city').innerHTML = '<option value="">Select City</option>';
                                        }, function(error) {
                                            console.log('EmailJS Error:', error);
                                            console.log('Falling back to PHP form submission...');
                                            alert('Using backup email system...');
                                            // Fallback to PHP form
                                            submitUniversityFormViaPhp(form);
                                        })
                                        .finally(function() {
                                            // Restore button
                                            submitBtn.innerHTML = originalText;
                                            submitBtn.disabled = false;
                                        });
                                });
                                
                                // Fallback to PHP submission
                                function submitUniversityFormViaPhp(form) {
                                    const formData = new FormData(form);
                                    
                                    fetch('process-university-inquiry.php', {
                                        method: 'POST',
                                        body: formData
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.status === 'success' || data.success) {
                                            alert(data.message);
                                            form.reset();
                                        } else {
                                            alert('Error: ' + data.message);
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        alert('There was an error submitting your form. Please try again or contact us directly.');
                                    });
                                }

                                // Location Selection Logic using Location.json
                                document.getElementById('country').addEventListener('change', function() {
                                    const countryName = this.value;
                                    console.log('Country changed to:', countryName);
                                    loadStatesForCountry(countryName);
                                });

                                document.getElementById('state').addEventListener('change', function() {
                                    const countryName = document.getElementById('country').value;
                                    const stateName = this.value;
                                    console.log('State changed to:', stateName);
                                    loadCitiesForState(countryName, stateName);
                                });

                                // Custom city functionality
                                document.getElementById('addCustomCity').addEventListener('click', function(e) {
                                    e.preventDefault();
                                    const customCityInput = document.getElementById('customCity');
                                    const citySelect = document.getElementById('city');
                                    
                                    // Show custom input and hide city select
                                    customCityInput.style.display = 'block';
                                    customCityInput.focus();
                                    citySelect.required = false;
                                    customCityInput.required = true;
                                    
                                    this.textContent = 'Use dropdown instead';
                                    this.onclick = function(e) {
                                        e.preventDefault();
                                        customCityInput.style.display = 'none';
                                        customCityInput.value = '';
                                        citySelect.required = true;
                                        customCityInput.required = false;
                                        this.textContent = 'Add your city manually';
                                        this.onclick = arguments.callee;
                                    };
                                });
                            </script>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="modern-card contact-card">
                        <div class="modern-card-header">
                            <h5 class="sidebar-card-title">
                                <i class="fas fa-headset card-icon"></i>Need Immediate Help?
                            </h5>
                        </div>
                        <div class="card-body p-3">
                            <div class="contact-item mb-2">
                                <i class="fas fa-phone text-success me-2"></i>
                                <a href="tel:+919996817513" class="text-decoration-none">+91-9996817513</a>
                            </div>
                            <div class="contact-item mb-2">
                                <i class="fab fa-whatsapp text-success me-2"></i>
                                <a href="https://wa.me/919996817513" class="text-decoration-none" target="_blank">WhatsApp Chat</a>
                            </div>
                            <div class="contact-item">
                                <i class="fas fa-envelope text-primary me-2"></i>
                                <a href="mailto:sunriseglobaleducationgurgaon@gmail.com" class="text-decoration-none">sunriseglobaleducationgurgaon@gmail.com</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Enhanced CSS for University Detail -->
<style>
/* University Detail Custom Styles */
.university-detail-hero {
    min-height: 220px;
    position: relative;
    display: flex;
    align-items: center;
    padding: 40px 0;
}

.hero-background {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    z-index: 1;
}

.hero-background::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.05) 0%, transparent 50%);
    z-index: 2;
}

.university-detail-hero .container {
    z-index: 3;
}

/* Breadcrumb Styles */
.hero-breadcrumb {
    margin-bottom: 32px;
}

.hero-breadcrumb-list {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 50px;
    padding: 12px 24px;
    margin: 0;
    display: inline-flex;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}

.hero-breadcrumb-link {
    color: rgba(255, 255, 255, 0.9);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
}

.hero-breadcrumb-link:hover {
    color: #ffffff;
    transform: translateY(-1px);
}

.hero-breadcrumb-active {
    color: var(--accent-color) !important;
    font-size: 14px;
    font-weight: 600;
}

/* Hero Content */
.hero-content {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 24px;
}

.hero-main-info {
    display: flex;
    align-items: center;
    gap: 24px;
    flex: 1;
    min-width: 0;
}

/* University Logo */
.hero-university-logo {
    flex-shrink: 0;
}

.hero-logo-image {
    width: 90px;
    height: 90px;
    object-fit: contain;
    background: rgba(255, 255, 255, 0.95);
    padding: 12px;
    border-radius: 16px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.15);
    border: 2px solid rgba(255, 255, 255, 0.2);
    transition: transform 0.3s ease;
}

.hero-logo-image:hover {
    transform: scale(1.05);
}

/* University Details */
.hero-university-details {
    flex: 1;
    min-width: 0;
}

.hero-university-name {
    font-size: 2.25rem;
    font-weight: 700;
    color: #ffffff;
    margin: 0 0 12px 0;
    line-height: 1.2;
    letter-spacing: -0.02em;
}

.hero-university-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.hero-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(255, 255, 255, 0.1);
    padding: 8px 16px;
    border-radius: 25px;
    border: 1px solid rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(5px);
}

.hero-meta-icon {
    color: rgba(255, 255, 255, 0.8);
    font-size: 14px;
    width: 16px;
    text-align: center;
}

.hero-meta-text {
    color: rgba(255, 255, 255, 0.9);
    font-size: 15px;
    font-weight: 500;
    white-space: nowrap;
}

/* Hero Actions */
.hero-actions {
    display: flex;
    gap: 12px;
    flex-shrink: 0;
}

.hero-action-btn {
    display: flex;
    align-items: center;
    padding: 12px 24px;
    border-radius: 50px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    position: relative;
    overflow: hidden;
}

.hero-action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.hero-action-btn:hover::before {
    left: 100%;
}

.hero-action-primary {
    background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    color: #000;
    border: 2px solid transparent;
}

.hero-action-primary:hover {
    background: linear-gradient(135deg, #ffb300 0%, #f57c00 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 193, 7, 0.3);
    color: #000;
}

.hero-action-secondary {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
    border: 2px solid rgba(255, 255, 255, 0.3);
    backdrop-filter: blur(10px);
}

.hero-action-secondary:hover {
    background: rgba(255, 255, 255, 0.25);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 255, 255, 0.1);
    color: #ffffff;
}

/* ===== MAIN CONTENT SECTION STYLES ===== */
.main-content-section {
    padding: var(--xxl) 0;
    background: linear-gradient(135deg, var(--light-bg) 0%, #ffffff 100%);
    min-height: 100vh;
}

/* Modern Card Styles */
.modern-card {
    background: var(--white);
    border-radius: var(--large);
    box-shadow: var(--medium-shadow);
    border: 1px solid var(--border-color);
    overflow: hidden;
    transition: var(--transition);
    margin-bottom: var(--xl);
}

.modern-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--large-shadow);
}

.modern-card-header {
    background: linear-gradient(135deg, var(--light-bg) 0%, #f0f4f8 100%);
    padding: var(--lg) var(--xl);
    border-bottom: 2px solid var(--border-color);
    position: relative;
}

.modern-card-header::before {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 60px;
    height: 3px;
    background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
    border-radius: var(--small-radius);
}

.modern-card-title {
    font-size: var(--medium);
    font-weight: var(--semibold);
    color: var(--text-color);
    margin: 0;
    display: flex;
    align-items: center;
    gap: var(--sm);
}

.card-icon {
    color: var(--primary-color);
    font-size: var(--medium);
    width: 20px;
    text-align: center;
}

.modern-card-body {
    padding: var(--xl);
}

/* Sidebar Card Styles */
.sidebar-info-card .modern-card-header.primary-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: var(--white);
}

.sidebar-info-card .primary-header .card-icon {
    color: var(--white);
}

.consultation-form-card .modern-card-header.accent-header {
    background: linear-gradient(135deg, var(--accent-color) 0%, var(--accent-dark) 100%);
    color: var(--text-color);
}

.consultation-form-card .accent-header .card-icon {
    color: var(--text-color);
}

.sidebar-card-title {
    font-size: var(--base);
    font-weight: var(--semibold);
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--sm);
    text-align: center;
}

/* ===== PROFESSIONAL FORM STYLES ===== */
.form-body {
    padding: var(--xl) var(--lg);
}

.form-group {
    margin-bottom: var(--lg);
    position: relative;
}

.form-row {
    display: flex;
    gap: var(--md);
    margin-bottom: var(--lg);
}

.col-half {
    flex: 1;
}

.modern-form-label {
    display: block;
    font-size: var(--small);
    font-weight: var(--medium-weight);
    color: var(--text-color);
    margin-bottom: var(--xs);
    letter-spacing: 0.02em;
}

.required-star {
    color: #e74c3c;
    font-weight: var(--bold);
}

.modern-form-input,
.modern-form-select,
.modern-form-textarea {
    width: 100%;
    padding: var(--md) var(--sm);
    border: 2px solid var(--border-color);
    border-radius: var(--medium-radius);
    font-size: var(--small);
    font-family: var(--font-family);
    background: var(--white);
    transition: var(--transition);
    outline: none;
}

.modern-form-input:focus,
.modern-form-select:focus,
.modern-form-textarea:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(0, 53, 133, 0.1);
    transform: translateY(-1px);
}

.modern-form-input:hover,
.modern-form-select:hover,
.modern-form-textarea:hover {
    border-color: var(--primary-light);
}

.modern-form-select {
    cursor: pointer;
    background-image: url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 4 5'%3E%3Cpath fill='%23777' d='M2 0L0 2h4zm0 5L0 3h4z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right var(--sm) center;
    background-size: 12px;
    padding-right: var(--xl);
}

.modern-form-textarea {
    resize: vertical;
    min-height: 90px;
    line-height: var(--normal);
}

.modern-form-textarea::placeholder {
    color: var(--light-text);
    font-style: italic;
}

.form-submit {
    margin-top: var(--lg);
}

.modern-submit-btn {
    width: 100%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: var(--white);
    border: none;
    padding: var(--md) var(--lg);
    border-radius: var(--medium-radius);
    font-size: var(--base);
    font-weight: var(--semibold);
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--sm);
    position: relative;
    overflow: hidden;
}

.modern-submit-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.modern-submit-btn:hover::before {
    left: 100%;
}

.modern-submit-btn:hover {
    background: linear-gradient(135deg, var(--primary-dark) 0%, var(--primary-color) 100%);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 53, 133, 0.3);
}

.modern-submit-btn:active {
    transform: translateY(0);
}

.submit-icon {
    font-size: var(--small);
}

/* Gallery Styles */
.main-image-container {
    height: 400px;
    overflow: hidden;
    border-radius: var(--medium-radius);
}

.main-university-image {
    height: 100%;
    object-fit: cover;
    border-radius: var(--medium-radius);
}

.placeholder-image {
    height: 400px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: var(--medium-radius);
}

.gallery-nav {
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    transform: translateY(-50%);
    display: flex;
    justify-content: space-between;
    padding: 0 20px;
    pointer-events: none;
}

.gallery-nav-btn {
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    pointer-events: all;
}

.gallery-nav-btn:hover {
    background: rgba(0, 0, 0, 0.8);
    transform: scale(1.1);
}

.image-counter {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.8rem;
}

.thumbnail-image {
    width: 80px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    opacity: 0.7;
}

.thumbnail-image:hover,
.thumbnail-image.active {
    opacity: 1;
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Info Items - Enhanced */
.info-item {
    display: flex;
    align-items: flex-start;
    gap: var(--md);
    padding: var(--md);
    background: linear-gradient(135deg, var(--light-bg) 0%, #fafbfc 100%);
    border-radius: var(--medium-radius);
    border: 1px solid var(--border-color);
    transition: var(--transition);
}

.info-item:hover {
    transform: translateY(-2px);
    box-shadow: var(--small-shadow);
    border-color: var(--primary-light);
}

.info-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
    color: var(--white);
    border-radius: var(--circle);
    font-size: var(--small);
}

.info-content {
    flex: 1;
}

.info-title {
    font-size: var(--small);
    font-weight: var(--semibold);
    color: var(--text-color);
    margin-bottom: var(--xs);
    letter-spacing: 0.02em;
}

.info-value {
    font-size: var(--base);
    color: var(--light-text);
    margin: 0;
    font-weight: var(--medium-weight);
}

/* Feature Items - Enhanced */
.feature-item {
    display: flex;
    align-items: center;
    gap: var(--sm);
    padding: var(--sm) var(--md);
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-radius: var(--medium-radius);
    border: 1px solid #bfdbfe;
    margin-bottom: var(--sm);
    transition: var(--transition);
    font-size: var(--small);
    font-weight: var(--medium-weight);
}

.feature-item:hover {
    transform: translateX(4px);
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border-color: var(--primary-light);
}

.feature-item i {
    color: #10b981;
    font-size: var(--base);
}

/* Sidebar Styles */
.sticky-sidebar {
    position: sticky;
    top: 20px;
}

.university-sidebar-logo {
    width: 60px;
    height: 60px;
    object-fit: contain;
    border-radius: 8px;
}

.university-sidebar-name {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 12px;
    text-align: center;
}

.quick-info-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.quick-info-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    padding: 4px 0;
}

/* Contact Items */
.contact-item {
    display: flex;
    align-items: center;
    font-size: 0.9rem;
}

/* University Content from TinyMCE */
.university-content {
    line-height: 1.6;
    color: var(--text-color);
}

.university-content p {
    margin-bottom: 1rem;
}

.university-content h1,
.university-content h2,
.university-content h3,
.university-content h4,
.university-content h5,
.university-content h6 {
    color: var(--primary-color);
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.university-content h1 { font-size: 1.75rem; }
.university-content h2 { font-size: 1.5rem; }
.university-content h3 { font-size: 1.25rem; }
.university-content h4 { font-size: 1.1rem; }
.university-content h5 { font-size: 1rem; }
.university-content h6 { font-size: 0.9rem; }

.university-content ul,
.university-content ol {
    margin-bottom: 1rem;
    padding-left: 1.5rem;
}

.university-content li {
    margin-bottom: 0.25rem;
}

.university-content strong,
.university-content b {
    font-weight: 600;
    color: var(--text-color);
}

.university-content em,
.university-content i {
    font-style: italic;
}

.university-content a {
    color: var(--primary-color);
    text-decoration: none;
}

.university-content a:hover {
    color: var(--primary-dark);
    text-decoration: underline;
}

.university-content blockquote {
    border-left: 4px solid var(--primary-color);
    margin: 1rem 0;
    padding: 0.5rem 1rem;
    background: var(--light-bg);
    font-style: italic;
}

.university-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1rem 0;
}

.university-content table th,
.university-content table td {
    border: 1px solid var(--border-color);
    padding: 0.5rem;
    text-align: left;
}

.university-content table th {
    background: var(--light-bg);
    font-weight: 600;
    color: var(--primary-color);
}

.university-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--medium-radius);
    margin: 1rem 0;
}

/* Form Enhancements */
.form-control-sm,
.form-select-sm {
    font-size: 0.875rem;
}

/* Responsive Design */
@media (max-width: 992px) {
    .sticky-sidebar {
        position: static;
        margin-top: 0;
    }
    
    .main-image-container {
        height: 300px;
    }
    
    .hero-content {
        flex-direction: column;
        align-items: center;
        gap: 20px;
        text-align: center;
    }
    
    .hero-main-info {
        align-items: center;
        text-align: center;
    }
    
    .hero-university-details {
        text-align: center;
    }
    
    .hero-actions {
        align-self: center;
        justify-content: center;
        max-width: 400px;
        width: 100%;
    }
    
    .hero-university-name {
        font-size: 1.8rem;
        text-align: center;
    }
    
    .hero-university-meta {
        justify-content: center;
    }
    
    .hero-logo-image {
        width: 75px;
        height: 75px;
    }
    
    /* Modern Cards Tablet Responsive */
    .main-content-section {
        padding: var(--xl) 0;
    }
    
    .modern-card {
        margin-bottom: var(--lg);
    }
    
    .modern-card-header {
        padding: var(--md) var(--lg);
    }
    
    .modern-card-body {
        padding: var(--lg);
    }
    
    .form-body {
        padding: var(--lg) var(--md);
    }
}

@media (max-width: 768px) {
    .university-detail-hero {
        min-height: 200px;
        padding: 30px 0;
        text-align: center;
    }
    
    .university-detail-hero .container {
        padding-left: 15px;
        padding-right: 15px;
        max-width: 100%;
    }
    
    .university-detail-hero .row {
        margin-left: 0;
        margin-right: 0;
    }
    
    .university-detail-hero .col-12 {
        padding-left: 0;
        padding-right: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
    }
    
    .hero-breadcrumb {
        margin-bottom: 24px;
        text-align: center;
        width: 100%;
        display: flex;
        justify-content: center;
    }
    
    .hero-breadcrumb-list {
        padding: 10px 20px;
        font-size: 13px;
        margin: 0 auto;
    }
    
    .hero-content {
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 20px;
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
    }
    
    .hero-main-info {
        flex-direction: column;
        align-items: center;
        gap: 16px;
        text-align: center;
        width: 100%;
        margin: 0 auto;
    }
    
    .hero-university-logo {
        order: -1;
        margin-bottom: 8px;
        display: flex;
        justify-content: center;
        width: 100%;
    }
    
    .hero-university-details {
        width: 100%;
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .hero-university-name {
        font-size: 1.6rem;
        margin-bottom: 16px;
        text-align: center;
        line-height: 1.2;
        width: 100%;
        margin-left: auto;
        margin-right: auto;
    }
    
    .hero-university-meta {
        flex-direction: column;
        gap: 12px;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin: 0 auto;
    }
    
    .hero-meta-item {
        padding: 6px 14px;
        font-size: 14px;
        margin: 0 auto;
    }
    
    .hero-actions {
        width: 100%;
        flex-direction: column;
        gap: 10px;
        align-items: center;
        margin: 0 auto;
        padding: 0 15px;
    }
    
    .hero-action-btn {
        padding: 14px 20px;
        justify-content: center;
        width: 100%;
        max-width: 280px;
        margin: 0 auto;
    }
    
    .main-image-container {
        height: 250px;
    }
    
    .thumbnail-image {
        width: 60px;
        height: 45px;
    }
    
    /* Modern Cards Mobile Responsive */
    .main-content-section {
        padding: var(--lg) 0;
    }
    
    .modern-card {
        margin-bottom: var(--md);
        border-radius: var(--medium-radius);
    }
    
    .modern-card-header {
        padding: var(--md);
    }
    
    .modern-card-title {
        font-size: var(--base);
        flex-direction: column;
        gap: var(--xs);
        text-align: center;
    }
    
    .sidebar-card-title {
        font-size: var(--small);
    }
    
    .modern-card-body {
        padding: var(--md);
    }
    
    /* Form Mobile Responsive */
    .form-body {
        padding: var(--md);
    }
    
    .form-row {
        flex-direction: column;
        gap: 0;
    }
    
    .col-half {
        margin-bottom: var(--md);
    }
    
    .modern-form-input,
    .modern-form-select,
    .modern-form-textarea {
        padding: var(--sm) var(--md);
        font-size: var(--base);
    }
    
    .modern-form-label {
        font-size: var(--base);
        margin-bottom: var(--sm);
    }
    
    .modern-submit-btn {
        padding: var(--lg);
        font-size: var(--base);
    }
}

@media (max-width: 576px) {
    .university-detail-hero {
        min-height: 180px;
        padding: 24px 0;
        text-align: center;
    }
    
    .university-detail-hero .container {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .university-detail-hero .col-12 {
        padding-left: 0;
        padding-right: 0;
    }
    
    .hero-breadcrumb {
        margin-bottom: 20px;
        text-align: center;
        width: 100%;
        display: flex;
        justify-content: center;
    }
    
    .hero-breadcrumb-list {
        padding: 8px 16px;
        font-size: 12px;
        flex-wrap: wrap;
        gap: 4px;
        justify-content: center;
        margin: 0 auto;
    }
    
    .hero-content {
        align-items: center;
        text-align: center;
        width: 100%;
        margin: 0 auto;
    }
    
    .hero-main-info {
        gap: 12px;
        align-items: center;
        text-align: center;
        width: 100%;
        margin: 0 auto;
    }
    
    .hero-logo-image {
        width: 60px;
        height: 60px;
        padding: 8px;
        margin: 0 auto;
    }
    
    .hero-university-details {
        text-align: center;
        width: 100%;
        margin: 0 auto;
    }
    
    .hero-university-name {
        font-size: 1.4rem;
        line-height: 1.3;
        text-align: center;
        width: 100%;
        margin-left: auto;
        margin-right: auto;
    }
    
    .hero-university-meta {
        gap: 8px;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin: 0 auto;
    }
    
    .hero-meta-item {
        padding: 5px 12px;
        font-size: 13px;
        margin: 0 auto;
    }
    
    .hero-meta-text {
        font-size: 13px;
    }
    
    .hero-actions {
        align-items: center;
        width: 100%;
        margin: 0 auto;
        padding: 0 10px;
    }
    
    .hero-action-btn {
        padding: 12px 16px;
        font-size: 14px;
        max-width: 250px;
        margin: 0 auto;
    }
    
    .main-image-container {
        height: 200px;
    }
    
    .gallery-nav {
        padding: 0 10px;
    }
    
    .gallery-nav-btn {
        width: 35px;
        height: 35px;
    }
    
    .thumbnail-image {
        width: 50px;
        height: 37px;
    }
    
    .info-item {
        flex-direction: column;
        gap: 4px;
    }
    
    .info-icon {
        align-self: flex-start;
    }
    
    /* Modern Cards Small Mobile Responsive */
    .main-content-section {
        padding: var(--md) 0;
    }
    
    .modern-card {
        margin-bottom: var(--sm);
        border-radius: var(--small-radius);
    }
    
    .modern-card-header {
        padding: var(--sm) var(--md);
    }
    
    .modern-card-header::before {
        width: 40px;
        height: 2px;
    }
    
    .modern-card-title {
        font-size: var(--small);
        flex-direction: column;
        gap: 4px;
    }
    
    .card-icon {
        font-size: var(--small);
    }
    
    .sidebar-card-title {
        font-size: 0.8rem;
    }
    
    .modern-card-body {
        padding: var(--sm) var(--md);
    }
    
    /* Form Small Mobile Responsive */
    .form-body {
        padding: var(--sm) var(--md);
    }
    
    .form-group {
        margin-bottom: var(--md);
    }
    
    .col-half {
        margin-bottom: var(--sm);
    }
    
    .modern-form-input,
    .modern-form-select,
    .modern-form-textarea {
        padding: var(--md);
        font-size: var(--small);
    }
    
    .modern-form-label {
        font-size: var(--small);
    }
    
    .modern-submit-btn {
        padding: var(--md) var(--lg);
        font-size: var(--small);
    }
    
    .submit-icon {
        font-size: 0.75rem;
    }
}
</style>

<!-- External CSS -->
<link rel="stylesheet" href="assets/css/university-detail.css">

<!-- JavaScript -->
<script src="assets/js/university-detail.js"></script>
<script>
// University Detail Page JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Gallery functionality
    const galleryImages = [
        <?php if (!empty($university['featured_image'])): ?>
        '<?php echo addslashes($university['featured_image']); ?>',
        <?php endif; ?>
        <?php foreach ($university_images as $image): ?>
        '<?php echo addslashes($image['image_url']); ?>',
        <?php endforeach; ?>
    ];
    
    let currentImageIndex = 0;
    
    window.changeGalleryImage = function(direction) {
        currentImageIndex += direction;
        
        if (currentImageIndex < 0) {
            currentImageIndex = galleryImages.length - 1;
        } else if (currentImageIndex >= galleryImages.length) {
            currentImageIndex = 0;
        }
        
        setMainImage(galleryImages[currentImageIndex], currentImageIndex);
    };
    
    window.setMainImage = function(imageSrc, index) {
        const mainImage = document.getElementById('mainUniversityImage');
        const currentIndexElement = document.getElementById('currentImageIndex');
        
        if (mainImage) {
            mainImage.src = imageSrc;
        }
        
        if (currentIndexElement) {
            currentIndexElement.textContent = index + 1;
        }
        
        // Update thumbnail active state
        document.querySelectorAll('.thumbnail-image').forEach((thumb, i) => {
            thumb.classList.toggle('active', i === index);
        });
        
        currentImageIndex = index;
    };
    
    // Note: Form handling is now done by EmailJS in the inline script above
    
    // Smooth scrolling
    document.querySelectorAll('.smooth-scroll').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href').substring(1);
            const targetElement = document.getElementById(targetId);
            
            if (targetElement) {
                targetElement.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?> 