<?php 
include('includes/header.php'); 
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-carousel">
        <!-- Slide 1 -->
        <div class="hero-slide active">
            <img src="assets/images/media/home-page/hero-section/one.webp" alt="Medical Education Abroad" class="hero-slide-bg" width="1920" height="600">
        </div>
        
        <!-- Slide 2 -->
        <!-- <div class="hero-slide">
            <img src="assets/images/media/home-page/hero-section/two.webp" alt="MBBS Admission Guidance" class="hero-slide-bg" width="1920" height="600">
        </div> -->
        
        <!-- Slide 3 -->
        <!-- <div class="hero-slide">
            <img src="assets/images/media/home-page/hero-section/three.webp" alt="Medical Education Consultancy" class="hero-slide-bg" width="1920" height="600">
        </div> -->
        
        <!-- Slide 4 -->
        <!-- <div class="hero-slide">
            <img src="assets/images/media/home-page/hero-section/four.webp" alt="Medical Study Abroad" class="hero-slide-bg" width="1920" height="600">
        </div> -->
        
        <!-- Carousel Dots -->
        <div class="hero-carousel-dots">
            <span class="hero-dot active" data-slide="0"></span>
            <span class="hero-dot" data-slide="1"></span>
            <span class="hero-dot" data-slide="2"></span>
            <span class="hero-dot" data-slide="3"></span>
        </div>
    </div>
    
    <!-- Single Persistent Form - Overlays all slides -->
    <div class="hero-form-overlay">
        <div class="hero-container">
            <div class="hero-content-wrapper">
                <div class="hero-form-wrapper">
                    <h3 class="hero-form-title">Request Free Counselling</h3>
                    <form class="hero-form" action="process-form.php" method="post" id="hero-form">
                        <div class="form-group">
                            <input type="text" class="form-control" name="name" placeholder="Full Name*" required>
                        </div>
                        <div class="form-group">
                            <input type="email" class="form-control" name="email" placeholder="Email Address*" required>
                        </div>
                        <div class="form-group">
                            <input type="tel" class="form-control" name="phone" placeholder="Phone Number*" required>
                        </div>
                        <div class="form-group">
                            <select class="form-control" name="country" required>
                                <option value="">Select Preferred Country*</option>
                                <option value="India">India</option>
                                <option value="Iran">Iran</option>
                                <option value="Bangladesh">Bangladesh</option>
                                <option value="Russia">Russia</option>
                                <option value="Kazakhstan">Kazakhstan</option>
                                <option value="Kyrgyzstan">Kyrgyzstan</option>
                                <option value="Georgia">Georgia</option>
                                <option value="Uzbekistan">Uzbekistan</option>
                                <option value="Nepal">Nepal</option>
                                <option value="China">China</option>
                                <option value="Egypt">Egypt</option>
                                <option value="Belarus">Belarus</option>
                                <option value="Philippines">Philippines</option>
                                <option value="Armenia">Armenia</option>
                                <option value="Poland">Poland</option>
                                <option value="Romania">Romania</option>
                                <option value="Hungary">Hungary</option>
                                <option value="Malaysia">Malaysia</option>
                                <option value="Azerbaijan">Azerbaijan</option>
                                <option value="Lithuania">Lithuania</option>
                                <option value="Latvia">Latvia</option>
                                <option value="Slovakia">Slovakia</option>
                                <option value="Tajikistan">Tajikistan</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Application</button>
                    </form>

                    <!-- EmailJS Integration Script -->
                    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@3/dist/email.min.js"></script>
                    <script>
                        // Initialize EmailJS
                        (function() {
                            emailjs.init("Xl2-rb_v5qwA8iJpI"); // Your EmailJS public key
                        })();

                        // Handle form submission
                        document.getElementById('hero-form').addEventListener('submit', function(event) {
                            event.preventDefault();
                            
                            const form = event.target;
                            const submitBtn = form.querySelector('button[type="submit"]');
                            const originalText = submitBtn.innerHTML;
                            
                            // Show loading state
                            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                            submitBtn.disabled = true;
                            
                            // Prepare template parameters
                            const templateParams = {
                                from_name: form.name.value,
                                from_email: form.email.value,
                                phone: form.phone.value,
                                country: form.country.value,
                                message: `New MBBS inquiry from ${form.name.value}`,
                                to_email: 'ajaysingh261526@gmail.com',
                                date: new Date().toLocaleString()
                            };
                            
                            // Send via EmailJS
                            emailjs.send('service_igiat6d', 'template_5gxtwzk', templateParams)
                                .then(function() {
                                    alert('Thank you! Your inquiry has been sent successfully. We will contact you soon.');
                                    form.reset();
                                }, function(error) {
                                    console.log('EmailJS Error:', error);
                                    console.log('Falling back to PHP form submission...');
                                    alert('Using backup email system...');
                                    // Fallback to PHP form
                                    submitFormViaPhp(form);
                                })
                                .finally(function() {
                                    // Restore button
                                    submitBtn.innerHTML = originalText;
                                    submitBtn.disabled = false;
                                });
                        });
                        
                        // Fallback to PHP submission
                        function submitFormViaPhp(form) {
                            const formData = new FormData(form);
                            
                            fetch('process-form.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.status === 'success') {
                                    alert(data.message);
                                    form.reset();
                                } else {
                                    alert('Error: ' + data.message);
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                alert('There was an error submitting your form. Please try again.');
                            });
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Media Partners Section -->
<section class="media-partners-section section-padding">
    <div class="container">
        <div class="media-partners-content">
            <div class="media-partners-grid">
                <div class="media-partner-item">
                    <div class="media-partner-logo">
                        <img src="assets/images/media/home-page/Media-Partners-section/logos/ani.webp" alt="ANI News" loading="lazy">
                    </div>
                </div>
                <!-- <div class="media-partner-item">
                    <div class="media-partner-logo">
                        <img src="assets/images/media/home-page/Media-Partners-section/logos/dailyhunt.webp" alt="Dailyhunt" loading="lazy">
                    </div>
                </div> -->
                <!-- <div class="media-partner-item">
                    <div class="media-partner-logo">
                        <img src="assets/images/media/home-page/Media-Partners-section/logos/the-print.webp" alt="The Print" loading="lazy">
                    </div>
                </div> -->
                <div class="media-partner-item">
                    <div class="media-partner-logo">
                        <img src="assets/images/media/home-page/Media-Partners-section/logos/zee5.webp" alt="ZEE5" loading="lazy">
                    </div>
                </div>
                <div class="media-partner-item">
                    <div class="media-partner-logo">
                        <img src="assets/images/media/home-page/Media-Partners-section/logos/latestly.webp" alt="Latestly" loading="lazy">
                    </div>
                </div>
                <!-- <div class="media-partner-item">
                    <div class="media-partner-logo">
                        <img src="assets/images/media/home-page/Media-Partners-section/logos/lockmat.webp" alt="Lokmat" loading="lazy">
                    </div>
                </div> -->
                <div class="media-partner-item">
                    <div class="media-partner-logo">
                        <img src="assets/images/media/home-page/Media-Partners-section/logos/The-Tribune.webp" alt="The Tribune" loading="lazy">
                    </div>
                </div>
                <div class="media-partner-item">
                    <div class="media-partner-logo">
                        <img src="assets/images/media/home-page/Media-Partners-section/logos/hj.webp" alt="HJ Media" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section class="about-section">
    <div class="about-container">
        <h2 class="about-question">Welcome to <span class="highlight-text-update">Sunrise Global Education</span></h2>
        
        <div class="about-intro">
            <p>At Sunrise Global Education, we are committed to turning your dream of studying MBBS abroad into reality. As a leading overseas education consultancy, we specialize in MBBS abroad counseling, complete documentation support, and seamless admission guidance.</p>
        </div>
        
        <div class="about-highlights">
            <div class="highlight-card">
                <div class="highlight-icon">
                    <i class="fas fa-university"></i>
                </div>
                <h3>WHO-Approved Universities</h3>
                <p>We proudly assist aspiring medical students in securing admissions to globally recognized universities that are WHO-approved and listed by the NMC (formerly MCI).</p>
            </div>
            
            <div class="highlight-card">
                <div class="highlight-icon">
                    <i class="fas fa-globe-americas"></i>
                </div>
                <h3>Expert Guidance</h3>
                <p>Our expertise lies in providing accurate, transparent, and up-to-date information about universities and medical education systems in Russia, Kazakhstan, and Bangladesh.</p>
            </div>
            
            <div class="highlight-card">
                <div class="highlight-icon">
                    <i class="fas fa-hands-helping"></i>
                </div>
                <h3>End-to-End Support</h3>
                <p>With Sunrise Global Education, you get personalized guidance and complete support—from selecting the right university to settling down in your dream destination.</p>
            </div>
        </div>
        
        <div class="about-actions">
            <a href="about.php" class="btn btn-primary">Discover Our Story</a>
            <a href="contact.php" class="btn btn-outline-primary">Get Free Consultation</a>
        </div>
    </div>
