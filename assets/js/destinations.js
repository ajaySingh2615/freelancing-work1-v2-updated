/**
 * MBBS Destinations Page JavaScript
 * Enhanced Bootstrap 4.6 Integration with Modern Features
 * Optimized for the new Bootstrap-enhanced HTML structure
 */

(function () {
  "use strict";

  // ===== CONFIGURATION =====
  const CONFIG = {
    searchDebounce: 300,
    animationDelay: 100,
    suggestionCount: 6,
    notificationDuration: 3000,
    scrollOffset: 80,
  };

  // ===== STATE MANAGEMENT =====
  const state = {
    activeFilter: "all",
    currentSearchTerm: "",
    isLoading: false,
    totalCountries: 0,
    visibleCountries: 0,
  };

  // ===== DOM ELEMENTS =====
  const elements = {
    searchInput: null,
    countryCards: null,
    searchClear: null,
    resultCount: null,
    filterButtons: null,
    searchSuggestions: null,
    noResultsMessage: null,
  };

  // ===== SEARCH SUGGESTIONS DATA =====
  const suggestions = [
    {
      text: "Russia",
      type: "country",
      icon: "fas fa-flag",
      description: "Medical education in Russia",
    },
    {
      text: "Georgia",
      type: "country",
      icon: "fas fa-flag",
      description: "Study MBBS in Georgia",
    },
    {
      text: "Kazakhstan",
      type: "country",
      icon: "fas fa-flag",
      description: "Medical universities in Kazakhstan",
    },
    {
      text: "Egypt",
      type: "country",
      icon: "fas fa-flag",
      description: "MBBS programs in Egypt",
    },
    {
      text: "Nepal",
      type: "country",
      icon: "fas fa-flag",
      description: "Medical education in Nepal",
    },
    {
      text: "China",
      type: "country",
      icon: "fas fa-flag",
      description: "Study medicine in China",
    },
    {
      text: "Germany",
      type: "country",
      icon: "fas fa-flag",
      description: "Medical universities in Germany",
    },
    {
      text: "Italy",
      type: "country",
      icon: "fas fa-flag",
      description: "Medical education in Italy",
    },
    {
      text: "Poland",
      type: "country",
      icon: "fas fa-flag",
      description: "MBBS in Poland",
    },
    {
      text: "Belarus",
      type: "country",
      icon: "fas fa-flag",
      description: "Medical universities in Belarus",
    },
    {
      text: "Latvia",
      type: "country",
      icon: "fas fa-flag",
      description: "Study medicine in Latvia",
    },
    {
      text: "Uzbekistan",
      type: "country",
      icon: "fas fa-flag",
      description: "Medical education in Uzbekistan",
    },
    {
      text: "MBBS",
      type: "program",
      icon: "fas fa-graduation-cap",
      description: "Bachelor of Medicine",
    },
    {
      text: "Medicine",
      type: "program",
      icon: "fas fa-stethoscope",
      description: "Medical programs",
    },
    {
      text: "Medical University",
      type: "university",
      icon: "fas fa-university",
      description: "Top medical universities",
    },
    {
      text: "Budget friendly",
      type: "feature",
      icon: "fas fa-dollar-sign",
      description: "Affordable medical education",
    },
    {
      text: "Popular destination",
      type: "feature",
      icon: "fas fa-fire",
      description: "Most chosen countries",
    },
    {
      text: "Europe",
      type: "region",
      icon: "fas fa-map-marker-alt",
      description: "European medical universities",
    },
    {
      text: "Asia",
      type: "region",
      icon: "fas fa-map-marker-alt",
      description: "Asian medical universities",
    },
  ];

  // ===== UTILITY FUNCTIONS =====

  /**
   * Debounce function to limit function calls
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
   * Show notification message
   */
  function showNotification(message, type = "success") {
    const notification = document.createElement("div");
    notification.className = `alert alert-${type} notification-toast`;
    notification.innerHTML = `
            <i class="fas fa-${
              type === "success" ? "check-circle" : "info-circle"
            } me-2"></i>
            ${message}
        `;

    document.body.appendChild(notification);

    // Show with animation
    setTimeout(() => notification.classList.add("show"), 100);

    // Hide after duration
    setTimeout(() => {
      notification.classList.remove("show");
      setTimeout(() => notification.remove(), 300);
    }, CONFIG.notificationDuration);
  }

  /**
   * Smooth scroll to element
   */
  function smoothScrollTo(element) {
    if (!element) return;

    const elementPosition =
      element.getBoundingClientRect().top + window.pageYOffset;
    const offsetPosition = elementPosition - CONFIG.scrollOffset;

    window.scrollTo({
      top: offsetPosition,
      behavior: "smooth",
    });
  }

  /**
   * Update result counter
   */
  function updateResultCount(count) {
    if (!elements.resultCount) return;

    const countText = count === 1 ? "1 Country" : `${count} Countries`;
    elements.resultCount.textContent = countText;
    state.visibleCountries = count;
  }

  // ===== SEARCH FUNCTIONALITY =====

  /**
   * Perform search with current filters and search term
   */
  function performSearch() {
    if (!elements.countryCards) return;

    let visibleCount = 0;

    // Show loading state
    state.isLoading = true;

    // Add staggered animation delays
    elements.countryCards.forEach((card, index) => {
      const cardData = getCardData(card);

      // Check search term match
      const matchesSearch =
        state.currentSearchTerm.length === 0 ||
        Object.values(cardData).some((value) =>
          value.toLowerCase().includes(state.currentSearchTerm)
        );

      // Check filter match
      const matchesFilter = checkFilterMatch(card, cardData);

      if (matchesSearch && matchesFilter) {
        card.style.display = "block";
        card.style.animationDelay = `${visibleCount * CONFIG.animationDelay}ms`;
        card.classList.add("country-card-visible");
        visibleCount++;
      } else {
        card.style.display = "none";
        card.classList.remove("country-card-visible");
      }
    });

    // Update UI
    updateResultCount(visibleCount);
    toggleNoResultsMessage(visibleCount === 0);

    state.isLoading = false;
  }

  /**
   * Get searchable data from country card
   */
  function getCardData(card) {
    const nameElement = card.querySelector(".card-title, .country-name");
    const descriptionElement = card.querySelector(
      ".card-text, .country-description"
    );
    const locationElement = card.querySelector(
      '[class*="map-marker"]'
    )?.nextElementSibling;

    return {
      name: nameElement?.textContent || "",
      description: descriptionElement?.textContent || "",
      location: locationElement?.textContent || "",
      region: card.getAttribute("data-region") || "",
      category: card.getAttribute("data-category") || "",
    };
  }

  /**
   * Check if card matches current filter
   */
  function checkFilterMatch(card, cardData) {
    if (state.activeFilter === "all") return true;

    const region = card.getAttribute("data-region");
    const categories = card.getAttribute("data-category")?.split(",") || [];

    if (state.activeFilter === "europe" || state.activeFilter === "asia") {
      return region === state.activeFilter;
    }

    return categories.includes(state.activeFilter);
  }

  /**
   * Show/hide search suggestions
   */
  function showSuggestions(searchTerm) {
    if (!elements.searchSuggestions || searchTerm.length === 0) {
      hideSuggestions();
      return;
    }

    const filteredSuggestions = suggestions
      .filter(
        (suggestion) =>
          suggestion.text.toLowerCase().includes(searchTerm) ||
          suggestion.description.toLowerCase().includes(searchTerm)
      )
      .slice(0, CONFIG.suggestionCount);

    if (filteredSuggestions.length === 0) {
      hideSuggestions();
      return;
    }

    const suggestionsHTML = filteredSuggestions
      .map((suggestion) => {
        const highlightedText = suggestion.text.replace(
          new RegExp(`(${searchTerm})`, "gi"),
          '<mark class="bg-primary text-white rounded px-1">$1</mark>'
        );

        return `
                <div class="suggestion-item d-flex align-items-center p-2 border-bottom" 
                     data-suggestion="${suggestion.text}" 
                     role="option">
                    <i class="${suggestion.icon} text-primary me-2"></i>
                    <div class="flex-grow-1">
                        <div class="suggestion-text fw-medium">${highlightedText}</div>
                        <small class="text-muted">${suggestion.description}</small>
                    </div>
                </div>
            `;
      })
      .join("");

    elements.searchSuggestions.innerHTML = suggestionsHTML;
    elements.searchSuggestions.classList.add("show");

    // Bind suggestion click events
    bindSuggestionEvents();
  }

  /**
   * Hide search suggestions
   */
  function hideSuggestions() {
    if (elements.searchSuggestions) {
      elements.searchSuggestions.classList.remove("show");
    }
  }

  /**
   * Bind suggestion click events
   */
  function bindSuggestionEvents() {
    const suggestionItems =
      elements.searchSuggestions.querySelectorAll(".suggestion-item");

    suggestionItems.forEach((item) => {
      item.addEventListener("click", function () {
        const suggestionText = this.getAttribute("data-suggestion");
        selectSuggestion(suggestionText);
      });

      item.addEventListener("mouseenter", function () {
        // Remove active class from all suggestions
        suggestionItems.forEach((s) => s.classList.remove("active"));
        // Add active class to hovered item
        this.classList.add("active");
      });
    });
  }

  /**
   * Select a suggestion
   */
  function selectSuggestion(suggestionText) {
    elements.searchInput.value = suggestionText;
    state.currentSearchTerm = suggestionText.toLowerCase();

    // Show clear button
    if (elements.searchClear) {
      elements.searchClear.style.display = "block";
    }

    hideSuggestions();
    performSearch();

    // Track suggestion selection
    trackEvent("suggestion_selected", suggestionText);
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
                    <i class="fas fa-search fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mb-3">No countries found</h4>
                <p class="text-muted mb-4">Try adjusting your search terms or filters to find what you're looking for.</p>
                <button type="button" class="btn btn-primary" onclick="clearAllFilters()">
                    <i class="fas fa-undo me-2"></i>Clear All Filters
                </button>
            `;

      const gridContainer =
        document.getElementById("countriesGrid") ||
        document.querySelector(".row.g-4") ||
        document.querySelector(".countries-grid");

      if (gridContainer) {
        gridContainer.appendChild(noResultsDiv);
      }
    } else if (!show && noResultsDiv) {
      noResultsDiv.remove();
    }
  }

  // ===== EVENT HANDLERS =====

  /**
   * Handle search input
   */
  const handleSearchInput = debounce((value) => {
    state.currentSearchTerm = value.toLowerCase().trim();

    // Toggle clear button
    if (elements.searchClear) {
      elements.searchClear.style.display = state.currentSearchTerm
        ? "block"
        : "none";
    }

    // Show suggestions
    showSuggestions(state.currentSearchTerm);

    // Perform search
    performSearch();

    // Track search
    if (state.currentSearchTerm.length >= 3) {
      trackEvent("search_performed", state.currentSearchTerm);
    }
  }, CONFIG.searchDebounce);

  /**
   * Handle filter button click
   */
  function handleFilterClick(button) {
    // Remove active class from all filter buttons
    elements.filterButtons.forEach((btn) => btn.classList.remove("active"));

    // Add active class to clicked button
    button.classList.add("active");

    // Update active filter
    state.activeFilter = button.getAttribute("data-filter");

    // Perform search with new filter
    performSearch();

    // Track filter usage
    trackEvent("filter_applied", state.activeFilter);
  }

  /**
   * Handle keyboard navigation
   */
  function handleKeyboardNavigation(event) {
    if (!elements.searchSuggestions.classList.contains("show")) return;

    const suggestions =
      elements.searchSuggestions.querySelectorAll(".suggestion-item");
    const currentActive = elements.searchSuggestions.querySelector(
      ".suggestion-item.active"
    );

    switch (event.key) {
      case "ArrowDown":
        event.preventDefault();
        navigateSuggestions(suggestions, currentActive, "down");
        break;
      case "ArrowUp":
        event.preventDefault();
        navigateSuggestions(suggestions, currentActive, "up");
        break;
      case "Enter":
        event.preventDefault();
        if (currentActive) {
          selectSuggestion(currentActive.getAttribute("data-suggestion"));
        } else {
          hideSuggestions();
          performSearch();
        }
        break;
      case "Escape":
        event.preventDefault();
        hideSuggestions();
        elements.searchInput.blur();
        break;
    }
  }

  /**
   * Navigate through suggestions with keyboard
   */
  function navigateSuggestions(suggestions, currentActive, direction) {
    if (suggestions.length === 0) return;

    // Remove active class from current
    if (currentActive) {
      currentActive.classList.remove("active");
    }

    let nextIndex = 0;

    if (currentActive) {
      const currentIndex = Array.from(suggestions).indexOf(currentActive);
      if (direction === "down") {
        nextIndex = (currentIndex + 1) % suggestions.length;
      } else {
        nextIndex =
          currentIndex === 0 ? suggestions.length - 1 : currentIndex - 1;
      }
    }

    suggestions[nextIndex].classList.add("active");
    suggestions[nextIndex].scrollIntoView({ block: "nearest" });
  }

  // ===== ANIMATION AND UI ENHANCEMENTS =====

  /**
   * Initialize intersection observer for animations
   */
  function initializeAnimations() {
    const observerOptions = {
      threshold: 0.1,
      rootMargin: "0px 0px -50px 0px",
    };

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("animate-in");
        }
      });
    }, observerOptions);

    // Observe all country cards
    elements.countryCards.forEach((card) => {
      observer.observe(card);
    });
  }

  /**
   * Initialize hover effects for Bootstrap cards
   */
  function initializeHoverEffects() {
    elements.countryCards.forEach((card) => {
      card.addEventListener("mouseenter", function () {
        this.style.transform = "translateY(-8px)";
        this.classList.add("shadow-lg");
      });

      card.addEventListener("mouseleave", function () {
        this.style.transform = "translateY(0)";
        this.classList.remove("shadow-lg");
      });
    });
  }

  // ===== ANALYTICS AND TRACKING =====

  /**
   * Track events for analytics
   */
  function trackEvent(action, label, value = 1) {
    // Google Analytics 4
    if (typeof gtag !== "undefined") {
      gtag("event", action, {
        event_category: "destinations_page",
        event_label: label,
        value: value,
      });
    }

    // Console logging for development
    console.log("Event tracked:", { action, label, value });
  }

  /**
   * Track button clicks
   */
  function initializeButtonTracking() {
    const trackableButtons = document.querySelectorAll("[data-track]");

    trackableButtons.forEach((button) => {
      button.addEventListener("click", function () {
        const action = this.getAttribute("data-track");
        const country =
          this.getAttribute("data-country") ||
          this.closest(".country-card-wrapper, .col-lg-6")?.getAttribute(
            "data-country"
          );

        trackEvent(action, country);

        // Add visual feedback
        this.style.transform = "scale(0.95)";
        setTimeout(() => {
          this.style.transform = "";
        }, 150);
      });
    });
  }

  // ===== GLOBAL FUNCTIONS =====

  /**
   * Clear all filters and search
   */
  window.clearAllFilters = function () {
    // Clear search
    if (elements.searchInput) {
      elements.searchInput.value = "";
      state.currentSearchTerm = "";
    }

    // Clear filters
    state.activeFilter = "all";
    elements.filterButtons.forEach((btn) => {
      btn.classList.toggle("active", btn.getAttribute("data-filter") === "all");
    });

    // Hide suggestions and clear button
    hideSuggestions();
    if (elements.searchClear) {
      elements.searchClear.style.display = "none";
    }

    // Perform search
    performSearch();

    // Show notification
    showNotification("All filters cleared successfully!");
  };

  // ===== INITIALIZATION =====

  /**
   * Cache DOM elements
   */
  function cacheElements() {
    elements.searchInput = document.getElementById("countrySearch");
    elements.countryCards = document.querySelectorAll(
      ".country-card-wrapper, .country-card"
    );
    elements.searchClear = document.getElementById("searchClear");
    elements.resultCount = document.getElementById("resultCount");
    elements.filterButtons = document.querySelectorAll("[data-filter]");
    elements.searchSuggestions = document.getElementById("searchSuggestions");

    // Set initial state
    state.totalCountries = elements.countryCards.length;
  }

  /**
   * Bind event listeners
   */
  function bindEvents() {
    // Search input
    if (elements.searchInput) {
      elements.searchInput.addEventListener("input", (e) => {
        handleSearchInput(e.target.value);
      });

      elements.searchInput.addEventListener("focus", () => {
        if (state.currentSearchTerm) {
          showSuggestions(state.currentSearchTerm);
        }
      });

      elements.searchInput.addEventListener("blur", () => {
        // Delay hiding to allow clicking suggestions
        setTimeout(hideSuggestions, 200);
      });
    }

    // Clear search button
    if (elements.searchClear) {
      elements.searchClear.addEventListener("click", () => {
        elements.searchInput.value = "";
        elements.searchInput.focus();
        handleSearchInput("");
      });
    }

    // Filter buttons
    elements.filterButtons.forEach((button) => {
      button.addEventListener("click", () => {
        handleFilterClick(button);
      });
    });

    // Keyboard navigation
    document.addEventListener("keydown", handleKeyboardNavigation);

    // Window resize handler
    window.addEventListener(
      "resize",
      debounce(() => {
        // Handle responsive adjustments
        hideSuggestions();
      }, 250)
    );
  }

  /**
   * Add custom CSS for enhancements
   */
  function addCustomStyles() {
    const style = document.createElement("style");
    style.textContent = `
            .notification-toast {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1050;
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease;
                min-width: 300px;
                border: none;
                border-radius: 0.5rem;
                box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            }
            
            .notification-toast.show {
                opacity: 1;
                transform: translateX(0);
            }
            
            .suggestion-item {
                cursor: pointer;
                transition: background-color 0.2s ease;
            }
            
            .suggestion-item:hover,
            .suggestion-item.active {
                background-color: rgba(0, 123, 255, 0.1);
            }
            
            .country-card-wrapper {
                transition: all 0.3s ease;
            }
            
            .animate-in {
                animation: fadeInUp 0.6s ease-out;
            }
            
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
            
            .hover-lift {
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            
            .hover-lift:hover {
                transform: translateY(-8px);
                box-shadow: 0 1rem 3rem rgba(0,0,0,.175);
            }
            
            .search-suggestions.show {
                display: block;
                animation: slideDown 0.2s ease-out;
            }
            
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
        `;
    document.head.appendChild(style);
  }

  /**
   * Initialize the page
   */
  function init() {
    try {
      cacheElements();
      bindEvents();
      addCustomStyles();
      initializeAnimations();
      initializeHoverEffects();
      initializeButtonTracking();

      // Perform initial search to setup UI
      performSearch();

      console.log(
        "MBBS Destinations page initialized with Bootstrap enhancements"
      );
    } catch (error) {
      console.error("Error initializing destinations page:", error);
    }
  }

  // ===== AUTO-INITIALIZATION =====

  // Initialize when DOM is ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  // Expose public API
  window.DestinationsPage = {
    performSearch,
    clearAllFilters: window.clearAllFilters,
    showNotification,
    trackEvent,
  };
})();
