/**
 * MedStudy Global - MBBS Abroad Consultancy
 * Main JavaScript File
 */

(function ($) {
    'use strict';
    
    // Window Load Function
    $(window).on('load', function () {
        // Preloader
        setTimeout(function () {
            $('.preloader').fadeOut();
        }, 200);
    });

    // Document Ready Function
    $(document).ready(function () {
        
        // Mobile Menu
        $('#mobile-menu-toggle').on('click', function () {
            $(this).toggleClass('active');
            $('.mobile-menu').toggleClass('active');
            $('body').toggleClass('menu-open');
        });
        
        $('.close-menu, .menu-overlay').on('click', function () {
            $('#mobile-menu-toggle').removeClass('active');
            $('.mobile-menu').removeClass('active');
            $('body').removeClass('menu-open');
        });
        
        // ESC key functionality for other components
$(document).on('keydown', function(e) {
    if (e.keyCode === 27) {
        // Close mobile menu if open
        if ($('.mobile-menu').hasClass('active')) {
            $('.mobile-menu').removeClass('active');
            $('body').removeClass('menu-open');
        }
    }
});
        
        // Back to Top Button
        $(window).on('scroll', function () {
            if ($(this).scrollTop() > 200) {
                $('.back-to-top').addClass('active');
            } else {
                $('.back-to-top').removeClass('active');
            }
        });
        
        $('.back-to-top').on('click', function (e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: 0
            }, 800);
        });
        
        // Sticky Header
        $(window).on('scroll', function () {
            if ($(this).scrollTop() > 100) {
                $('.main-header').addClass('sticky');
            } else {
                $('.main-header').removeClass('sticky');
            }
        });
        
        // Navigation Hover Effect
        $('.main-nav ul li a').on('mouseenter', function() {
            var width = $(this).width();
            $(this).css('width', width + 'px');
            $(this).find('after').css('width', '100%');
        }).on('mouseleave', function() {
            if (!$(this).hasClass('active')) {
                $(this).find('after').css('width', '0');
            }
        });
        
        // Dropdown Menu
        $('.main-nav li.has-dropdown').on('mouseenter', function () {
            $(this).find('.dropdown').fadeIn();
        }).on('mouseleave', function () {
            $(this).find('.dropdown').fadeOut();
        });
        
        // Counter Animation
        $('.counter').each(function () {
            $(this).prop('Counter', 0).animate({
                Counter: $(this).text()
            }, {
                duration: 3000,
                easing: 'swing',
                step: function (now) {
                    $(this).text(Math.ceil(now));
                }
            });
        });
        
        // Testimonial Slider
        if ($('.testimonial-slider').length) {
            $('.testimonial-slider').slick({
                dots: true,
                arrows: false,
                infinite: true,
                speed: 500,
                slidesToShow: 1,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 5000
            });
        }
        
        // Partner Slider
        if ($('.partner-slider').length) {
            $('.partner-slider').slick({
                dots: false,
                arrows: false,
                infinite: true,
                speed: 500,
                slidesToShow: 5,
                slidesToScroll: 1,
                autoplay: true,
                autoplaySpeed: 3000,
                responsive: [{
                    breakpoint: 992,
                    settings: {
                        slidesToShow: 4
                    }
                }, {
                    breakpoint: 768,
                    settings: {
                        slidesToShow: 3
                    }
                }, {
                    breakpoint: 576,
                    settings: {
                        slidesToShow: 2
                    }
                }]
            });
        }
        
        // Popup Video
        if ($.fn.magnificPopup) {
            $('.video-link').magnificPopup({
                type: 'iframe',
                mainClass: 'mfp-fade',
                removalDelay: 160,
                preloader: false,
                fixedContentPos: false
            });
        }
        
        // Form Validation
        $('#application-form').on('submit', function (e) {
            var form = $(this);
            var formData = form.serialize();
            
            if (form[0].checkValidity() === false) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: formData,
                    success: function (response) {
                        $('#applyModal').modal('hide');
                        alert('Thank you for your application! Our counselors will contact you soon.');
                        form[0].reset();
                    },
                    error: function (error) {
                        alert('There was an error submitting your application. Please try again later.');
                    }
                });
            }
            
            form.addClass('was-validated');
        });
        
        // Initialize AOS (Animate on Scroll) if available
        if (typeof AOS !== 'undefined') {
            AOS.init({
                duration: 1000,
                once: true,
                offset: 100
            });
        }
        
        // Tabs functionality with smooth transition
        $('.feature-nav .nav-link').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });
        
        // Card hover effects
        $('.university-card, .blog-card, .news-card').hover(
            function() {
                $(this).css('transform', 'translateY(-0.3125rem)');
                $(this).css('box-shadow', '0 0.5rem 1.25rem rgba(0, 0, 0, 0.15)');
            },
            function() {
                $(this).css('transform', 'translateY(0)');
                $(this).css('box-shadow', '0 0.25rem 0.625rem rgba(0, 0, 0, 0.1)');
            }
        );
        
        // WhatsApp button hover effect
        $('.whatsapp-btn, .whatsapp-float').hover(
            function() {
                $(this).css('transform', 'scale(1.1)');
            },
            function() {
                $(this).css('transform', 'scale(1)');
            }
        );
        
        // Contact form validation
        $('#contact-form').on('submit', function(e) {
            var form = $(this);
            
            if (form[0].checkValidity() === false) {
                e.preventDefault();
                e.stopPropagation();
            } else {
                e.preventDefault();
                
                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(response) {
                        $('#contact-success').fadeIn().delay(3000).fadeOut();
                        form[0].reset();
                    },
                    error: function(error) {
                        $('#contact-error').fadeIn().delay(3000).fadeOut();
                    }
                });
            }
            
            form.addClass('was-validated');
        });
        
        // State and City Dropdown Population
        var states = {
            'INDIA': [
                'Andhra Pradesh',
                'Arunachal Pradesh',
                'Assam',
                'Bihar',
                'Chhattisgarh',
                'Goa',
                'Gujarat',
                'Haryana',
                'Himachal Pradesh',
                'Jharkhand',
                'Karnataka',
                'Kerala',
                'Madhya Pradesh',
                'Maharashtra',
                'Manipur',
                'Meghalaya',
                'Mizoram',
                'Nagaland',
                'Odisha',
                'Punjab',
                'Rajasthan',
                'Sikkim',
                'Tamil Nadu',
                'Telangana',
                'Tripura',
                'Uttar Pradesh',
                'Uttarakhand',
                'West Bengal',
                'Delhi'
            ]
        };
        
        var cities = {
            'Delhi': ['New Delhi', 'North Delhi', 'South Delhi', 'East Delhi', 'West Delhi'],
            'Maharashtra': ['Mumbai', 'Pune', 'Nagpur', 'Thane', 'Nashik'],
            'Karnataka': ['Bengaluru', 'Mysuru', 'Hubli', 'Mangalore', 'Belgaum'],
            'Tamil Nadu': ['Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem']
            // Add more cities as needed
        };
        
        $('#country').on('change', function () {
            var country = $(this).val();
            var stateOptions = '<option value="">Select Your State</option>';
            
            if (country && states[country]) {
                for (var i = 0; i < states[country].length; i++) {
                    stateOptions += '<option value="' + states[country][i] + '">' + states[country][i] + '</option>';
                }
            }
            
            $('#state').html(stateOptions);
            $('#city').html('<option value="">Select Your City</option>');
        });
        
        $('#state').on('change', function () {
            var state = $(this).val();
            var cityOptions = '<option value="">Select Your City</option>';
            
            if (state && cities[state]) {
                for (var i = 0; i < cities[state].length; i++) {
                    cityOptions += '<option value="' + cities[state][i] + '">' + cities[state][i] + '</option>';
                }
            }
            
            $('#city').html(cityOptions);
        });
        


        // Hero Carousel
        const heroCarousel = document.querySelector('.hero-carousel');
        
        if (heroCarousel) {
            const slides = document.querySelectorAll('.hero-slide');
            const dots = document.querySelectorAll('.hero-dot');
            let currentSlide = 0;
            let slideInterval;
            
            // Initialize carousel
            function initCarousel() {
                // Set first slide as active
                slides[0].classList.add('active');
                
                // Start autoplay
                startAutoplay();
                
                // Add click event to dots
                dots.forEach((dot, index) => {
                    dot.addEventListener('click', () => {
                        goToSlide(index);
                        resetAutoplay();
                    });
                });
            }
            
            // Go to specific slide
            function goToSlide(slideIndex) {
                // Remove active class from all slides and dots
                slides.forEach(slide => slide.classList.remove('active'));
                dots.forEach(dot => dot.classList.remove('active'));
                
                // Add active class to current slide and dot
                slides[slideIndex].classList.add('active');
                dots[slideIndex].classList.add('active');
                
                // Update current slide index
                currentSlide = slideIndex;
            }
            
            // Go to next slide
            function nextSlide() {
                let nextIndex = currentSlide + 1;
                if (nextIndex >= slides.length) {
                    nextIndex = 0;
                }
                goToSlide(nextIndex);
            }
            
            // Start autoplay
            function startAutoplay() {
                slideInterval = setInterval(nextSlide, 5000);
            }
            
            // Reset autoplay
            function resetAutoplay() {
                clearInterval(slideInterval);
                startAutoplay();
            }
            
            // Initialize carousel
            initCarousel();
        }
            });
        
    })(jQuery);

