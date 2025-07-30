/**
 * Gallery JavaScript
 * Handles lightbox functionality and gallery interactions
 */

// Get all gallery images
const galleryImages = document.querySelectorAll('.gallery-slide .gallery-image img, .gallery-grid-item img');
const lightbox = document.getElementById('lightbox');
const lightboxImg = document.getElementById('lightbox-img');
const lightboxCaption = document.getElementById('lightbox-caption');
const lightboxClose = document.querySelector('.lightbox-close');

let currentImageIndex = 0;
let allImages = [];

// Initialize gallery
document.addEventListener('DOMContentLoaded', function() {
    // Create array of all images for navigation
    allImages = Array.from(galleryImages).map(img => ({
        src: img.dataset.full || img.src,
        alt: img.alt,
        caption: img.alt
    }));
    
    // Add click event listeners to all gallery images
    galleryImages.forEach((img, index) => {
        img.addEventListener('click', function() {
            currentImageIndex = index;
            openLightbox();
        });
        
        // Add loading animation
        img.addEventListener('load', function() {
            this.style.opacity = '1';
        });
    });
    
    // Close lightbox when clicking on close button
    lightboxClose.addEventListener('click', closeLightbox);
    
    // Close lightbox when clicking outside the image
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });
    
    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (lightbox.style.display === 'block') {
            switch(e.key) {
                case 'Escape':
                    closeLightbox();
                    break;
                case 'ArrowLeft':
                    changeImage(-1);
                    break;
                case 'ArrowRight':
                    changeImage(1);
                    break;
            }
        }
    });
    
    // Prevent body scroll when lightbox is open
    lightbox.addEventListener('wheel', function(e) {
        e.preventDefault();
    });
    
    // Add lazy loading for images
    if ('IntersectionObserver' in window) {
        const lazyImages = document.querySelectorAll('img[data-src]');
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });
        
        lazyImages.forEach(img => imageObserver.observe(img));
    }
    
    // Initialize Bootstrap carousel
    initializeBootstrapCarousel();
});

// Open lightbox
function openLightbox() {
    const currentImage = allImages[currentImageIndex];
    
    lightboxImg.src = currentImage.src;
    lightboxImg.alt = currentImage.alt;
    lightboxCaption.textContent = currentImage.caption;
    
    lightbox.style.display = 'block';
    document.body.style.overflow = 'hidden';
    
    // Add fade-in animation
    lightboxImg.style.opacity = '0';
    lightboxImg.onload = function() {
        this.style.opacity = '1';
    };
}

// Close lightbox
function closeLightbox() {
    lightbox.style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Change image in lightbox
function changeImage(direction) {
    currentImageIndex += direction;
    
    // Loop around if at beginning or end
    if (currentImageIndex < 0) {
        currentImageIndex = allImages.length - 1;
    } else if (currentImageIndex >= allImages.length) {
        currentImageIndex = 0;
    }
    
    // Update lightbox with new image
    const currentImage = allImages[currentImageIndex];
    lightboxImg.style.opacity = '0';
    
    setTimeout(() => {
        lightboxImg.src = currentImage.src;
        lightboxImg.alt = currentImage.alt;
        lightboxCaption.textContent = currentImage.caption;
        
        lightboxImg.onload = function() {
            this.style.opacity = '1';
        };
    }, 150);
}

// Initialize Bootstrap carousel functionality
function initializeBootstrapCarousel() {
    const carousel = document.querySelector('#galleryCarousel');
    if (!carousel) return;
    
    // Bootstrap 4 carousel is automatically initialized by data-ride attribute
    // We just need to add any custom functionality here
    
    // Add custom event listeners if needed
    $(carousel).on('slide.bs.carousel', function (e) {
        // Optional: Add custom behavior when slide starts
        console.log('Carousel sliding to:', e.to);
    });
    
    $(carousel).on('slid.bs.carousel', function (e) {
        // Optional: Add custom behavior when slide completes
        console.log('Carousel slid to:', e.to);
    });
}

// Bootstrap carousel handles touch/swipe support natively

// Bootstrap carousel handles its own styling

// Add fade-in animation for gallery items
function addFadeInAnimation() {
    const galleryItems = document.querySelectorAll('.gallery-slide, .gallery-grid-item');
    
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);
    
    galleryItems.forEach(item => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(30px)';
        item.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(item);
    });
}

// Initialize fade-in animation
addFadeInAnimation();

// Performance optimization: Debounce resize events
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Handle window resize for responsive behavior
window.addEventListener('resize', debounce(() => {
    // Bootstrap carousel handles its own responsive behavior
    // No additional resize handling needed
}, 250));

// Add loading states
function addLoadingStates() {
    const images = document.querySelectorAll('.gallery-slide .gallery-image img, .gallery-grid-item img');
    
    images.forEach(img => {
        img.style.opacity = '0';
        img.style.transition = 'opacity 0.3s ease';
        
        if (img.complete) {
            img.style.opacity = '1';
        } else {
            img.addEventListener('load', function() {
                this.style.opacity = '1';
            });
            
            img.addEventListener('error', function() {
                this.style.opacity = '0.5';
                this.alt = 'Image failed to load';
            });
        }
    });
}

// Initialize loading states
addLoadingStates(); 