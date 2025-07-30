/**
 * University Detail Page JavaScript
 * Handles image gallery, form submissions, and interactive features
 * Bootstrap 4.6 + Vanilla JavaScript
 */

(function () {
  "use strict";

  // ===== CONFIGURATION =====
  const CONFIG = {
    galleryTransitionSpeed: 300,
    formSubmitTimeout: 10000,
    scrollOffset: 80,
    thumbnailsPerRow: 6,
    retryAttempts: 3,
    autoplaySpeed: 5000,
  };

  // ===== STATE MANAGEMENT =====
  const state = {
    currentImageIndex: 0,
    galleryImages: [],
    isGalleryAutoplay: false,
    autoplayInterval: null,
    formData: {},
    isLoading: false,
  };

  // ===== DOM ELEMENTS =====
  const elements = {
    mainImage: null,
    thumbnailImages: null,
    galleryNavBtns: null,
    imageCounter: null,
    consultationForm: null,
    countrySelect: null,
    stateSelect: null,
    citySelect: null,
    submitButton: null,
    currentIndexElement: null,
    totalImagesElement: null,
  };

  // ===== UTILITY FUNCTIONS =====

  /**
   * Show loading state on button
   */
  function showLoading(element, text = "Loading...") {
    if (!element) return;

    const originalText = element.innerHTML;
    element.setAttribute("data-original-text", originalText);
    element.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${text}`;
    element.disabled = true;
    element.classList.add("btn-loading");
  }

  /**
   * Hide loading state from button
   */
  function hideLoading(element) {
    if (!element) return;

    const originalText = element.getAttribute("data-original-text");
    if (originalText) {
      element.innerHTML = originalText;
    }
    element.disabled = false;
    element.classList.remove("btn-loading");
  }

  /**
   * Show alert message
   */
  function showAlert(message, type = "info", container = null) {
    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
            <i class="fas fa-${
              type === "success"
                ? "check-circle"
                : type === "danger"
                ? "exclamation-circle"
                : "info-circle"
            } me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;

    const targetContainer =
      container ||
      elements.consultationForm?.parentNode ||
      document.querySelector(".container");
    if (targetContainer) {
      targetContainer.insertBefore(alertDiv, targetContainer.firstChild);

      // Auto dismiss after 5 seconds
      setTimeout(() => {
        if (alertDiv.parentNode) {
          alertDiv.remove();
        }
      }, 5000);
    }
  }

  /**
   * Smooth scroll to element
   */
  function smoothScrollTo(element, offset = CONFIG.scrollOffset) {
    if (!element) return;

    const elementPosition =
      element.getBoundingClientRect().top + window.pageYOffset;
    const offsetPosition = elementPosition - offset;

    window.scrollTo({
      top: offsetPosition,
      behavior: "smooth",
    });
  }

  /**
   * Debounce function
   */
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

  // ===== IMAGE GALLERY FUNCTIONALITY =====

  /**
   * Initialize gallery images array
   */
  function initializeGallery() {
    // Get all gallery images (featured + additional)
    const mainImage = elements.mainImage;
    const thumbnails = elements.thumbnailImages;

    state.galleryImages = [];

    if (mainImage && mainImage.src) {
      state.galleryImages.push(mainImage.src);
    }

    if (thumbnails) {
      thumbnails.forEach((thumb, index) => {
        if (index > 0 && thumb.src) {
          // Skip first thumbnail as it's the featured image
          state.galleryImages.push(thumb.src);
        }
      });
    }

    // Update total images counter
    if (elements.totalImagesElement) {
      elements.totalImagesElement.textContent = state.galleryImages.length;
    }

    console.log(
      "Gallery initialized with",
      state.galleryImages.length,
      "images"
    );
  }

  /**
   * Set main image and update UI
   */
  function setMainImage(imageSrc, index) {
    if (!elements.mainImage || !imageSrc) return;

    // Fade out current image
    elements.mainImage.style.opacity = "0.5";

    // Preload new image
    const img = new Image();
    img.onload = () => {
      elements.mainImage.src = imageSrc;
      elements.mainImage.style.opacity = "1";
    };
    img.onerror = () => {
      console.error("Failed to load image:", imageSrc);
      elements.mainImage.style.opacity = "1";
    };
    img.src = imageSrc;

    // Update current index
    state.currentImageIndex = index;

    // Update counter
    if (elements.currentIndexElement) {
      elements.currentIndexElement.textContent = index + 1;
    }

    // Update thumbnail active states
    updateThumbnailStates(index);

    // Track analytics
    trackGalleryInteraction("image_view", index);
  }

  /**
   * Update thumbnail active states
   */
  function updateThumbnailStates(activeIndex) {
    if (!elements.thumbnailImages) return;

    elements.thumbnailImages.forEach((thumb, index) => {
      thumb.classList.toggle("active", index === activeIndex);
    });
  }

  /**
   * Navigate gallery images
   */
  function changeGalleryImage(direction) {
    if (state.galleryImages.length <= 1) return;

    let newIndex = state.currentImageIndex + direction;

    // Handle wrap around
    if (newIndex < 0) {
      newIndex = state.galleryImages.length - 1;
    } else if (newIndex >= state.galleryImages.length) {
      newIndex = 0;
    }

    setMainImage(state.galleryImages[newIndex], newIndex);

    // Track navigation
    trackGalleryInteraction("navigation", direction > 0 ? "next" : "previous");
  }

  /**
   * Start gallery autoplay
   */
  function startGalleryAutoplay() {
    if (state.galleryImages.length <= 1) return;

    state.isGalleryAutoplay = true;
    state.autoplayInterval = setInterval(() => {
      changeGalleryImage(1);
    }, CONFIG.autoplaySpeed);
  }

  /**
   * Stop gallery autoplay
   */
  function stopGalleryAutoplay() {
    if (state.autoplayInterval) {
      clearInterval(state.autoplayInterval);
      state.autoplayInterval = null;
    }
    state.isGalleryAutoplay = false;
  }

  /**
   * Handle keyboard navigation for gallery
   */
  function handleGalleryKeyboard(event) {
    if (!elements.mainImage) return;

    switch (event.key) {
      case "ArrowLeft":
        event.preventDefault();
        changeGalleryImage(-1);
        break;
      case "ArrowRight":
        event.preventDefault();
        changeGalleryImage(1);
        break;
      case "Escape":
        event.preventDefault();
        // Could be used for fullscreen mode in future
        break;
    }
  }

  // ===== FORM HANDLING =====

  /**
   * Load states for selected country
   */
  async function loadStates(countryCode) {
    if (!elements.stateSelect || !countryCode) return;

    try {
      showLoading(elements.stateSelect);

      const response = await fetch(
        `api/get-locations.php?type=states&country=${countryCode}`
      );
      const data = await response.json();

      // Clear existing options
      elements.stateSelect.innerHTML = '<option value="">Select State</option>';
      elements.citySelect.innerHTML = '<option value="">Select City</option>';

      if (data.success && data.states) {
        data.states.forEach((state) => {
          const option = document.createElement("option");
          option.value = state.code;
          option.textContent = state.name;
          elements.stateSelect.appendChild(option);
        });
      }
    } catch (error) {
      console.error("Error loading states:", error);
      showAlert("Failed to load states. Please try again.", "danger");
    } finally {
      hideLoading(elements.stateSelect);
    }
  }

  /**
   * Load cities for selected state
   */
  async function loadCities(countryCode, stateCode) {
    if (!elements.citySelect || !countryCode || !stateCode) return;

    try {
      showLoading(elements.citySelect);

      const response = await fetch(
        `api/get-locations.php?type=cities&country=${countryCode}&state=${stateCode}`
      );
      const data = await response.json();

      // Clear existing options
      elements.citySelect.innerHTML = '<option value="">Select City</option>';

      if (data.success && data.cities) {
        data.cities.forEach((city) => {
          const option = document.createElement("option");
          option.value = city.name;
          option.textContent = city.name;
          elements.citySelect.appendChild(option);
        });
      }
    } catch (error) {
      console.error("Error loading cities:", error);
      showAlert("Failed to load cities. Please try again.", "danger");
    } finally {
      hideLoading(elements.citySelect);
    }
  }

  /**
   * Validate form data
   */
  function validateForm(formData) {
    const errors = [];

    // Required fields validation
    const requiredFields = [
      "student_name",
      "email",
      "phone",
      "country",
      "state",
      "city",
    ];

    requiredFields.forEach((field) => {
      if (!formData.get(field)?.trim()) {
        errors.push(
          `${field
            .replace("_", " ")
            .replace(/\b\w/g, (l) => l.toUpperCase())} is required`
        );
      }
    });

    // Email validation
    const email = formData.get("email");
    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      errors.push("Please enter a valid email address");
    }

    // Phone validation
    const phone = formData.get("phone");
    if (phone && !/^[\+]?[1-9][\d]{0,15}$/.test(phone.replace(/\s/g, ""))) {
      errors.push("Please enter a valid phone number");
    }

    return errors;
  }

  /**
   * Submit consultation form
   */
  async function submitForm(formData) {
    try {
      showLoading(elements.submitButton, "Submitting...");

      const response = await fetch("process-university-inquiry.php", {
        method: "POST",
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        showAlert(
          result.message ||
            "Your inquiry has been submitted successfully! Our team will contact you within 24 hours.",
          "success"
        );
        elements.consultationForm.reset();

        // Track successful submission
        trackFormSubmission("success");

        // Scroll to show success message
        smoothScrollTo(elements.consultationForm);
      } else {
        throw new Error(result.message || "Failed to submit form");
      }
    } catch (error) {
      console.error("Form submission error:", error);
      showAlert(
        error.message ||
          "An error occurred while submitting the form. Please try again.",
        "danger"
      );
      trackFormSubmission("error", error.message);
    } finally {
      hideLoading(elements.submitButton);
    }
  }

  // ===== EVENT HANDLERS =====

  /**
   * Handle form submission
   */
  function handleFormSubmit(event) {
    event.preventDefault();

    if (state.isLoading) return;

    const formData = new FormData(elements.consultationForm);
    const validationErrors = validateForm(formData);

    if (validationErrors.length > 0) {
      showAlert(
        `Please correct the following errors:<br>• ${validationErrors.join(
          "<br>• "
        )}`,
        "danger"
      );
      return;
    }

    state.isLoading = true;
    submitForm(formData).finally(() => {
      state.isLoading = false;
    });
  }

  /**
   * Handle thumbnail click
   */
  function handleThumbnailClick(thumbnail, index) {
    const imageSrc = thumbnail.src;
    setMainImage(imageSrc, index);
    stopGalleryAutoplay(); // Stop autoplay when user manually navigates
  }

  // ===== ANALYTICS TRACKING =====

  /**
   * Track gallery interactions
   */
  function trackGalleryInteraction(action, value = null) {
    if (typeof gtag !== "undefined") {
      gtag("event", "gallery_interaction", {
        event_category: "university_detail",
        event_label: action,
        value: value,
      });
    }

    console.log("Gallery interaction:", { action, value });
  }

  /**
   * Track form submission
   */
  function trackFormSubmission(status, error = null) {
    if (typeof gtag !== "undefined") {
      gtag("event", "form_submission", {
        event_category: "university_inquiry",
        event_label: status,
        value: status === "success" ? 1 : 0,
      });
    }

    console.log("Form submission:", { status, error });
  }

  // ===== INITIALIZATION =====

  /**
   * Cache DOM elements
   */
  function cacheElements() {
    elements.mainImage = document.getElementById("mainUniversityImage");
    elements.thumbnailImages = document.querySelectorAll(".thumbnail-image");
    elements.galleryNavBtns = document.querySelectorAll(".gallery-nav-btn");
    elements.consultationForm = document.getElementById("universityDetailForm");
    elements.countrySelect = document.getElementById("country");
    elements.stateSelect = document.getElementById("state");
    elements.citySelect = document.getElementById("city");
    elements.submitButton = elements.consultationForm?.querySelector(
      'button[type="submit"]'
    );
    elements.currentIndexElement = document.getElementById("currentImageIndex");
    elements.totalImagesElement = document.getElementById("totalImages");
  }

  /**
   * Bind event listeners
   */
  function bindEvents() {
    // Gallery navigation buttons
    if (elements.galleryNavBtns) {
      elements.galleryNavBtns.forEach((btn) => {
        btn.addEventListener("click", (e) => {
          e.preventDefault();
          const direction = btn.classList.contains("gallery-prev") ? -1 : 1;
          changeGalleryImage(direction);
          stopGalleryAutoplay();
        });
      });
    }

    // Thumbnail clicks
    if (elements.thumbnailImages) {
      elements.thumbnailImages.forEach((thumb, index) => {
        thumb.addEventListener("click", () => {
          handleThumbnailClick(thumb, index);
        });

        thumb.addEventListener("keydown", (e) => {
          if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            handleThumbnailClick(thumb, index);
          }
        });
      });
    }

    // Keyboard navigation for gallery
    document.addEventListener("keydown", handleGalleryKeyboard);

    // Location dropdowns
    if (elements.countrySelect) {
      elements.countrySelect.addEventListener("change", function () {
        const countryCode = this.value;
        if (countryCode) {
          loadStates(countryCode);
        } else {
          elements.stateSelect.innerHTML =
            '<option value="">Select State</option>';
          elements.citySelect.innerHTML =
            '<option value="">Select City</option>';
        }
      });
    }

    if (elements.stateSelect) {
      elements.stateSelect.addEventListener("change", function () {
        const stateCode = this.value;
        const countryCode = elements.countrySelect?.value;
        if (stateCode && countryCode) {
          loadCities(countryCode, stateCode);
        } else {
          elements.citySelect.innerHTML =
            '<option value="">Select City</option>';
        }
      });
    }

    // Form submission
    if (elements.consultationForm) {
      elements.consultationForm.addEventListener("submit", handleFormSubmit);
    }

    // Smooth scrolling for anchor links
    document.querySelectorAll(".smooth-scroll").forEach((link) => {
      link.addEventListener("click", function (e) {
        e.preventDefault();
        const targetId = this.getAttribute("href")?.substring(1);
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
          smoothScrollTo(targetElement);
        }
      });
    });

    // Stop autoplay on mouse hover over gallery
    if (elements.mainImage) {
      elements.mainImage.addEventListener("mouseenter", stopGalleryAutoplay);
      elements.mainImage.addEventListener("mouseleave", () => {
        // Restart autoplay after a delay
        setTimeout(() => {
          if (!state.isGalleryAutoplay && state.galleryImages.length > 1) {
            startGalleryAutoplay();
          }
        }, 2000);
      });
    }

    // Handle window resize for responsive adjustments
    window.addEventListener(
      "resize",
      debounce(() => {
        // Adjust gallery layout if needed
        // This can be expanded for responsive gallery features
      }, 250)
    );

    // Print functionality
    const printBtn = document.querySelector('button[onclick="window.print()"]');
    if (printBtn) {
      printBtn.addEventListener("click", (e) => {
        e.preventDefault();
        window.print();

        // Track print action
        if (typeof gtag !== "undefined") {
          gtag("event", "print_page", {
            event_category: "university_detail",
            event_label: "print_button",
          });
        }
      });
    }
  }

  /**
   * Initialize lightbox functionality (future enhancement)
   */
  function initializeLightbox() {
    // Placeholder for lightbox functionality
    // Can be implemented in future updates
  }

  /**
   * Initialize page features
   */
  function init() {
    try {
      cacheElements();
      initializeGallery();
      bindEvents();

      // Auto-start gallery slideshow if multiple images
      if (state.galleryImages.length > 1) {
        // Start autoplay after a delay
        setTimeout(() => {
          startGalleryAutoplay();
        }, 3000);
      }

      console.log("University Detail page initialized successfully");
    } catch (error) {
      console.error("Error initializing University Detail page:", error);
    }
  }

  // ===== GLOBAL FUNCTIONS =====

  // Expose functions globally for inline onclick handlers
  window.changeGalleryImage = changeGalleryImage;
  window.setMainImage = setMainImage;

  // ===== AUTO-INITIALIZATION =====

  // Initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  // Cleanup on page unload
  window.addEventListener("beforeunload", () => {
    stopGalleryAutoplay();
  });

  // Expose public API for external access if needed
  window.UniversityDetail = {
    setMainImage,
    changeGalleryImage,
    showAlert,
    smoothScrollTo,
    startAutoplay: startGalleryAutoplay,
    stopAutoplay: stopGalleryAutoplay,
  };
})();