</section>

<!-- Enhanced Mobile Responsive CSS for About Section -->
<style>
/* ===== ENHANCED MOBILE RESPONSIVE STYLES FOR ABOUT SECTION ===== */

/* About Section Base Styles */
.about-section {
    padding: 5rem 0;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    position: relative;
    overflow: hidden;
}

.about-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at 20% 30%, rgba(0, 53, 133, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 70%, rgba(254, 186, 2, 0.05) 0%, transparent 50%);
    pointer-events: none;
    z-index: 1;
}

.about-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    position: relative;
    z-index: 2;
    text-align: center;
}

.about-question {
    font-size: 2.5rem;
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 2rem;
    line-height: 1.2;
}

.about-intro {
    max-width: 800px;
    margin: 0 auto 3rem auto;
}

.about-intro p {
    font-size: 1.125rem;
    line-height: 1.6;
    color: #6c757d;
}

.about-highlights {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 2rem;
    margin-bottom: 3rem;
    max-width: 1000px;
    margin-left: auto;
    margin-right: auto;
}

.highlight-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.highlight-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 8px 30px rgba(0, 53, 133, 0.15);
}

.highlight-icon {
    width: 4rem;
    height: 4rem;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem auto;
}

.highlight-icon i {
    font-size: 1.5rem;
    color: white;
}