// Testimonial Video Play Function
function playTestimonialVideo() {
    // You can replace this with actual video modal or embedded video functionality
    alert('Opening student testimonial video...\n\nReplace this with your actual video implementation.');
    
    // Example of how you might open a video modal:
    // $('#videoModal').modal('show');
    // or redirect to YouTube/Vimeo:
    // window.open('https://www.youtube.com/watch?v=YOUR_VIDEO_ID', '_blank');
}

// Reviews Carousel Functionality
let currentReview = 0;
const reviewCards = document.querySelectorAll('.review-card');
const totalReviews = reviewCards.length;

function showReview(index) {
    // Remove active class from all reviews
    reviewCards.forEach(card => card.classList.remove('active'));
    
    // Show the selected review
    if (reviewCards[index]) {
        reviewCards[index].classList.add('active');
    }
    
    currentReview = index;
}

function changeReview(direction) {
    let nextReview = currentReview + direction;
    
    // Handle wrap around
    if (nextReview >= totalReviews) {
        nextReview = 0;
    } else if (nextReview < 0) {
        nextReview = totalReviews - 1;
    }
    
    showReview(nextReview);
}

// Initialize reviews carousel when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (reviewCards.length > 0) {
        showReview(0); // Show first review initially
        
        // Auto-advance reviews every 8 seconds
        setInterval(() => {
            changeReview(1);
        }, 8000);
    }
}); 

