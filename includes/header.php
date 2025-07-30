<?php 
// Static popular countries for header dropdown (no database needed)
$headerPopularCountries = [
    ['name' => 'India', 'slug' => 'india', 'flag_code' => 'in'],
    ['name' => 'Iran', 'slug' => 'iran', 'flag_code' => 'ir'],
    ['name' => 'Bangladesh', 'slug' => 'bangladesh', 'flag_code' => 'bd'],
    ['name' => 'Russia', 'slug' => 'russia', 'flag_code' => 'ru'],
    ['name' => 'Kazakhstan', 'slug' => 'kazakhstan', 'flag_code' => 'kz'],
    ['name' => 'Kyrgyzstan', 'slug' => 'kyrgyzstan', 'flag_code' => 'kg'],
    ['name' => 'Georgia', 'slug' => 'georgia', 'flag_code' => 'ge'],
    ['name' => 'Uzbekistan', 'slug' => 'uzbekistan', 'flag_code' => 'uz'],
    ['name' => 'Nepal', 'slug' => 'nepal', 'flag_code' => 'np'],
    ['name' => 'China', 'slug' => 'china', 'flag_code' => 'cn'],
    ['name' => 'Egypt', 'slug' => 'egypt', 'flag_code' => 'eg'],
    ['name' => 'Belarus', 'slug' => 'belarus', 'flag_code' => 'by']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MedStudy Global - Study MBBS Abroad</title>
    
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    
    <!-- Flag Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.5.0/css/flag-icon.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- CSS files were split for better organization -->
    
    <!-- Bootstrap Dropdown Custom Styles -->
    <style>
        /* Fix Bootstrap dropdown positioning and z-index */
        .main-nav {
            position: relative;
            z-index: 1000;
        }
        
        .main-nav .dropdown {
            position: relative;
        }
        
        /* Ensure dropdown toggle aligns with other nav links */
        .main-nav ul li .dropdown-toggle {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-color);
            text-decoration: none;
            position: relative;
            display: inline-block;
            letter-spacing: 0.01em;
            text-transform: uppercase;
            transition: all 0.3s ease;
            white-space: nowrap;
            padding: 0;
            border: none;
            background: none;
        }
        
        .main-nav ul li .dropdown-toggle:hover,
        .main-nav ul li .dropdown-toggle:focus {
            color: var(--primary-color);
            text-decoration: none;
            outline: none;
            box-shadow: none;
        }
        
        /* Remove Bootstrap dropdown arrow styling and add custom underline */
        .main-nav ul li .dropdown-toggle::after {
            display: none;
        }
        
        .main-nav ul li .dropdown-toggle::before {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--primary-color);
            transform: scaleX(0);
            transition: transform 0.3s ease;
            transform-origin: center;
        }
        
        .main-nav ul li .dropdown-toggle:hover::before,
        .main-nav ul li .dropdown-toggle.show::before {
            transform: scaleX(1);
        }
        
        /* Custom Bootstrap Dropdown Styling - Clean Design */
        .destinations-dropdown {
            width: 420px;
            border: 1px solid #e1e5e9;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-radius: 8px;
            padding: 16px;
            margin-top: 8px;
            position: absolute !important;
            top: 100% !important;
            left: 50% !important;
            transform: translateX(-50%) !important;
            background: #ffffff;
            /* Ensure proper z-index */
            z-index: 1050 !important;
            /* Force hide by default */
            visibility: hidden !important;
            opacity: 0 !important;
            display: none !important;
            transition: all 0.2s ease !important;
        }
        
        /* Only show when Bootstrap adds the 'show' class */
        .destinations-dropdown.show {
            visibility: visible !important;
            opacity: 1 !important;
            display: block !important;
        }
        
        /* Ensure dropdown stays within viewport */
        @media (max-width: 1400px) {
            .destinations-dropdown {
                left: 0 !important;
                transform: none !important;
                width: 400px;
            }
        }
        
        @media (max-width: 600px) {
            .destinations-dropdown {
                left: 50% !important;
                transform: translateX(-50%) !important;
                width: 360px;
                padding: 12px;
            }
        }
        
        .destinations-dropdown .country-item {
            transition: all 0.15s ease;
            border-radius: 6px;
            margin: 0;
            text-decoration: none;
            padding: 8px 12px !important;
            display: flex !important;
            align-items: center !important;
            gap: 12px;
            min-height: 40px;
            width: 100%;
        }
        
        .destinations-dropdown .row {
            margin: 0 -6px;
            padding: 0;
        }
        
        .destinations-dropdown .row > .col-6 {
            padding: 0 6px;
            margin-bottom: 8px;
        }
        
        .destinations-dropdown .country-flag {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            border: 1px solid #e1e5e9;
        }
        
        .destinations-dropdown .country-flag img {
            width: 24px !important;
            height: 18px !important;
            object-fit: cover;
            border-radius: 50%;
        }
        
        .destinations-dropdown .country-name {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            white-space: nowrap;
            margin: 0;
            flex: 1;
            line-height: 1.2;
        }
        
        .destinations-dropdown .country-item:hover {
            background: #f8f9fa !important;
            color: #003585 !important;
            text-decoration: none;
        }
        
        .destinations-dropdown .country-item:hover .country-name {
            color: #003585 !important;
        }
        
        .destinations-dropdown .explore-more-btn {
            background: #dc3545;
            border: 1px solid #dc3545;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            padding: 12px 24px;
            transition: all 0.2s ease;
            color: white;
            margin-top: 16px;
            text-align: center;
        }
        
        .destinations-dropdown .explore-more-btn:hover {
            background: #c82333;
            border-color: #c82333;
            transform: translateY(-1px);
            box-shadow: 0 3px 8px rgba(220, 53, 69, 0.3);
            color: white;
            text-decoration: none;
        }
        
        /* Fix nav overlap issues */
        .main-nav ul {
            position: relative;
            z-index: 999;
            white-space: nowrap;
            overflow: visible;
        }
        
        .main-nav ul li {
            position: relative;
            flex-shrink: 0;
        }
        
        /* Ensure navigation doesn't wrap or collapse */
        .main-nav {
            width: 100%;
            overflow: visible;
        }
        
        .main-nav ul {
            display: flex !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        /* Prevent dropdown from affecting document flow */
        .main-nav .dropdown {
            position: relative;
            overflow: visible;
        }
        
        .destinations-dropdown {
            pointer-events: none;
        }
        
        .destinations-dropdown.show {
            pointer-events: auto;
        }
        
        /* Ensure proper stacking context */
        .main-header {
            position: relative;
            z-index: 100;
        }
        
        /* Mobile responsive */
        @media (max-width: 768px) {
            .destinations-dropdown {
                min-width: 300px;
                max-width: 350px;
                position: fixed !important;
                top: 60px !important;
                left: 10px !important;
                right: 10px !important;
                transform: none !important;
            }
            
            .destinations-dropdown .row .col-4 {
                flex: 0 0 33.333333%;
                max-width: 33.333333%;
            }
        }
    </style>
</head>
<body>

    <!-- Skip to Content Link -->
    <a href="#content" class="skip-link">Skip to content</a>
    
    <!-- Header Area -->
    <header id="header" class="header-area">
        <!-- Top Header (Contact Bar) -->
        <div class="top-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6">
                        <div class="top-contact">
                            <ul>
                                <li>
                                    <a href="tel:+91-9729317513">
                                        <i class="fas fa-phone-alt pulse"></i> Student Helpline: +91-9729317513
                                    </a>
                                </li>
                                <li>
                                    <a href="mailto:sunriseglobaleducationgurgaon@gmail.com">
                                        <i class="fas fa-envelope pulse"></i> E-mail: sunriseglobaleducationgurgaon@gmail.com
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="top-right">
                            <ul class="social-icons">
                                <li><a href="https://www.facebook.com/people/Sunrise-Global-Education-Pvt-Ltd/61577444481874/" target="_blank" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a></li>
                                <li><a href="https://x.com/sge_ggn" target="_blank" aria-label="Twitter"><i class="fab fa-twitter"></i></a></li>
                                <li><a href="https://www.linkedin.com/company/sunrise-global-education-ggn/" target="_blank" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a></li>
                                <li><a href="https://www.youtube.com/@sunriseglobaleducationggn" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a></li>
                                <li><a href="https://www.instagram.com/sunriseglobaleducation_ggn/" target="_blank" aria-label="Instagram"><i class="fab fa-instagram"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Main Header (Navigation Bar) -->
        <div class="main-header">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-2 col-md-3">
                        <div class="logo">
                            <a href="index.php" class="logo-link">
                                <img src="assets/images/media/logo/sunrise-logo.webp" alt="MedStudy Global">
                                <!-- <span class="tagline">Study Abroad Consultants</span> -->
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-7">
                        <nav class="main-nav">
                            <ul>
                                <li><a href="index.php" class="active">Home</a></li>
                                <li><a href="about.php">About Us</a></li>
                                <li><a href="services.php">Our Services</a></li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" id="mbbsDestinationsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        MBBS Destinations
                                    </a>
                                    <div class="dropdown-menu destinations-dropdown" aria-labelledby="mbbsDestinationsDropdown">
                                        <!-- Countries Grid -->
                                        <div class="row">
                                            <?php foreach (array_slice($headerPopularCountries, 0, 12) as $index => $country): ?>
                                            <div class="col-6">
                                                <a href="university-partners.php?country=<?php echo urlencode($country['slug']); ?>" 
                                                   class="dropdown-item country-item" 
                                                   title="Study MBBS in <?php echo htmlspecialchars($country['name']); ?>">
                                                    <div class="country-flag">
                                                        <img src="https://flagcdn.com/32x24/<?php echo strtolower($country['flag_code']); ?>.png" 
                                                             alt="<?php echo htmlspecialchars($country['name']); ?> Flag"
                                                             loading="lazy">
                                                    </div>
                                                    <span class="country-name">
                                                        <?php echo htmlspecialchars($country['name']); ?>
                                                    </span>
                                                </a>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <!-- Explore More Button -->
                                        <a href="destinations.php" class="btn w-100 explore-more-btn">
                                            Explore More
                                        </a>
                                    </div>
                                </li>
                                <li><a href="resources.php">University Partners</a></li>
                                <li><a href="blog.php">Blog</a></li>
                                <li><a href="gallery.php">Gallery</a></li>
                                <li><a href="contact.php">Contact Us</a></li>
                            </ul>
                        </nav>
                    </div>
                    <div class="col-lg-2 col-md-2">
                        <div class="header-actions">
                            <ul>
                                <li>
                                    <button class="hamburger-btn" id="mobile-menu-toggle" aria-label="Open mobile menu">
                                        <span></span>
                                        <span></span>
                                        <span></span>
                                    </button>
                                </li>
                                <li>
                                    <a href="destinations.php" class="apply-btn-small">
                                        APPLY NOW
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Mobile Menu -->
    <div class="mobile-menu">
        <div class="menu-overlay"></div>
        <div class="menu-content">
            <div class="menu-header">
                <a href="index.php">
                    <img src="assets/images/logo.png" alt="MedStudy Global" class="img-fluid">
                </a>
                <button class="close-menu" aria-label="Close mobile menu"><i class="fas fa-times"></i></button>
            </div>
            <div class="menu-body">
                <ul class="nav-menu">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="services.php">Our Services</a></li>
                    <li><a href="destinations.php">MBBS Destinations</a></li>
                    <li><a href="resources.php">University Partners</a></li>
                    <li><a href="blog.php">Blog</a></li>
                    <li><a href="gallery.php">Gallery</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
                <div class="menu-contact">
                    <h4>Contact Info</h4>
                    <ul>
                        <li><a href="tel:+91-9996817513"><i class="fas fa-phone-alt"></i> +91-9996817513</a></li>
                        <li><a href="mailto:sunriseglobaleducationgurgaon@gmail.com"><i class="fas fa-envelope"></i> sunriseglobaleducationgurgaon@gmail.com</a></li>
                    </ul>
                </div>
                <div class="menu-social">
                    <h4>Follow Us</h4>
                    <ul class="social-icons">
                        <li><a href="https://www.facebook.com/people/Sunrise-Global-Education-Pvt-Ltd/61577444481874/" target="_blank"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="https://x.com/sge_ggn" target="_blank"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="https://www.linkedin.com/company/sunrise-global-education-ggn/" target="_blank"><i class="fab fa-linkedin-in"></i></a></li>
                        <li><a href="https://www.youtube.com/@sunriseglobaleducationggn" target="_blank"><i class="fab fa-youtube"></i></a></li>
                        <li><a href="https://www.instagram.com/sunriseglobaleducation_ggn/" target="_blank"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Book Consultation Button -->
    <button class="consultation-btn" data-toggle="modal" data-target="#applyModal">
        Book Free Consultation Now
    </button>
    
    <!-- WhatsApp Button -->
    <a href="https://api.whatsapp.com/send?phone=123456789" class="whatsapp-btn" target="_blank">
        <img src="assets/images/whatsapp-icon.png" alt="WhatsApp">
    </a>
    
    <!-- Main Content Area -->
    <main id="content" class="main-content">
    
    <!-- Bootstrap Dropdown Fix & Analytics -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ensure dropdown is hidden on page load
            const dropdown = document.querySelector('.destinations-dropdown');
            const dropdownToggle = document.querySelector('#mbbsDestinationsDropdown');
            const dropdownParent = document.querySelector('.dropdown');
            
            if (dropdown && dropdownToggle && dropdownParent) {
                // Force hide dropdown on load with multiple methods
                dropdown.classList.remove('show');
                dropdown.style.display = 'none';
                dropdown.style.visibility = 'hidden';
                dropdown.style.opacity = '0';
                dropdownToggle.setAttribute('aria-expanded', 'false');
                dropdownParent.classList.remove('show');
                
                // Disable Bootstrap's automatic dropdown behavior temporarily
                dropdownToggle.setAttribute('data-toggle', '');
                
                // Add hover behavior instead of click
                let hoverTimeout;
                
                dropdownParent.addEventListener('mouseenter', function() {
                    clearTimeout(hoverTimeout);
                    dropdown.classList.add('show');
                    dropdown.style.display = 'block';
                    dropdown.style.visibility = 'visible';
                    dropdown.style.opacity = '1';
                    dropdownToggle.setAttribute('aria-expanded', 'true');
                });
                
                dropdownParent.addEventListener('mouseleave', function() {
                    hoverTimeout = setTimeout(function() {
                        dropdown.classList.remove('show');
                        dropdown.style.display = 'none';
                        dropdown.style.visibility = 'hidden';
                        dropdown.style.opacity = '0';
                        dropdownToggle.setAttribute('aria-expanded', 'false');
                    }, 100); // Small delay to prevent flickering
                });
                
                // Keep dropdown open when hovering over it
                dropdown.addEventListener('mouseenter', function() {
                    clearTimeout(hoverTimeout);
                });
                
                dropdown.addEventListener('mouseleave', function() {
                    hoverTimeout = setTimeout(function() {
                        dropdown.classList.remove('show');
                        dropdown.style.display = 'none';
                        dropdown.style.visibility = 'hidden';
                        dropdown.style.opacity = '0';
                        dropdownToggle.setAttribute('aria-expanded', 'false');
                    }, 100);
                });
                
                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!dropdownParent.contains(e.target)) {
                        dropdown.classList.remove('show');
                        dropdown.style.display = 'none';
                        dropdown.style.visibility = 'hidden';
                        dropdown.style.opacity = '0';
                        dropdownToggle.setAttribute('aria-expanded', 'false');
                    }
                });
            }
            
            // Track country clicks for analytics
            const countryItems = document.querySelectorAll('.country-item');
            countryItems.forEach(item => {
                item.addEventListener('click', function() {
                    const countryName = this.querySelector('.country-name').textContent;
                    
                    // Analytics tracking (if available)
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'country_click_header_dropdown', {
                            'event_category': 'navigation',
                            'event_label': countryName,
                            'value': 1
                        });
                    }
                });
            });
            
            // Track explore more button clicks
            const exploreMoreBtn = document.querySelector('.explore-more-btn');
            if (exploreMoreBtn) {
                exploreMoreBtn.addEventListener('click', function() {
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'explore_more_click', {
                            'event_category': 'navigation',
                            'event_label': 'header_dropdown',
                            'value': 1
                        });
                    }
                });
            }
        });
    </script>
    
    <!-- Bootstrap 4 JavaScript (Required for dropdowns) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script> 