.highlight-card h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-color);
    margin-bottom: 1rem;
}

.highlight-card p {
    font-size: 0.95rem;
    line-height: 1.5;
    color: #6c757d;
    margin: 0;
}

.about-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.about-actions .btn {
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    min-width: 200px;
    justify-content: center;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
    border: 2px solid var(--primary-color);
}

.btn-primary:hover {
    background: var(--primary-dark);
    border-color: var(--primary-dark);
    transform: translateY(-2px);
    color: white;
}

.btn-outline-primary {
    background: transparent;
    color: var(--primary-color);
    border: 2px solid var(--primary-color);
}

.btn-outline-primary:hover {
    background: var(--primary-color);
    color: white;
    transform: translateY(-2px);
}

/* ===== TABLET RESPONSIVE (992px and below) ===== */
@media (max-width: 992px) {
    .about-section {
        padding: 4rem 0;
    }
    
    .about-container {
        padding: 0 30px;
    }
    
    .about-question {
        font-size: 2.25rem;
        margin-bottom: 1.5rem;
    }
    
    .about-intro {
        margin-bottom: 2.5rem;
    }
    
    .about-intro p {
        font-size: 1rem;
    }
    
    .about-highlights {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }
    
    .highlight-card {
        padding: 1.5rem;
    }
    
    .highlight-icon {
        width: 3.5rem;
        height: 3.5rem;
        margin-bottom: 1rem;
    }
    
    .highlight-icon i {
        font-size: 1.25rem;
    }
    
    .highlight-card h3 {
        font-size: 1.125rem;
    }
}