// FAQ Accordion Active State Handler
$(document).ready(function() {
    // Handle FAQ accordion active state
    $('.contact-page .accordion .card-header .btn').on('click', function() {
        // Remove active class from all headers
        $('.contact-page .accordion .card-header').removeClass('active');
        
        // Add active class to current header if it's being expanded
        if ($(this).hasClass('collapsed')) {
            // This will be expanded, so add active class
            $(this).closest('.card-header').addClass('active');
        }
    });
    
    // Set initial active state for expanded accordion items
    $('.contact-page .accordion .card-header .btn:not(.collapsed)').each(function() {
        $(this).closest('.card-header').addClass('active');
    });
    
    // Blog Page Functionality
    if ($('.blog-page').length > 0) {
        // Category Filter
        $('#categoryFilter').on('change', function() {
            var selectedCategory = $(this).val();
            filterBlogPosts(selectedCategory, $('#searchInput').val());
        });
        
        // Search Filter
        $('#searchInput').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            filterBlogPosts($('#categoryFilter').val(), searchTerm);
        });
        
        // Filter Function
        function filterBlogPosts(category, searchTerm) {
            $('.blog-card').each(function() {
                var $card = $(this);
                var cardCategory = $card.find('.blog-category').text().toLowerCase().replace(/\s+/g, '-');
                var cardTitle = $card.find('.blog-card-title a').text().toLowerCase();
                var cardExcerpt = $card.find('.blog-excerpt').text().toLowerCase();
                
                var categoryMatch = !category || cardCategory === category;
                var searchMatch = !searchTerm || cardTitle.includes(searchTerm) || cardExcerpt.includes(searchTerm);
                
                if (categoryMatch && searchMatch) {
                    $card.show();
                } else {
                    $card.hide();
                }
            });
        }
        

        
        // Smooth scroll for pagination
        $('.pagination-btn, .pagination-number').on('click', function(e) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.blog-grid-section').offset().top - 100
            }, 800);
        });
    }
}); 