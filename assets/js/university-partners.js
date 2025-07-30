/**
 * University Partners Page JavaScript
 * Handles search, filtering, form submissions, and interactive features
 * Bootstrap 4.6 + Vanilla JavaScript
 */

(function () {
  "use strict";

  // ===== CONFIGURATION =====
  const CONFIG = {
    animationDelay: 100,
    searchDebounce: 300,
    formSubmitTimeout: 10000,
    scrollOffset: 80,
    retryAttempts: 3,
  };

  // ===== STATE MANAGEMENT =====
  const state = {
    currentFilter: "all",
    currentSort: "name",
    searchQuery: "",
    universities: [],
    filteredUniversities: [],
    isLoading: false,
    formData: {},
  };

  // ===== DOM ELEMENTS =====
  const elements = {
    searchInput: null,
    sortFilter: null,
    universityCards: null,
    universityCount: null,
    inquiryButtons: null,
    consultationForm: null,
    countrySelect: null,
    stateSelect: null,
    citySelect: null,
    submitButton: null,
  };

  // ===== UTILITY FUNCTIONS =====

  /**
   * Debounce function to limit API calls
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

  /**
   * Show loading state
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
   * Hide loading state
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

    const targetContainer = container || document.querySelector(".container");
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
   * Get university card data
   */
  function getUniversityData(card) {
    const nameElement = card.querySelector(".university-name");
    const descriptionElement = card.querySelector(".card-text");
    const locationElement = card.querySelector(
      '[class*="map-marker"]'
    )?.nextElementSibling;
    const languageElement = card.querySelector(
      '[class*="language"]'
    )?.nextElementSibling;
    const durationElement =
      card.querySelector('[class*="clock"]')?.nextElementSibling;

    return {
      name: nameElement?.textContent.toLowerCase() || "",
      description: descriptionElement?.textContent.toLowerCase() || "",
      location: locationElement?.textContent.toLowerCase() || "",
      language: languageElement?.textContent.toLowerCase() || "",
      duration: durationElement?.textContent.toLowerCase() || "",
    };
  }

  // ===== SEARCH FUNCTIONALITY =====

  /**
   * Filter universities based on search query
   */
  function filterUniversities(query = state.searchQuery) {
    if (!elements.universityCards) return;

    let visibleCount = 0;

    elements.universityCards.forEach((card, index) => {
      const universityData = getUniversityData(card);
      const searchTerms = query.toLowerCase().trim();

      let matches = true;

      if (searchTerms) {
        matches = Object.values(universityData).some((value) =>
          value.includes(searchTerms)
        );
      }

      if (matches) {
        card.style.display = "block";
        card.style.animationDelay = `${visibleCount * CONFIG.animationDelay}ms`;
        visibleCount++;
      } else {
        card.style.display = "none";
      }
    });

    // Update counter
    if (elements.universityCount) {
      elements.universityCount.textContent = `${visibleCount} Universities`;
    }

    // Show no results message if needed
    toggleNoResultsMessage(visibleCount === 0);

    return visibleCount;
  }

  /**
   * Toggle no results message
   */
  function toggleNoResultsMessage(show) {
    let noResultsDiv = document.getElementById("no-results-message");

    if (show && !noResultsDiv) {
      noResultsDiv = document.createElement("div");
      noResultsDiv.id = "no-results-message";
      noResultsDiv.className = "col-12 text-center py-5";
      noResultsDiv.innerHTML = `
                <div class="mb-4">
                    <i class="fas fa-search fa-3x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">No universities found</h4>
                <p class="text-muted">Try adjusting your search terms or filters.</p>
            `;

      const gridContainer = document.getElementById("universitiesGrid");
      if (gridContainer) {
        gridContainer.appendChild(noResultsDiv);
      }
    } else if (!show && noResultsDiv) {
      noResultsDiv.remove();
    }
  }

  /**
   * Sort universities
   */
  function sortUniversities(sortBy = state.currentSort) {
    if (!elements.universityCards) return;

    const cardsArray = Array.from(elements.universityCards);
    const gridContainer = document.getElementById("universitiesGrid");

    cardsArray.sort((a, b) => {
      const aData = getUniversityData(a);
      const bData = getUniversityData(b);

      switch (sortBy) {
        case "name":
          return aData.name.localeCompare(bData.name);
        case "location":
          return aData.location.localeCompare(bData.location);
        case "fees":
          // Extract fees from card if available
          const aFees =
            a
              .querySelector(".text-success")
              ?.textContent.replace(/[^\d]/g, "") || "0";
          const bFees =
            b
              .querySelector(".text-success")
              ?.textContent.replace(/[^\d]/g, "") || "0";
          return parseInt(aFees) - parseInt(bFees);
        default:
          return 0;
      }
    });

    // Re-append sorted cards
    cardsArray.forEach((card) => {
      if (gridContainer) {
        gridContainer.appendChild(card);
      }
    });

    state.currentSort = sortBy;
  }

  // ===== FORM HANDLING =====

  /**
   * Load states for selected country
   */
  async function loadStates(countryCode) {
    if (!elements.stateSelect || !countryCode) return;

    try {
      showLoading(elements.stateSelect, "Loading states...");

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
      showLoading(elements.citySelect, "Loading cities...");

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
          result.message || "Your inquiry has been submitted successfully!",
          "success"
        );
        elements.consultationForm.reset();

        // Scroll to top of form to show success message
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
    } finally {
      hideLoading(elements.submitButton);
    }
  }

  // ===== EVENT HANDLERS =====

  /**
   * Handle search input
   */
  const handleSearch = debounce((query) => {
    state.searchQuery = query;
    filterUniversities(query);
  }, CONFIG.searchDebounce);

  /**
   * Handle sort change
   */
  function handleSortChange(sortBy) {
    state.currentSort = sortBy;
    sortUniversities(sortBy);
  }

  /**
   * Handle university inquiry button click
   */
  function handleUniversityInquiry(universityName) {
    const selectedUniversityField =
      document.getElementById("selectedUniversity");
    const preferredUniversitySelect = document.getElementById(
      "preferred_university"
    );

    if (selectedUniversityField) {
      selectedUniversityField.value = universityName;
    }

    if (preferredUniversitySelect) {
      preferredUniversitySelect.value = universityName;
    }

    // Scroll to consultation form
    const consultationForm = document.getElementById("consultation-form");
    if (consultationForm) {
      smoothScrollTo(consultationForm);
    }
  }

  /**
   * Handle form submission
   */
  function handleFormSubmit(event) {
    event.preventDefault();

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

    submitForm(formData);
  }

  // ===== INITIALIZATION =====

  /**
   * Cache DOM elements
   */
  function cacheElements() {
    elements.searchInput = document.getElementById("universitySearch");
    elements.sortFilter = document.getElementById("sortFilter");
    elements.universityCards = document.querySelectorAll(
      ".university-card-wrapper"
    );
    elements.universityCount = document.getElementById("universityCount");
    elements.inquiryButtons = document.querySelectorAll(
      ".university-inquiry-btn"
    );
    elements.consultationForm = document.getElementById(
      "universityInquiryForm"
    );
    elements.countrySelect = document.getElementById("country");
    elements.stateSelect = document.getElementById("state");
    elements.citySelect = document.getElementById("city");
    elements.submitButton = elements.consultationForm?.querySelector(
      'button[type="submit"]'
    );
  }

  /**
   * Bind event listeners
   */
  function bindEvents() {
    // Search functionality
    if (elements.searchInput) {
      elements.searchInput.addEventListener("input", (e) => {
        handleSearch(e.target.value);
      });
    }

    // Sort functionality
    if (elements.sortFilter) {
      elements.sortFilter.addEventListener("change", (e) => {
        handleSortChange(e.target.value);
      });
    }

    // University inquiry buttons
    elements.inquiryButtons.forEach((button) => {
      button.addEventListener("click", (e) => {
        e.preventDefault();
        const universityName = button.getAttribute("data-university");
        if (universityName) {
          handleUniversityInquiry(universityName);
        }
      });
    });

    // Location dropdowns
    if (elements.countrySelect) {
      elements.countrySelect.addEventListener("change", (e) => {
        const countryCode = e.target.value;
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
      elements.stateSelect.addEventListener("change", (e) => {
        const stateCode = e.target.value;
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
      link.addEventListener("click", (e) => {
        e.preventDefault();
        const targetId = link.getAttribute("href")?.substring(1);
        const targetElement = document.getElementById(targetId);
        if (targetElement) {
          smoothScrollTo(targetElement);
        }
      });
    });

    // Clear search functionality
    const clearButton = document.getElementById("searchClear");
    if (clearButton && elements.searchInput) {
      clearButton.addEventListener("click", () => {
        elements.searchInput.value = "";
        handleSearch("");
      });
    }
  }

  /**
   * Initialize analytics tracking
   */
  function initAnalytics() {
    // Track button clicks
    const trackableButtons = document.querySelectorAll("[data-track]");
    trackableButtons.forEach((button) => {
      button.addEventListener("click", (e) => {
        const action = button.getAttribute("data-track");
        const university = button.getAttribute("data-university");
        const country = button.getAttribute("data-country");

        // Google Analytics tracking
        if (typeof gtag !== "undefined") {
          gtag("event", action, {
            event_category: "university_interaction",
            event_label: university || country,
            value: 1,
          });
        }

        // Console logging for development
        console.log("University Action:", {
          action,
          university,
          country,
          url: button.href || button.getAttribute("href"),
        });
      });
    });
  }

  /**
   * Initialize page features
   */
  function init() {
    try {
      cacheElements();
      bindEvents();
      initAnalytics();

      // Initialize with current state
      if (elements.universityCards.length > 0) {
        filterUniversities();
        sortUniversities();
      }

      console.log("University Partners page initialized successfully");
    } catch (error) {
      console.error("Error initializing University Partners page:", error);
    }
  }

  // ===== AUTO-INITIALIZATION =====

  // Initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  // Expose public API for external access if needed
  window.UniversityPartners = {
    filter: filterUniversities,
    sort: sortUniversities,
    search: handleSearch,
    showAlert,
    smoothScrollTo,
  };
})();
