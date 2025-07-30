<?php include 'includes/header.php'; ?>

<!-- Include Gallery Page CSS -->
<link rel="stylesheet" href="assets/css/variables.css">
<link rel="stylesheet" href="assets/css/gallery.css">

<div class="gallery-page">
    <!-- Gallery Hero Section -->
    <section class="gallery-hero-section">
        <div class="container">
            <div class="hero-content">
                <h1 class="hero-title">Our Gallery</h1>
                <p class="hero-subtitle">Explore our collection of memorable moments and achievements</p>
            </div>
        </div>
    </section>

    <!-- Horizontal Gallery Section -->
    <section class="horizontal-gallery-section">
        <div class="container">
            <div class="gallery-carousel-wrapper">
                <!-- Bootstrap Carousel -->
                <div id="galleryCarousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner">
                        <!-- Slide 1 -->
                        <div class="carousel-item active">
                            <div class="gallery-slide">
                                <div class="gallery-image">
                                    <img src="assets/images/media/gallary-images/i-1.webp" alt="Gallery Image 1" data-full="assets/images/media/gallary-images/i-1.webp">
                                </div>
                                <div class="gallery-text">
                                    <h3 class="gallery-title">Academic Excellence</h3>
                                    <p class="gallery-subtitle">Celebrating Achievements</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slide 2 -->
                        <div class="carousel-item">
                            <div class="gallery-slide">
                                <div class="gallery-image">
                                    <img src="assets/images/media/gallary-images/i-2.webp" alt="Gallery Image 2" data-full="assets/images/media/gallary-images/i-2.webp">
                                </div>
                                <div class="gallery-text">
                                    <h3 class="gallery-title">Student Success</h3>
                                    <p class="gallery-subtitle">Empowering Future Doctors</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slide 3 -->
                        <div class="carousel-item">
                            <div class="gallery-slide">
                                <div class="gallery-image">
                                    <img src="assets/images/media/gallary-images/i-3.webp" alt="Gallery Image 3" data-full="assets/images/media/gallary-images/i-3.webp">
                                </div>
                                <div class="gallery-text">
                                    <h3 class="gallery-title">Global Education</h3>
                                    <p class="gallery-subtitle">International Opportunities</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slide 4 -->
                        <div class="carousel-item">
                            <div class="gallery-slide">
                                <div class="gallery-image">
                                    <img src="assets/images/media/gallary-images/i-4.webp" alt="Gallery Image 4" data-full="assets/images/media/gallary-images/i-4.webp">
                                </div>
                                <div class="gallery-text">
                                    <h3 class="gallery-title">Medical Excellence</h3>
                                    <p class="gallery-subtitle">Education & Achievement</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slide 5 -->
                        <div class="carousel-item">
                            <div class="gallery-slide">
                                <div class="gallery-image">
                                    <img src="assets/images/media/gallary-images/i-5.webp" alt="Gallery Image 5" data-full="assets/images/media/gallary-images/i-5.webp">
                                </div>
                                <div class="gallery-text">
                                    <h3 class="gallery-title">Research & Innovation</h3>
                                    <p class="gallery-subtitle">Breaking New Ground</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slide 6 -->
                        <div class="carousel-item">
                            <div class="gallery-slide">
                                <div class="gallery-image">
                                    <img src="assets/images/media/gallary-images/i-6.webp" alt="Gallery Image 6" data-full="assets/images/media/gallary-images/i-6.webp">
                                </div>
                                <div class="gallery-text">
                                    <h3 class="gallery-title">Campus Life</h3>
                                    <p class="gallery-subtitle">Modern Learning Environment</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Slide 7 -->
                        <div class="carousel-item">
                            <div class="gallery-slide">
                                <div class="gallery-image">
                                    <img src="assets/images/media/gallary-images/i-7.webp" alt="Gallery Image 7" data-full="assets/images/media/gallary-images/i-7.webp">
                                </div>
                                <div class="gallery-text">
                                    <h3 class="gallery-title">Future Leaders</h3>
                                    <p class="gallery-subtitle">Shaping Tomorrow's Medicine</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Custom Navigation Buttons -->
                    <a class="carousel-control-prev custom-carousel-btn" href="#galleryCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next custom-carousel-btn" href="#galleryCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                    
                    <!-- Indicators -->
                    <ol class="carousel-indicators custom-indicators">
                        <li data-target="#galleryCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#galleryCarousel" data-slide-to="1"></li>
                        <li data-target="#galleryCarousel" data-slide-to="2"></li>
                        <li data-target="#galleryCarousel" data-slide-to="3"></li>
                        <li data-target="#galleryCarousel" data-slide-to="4"></li>
                        <li data-target="#galleryCarousel" data-slide-to="5"></li>
                        <li data-target="#galleryCarousel" data-slide-to="6"></li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <!-- Additional Gallery Grid Section -->
    <section class="gallery-grid-section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title">More From Our Gallery</h2>
                <p class="section-subtitle">Discover more moments from our journey</p>
            </div>
            
            <div class="gallery-grid">
                <div class="gallery-grid-item">
                    <img src="assets/images/media/gallary-images/i-8.webp" alt="Gallery Image 8" data-full="assets/images/media/gallary-images/i-8.webp">
                    <div class="grid-overlay">
                        <div class="grid-content">
                            <h4>Academic Excellence</h4>
                            <p>Celebrating achievements</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-grid-item">
                    <img src="assets/images/media/gallary-images/i-9.webp" alt="Gallery Image 9" data-full="assets/images/media/gallary-images/i-9.webp">
                    <div class="grid-overlay">
                        <div class="grid-content">
                            <h4>Student Life</h4>
                            <p>Campus experiences</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-grid-item">
                    <img src="assets/images/media/gallary-images/i-10.webp" alt="Gallery Image 10" data-full="assets/images/media/gallary-images/i-10.webp">
                    <div class="grid-overlay">
                        <div class="grid-content">
                            <h4>Events & Ceremonies</h4>
                            <p>Special occasions</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-grid-item">
                    <img src="assets/images/media/gallary-images/i-11.webp" alt="Gallery Image 11" data-full="assets/images/media/gallary-images/i-11.webp">
                    <div class="grid-overlay">
                        <div class="grid-content">
                            <h4>International Programs</h4>
                            <p>Global connections</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-grid-item">
                    <img src="assets/images/media/gallary-images/i-12.webp" alt="Gallery Image 12" data-full="assets/images/media/gallary-images/i-12.webp">
                    <div class="grid-overlay">
                        <div class="grid-content">
                            <h4>Research & Innovation</h4>
                            <p>Breaking boundaries</p>
                        </div>
                    </div>
                </div>
                
                <div class="gallery-grid-item">
                    <img src="assets/images/media/gallary-images/i-14.webp" alt="Gallery Image 14" data-full="assets/images/media/gallary-images/i-14.webp">
                    <div class="grid-overlay">
                        <div class="grid-content">
                            <h4>Medical Training</h4>
                            <p>Hands-on learning</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Lightbox Modal -->
<div id="lightbox" class="lightbox">
    <span class="lightbox-close">&times;</span>
    <img class="lightbox-content" id="lightbox-img">
    <div class="lightbox-caption" id="lightbox-caption"></div>
    
    <!-- Navigation arrows -->
    <a class="lightbox-prev" onclick="changeImage(-1)">&#10094;</a>
    <a class="lightbox-next" onclick="changeImage(1)">&#10095;</a>
</div>

<script src="assets/js/gallery.js"></script>

<?php include 'includes/footer.php'; ?> 