/* ===== MOBILE RESPONSIVE (768px and below) ===== */
@media (max-width: 768px) {
    .about-section {
        padding: 3rem 0;
    }
    
    .about-container {
        padding: 0 20px;
    }
    
    .about-question {
        font-size: 1.875rem;
        margin-bottom: 1.25rem;
        line-height: 1.3;
    }
    
    .about-intro {
        margin-bottom: 2rem;
    }
    
    .about-intro p {
        font-size: 0.95rem;
        line-height: 1.5;
    }
    
    .about-highlights {
        grid-template-columns: 1fr;
        gap: 1.25rem;
        margin-bottom: 2rem;
        max-width: 400px;
    }
    
    .highlight-card {
        padding: 1.25rem;
        border-radius: 10px;
    }
    
    .highlight-icon {
        width: 3rem;
        height: 3rem;
        margin-bottom: 0.75rem;
    }
    
    .highlight-icon i {
        font-size: 1rem;
    }
    
    .highlight-card h3 {
        font-size: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .highlight-card p {
        font-size: 0.875rem;
        line-height: 1.4;
    }
    
    .about-actions {
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
    }
    
    .about-actions .btn {
        min-width: 280px;
        padding: 14px 20px;
        font-size: 0.9rem;
    }
}

/* ===== SMALL MOBILE (576px and below) ===== */
@media (max-width: 576px) {
    .about-section {
        padding: 2.5rem 0;
    }
    
    .about-container {
        padding: 0 15px;
    }
    
    .about-question {
        font-size: 1.5rem;
        margin-bottom: 1rem;
        line-height: 1.25;
    }
    
    .about-intro {
        margin-bottom: 1.5rem;
    }
    
    .about-intro p {
        font-size: 0.875rem;
        line-height: 1.4;
    }
    
    .about-highlights {
        gap: 1rem;
        margin-bottom: 1.5rem;
        max-width: 350px;
    }
    
    .highlight-card {
        padding: 1rem;
        border-radius: 8px;
    }
    
    .highlight-icon {
        width: 2.5rem;
        height: 2.5rem;
        margin-bottom: 0.5rem;
    }
    
    .highlight-icon i {
        font-size: 0.875rem;
    }
    
    .highlight-card h3 {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }
    
    .highlight-card p {
        font-size: 0.8rem;
        line-height: 1.3;
    }
    
    .about-actions {
        gap: 0.5rem;
    }
    
    .about-actions .btn {
        min-width: 260px;
        padding: 12px 16px;
        font-size: 0.85rem;
    }
}

/* ===== EXTRA SMALL MOBILE (480px and below) ===== */
@media (max-width: 480px) {
    .about-section {
        padding: 2rem 0;
    }
    
    .about-container {
        padding: 0 10px;
    }
    
    .about-question {
        font-size: 1.25rem;
        margin-bottom: 0.75rem;
    }
    
    .about-intro {
        margin-bottom: 1.25rem;
    }
    
    .about-intro p {
        font-size: 0.8rem;
        line-height: 1.3;
    }
    
    .about-highlights {
        gap: 0.75rem;
        margin-bottom: 1.25rem;
        max-width: 300px;
    }
    
    .highlight-card {
        padding: 0.75rem;
    }
    
    .highlight-icon {
        width: 2rem;
        height: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .highlight-icon i {
        font-size: 0.75rem;
    }
    
    .highlight-card h3 {
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
    }
    
    .highlight-card p {
        font-size: 0.75rem;
        line-height: 1.25;
    }
    
    .about-actions .btn {
        min-width: 240px;
        padding: 10px 14px;
        font-size: 0.8rem;
    }
}

/* ===== VERY SMALL MOBILE (360px and below) ===== */
@media (max-width: 360px) {
    .about-section {
        padding: 1.5rem 0;
    }
    
    .about-container {
        padding: 0 8px;
    }
    
    .about-question {
        font-size: 1.125rem;
        margin-bottom: 0.5rem;
    }
    
    .about-intro p {
        font-size: 0.75rem;
    }
    
    .about-highlights {
        max-width: 280px;
    }
    
    .highlight-card {
        padding: 0.5rem;
    }
    
    .highlight-icon {
        width: 1.75rem;
        height: 1.75rem;
    }
    
    .highlight-icon i {
        font-size: 0.65rem;
    }
    
    .highlight-card h3 {
        font-size: 0.75rem;
    }
    
    .highlight-card p {
        font-size: 0.7rem;
    }
    
    .about-actions .btn {
        min-width: 220px;
        padding: 8px 12px;
        font-size: 0.75rem;
    }
}
</style>

<!-- Study Destinations Section -->
<section class="study-destinations-section">
    <div class="destinations-container">
        <h2 class="destinations-question">Where would you <span class="highlight-text">like to study?</span></h2>
        
        <div class="destinations-grid">
            <a href="destinations.php" class="destination-item">
                <div class="flag-circle">
                    <span class="flag-icon flag-icon-in"></span>
                </div>
                <span class="country-label">India</span>
            </a>
            <a href="destinations.php" class="destination-item">
                <div class="flag-circle">
                    <span class="flag-icon flag-icon-ir"></span>
                </div>
                <span class="country-label">Iran</span>
            </a>
            <a href="destinations.php" class="destination-item">
                <div class="flag-circle">
                    <span class="flag-icon flag-icon-bd"></span>
                </div>
                <span class="country-label">Bangladesh</span>
            </a>
            <a href="destinations.php" class="destination-item">
                <div class="flag-circle">
                    <span class="flag-icon flag-icon-ru"></span>
                </div>
                <span class="country-label">Russia</span>
            </a>
            <a href="destinations.php" class="destination-item">
                <div class="flag-circle">
                    <span class="flag-icon flag-icon-kz"></span>
                </div>
                <span class="country-label">Kazakhstan</span>
            </a>
            <a href="destinations.php" class="destination-item">
                <div class="flag-circle">
                    <span class="flag-icon flag-icon-kg"></span>
                </div>
                <span class="country-label">Kyrgyzstan</span>
            </a>
            <a href="destinations.php" class="destination-item">
                <div class="flag-circle">
                    <span class="flag-icon flag-icon-ge"></span>
                </div>
                <span class="country-label">Georgia</span>
            </a>
            <a href="destinations.php" class="destination-item">
                <div class="flag-circle">
                    <span class="flag-icon flag-icon-uz"></span>
                </div>
                <span class="country-label">Uzbekistan</span>
            </a>
            <a href="destinations.php" class="destination-item">
                <div class="flag-circle">
                    <span class="flag-icon flag-icon-np"></span>
                </div>
                <span class="country-label">Nepal</span>
            </a>
            <a href="destinations.php" class="destination-item">
                <div class="flag-circle">
                    <span class="flag-icon flag-icon-cn"></span>
                </div>
                <span class="country-label">China</span>
            </a>
            <a href="destinations.php" class="destination-item">
                <div class="flag-circle">
                    <span class="flag-icon flag-icon-eg"></span>
                </div>
                <span class="country-label">Egypt</span>
            </a>
            <a href="destinations.php" class="destination-item">
                <div class="flag-circle">
                    <span class="flag-icon flag-icon-by"></span>
                </div>
                <span class="country-label">Belarus</span>
            </a>
           
        </div>
        
        <div class="view-all-container">
            <a href="destinations.php" class="view-all-link">
                <span>View All</span>
            </a>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="services-section section-padding">
    <div class="services-container">
        <div class="services-header">
            <h2 class="services-title">Services At <span class="highlight-text">SGE</span></h2>
            <p class="services-description">Study MBBS: Affordable, WHO-approved universities, English-medium courses, and globally recognized degrees for your medical career.</p>
        </div>
        
        <div class="services-layout">
            <div class="services-left">
                <div class="service-group">
                    <div class="service-header">
                        <div class="service-icon">
                            <i class="fas fa-user-graduate"></i>
                        </div>
                        <h3>100% Admission Support</h3>
                    </div>
                    <p>Complete guidance from application to admission with guaranteed seat allocation.</p>
                </div>
                
                <div class="service-group">
                    <div class="service-header">
                        <div class="service-icon">
                            <i class="fas fa-plane"></i>
                        </div>
                        <h3>Visa & Travel Arrangements</h3>
                    </div>
                    <p>Expert visa processing with 100% success rate and complete travel coordination.</p>
                </div>
            </div>
            
            <div class="services-image">
                <img src="assets/images/media/home-page/service-section/uk.webp" alt="Medical Students" class="img-fluid">
            </div>
            
            <div class="services-right">
                <div class="service-group">
                    <div class="service-header">
                        <div class="service-icon">
                            <i class="fas fa-microscope"></i>
                        </div>
                        <h3>FMGE/NExT Coaching</h3>
                    </div>
                    <p>Comprehensive preparation for medical licensing exams with expert faculty.</p>
                </div>
                
                <div class="service-group">
                    <div class="service-header">
                        <div class="service-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <h3>Hostels, Canteens & More</h3>
                    </div>
                    <p>Complete accommodation support with comfortable hostels and quality food arrangements.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Universities Section -->
<section class="universities-section section-padding">
    <div class="container">
        <div class="section-heading">
            <h2>Leading <span class="highlight-text">Medical Universities</span> Worldwide</h2>
        </div>
        
        <div class="universities-logo-wall">
            <div class="logo-grid">
                <div class="logo-item">
                    <img src="assets/images/media/home-page/medical-universities-section/logos/Asfendiyarov-Kazakh-National.webp" alt="Asfendiyarov Kazakh National Medical University">
                </div>
                <div class="logo-item">
                    <img src="assets/images/media/home-page/medical-universities-section/logos/Astana-Medical-University.webp" alt="Astana Medical University">
                </div>
                <div class="logo-item">
                    <img src="assets/images/media/home-page/medical-universities-section/logos/South-Kazakhstan-Medical-Academy.webp" alt="South Kazakhstan Medical Academy">
                </div>
                <div class="logo-item">
                    <img src="assets/images/media/home-page/medical-universities-section/logos/Al-Farabi-Kazakh-National-University.webp" alt="Semey State Medical University">
                </div>
                <div class="logo-item">
                    <img src="assets/images/media/home-page/medical-universities-section/logos/Asian-Medical-Institute.webp" alt="Asian Medical Institute">
                </div>
                <div class="logo-item">
                    <img src="assets/images/media/home-page/medical-universities-section/logos/Crimea-Federal-University.webp" alt="Orel State University">
                </div>
                <div class="logo-item">
                    <img src="assets/images/media/home-page/medical-universities-section/logos/Jalal-Abad-State-Medical.webp" alt="Jalal-Abad State Medical University">
                </div>
                <div class="logo-item">
                    <img src="assets/images/media/home-page/medical-universities-section/logos/International-Higher-Schoo.webp" alt="International Higher School of Medicine">
                </div>
            </div>
        </div>
        
        <div class="universities-actions">
            <a href="resources.php" class="btn btn-primary">View All Universities</a>
            <a href="contact.php" class="btn btn-outline-primary">Get Admission Guide</a>
        </div>
    </div>
</section>

<!-- Reviews Section -->
<section class="reviews-section section-padding">
    <div class="container">
        <div class="reviews-header">
            <h2 class="reviews-title">Happy & <span class="highlight-text">Satisfied Faces</span></h2>
            <p class="reviews-description">Here's what some of our satisfied clients have to say about their journey with us</p>
        </div>
        
        <div class="reviews-carousel">
            <div class="reviews-navigation">
                <button class="review-nav-btn review-prev" onclick="changeReview(-1)">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button class="review-nav-btn review-next" onclick="changeReview(1)">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
            
            <div class="reviews-container">
                <div class="review-card active">
                    <div class="review-content">
                        <div class="review-image-wrapper">
                            <div class="review-image-bg"></div>
                            <img src="assets/images/media/home-page/happy-faces/image-1.webp" alt="Priya Sharma" class="review-image">
                        </div>
                        <div class="review-text">
                            <div class="quote-icon">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <p class="review-message">"Sunrise Global Education made my dream of studying MBBS in Russia a reality. Their guidance was exceptional throughout the entire process, from university selection to visa processing. The team was always available to answer my questions and provided honest, transparent advice."</p>
                            <div class="review-client">
                                <h4 class="client-name">Priya Sharma</h4>
                                <p class="client-designation">MBBS Student, Crimea Federal University</p>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="review-card">
                    <div class="review-content">
                        <div class="review-image-wrapper">
                            <div class="review-image-bg"></div>
                            <img src="assets/images/media/home-page/happy-faces/image-2.webp" alt="Arjun Patel" class="review-image">
                        </div>
                        <div class="review-text">
                            <div class="quote-icon">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <p class="review-message">"I was initially worried about studying abroad, but Sunrise Global Education's comprehensive support system put all my concerns to rest. From admission to accommodation, they handled everything professionally. Now I'm successfully pursuing my medical degree in Kazakhstan."</p>
                            <div class="review-client">
                                <h4 class="client-name">Diksha Yadav</h4>
                                <p class="client-designation">MBBS Student, Astana Medical University</p>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="review-card">
                    <div class="review-content">
                        <div class="review-image-wrapper">
                            <div class="review-image-bg"></div>
                            <img src="assets/images/media/home-page/happy-faces/image-3.webp" alt="Sneha Gupta" class="review-image">
                        </div>
                        <div class="review-text">
                            <div class="quote-icon">
                                <i class="fas fa-quote-left"></i>
                            </div>
                            <p class="review-message">"The team at Sunrise Global Education is incredibly knowledgeable and supportive. They helped me understand all the requirements, prepared me for the interviews, and ensured a smooth transition to my new university. Highly recommended for anyone considering MBBS abroad."</p>
                            <div class="review-client">
                                <h4 class="client-name">Sneha Gupta</h4>
                                <p class="client-designation">MBBS Student, South Kazakhstan Medical Academy</p>
                                <div class="review-rating">
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="why-choose-us-section section-padding">
    <div class="container">
        <div class="why-choose-us-header">
            <h2 class="why-choose-us-title">Why <span class="highlight-text">Choose Us?</span></h2>
        </div>
        
        <div class="why-choose-us-content">
            <!-- Left Column - Benefit Cards -->
            <div class="benefits-cards">
                <!-- Card 1 - Rewarding Career -->
                <div class="benefit-card card-blue">
                    <div class="benefit-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="benefit-content">
                        <h3 class="benefit-title">Rewarding Career</h3>
                        <p class="benefit-description">Students pursuing higher education from abroad can have a rewarding career with higher and stable earnings with endless growth opportunities.</p>
                    </div>
                </div>
                
                <!-- Card 2 - Easy Admission Process -->
                <div class="benefit-card card-purple">
                    <div class="benefit-icon">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <div class="benefit-content">
                        <h3 class="benefit-title">Easy Admission Process</h3>
                        <p class="benefit-description">The admission process for study abroad is hassle-free with simple step-by-step procedures for MBBS and other courses.</p>
                    </div>
                </div>
                
                <!-- Card 3 - Exposure to International Culture -->
                <div class="benefit-card card-red">
                    <div class="benefit-icon">
                        <i class="fas fa-globe-americas"></i>
                    </div>
                    <div class="benefit-content">
                        <h3 class="benefit-title">Exposure to International Culture</h3>
                        <p class="benefit-description">Indian students get the opportunity to interact with students from different nations and learn about diverse international cultures.</p>
                    </div>
                </div>
                
                <!-- Card 4 - World Class Education -->
                <div class="benefit-card card-green">
                    <div class="benefit-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="benefit-content">
                        <h3 class="benefit-title">World Class Education</h3>
                        <p class="benefit-description">Universities abroad provide world-class education to international students at top-ranked educational institutions.</p>
                    </div>
                </div>
            </div>
            
            <!-- Right Column - Image -->
            <div class="benefits-image">
                <div class="benefits-image-wrapper">
                    <img src="assets/images/media/home-page/why-choose-us/image-1.webp" alt="Doctor and Patient consultation" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Working Process Section -->
<section class="working-process-section section-padding">
    <div class="container">
        <div class="process-header">
            <h2 class="process-title">How <span class="highlight-text">Does It Work</span></h2>
            <p class="process-description">Our streamlined three-step process ensures a smooth and hassle-free journey from consultation to admission, making your dream of studying abroad a reality.</p>
        </div>
        
        <div class="process-steps">
            <!-- Step 1 -->
            <div class="process-step">
                <div class="step-image-wrapper">
                    <div class="step-image-circle">
                        <div class="step-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
                <div class="step-content">
                    <span class="step-number">STEP—01</span>
                    <h3 class="step-title">Free Counseling</h3>
                    <p class="step-description">Sunrise Global Education provide free Counseling and guide students for better education.</p>
                    <a href="#" class="step-cta">Learn More →</a>
                </div>
            </div>
            
            <!-- Arrow 1 -->
            <div class="process-arrow">
                <svg viewBox="0 0 100 50" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10,25 Q50,5 90,25" stroke="#E5E7EB" stroke-width="2" fill="none" stroke-dasharray="5,5"/>
                    <polygon points="85,20 95,25 85,30" fill="#E5E7EB"/>
                </svg>
            </div>
            
            <!-- Step 2 -->
            <div class="process-step">
                <div class="step-image-wrapper">
                    <div class="step-image-circle">
                        <div class="step-icon">
                            <i class="fas fa-university"></i>
                        </div>
                    </div>
                </div>
                <div class="step-content">
                    <span class="step-number">STEP—02</span>
                    <h3 class="step-title">Guaranteed Admission</h3>
                    <p class="step-description">Sunrise Global Education Guaranteed Admission in your desired university.</p>
                    <a href="#" class="step-cta">Learn More →</a>
                </div>
            </div>
            
            <!-- Arrow 2 -->
            <div class="process-arrow">
                <svg viewBox="0 0 100 50" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10,25 Q50,5 90,25" stroke="#E5E7EB" stroke-width="2" fill="none" stroke-dasharray="5,5"/>
                    <polygon points="85,20 95,25 85,30" fill="#E5E7EB"/>
                </svg>
            </div>
            
            <!-- Step 3 -->
            <div class="process-step">
                <div class="step-image-wrapper">
                    <div class="step-image-circle">
                        <div class="step-icon">
                            <i class="fas fa-passport"></i>
                        </div>
                    </div>
                </div>
                <div class="step-content">
                    <span class="step-number">STEP—03</span>
                    <h3 class="step-title">Visa Clearance Assistance</h3>
                    <p class="step-description">Sunrise Global Education provide full visa Clearance Assistance and extend of visa till completion of course.</p>
                    <a href="#" class="step-cta">Learn More →</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonial Section -->
<section class="testimonial-section">
    <div class="testimonial-container">
        <div class="testimonial-left">
            <h2 class="testimonial-heading">What Do Our Students Say About Us?</h2>
            <p class="testimonial-description">At SGE, we are dedicated to bringing you one step closer to your dream of becoming a successful doctor. Hear what our students have to say about their journey and experience with us!</p>
            
            <div class="testimonial-stats">
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number">500+</h3>
                        <p class="stat-label">Universities</p>
                    </div>
                </div>
                
                <div class="stat-divider"></div>
                
                <div class="stat-item">
                    <div class="stat-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <div class="stat-content">
                        <h3 class="stat-number">10000+</h3>
                        <p class="stat-label">Current Students</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="testimonial-right">
            <div class="testimonial-image">
                <img src="assets/images/media/home-page/student-testimonial-section/image-1.webp" alt="Medical Student with Stethoscope" class="img-fluid">
                <div class="video-overlay">
                    <button class="video-play-btn" onclick="playTestimonialVideo()">
                        <i class="fas fa-play"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>



<!-- News Section -->
<section class="blog-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Latest <span class="highlight-text">News & Insights</span></h2>
            <p class="section-description">Stay updated with the latest insights, success stories, and developments in medical education abroad through our informative blog posts.</p>
        </div>

        <div class="blog-grid">
            <!-- Blog Card 1 -->
            <article class="blog-card">
                <img src="assets/images/media/home-page/blogs-section/1.jpg" alt="Blog Post 1" class="blog-card__image" loading="lazy">
                <div class="blog-card__content">
                    <span class="blog-card__meta">by Admin on March 26, 2024</span>
                    <h3 class="blog-card__title">Top Medical Universities in Europe for International Students</h3>
                    <p class="blog-card__excerpt">Discover the leading medical universities in Europe that offer world-class education, state-of-the-art facilities, and diverse cultural experiences for international students...</p>
                    <a href="blog.php" class="blog-card__link">Read More</a>
                </div>
            </article>

            <!-- Blog Card 2 -->
            <article class="blog-card">
                <img src="assets/images/media/home-page/blogs-section/2.jpg" alt="Blog Post 2" class="blog-card__image" loading="lazy">
                <div class="blog-card__content">
                    <span class="blog-card__meta">by Admin on March 24, 2024</span>
                    <h3 class="blog-card__title">How to Prepare for Medical School Interviews</h3>
                    <p class="blog-card__excerpt">Expert tips and strategies to help you ace your medical school interviews, including common questions, preparation techniques, and what admissions committees look for...</p>
                    <a href="blog.php" class="blog-card__link">Read More</a>
                </div>
            </article>

            <!-- Blog Card 3 -->
            <article class="blog-card">
                <img src="assets/images/media/home-page/blogs-section/3.jpg" alt="Blog Post 3" class="blog-card__image" loading="lazy">
                <div class="blog-card__content">
                    <span class="blog-card__meta">by Admin on March 22, 2024</span>
                    <h3 class="blog-card__title">Understanding Medical Education Systems Worldwide</h3>
                    <p class="blog-card__excerpt">A comprehensive guide to different medical education systems around the world, including curriculum structures, admission requirements, and career opportunities...</p>
                    <a href="blog.php" class="blog-card__link">Read More</a>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="contact-section">
    <div class="container">
        <div class="contact-wrapper">
            <div class="contact-image">
                <img src="assets/images/media/home-page/contact-us-section/contact-image.webp" alt="Graduate Student" loading="lazy">
            </div>
            <div class="contact-content">
                <h2 class="contact-title">Leave us your contact details here!</h2>
                <p class="contact-description">SGE is a pioneer in facilitating the dreams of young aspirers... Medical Government Universities.</p>
                <a href="contact.php" class="contact-cta">
                    Apply Now
                    <span class="arrow-icon">&#8250;&#8250;</span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Blog Section -->
<section class="news-section section-padding">
    <div class="news-container">
        <div class="news-header">
            <h2 class="news-title">Latest <span class="highlight-text">Blogs & Articles</span></h2>
            <p class="news-description">Stay updated with the latest developments in medical education abroad, university news, and important announcements for aspiring medical students.</p>
        </div>
        
        <div class="news-grid">
            <article class="news-card">
                <a href="blog.php" class="news-image-link">
                    <div class="news-image">
                        <img src="https://images.unsplash.com/20/cambridge.JPG?q=80&w=1147&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="NEET UG 2025 Answer Key Released" loading="lazy">
                    </div>
                </a>
                <div class="news-content">
                    <h3 class="news-card-title">
                        <a href="blog.php">NEET UG 2025 Answer Key Released: Last Chance to Raise Objections Today</a>
                    </h3>
                    <a href="blog.php" class="news-read-more">READ MORE</a>
                    <p class="news-meta">ON JANUARY 22, 2023 BY SHAMMI AKTAR</p>
                </div>
            </article>
            
            <article class="news-card">
                <a href="blog.php" class="news-image-link">
                    <div class="news-image">
                        <img src="https://images.unsplash.com/photo-1559757148-5c350d0d3c56?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&h=600&q=80" alt="Mari State University Highlights India's Role" loading="lazy">
                    </div>
                </a>
                <div class="news-content">
                    <h3 class="news-card-title">
                        <a href="blog.php">Rector of Mari State University Highlights India's Role in World War II During Inspiring Lecture</a>
                    </h3>
                    <a href="blog.php" class="news-read-more">READ MORE</a>
                    <p class="news-meta">ON JANUARY 18, 2023 BY SHAMMI AKTAR</p>
                </div>
            </article>
            
            <article class="news-card">
                <a href="blog.php" class="news-image-link">
                    <div class="news-image">
                        <img src="https://images.unsplash.com/photo-1541339907198-e08756dedf3f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&h=600&q=80" alt="Russian Education Fair 2025 Kicks Off" loading="lazy">
                    </div>
                </a>
                <div class="news-content">
                    <h3 class="news-card-title">
                        <a href="blog.php">The 26th Russian Education Fair 2025 Kicks Off Its First Edition in Kolkata</a>
                    </h3>
                    <a href="blog.php" class="news-read-more">READ MORE</a>
                    <p class="news-meta">ON JANUARY 15, 2023 BY SHAMMI AKTAR</p>
                </div>
            </article>
        </div>
    </div>
</section>

<!-- Popup Form -->
<div class="modal fade" id="applyModal" tabindex="-1" role="dialog" aria-labelledby="applyModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applyModalLabel">Apply for MBBS in Abroad</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="application-form" action="process-form.php" method="post">
                    <div class="form-group">
                        <input type="text" class="form-control" id="name" name="name" placeholder="Name *" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" id="email" name="email" placeholder="E-mail *" required>
                    </div>
                    <div class="form-group">
                        <select class="form-control" id="country" name="country" required>
                            <option value="">Select Your Country</option>
                            <option value="INDIA">INDIA</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" id="state" name="state" required>
                            <option value="">Select Your State</option>
                            <!-- States will be populated via JavaScript -->
                        </select>
                    </div>
                    <div class="form-group">
                        <select class="form-control" id="city" name="city" required>
                            <option value="">Select Your City</option>
                            <!-- Cities will be populated via JavaScript -->
                        </select>
                    </div>
                    <div class="form-group phone-group">
                        <input type="text" class="form-control country-code" id="country-code" name="country-code" value="+91" readonly>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="Phone Number" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">SUBMIT NOW</button>
                </form>
            </div>
        </div>
    </div>
</div>



<!-- Back to Top Button -->
<a href="#" class="back-to-top">
    <i class="fas fa-arrow-up"></i>
</a>

<?php 
include('includes/footer.php'); 
?> 