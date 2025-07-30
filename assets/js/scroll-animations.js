/**
 * Scroll-Based Animations for Sunrise Global Education
 * Advanced scroll animations using Intersection Observer API
 * and CSS transforms for smooth, performant animations
 */

class ScrollAnimations {
  constructor() {
    this.observerOptions = {
      threshold: [0.1, 0.3, 0.6],
      rootMargin: "-50px 0px -50px 0px",
    };

    this.observer = new IntersectionObserver(
      this.handleIntersection.bind(this),
      this.observerOptions
    );

    this.animatedElements = new Set();
    this.init();
  }

  init() {
    // Wait for DOM to be ready
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", () =>
        this.setupAnimations()
      );
    } else {
      this.setupAnimations();
    }
  }

  setupAnimations() {
    // Check which page we're on
    const isAboutPage =
      window.location.pathname.includes("about.php") ||
      document.body.classList.contains("about-page");
    const isDestinationsPage =
      window.location.pathname.includes("destinations.php") ||
      document.body.classList.contains("destinations-page");
    const isResourcesPage =
      window.location.pathname.includes("resources.php") ||
      document.body.classList.contains("resources-page");
    const isContactPage =
      window.location.pathname.includes("contact.php") ||
      document.body.classList.contains("contact-page");
    const isGalleryPage =
      window.location.pathname.includes("gallery.php") ||
      document.body.classList.contains("gallery-page");
    const isServicesPage =
      window.location.pathname.includes("services.php") ||
      document.body.classList.contains("services-page");

    if (isAboutPage) {
      // About page specific animations
      this.addAboutHeroAnimations();
      this.addCompanyStoryAnimations();
      this.addTeamSectionAnimations();
      this.addValuesSectionAnimations();
      this.addAboutCtaAnimations();
      this.addMediaHighlightsAnimations();
    } else if (isDestinationsPage) {
      // Destinations page specific animations
      this.addDestinationsHeroAnimations();
      this.addSearchSectionAnimations();
      this.addCountriesGridAnimations();
      this.addDestinationsCtaAnimations();
    } else if (isResourcesPage) {
      // Resources page specific animations
      this.addResourcesHeroAnimations();
      this.addResourcesSearchFilterAnimations();
      this.addUniversityCardsGridAnimations();
    } else if (isContactPage) {
      // Contact page specific animations
      this.addContactHeaderAnimations();
      this.addContactInfoAnimations();
      this.addContactFormAnimations();
      this.addOfficeLocationsAnimations();
      this.addGoogleMapAnimations();
      this.addFaqSectionAnimations();
    } else if (isGalleryPage) {
      // Gallery page specific animations
      this.addGalleryHeroAnimations();
      this.addGalleryCarouselAnimations();
      this.addGalleryGridAnimations();
    } else if (isServicesPage) {
      // Services page specific animations
      this.addServicesHeroAnimations();
      this.addServicesContentHeaderAnimations();
      this.addServicesCardsAnimations();
      this.addAdditionalServicesAnimations();
      this.addServicesCtaAnimations();
    } else {
      // Index page animations
      this.addMediaPartnersAnimations();
      this.addAboutSectionAnimations();
      this.addDestinationsAnimations();
      this.addServicesAnimations();
      this.addUniversitiesAnimations();
      this.addWhyChooseUsAnimations();
      this.addWorkingProcessAnimations();
      this.addTestimonialAnimations();
      this.addBlogSectionAnimations();
      this.addContactSectionAnimations();
    }

    this.addStickyButtonAnimation();
    this.startObserving();
  }

  // 1. Media Partners Section - Staggered fade-in with slide up
  addMediaPartnersAnimations() {
    const section = document.querySelector(".media-partners-section");
    if (!section) return;

    section.setAttribute("data-animation", "media-partners");

    const partners = section.querySelectorAll(".media-partner-item");
    partners.forEach((partner, index) => {
      partner.setAttribute("data-animation", "partner-item");
      partner.setAttribute("data-delay", index * 150);
      partner.style.opacity = "0";
      partner.style.transform = "translateY(30px)";
    });
  }

  // 2. About Section - Multi-stage reveal
  addAboutSectionAnimations() {
    const section = document.querySelector(".about-section");
    if (!section) return;

    section.setAttribute("data-animation", "about-section");

    // Title animation
    const title = section.querySelector(".about-question");
    if (title) {
      title.setAttribute("data-animation", "title-reveal");
      title.style.opacity = "0";
      title.style.transform = "translateY(40px)";
    }

    // Intro text
    const intro = section.querySelector(".about-intro");
    if (intro) {
      intro.setAttribute("data-animation", "intro-fade");
      intro.style.opacity = "0";
      intro.style.transform = "translateY(30px)";
    }

    // Highlight cards with stagger
    const cards = section.querySelectorAll(".highlight-card");
    cards.forEach((card, index) => {
      card.setAttribute("data-animation", "highlight-card");
      card.setAttribute("data-delay", index * 200);
      card.style.opacity = "0";
      card.style.transform = "translateY(40px) scale(0.95)";
    });

    // Action buttons
    const actions = section.querySelector(".about-actions");
    if (actions) {
      actions.setAttribute("data-animation", "actions-reveal");
      actions.style.opacity = "0";
      actions.style.transform = "translateY(30px)";
    }
  }

  // 3. Study Destinations - Dynamic grid animation
  addDestinationsAnimations() {
    const section = document.querySelector(".study-destinations-section");
    if (!section) return;

    section.setAttribute("data-animation", "destinations-section");

    // Title
    const title = section.querySelector(".destinations-question");
    if (title) {
      title.setAttribute("data-animation", "title-slide-up");
      title.style.opacity = "0";
      title.style.transform = "translateY(50px)";
    }

    // Destination items with wave effect
    const destinations = section.querySelectorAll(".destination-item");
    destinations.forEach((dest, index) => {
      dest.setAttribute("data-animation", "destination-item");
      dest.setAttribute(
        "data-delay",
        Math.floor(index / 4) * 100 + (index % 4) * 150
      );
      dest.style.opacity = "0";
      dest.style.transform = "translateY(40px) scale(0.9)";
    });

    // View all link
    const viewAll = section.querySelector(".view-all-link");
    if (viewAll) {
      viewAll.setAttribute("data-animation", "view-all-bounce");
      viewAll.style.opacity = "0";
      viewAll.style.transform = "translateY(30px)";
    }
  }

  // 4. Services Section - Split animation
  addServicesAnimations() {
    const section = document.querySelector(".services-section");
    if (!section) return;

    section.setAttribute("data-animation", "services-section");

    // Header
    const header = section.querySelector(".services-header");
    if (header) {
      header.setAttribute("data-animation", "header-fade-up");
      header.style.opacity = "0";
      header.style.transform = "translateY(40px)";
    }

    // Left services
    const leftServices = section.querySelectorAll(
      ".services-left .service-group"
    );
    leftServices.forEach((service, index) => {
      service.setAttribute("data-animation", "service-slide-right");
      service.setAttribute("data-delay", index * 300);
      service.style.opacity = "0";
      service.style.transform = "translateX(-60px)";
    });

    // Center image
    const centerImage = section.querySelector(".services-image");
    if (centerImage) {
      centerImage.setAttribute("data-animation", "image-zoom-in");
      centerImage.style.opacity = "0";
      centerImage.style.transform = "scale(0.8)";
    }

    // Right services
    const rightServices = section.querySelectorAll(
      ".services-right .service-group"
    );
    rightServices.forEach((service, index) => {
      service.setAttribute("data-animation", "service-slide-left");
      service.setAttribute("data-delay", index * 300);
      service.style.opacity = "0";
      service.style.transform = "translateX(60px)";
    });
  }

  // 5. Universities Section - Logo wall animation
  addUniversitiesAnimations() {
    const section = document.querySelector(".universities-section");
    if (!section) return;

    section.setAttribute("data-animation", "universities-section");

    // Title
    const title = section.querySelector(".section-heading h2");
    if (title) {
      title.setAttribute("data-animation", "title-typewriter");
      title.style.opacity = "0";
    }

    // Logo items with matrix effect
    const logos = section.querySelectorAll(".logo-item");
    logos.forEach((logo, index) => {
      logo.setAttribute("data-animation", "logo-matrix");
      logo.setAttribute(
        "data-delay",
        Math.floor(index / 4) * 200 + (index % 4) * 100
      );
      logo.style.opacity = "0";
      logo.style.transform = "rotateY(90deg) scale(0.5)";
    });

    // Action buttons
    const actions = section.querySelector(".universities-actions");
    if (actions) {
      actions.setAttribute("data-animation", "actions-slide-up");
      actions.style.opacity = "0";
      actions.style.transform = "translateY(40px)";
    }
  }

  // 6. Why Choose Us Section - Card flip animations
  addWhyChooseUsAnimations() {
    const section = document.querySelector(".why-choose-us-section");
    if (!section) return;

    section.setAttribute("data-animation", "why-choose-section");

    // Title
    const title = section.querySelector(".why-choose-us-title");
    if (title) {
      title.setAttribute("data-animation", "title-glow");
      title.style.opacity = "0";
      title.style.transform = "translateY(30px)";
    }

    // Benefit cards with flip effect
    const cards = section.querySelectorAll(".benefit-card");
    cards.forEach((card, index) => {
      card.setAttribute("data-animation", "benefit-card-flip");
      card.setAttribute("data-delay", index * 250);
      card.style.opacity = "0";
      card.style.transform = "rotateX(-90deg)";
    });

    // Benefits image
    const image = section.querySelector(".benefits-image");
    if (image) {
      image.setAttribute("data-animation", "image-slide-left");
      image.style.opacity = "0";
      image.style.transform = "translateX(80px)";
    }
  }

  // 7. Working Process Section - Sequential step animation
  addWorkingProcessAnimations() {
    const section = document.querySelector(".working-process-section");
    if (!section) return;

    section.setAttribute("data-animation", "process-section");

    // Header
    const header = section.querySelector(".process-header");
    if (header) {
      header.setAttribute("data-animation", "header-reveal");
      header.style.opacity = "0";
      header.style.transform = "translateY(40px)";
    }

    // Process steps with connecting lines
    const steps = section.querySelectorAll(".process-step");
    const arrows = section.querySelectorAll(".process-arrow");

    steps.forEach((step, index) => {
      step.setAttribute("data-animation", "process-step");
      step.setAttribute("data-delay", index * 500);
      step.style.opacity = "0";
      step.style.transform = "translateY(60px) scale(0.8)";
    });

    arrows.forEach((arrow, index) => {
      arrow.setAttribute("data-animation", "process-arrow");
      arrow.setAttribute("data-delay", (index + 1) * 500 + 200);
      arrow.style.opacity = "0";
      arrow.style.transform = "scaleX(0)";
    });
  }

  // 8. Testimonial Section - Split reveal
  addTestimonialAnimations() {
    const section = document.querySelector(".testimonial-section");
    if (!section) return;

    section.setAttribute("data-animation", "testimonial-section");

    // Left content
    const leftContent = section.querySelector(".testimonial-left");
    if (leftContent) {
      leftContent.setAttribute("data-animation", "testimonial-left");
      leftContent.style.opacity = "0";
      leftContent.style.transform = "translateX(-50px)";
    }

    // Right image
    const rightImage = section.querySelector(".testimonial-right");
    if (rightImage) {
      rightImage.setAttribute("data-animation", "testimonial-right");
      rightImage.style.opacity = "0";
      rightImage.style.transform = "translateX(50px) scale(0.9)";
    }

    // Stats with counter animation
    const stats = section.querySelectorAll(".stat-item");
    stats.forEach((stat, index) => {
      stat.setAttribute("data-animation", "stat-counter");
      stat.setAttribute("data-delay", index * 300);
      stat.style.opacity = "0";
      stat.style.transform = "translateY(30px)";
    });
  }

  // 9. Blog/News Sections - Card cascade
  addBlogSectionAnimations() {
    const blogSections = document.querySelectorAll(
      ".blog-section, .news-section"
    );

    blogSections.forEach((section) => {
      section.setAttribute("data-animation", "blog-section");

      // Header
      const header = section.querySelector(".section-header, .news-header");
      if (header) {
        header.setAttribute("data-animation", "blog-header");
        header.style.opacity = "0";
        header.style.transform = "translateY(40px)";
      }

      // Blog/News cards
      const cards = section.querySelectorAll(".blog-card, .news-card");
      cards.forEach((card, index) => {
        card.setAttribute("data-animation", "blog-card");
        card.setAttribute("data-delay", index * 200);
        card.style.opacity = "0";
        card.style.transform = "translateY(50px) rotateX(20deg)";
      });
    });
  }

  // 10. Contact Section - Smooth reveal
  addContactSectionAnimations() {
    const section = document.querySelector(".contact-section");
    if (!section) return;

    section.setAttribute("data-animation", "contact-section");

    // Contact image
    const image = section.querySelector(".contact-image");
    if (image) {
      image.setAttribute("data-animation", "contact-image");
      image.style.opacity = "0";
      image.style.transform = "translateX(-40px)";
    }

    // Contact content
    const content = section.querySelector(".contact-content");
    if (content) {
      content.setAttribute("data-animation", "contact-content");
      content.style.opacity = "0";
      content.style.transform = "translateX(40px)";
    }
  }

  // 11. Sticky Button - Entrance animation
  addStickyButtonAnimation() {
    const stickyBtn = document.querySelector(".sticky-consultation");
    if (stickyBtn) {
      stickyBtn.setAttribute("data-animation", "sticky-entrance");
      stickyBtn.style.opacity = "0";
      stickyBtn.style.transform = "translateY(100px)";
    }
  }

  // ========== ABOUT PAGE SPECIFIC ANIMATIONS ==========

  // 1. About Hero Section - Multi-stage reveal with counter animations
  addAboutHeroAnimations() {
    const section = document.querySelector(".about-hero");
    if (!section) return;

    section.setAttribute("data-animation", "about-hero-section");

    // Tagline
    const tagline = section.querySelector(".about-hero-tagline");
    if (tagline) {
      tagline.setAttribute("data-animation", "hero-tagline");
      tagline.style.opacity = "0";
      tagline.style.transform = "translateY(20px)";
    }

    // Title
    const title = section.querySelector(".about-hero-title");
    if (title) {
      title.setAttribute("data-animation", "hero-title");
      title.style.opacity = "0";
      title.style.transform = "translateY(40px)";
    }

    // Hero Image
    const heroImage = section.querySelector(".hero-image");
    if (heroImage) {
      heroImage.setAttribute("data-animation", "hero-image");
      heroImage.style.opacity = "0";
      heroImage.style.transform = "scale(0.9) translateY(30px)";
    }

    // Stats with counter animation
    const stats = section.querySelectorAll(".hero-stat-item");
    stats.forEach((stat, index) => {
      stat.setAttribute("data-animation", "hero-stat-counter");
      stat.setAttribute("data-delay", index * 150);
      stat.style.opacity = "0";
      stat.style.transform = "translateY(30px) scale(0.95)";
    });
  }

  // 2. Company Story Section - Alternating content with images
  addCompanyStoryAnimations() {
    const section = document.querySelector(".company-story-section");
    if (!section) return;

    section.setAttribute("data-animation", "company-story-section");

    // Story Header
    const header = section.querySelector(".story-header");
    if (header) {
      header.setAttribute("data-animation", "story-header");
      header.style.opacity = "0";
      header.style.transform = "translateY(40px)";
    }

    // Story Content blocks
    const storyContents = section.querySelectorAll(".story-content");
    storyContents.forEach((content, index) => {
      content.setAttribute("data-animation", "story-content");
      content.setAttribute("data-delay", index * 300);

      const textContent = content.querySelector(".story-text");
      const imageContent = content.querySelector(".story-image");

      if (textContent) {
        textContent.style.opacity = "0";
        textContent.style.transform = content.classList.contains("reverse")
          ? "translateX(60px)"
          : "translateX(-60px)";
      }

      if (imageContent) {
        imageContent.style.opacity = "0";
        imageContent.style.transform = content.classList.contains("reverse")
          ? "translateX(-60px)"
          : "translateX(60px)";
      }
    });

    // MVV Cards (Mission, Vision, Values)
    const mvvCards = section.querySelectorAll(".mvv-card");
    mvvCards.forEach((card, index) => {
      card.setAttribute("data-animation", "mvv-card");
      card.setAttribute("data-delay", index * 200);
      card.style.opacity = "0";
      card.style.transform = "translateY(50px) rotateX(20deg)";
    });
  }

  // 3. Team Section - Profile card animations
  addTeamSectionAnimations() {
    const section = document.querySelector(".team-section");
    if (!section) return;

    section.setAttribute("data-animation", "team-section");

    // Team Header
    const header = section.querySelector(".team-header");
    if (header) {
      header.setAttribute("data-animation", "team-header");
      header.style.opacity = "0";
      header.style.transform = "translateY(40px)";
    }

    // Team Members with staggered entrance
    const teamMembers = section.querySelectorAll(".team-member");
    teamMembers.forEach((member, index) => {
      member.setAttribute("data-animation", "team-member");
      member.setAttribute("data-delay", index * 250);
      member.style.opacity = "0";
      member.style.transform = "translateY(60px) scale(0.9)";

      // Member image specific animation
      const memberImage = member.querySelector(".member-image");
      if (memberImage) {
        memberImage.style.transform = "scale(1.1)";
      }
    });
  }

  // 4. Values Section - Grid animations with culture cards
  addValuesSectionAnimations() {
    const section = document.querySelector(".values-section");
    if (!section) return;

    section.setAttribute("data-animation", "values-section");

    // Values Header
    const header = section.querySelector(".values-header");
    if (header) {
      header.setAttribute("data-animation", "values-header");
      header.style.opacity = "0";
      header.style.transform = "translateY(40px)";
    }

    // Value Cards with dynamic grid animation
    const valueCards = section.querySelectorAll(".value-card");
    valueCards.forEach((card, index) => {
      card.setAttribute("data-animation", "value-card");
      card.setAttribute(
        "data-delay",
        Math.floor(index / 3) * 200 + (index % 3) * 150
      );
      card.style.opacity = "0";
      card.style.transform = "translateY(50px) rotateY(15deg)";
    });

    // Culture Section
    const cultureSection = section.querySelector(".culture-section");
    if (cultureSection) {
      const cultureHeader = cultureSection.querySelector(".culture-header");
      if (cultureHeader) {
        cultureHeader.setAttribute("data-animation", "culture-header");
        cultureHeader.style.opacity = "0";
        cultureHeader.style.transform = "translateY(40px)";
      }

      // Culture Cards
      const cultureCards = cultureSection.querySelectorAll(".culture-card");
      cultureCards.forEach((card, index) => {
        card.setAttribute("data-animation", "culture-card");
        card.setAttribute("data-delay", index * 200);
        card.style.opacity = "0";
        card.style.transform = "translateY(50px) scale(0.95)";
      });
    }
  }

  // 5. About CTA Section - Split animation with illustration
  addAboutCtaAnimations() {
    const section = document.querySelector(".about-cta-section");
    if (!section) return;

    section.setAttribute("data-animation", "about-cta-section");

    // CTA Image/Illustration
    const ctaImage = section.querySelector(".cta-image");
    if (ctaImage) {
      ctaImage.setAttribute("data-animation", "cta-image");
      ctaImage.style.opacity = "0";
      ctaImage.style.transform = "translateX(-50px) scale(0.95)";
    }

    // CTA Content
    const ctaContent = section.querySelector(".cta-content");
    if (ctaContent) {
      ctaContent.setAttribute("data-animation", "cta-content");
      ctaContent.style.opacity = "0";
      ctaContent.style.transform = "translateX(50px)";
    }

    // CTA Buttons
    const ctaButtons = section.querySelectorAll(
      ".cta-btn-primary, .cta-btn-secondary"
    );
    ctaButtons.forEach((button, index) => {
      button.setAttribute("data-animation", "cta-button");
      button.setAttribute("data-delay", index * 150);
      button.style.opacity = "0";
      button.style.transform = "translateY(20px)";
    });
  }

  // 6. Media Highlights Section - Logo wall with wave animation
  addMediaHighlightsAnimations() {
    const section = document.querySelector(".media-highlights-section");
    if (!section) return;

    section.setAttribute("data-animation", "media-highlights-section");

    // Media Header
    const header = section.querySelector(".media-highlights-header");
    if (header) {
      header.setAttribute("data-animation", "media-header");
      header.style.opacity = "0";
      header.style.transform = "translateY(40px)";
    }

    // Media Logo Items with wave effect
    const logoItems = section.querySelectorAll(".media-logo-item");
    logoItems.forEach((logo, index) => {
      logo.setAttribute("data-animation", "media-logo");
      logo.setAttribute(
        "data-delay",
        Math.floor(index / 5) * 100 + (index % 5) * 120
      );
      logo.style.opacity = "0";
      logo.style.transform = "translateY(40px) scale(0.8)";
    });
  }

  // ========== DESTINATIONS PAGE SPECIFIC ANIMATIONS ==========

  // 1. Destinations Hero Section - Breadcrumb and title reveal
  addDestinationsHeroAnimations() {
    const section = document.querySelector(".destinations-hero");
    if (!section) return;

    section.setAttribute("data-animation", "destinations-hero-section");

    // Breadcrumb navigation
    const breadcrumb = section.querySelector(".breadcrumb");
    if (breadcrumb) {
      breadcrumb.setAttribute("data-animation", "destinations-breadcrumb");
      breadcrumb.style.opacity = "0";
      breadcrumb.style.transform = "translateY(-20px)";
    }

    // Hero title
    const title = section.querySelector(".hero-title");
    if (title) {
      title.setAttribute("data-animation", "destinations-hero-title");
      title.style.opacity = "0";
      title.style.transform = "translateY(40px)";
    }
  }

  // 2. Search Section - Progressive reveal with interactive elements
  addSearchSectionAnimations() {
    const section = document.querySelector(".search-section");
    if (!section) return;

    section.setAttribute("data-animation", "search-section");

    // Search header/subtitle
    const searchHeader = section.querySelector(".search-header");
    if (searchHeader) {
      searchHeader.setAttribute("data-animation", "search-header");
      searchHeader.style.opacity = "0";
      searchHeader.style.transform = "translateY(30px)";
    }

    // Search box with sliding effect
    const searchBox = section.querySelector(".search-box");
    if (searchBox) {
      searchBox.setAttribute("data-animation", "search-box");
      searchBox.style.opacity = "0";
      searchBox.style.transform = "translateY(40px) scale(0.95)";
    }

    // Search stats
    const searchStats = section.querySelector(".search-stats");
    if (searchStats) {
      searchStats.setAttribute("data-animation", "search-stats");
      searchStats.style.opacity = "0";
      searchStats.style.transform = "translateY(20px)";
    }

    // Filter tags with staggered animation
    const filterTags = section.querySelectorAll(".filter-tag");
    filterTags.forEach((tag, index) => {
      tag.setAttribute("data-animation", "search-filter-tag");
      tag.setAttribute("data-delay", index * 100);
      tag.style.opacity = "0";
      tag.style.transform = "translateY(30px) scale(0.9)";
    });
  }

  // 3. Countries Grid Section - Dynamic card animations
  addCountriesGridAnimations() {
    const section = document.querySelector(".countries-section");
    if (!section) return;

    section.setAttribute("data-animation", "countries-grid-section");

    // Country cards with masonry-like staggered effect
    const countryCards = section.querySelectorAll(".country-card");
    countryCards.forEach((card, index) => {
      card.setAttribute("data-animation", "country-card");
      // Calculate stagger based on grid position (assuming 3 columns)
      const row = Math.floor(index / 3);
      const col = index % 3;
      const delay = row * 150 + col * 100;
      card.setAttribute("data-delay", delay);
      card.style.opacity = "0";
      card.style.transform = "translateY(60px) scale(0.95)";

      // Country flag with special animation
      const flag = card.querySelector(".country-flag");
      if (flag) {
        flag.style.transform = "scale(0.8) rotate(5deg)";
      }

      // Country actions buttons
      const actions = card.querySelectorAll(".country-actions .btn");
      actions.forEach((btn, btnIndex) => {
        btn.style.opacity = "0";
        btn.style.transform = "translateY(20px)";
      });
    });

    // No countries fallback message
    const noCountries = section.querySelector(".no-countries");
    if (noCountries) {
      noCountries.setAttribute("data-animation", "no-countries-message");
      noCountries.style.opacity = "0";
      noCountries.style.transform = "translateY(40px) scale(0.95)";
    }
  }

  // 4. Destinations CTA Section - Final call to action
  addDestinationsCtaAnimations() {
    const section = document.querySelector(".cta-section");
    if (!section) return;

    section.setAttribute("data-animation", "destinations-cta-section");

    // CTA title
    const title = section.querySelector(".cta-title");
    if (title) {
      title.setAttribute("data-animation", "destinations-cta-title");
      title.style.opacity = "0";
      title.style.transform = "translateY(40px)";
    }

    // CTA description
    const description = section.querySelector(".cta-description");
    if (description) {
      description.setAttribute(
        "data-animation",
        "destinations-cta-description"
      );
      description.style.opacity = "0";
      description.style.transform = "translateY(30px)";
    }

    // CTA buttons
    const buttons = section.querySelectorAll(".cta-buttons .btn");
    buttons.forEach((button, index) => {
      button.setAttribute("data-animation", "destinations-cta-button");
      button.setAttribute("data-delay", index * 150);
      button.style.opacity = "0";
      button.style.transform = "translateY(30px) scale(0.95)";
    });
  }

  // ========== RESOURCES PAGE SPECIFIC ANIMATIONS ==========

  // 1. Resources Hero Section - Hero with decorative elements
  addResourcesHeroAnimations() {
    const section = document.querySelector(".university-partners-hero");
    if (!section) return;

    section.setAttribute("data-animation", "resources-hero-section");

    // Breadcrumb navigation
    const breadcrumb = section.querySelector(".hero-breadcrumb");
    if (breadcrumb) {
      breadcrumb.setAttribute("data-animation", "resources-breadcrumb");
      breadcrumb.style.opacity = "0";
      breadcrumb.style.transform = "translateY(-20px)";
    }

    // Hero title
    const title = section.querySelector(".hero-title");
    if (title) {
      title.setAttribute("data-animation", "resources-hero-title");
      title.style.opacity = "0";
      title.style.transform = "translateY(50px)";
    }

    // Hero subtitle
    const subtitle = section.querySelector(".hero-subtitle");
    if (subtitle) {
      subtitle.setAttribute("data-animation", "resources-hero-subtitle");
      subtitle.style.opacity = "0";
      subtitle.style.transform = "translateY(30px)";
    }

    // Airplane decorations with floating animations
    const airplanes = section.querySelectorAll(".airplane-icon");
    airplanes.forEach((airplane, index) => {
      airplane.setAttribute("data-animation", "resources-airplane");
      airplane.setAttribute("data-delay", index * 200);
      airplane.style.opacity = "0";
      airplane.style.transform = "translateY(40px) scale(0.8) rotate(45deg)";
    });
  }

  // 2. Resources Search and Filter Section - Interactive elements
  addResourcesSearchFilterAnimations() {
    const section = document.querySelector(".search-filter-section");
    if (!section) return;

    section.setAttribute("data-animation", "resources-search-filter-section");

    // Search wrapper
    const searchWrapper = section.querySelector(".search-wrapper");
    if (searchWrapper) {
      searchWrapper.setAttribute("data-animation", "resources-search-wrapper");
      searchWrapper.style.opacity = "0";
      searchWrapper.style.transform = "translateY(40px) scale(0.95)";
    }

    // Filter tabs with staggered animation
    const filterTabs = section.querySelectorAll(".filter-tab");
    filterTabs.forEach((tab, index) => {
      tab.setAttribute("data-animation", "resources-filter-tab");
      tab.setAttribute("data-delay", index * 100);
      tab.style.opacity = "0";
      tab.style.transform = "translateY(30px) scale(0.9)";
    });

    // Filter controls
    const filterControls = section.querySelector(".filter-controls");
    if (filterControls) {
      // Country filter dropdown
      const countryFilter = filterControls.querySelector(
        ".country-filter-dropdown"
      );
      if (countryFilter) {
        countryFilter.setAttribute(
          "data-animation",
          "resources-country-filter"
        );
        countryFilter.style.opacity = "0";
        countryFilter.style.transform = "translateY(30px)";
      }

      // Sort dropdown
      const sortDropdown = filterControls.querySelector(".sort-dropdown");
      if (sortDropdown) {
        sortDropdown.setAttribute("data-animation", "resources-sort-dropdown");
        sortDropdown.style.opacity = "0";
        sortDropdown.style.transform = "translateY(30px)";
      }

      // View toggle
      const viewToggle = filterControls.querySelector(".view-toggle");
      if (viewToggle) {
        viewToggle.setAttribute("data-animation", "resources-view-toggle");
        viewToggle.style.opacity = "0";
        viewToggle.style.transform = "translateY(30px) scale(0.9)";
      }
    }

    // Results info
    const resultsInfo = section.querySelector(".results-info");
    if (resultsInfo) {
      resultsInfo.setAttribute("data-animation", "resources-results-info");
      resultsInfo.style.opacity = "0";
      resultsInfo.style.transform = "translateY(30px)";
    }
  }

  // 3. University Cards Grid Section - Advanced staggered card reveals
  addUniversityCardsGridAnimations() {
    const section = document.querySelector(".universities-grid-section");
    if (!section) return;

    section.setAttribute("data-animation", "university-cards-grid-section");

    // University cards with intelligent grid staggering
    const universityCards = section.querySelectorAll(
      ".university-card-wrapper"
    );
    universityCards.forEach((card, index) => {
      card.setAttribute("data-animation", "university-card");
      // Calculate stagger based on grid position (3 columns)
      const row = Math.floor(index / 3);
      const col = index % 3;
      const delay = row * 200 + col * 120;
      card.setAttribute("data-delay", delay);
      card.style.opacity = "0";
      card.style.transform = "translateY(80px) scale(0.9)";

      // University image with special effects
      const universityImage = card.querySelector(
        ".university-featured-img, .university-placeholder-img"
      );
      if (universityImage) {
        universityImage.style.transform = "scale(1.1)";
      }

      // University logo badge
      const logoBadge = card.querySelector(".university-logo-badge");
      if (logoBadge) {
        logoBadge.style.transform = "scale(0.7) rotate(-10deg)";
        logoBadge.style.opacity = "0";
      }

      // Country flag badge
      const flagBadge = card.querySelector(".country-flag-badge");
      if (flagBadge) {
        flagBadge.style.transform = "scale(0.8) translateX(-20px)";
        flagBadge.style.opacity = "0";
      }

      // Duration badge
      const durationBadge = card.querySelector(".duration-badge");
      if (durationBadge) {
        durationBadge.style.transform = "scale(0.8) translateY(20px)";
        durationBadge.style.opacity = "0";
      }

      // Action button
      const actionButton = card.querySelector(".view-details-btn");
      if (actionButton) {
        actionButton.style.transform = "translateY(30px) scale(0.95)";
        actionButton.style.opacity = "0";
      }
    });

    // No universities message (fallback)
    const noUniversities = section.querySelector(".text-center.py-5");
    if (noUniversities) {
      noUniversities.setAttribute("data-animation", "no-universities-message");
      noUniversities.style.opacity = "0";
      noUniversities.style.transform = "translateY(50px) scale(0.95)";
    }
  }

  // ========== CONTACT PAGE SPECIFIC ANIMATIONS ==========

  // 1. Contact Header Section - Title and description
  addContactHeaderAnimations() {
    const section = document.querySelector(".contact-header-section");
    if (!section) return;

    section.setAttribute("data-animation", "contact-header-section");

    // Section title
    const title = section.querySelector(".section-title");
    if (title) {
      title.setAttribute("data-animation", "contact-header-title");
      title.style.opacity = "0";
      title.style.transform = "translateY(50px)";
    }

    // Section description
    const description = section.querySelector(".section-description");
    if (description) {
      description.setAttribute("data-animation", "contact-header-description");
      description.style.opacity = "0";
      description.style.transform = "translateY(30px)";
    }
  }

  // 2. Contact Info Section - Left side with decorative elements
  addContactInfoAnimations() {
    const section = document.querySelector(".contact-info-section");
    if (!section) return;

    section.setAttribute("data-animation", "contact-info-section");

    // Decorative shapes with staggered animation
    const shapes = section.querySelectorAll(".shape");
    shapes.forEach((shape, index) => {
      shape.setAttribute("data-animation", "contact-decorative-shape");
      shape.setAttribute("data-delay", index * 200);
      shape.style.opacity = "0";
      shape.style.transform = "scale(0.5) rotate(45deg)";
    });

    // Contact info text heading
    const heading = section.querySelector("h2");
    if (heading) {
      heading.setAttribute("data-animation", "contact-info-heading");
      heading.style.opacity = "0";
      heading.style.transform = "translateY(40px)";
    }

    // Contact info description
    const description = section.querySelector("p");
    if (description) {
      description.setAttribute("data-animation", "contact-info-description");
      description.style.opacity = "0";
      description.style.transform = "translateY(30px)";
    }

    // Contact details items
    const contactItems = section.querySelectorAll(".contact-item");
    contactItems.forEach((item, index) => {
      item.setAttribute("data-animation", "contact-detail-item");
      item.setAttribute("data-delay", index * 150);
      item.style.opacity = "0";
      item.style.transform = "translateX(-40px)";
    });

    // Social media icons
    const socialIcons = section.querySelectorAll(".social-icon");
    socialIcons.forEach((icon, index) => {
      icon.setAttribute("data-animation", "contact-social-icon");
      icon.setAttribute("data-delay", index * 100);
      icon.style.opacity = "0";
      icon.style.transform = "scale(0.7) translateY(20px)";
    });
  }

  // 3. Contact Form Section - Right side form
  addContactFormAnimations() {
    const section = document.querySelector(".contact-form-section");
    if (!section) return;

    section.setAttribute("data-animation", "contact-form-section");

    // Form groups with staggered animation
    const formGroups = section.querySelectorAll(".form-group");
    formGroups.forEach((group, index) => {
      group.setAttribute("data-animation", "contact-form-group");
      group.setAttribute("data-delay", index * 100);
      group.style.opacity = "0";
      group.style.transform = "translateY(30px)";
    });

    // Form rows
    const formRows = section.querySelectorAll(".form-row");
    formRows.forEach((row, index) => {
      row.setAttribute("data-animation", "contact-form-row");
      row.setAttribute("data-delay", index * 150);
      row.style.opacity = "0";
      row.style.transform = "translateY(40px)";
    });

    // Radio group
    const radioGroup = section.querySelector(".radio-group");
    if (radioGroup) {
      const radioItems = radioGroup.querySelectorAll(".radio-item");
      radioItems.forEach((item, index) => {
        item.setAttribute("data-animation", "contact-radio-item");
        item.setAttribute("data-delay", index * 80);
        item.style.opacity = "0";
        item.style.transform = "translateX(30px) scale(0.9)";
      });
    }

    // Submit button
    const submitBtn = section.querySelector(".btn-submit");
    if (submitBtn) {
      submitBtn.setAttribute("data-animation", "contact-submit-button");
      submitBtn.style.opacity = "0";
      submitBtn.style.transform = "translateY(40px) scale(0.95)";
    }
  }

  // 4. Office Locations Section - Branch offices
  addOfficeLocationsAnimations() {
    const section = document.querySelector(".office-section");
    if (!section) return;

    section.setAttribute("data-animation", "office-locations-section");

    // Section heading
    const heading = section.querySelector("h2");
    if (heading) {
      heading.setAttribute("data-animation", "office-section-heading");
      heading.style.opacity = "0";
      heading.style.transform = "translateY(40px)";
    }

    // Section description
    const description = section.querySelector("p");
    if (description) {
      description.setAttribute("data-animation", "office-section-description");
      description.style.opacity = "0";
      description.style.transform = "translateY(30px)";
    }

    // Office boxes
    const officeBoxes = section.querySelectorAll(".office-box");
    officeBoxes.forEach((box, index) => {
      box.setAttribute("data-animation", "office-box");
      box.setAttribute("data-delay", index * 200);
      box.style.opacity = "0";
      box.style.transform = "translateY(50px) scale(0.95)";
    });
  }

  // 5. Google Map Section - Map reveal
  addGoogleMapAnimations() {
    const mapSection = document.querySelector(".google-map");
    if (!mapSection) return;

    mapSection.setAttribute("data-animation", "google-map-section");

    // Map iframe
    const iframe = mapSection.querySelector("iframe");
    if (iframe) {
      iframe.setAttribute("data-animation", "google-map-iframe");
      iframe.style.opacity = "0";
      iframe.style.transform = "scale(0.95)";
    }
  }

  // 6. FAQ Section - Accordion with items
  addFaqSectionAnimations() {
    const section = document.querySelector(".faq-section");
    if (!section) return;

    section.setAttribute("data-animation", "faq-section");

    // FAQ prompt
    const prompt = section.querySelector(".faq-prompt");
    if (prompt) {
      prompt.setAttribute("data-animation", "faq-prompt");
      prompt.style.opacity = "0";
      prompt.style.transform = "translateY(30px)";
    }

    // FAQ title
    const title = section.querySelector(".faq-title");
    if (title) {
      title.setAttribute("data-animation", "faq-title");
      title.style.opacity = "0";
      title.style.transform = "translateY(40px)";
    }

    // FAQ subheading
    const subheading = section.querySelector(".faq-subheading");
    if (subheading) {
      subheading.setAttribute("data-animation", "faq-subheading");
      subheading.style.opacity = "0";
      subheading.style.transform = "translateY(30px)";
    }

    // FAQ accordion cards
    const faqCards = section.querySelectorAll(".card");
    faqCards.forEach((card, index) => {
      card.setAttribute("data-animation", "faq-accordion-card");
      card.setAttribute("data-delay", index * 150);
      card.style.opacity = "0";
      card.style.transform = "translateY(40px) scale(0.98)";
    });
  }

  // ========== GALLERY PAGE SPECIFIC ANIMATIONS ==========

  // 1. Gallery Hero Section - Title and subtitle
  addGalleryHeroAnimations() {
    const section = document.querySelector(".gallery-hero-section");
    if (!section) return;

    section.setAttribute("data-animation", "gallery-hero-section");

    // Hero title
    const title = section.querySelector(".hero-title");
    if (title) {
      title.setAttribute("data-animation", "gallery-hero-title");
      title.style.opacity = "0";
      title.style.transform = "translateY(60px) scale(0.95)";
    }

    // Hero subtitle
    const subtitle = section.querySelector(".hero-subtitle");
    if (subtitle) {
      subtitle.setAttribute("data-animation", "gallery-hero-subtitle");
      subtitle.style.opacity = "0";
      subtitle.style.transform = "translateY(40px)";
    }
  }

  // 2. Gallery Carousel Section - Horizontal gallery with controls
  addGalleryCarouselAnimations() {
    const section = document.querySelector(".horizontal-gallery-section");
    if (!section) return;

    section.setAttribute("data-animation", "gallery-carousel-section");

    // Carousel wrapper
    const carouselWrapper = section.querySelector(".gallery-carousel-wrapper");
    if (carouselWrapper) {
      carouselWrapper.setAttribute(
        "data-animation",
        "gallery-carousel-wrapper"
      );
      carouselWrapper.style.opacity = "0";
      carouselWrapper.style.transform = "scale(0.95) translateY(40px)";
    }

    // Carousel navigation buttons
    const prevBtn = section.querySelector(".carousel-control-prev");
    const nextBtn = section.querySelector(".carousel-control-next");
    if (prevBtn) {
      prevBtn.setAttribute("data-animation", "gallery-carousel-prev");
      prevBtn.style.opacity = "0";
      prevBtn.style.transform = "translateX(-30px) scale(0.8)";
    }
    if (nextBtn) {
      nextBtn.setAttribute("data-animation", "gallery-carousel-next");
      nextBtn.style.opacity = "0";
      nextBtn.style.transform = "translateX(30px) scale(0.8)";
    }

    // Carousel indicators
    const indicators = section.querySelectorAll(".carousel-indicators li");
    indicators.forEach((indicator, index) => {
      indicator.setAttribute("data-animation", "gallery-carousel-indicator");
      indicator.setAttribute("data-delay", index * 100);
      indicator.style.opacity = "0";
      indicator.style.transform = "scale(0.5) translateY(20px)";
    });
  }

  // 3. Gallery Grid Section - Additional gallery images
  addGalleryGridAnimations() {
    const section = document.querySelector(".gallery-grid-section");
    if (!section) return;

    section.setAttribute("data-animation", "gallery-grid-section");

    // Section header
    const sectionTitle = section.querySelector(".section-title");
    if (sectionTitle) {
      sectionTitle.setAttribute("data-animation", "gallery-grid-title");
      sectionTitle.style.opacity = "0";
      sectionTitle.style.transform = "translateY(50px)";
    }

    const sectionSubtitle = section.querySelector(".section-subtitle");
    if (sectionSubtitle) {
      sectionSubtitle.setAttribute("data-animation", "gallery-grid-subtitle");
      sectionSubtitle.style.opacity = "0";
      sectionSubtitle.style.transform = "translateY(30px)";
    }

    // Gallery grid items with masonry-style animation
    const gridItems = section.querySelectorAll(".gallery-grid-item");
    gridItems.forEach((item, index) => {
      item.setAttribute("data-animation", "gallery-grid-item");
      // Calculate stagger based on grid position (assuming 3 columns)
      const row = Math.floor(index / 3);
      const col = index % 3;
      const delay = row * 200 + col * 150;
      item.setAttribute("data-delay", delay);
      item.style.opacity = "0";
      item.style.transform = "translateY(80px) scale(0.8) rotateY(15deg)";

      // Grid overlay
      const overlay = item.querySelector(".grid-overlay");
      if (overlay) {
        overlay.style.opacity = "0";
        overlay.style.transform = "scale(1.1)";
      }
    });
  }

  // ========== SERVICES PAGE SPECIFIC ANIMATIONS ==========

  // 1. Services Hero Section - Clean banner image only
  addServicesHeroAnimations() {
    const section = document.querySelector(".services-hero");
    if (!section) return;

    // Hero section is now simplified with just background image
    // No text elements to animate - clean visual design approach
    section.setAttribute("data-animation", "services-hero-section");
  }

  // 2. Services Content Header - Section title and description
  addServicesContentHeaderAnimations() {
    const section = document.querySelector(".services-content");
    if (!section) return;

    section.setAttribute("data-animation", "services-content-section");

    // Section subheading
    const subheading = section.querySelector(".section-subheading");
    if (subheading) {
      subheading.setAttribute("data-animation", "services-section-subheading");
      subheading.style.opacity = "0";
      subheading.style.transform = "translateX(-40px)";
    }

    // Section heading
    const heading = section.querySelector(".section-heading");
    if (heading) {
      heading.setAttribute("data-animation", "services-section-heading");
      heading.style.opacity = "0";
      heading.style.transform = "translateY(50px)";
    }

    // Section description
    const description = section.querySelector(".section-description");
    if (description) {
      description.setAttribute(
        "data-animation",
        "services-section-description"
      );
      description.style.opacity = "0";
      description.style.transform = "translateY(30px)";
    }
  }

  // 3. Services Cards - Main service offerings
  addServicesCardsAnimations() {
    const section = document.querySelector(".services-grid");
    if (!section) return;

    section.setAttribute("data-animation", "services-cards-section");

    // Service cards with staggered animation
    const serviceCards = section.querySelectorAll(".service-card");
    serviceCards.forEach((card, index) => {
      card.setAttribute("data-animation", "services-service-card");
      card.setAttribute("data-delay", index * 250);
      card.style.opacity = "0";
      card.style.transform = "translateY(80px) scale(0.9) rotateX(15deg)";

      // Service image
      const serviceImage = card.querySelector(".service-image");
      if (serviceImage) {
        serviceImage.style.transform = "scale(1.1)";
        serviceImage.style.opacity = "0.8";
      }

      // Service title
      const serviceTitle = card.querySelector(".service-title");
      if (serviceTitle) {
        serviceTitle.style.transform = "translateY(30px)";
        serviceTitle.style.opacity = "0";
      }

      // Service description
      const serviceDescription = card.querySelector(".service-description");
      if (serviceDescription) {
        serviceDescription.style.transform = "translateY(20px)";
        serviceDescription.style.opacity = "0";
      }
    });
  }

  // 4. Additional Services - Support services grid
  addAdditionalServicesAnimations() {
    const section = document.querySelector(".additional-services");
    if (!section) return;

    section.setAttribute("data-animation", "additional-services-section");

    // Additional services title
    const title = section.querySelector(".additional-title");
    if (title) {
      title.setAttribute("data-animation", "additional-services-title");
      title.style.opacity = "0";
      title.style.transform = "translateY(40px)";
    }

    // Additional service items
    const additionalItems = section.querySelectorAll(".additional-item");
    additionalItems.forEach((item, index) => {
      item.setAttribute("data-animation", "additional-service-item");
      // Calculate stagger based on grid position (assuming 3 columns)
      const row = Math.floor(index / 3);
      const col = index % 3;
      const delay = row * 200 + col * 120;
      item.setAttribute("data-delay", delay);
      item.style.opacity = "0";
      item.style.transform = "translateY(50px) scale(0.9)";

      // Icon animation
      const icon = item.querySelector("i");
      if (icon) {
        icon.style.transform = "scale(0.7) rotateY(180deg)";
        icon.style.opacity = "0";
      }
    });
  }

  // 5. Services CTA Section - Call to action
  addServicesCtaAnimations() {
    const section = document.querySelector(".services-cta");
    if (!section) return;

    section.setAttribute("data-animation", "services-cta-section");

    // CTA content
    const ctaContent = section.querySelector(".cta-content");
    if (ctaContent) {
      ctaContent.setAttribute("data-animation", "services-cta-content");
      ctaContent.style.opacity = "0";
      ctaContent.style.transform = "translateY(50px) scale(0.95)";
    }

    // CTA title
    const ctaTitle = section.querySelector(".cta-content h3");
    if (ctaTitle) {
      ctaTitle.setAttribute("data-animation", "services-cta-title");
      ctaTitle.style.opacity = "0";
      ctaTitle.style.transform = "translateY(40px)";
    }

    // CTA description
    const ctaDesc = section.querySelector(".cta-content p");
    if (ctaDesc) {
      ctaDesc.setAttribute("data-animation", "services-cta-description");
      ctaDesc.style.opacity = "0";
      ctaDesc.style.transform = "translateY(30px)";
    }

    // CTA buttons
    const ctaButtons = section.querySelectorAll(".cta-buttons .btn");
    ctaButtons.forEach((button, index) => {
      button.setAttribute("data-animation", "services-cta-button");
      button.setAttribute("data-delay", index * 200);
      button.style.opacity = "0";
      button.style.transform = "translateY(40px) scale(0.9)";
    });
  }

  // Intersection Observer Handler
  handleIntersection(entries) {
    entries.forEach((entry) => {
      if (entry.isIntersecting && entry.intersectionRatio >= 0.1) {
        this.animateElement(entry.target);
      }
    });
  }

  // Start observing all animated elements
  startObserving() {
    const animatedElements = document.querySelectorAll("[data-animation]");
    animatedElements.forEach((element) => {
      this.observer.observe(element);
    });
  }

  // Animate individual elements
  animateElement(element) {
    if (this.animatedElements.has(element)) return;

    this.animatedElements.add(element);
    const animationType = element.getAttribute("data-animation");
    const delay = parseInt(element.getAttribute("data-delay")) || 0;

    setTimeout(() => {
      element.classList.add("animate-in");
      this.triggerSpecificAnimation(element, animationType);
    }, delay);
  }

  // Trigger specific animations based on type
  triggerSpecificAnimation(element, type) {
    switch (type) {
      // Index page animations
      case "partner-item":
        this.animatePartnerItem(element);
        break;
      case "title-reveal":
        this.animateTitleReveal(element);
        break;
      case "highlight-card":
        this.animateHighlightCard(element);
        break;
      case "destination-item":
        this.animateDestinationItem(element);
        break;
      case "service-slide-right":
        this.animateServiceSlideRight(element);
        break;
      case "service-slide-left":
        this.animateServiceSlideLeft(element);
        break;
      case "logo-matrix":
        this.animateLogoMatrix(element);
        break;
      case "benefit-card-flip":
        this.animateBenefitCardFlip(element);
        break;
      case "process-step":
        this.animateProcessStep(element);
        break;
      case "process-arrow":
        this.animateProcessArrow(element);
        break;
      case "stat-counter":
        this.animateStatCounter(element);
        break;
      case "blog-card":
        this.animateBlogCard(element);
        break;

      // About page animations
      case "hero-tagline":
        this.animateHeroTagline(element);
        break;
      case "hero-title":
        this.animateHeroTitle(element);
        break;
      case "hero-image":
        this.animateHeroImage(element);
        break;
      case "hero-stat-counter":
        this.animateHeroStatCounter(element);
        break;
      case "story-content":
        this.animateStoryContent(element);
        break;
      case "mvv-card":
        this.animateMvvCard(element);
        break;
      case "team-member":
        this.animateTeamMember(element);
        break;
      case "value-card":
        this.animateValueCard(element);
        break;
      case "culture-card":
        this.animateCultureCard(element);
        break;
      case "cta-image":
        this.animateCtaImage(element);
        break;
      case "cta-content":
        this.animateCtaContent(element);
        break;
      case "cta-button":
        this.animateCtaButton(element);
        break;
      case "media-logo":
        this.animateMediaLogo(element);
        break;

      // Destinations page animations
      case "destinations-breadcrumb":
        this.animateDestinationsBreadcrumb(element);
        break;
      case "destinations-hero-title":
        this.animateDestinationsHeroTitle(element);
        break;
      case "search-header":
        this.animateSearchHeader(element);
        break;
      case "search-box":
        this.animateSearchBox(element);
        break;
      case "search-stats":
        this.animateSearchStats(element);
        break;
      case "search-filter-tag":
        this.animateSearchFilterTag(element);
        break;
      case "country-card":
        this.animateCountryCard(element);
        break;
      case "no-countries-message":
        this.animateNoCountriesMessage(element);
        break;
      case "destinations-cta-title":
        this.animateDestinationsCtaTitle(element);
        break;
      case "destinations-cta-description":
        this.animateDestinationsCtaDescription(element);
        break;
      case "destinations-cta-button":
        this.animateDestinationsCtaButton(element);
        break;

      // Resources page animations
      case "resources-breadcrumb":
        this.animateResourcesBreadcrumb(element);
        break;
      case "resources-hero-title":
        this.animateResourcesHeroTitle(element);
        break;
      case "resources-hero-subtitle":
        this.animateResourcesHeroSubtitle(element);
        break;
      case "resources-airplane":
        this.animateResourcesAirplane(element);
        break;
      case "resources-search-wrapper":
        this.animateResourcesSearchWrapper(element);
        break;
      case "resources-filter-tab":
        this.animateResourcesFilterTab(element);
        break;
      case "resources-country-filter":
        this.animateResourcesCountryFilter(element);
        break;
      case "resources-sort-dropdown":
        this.animateResourcesSortDropdown(element);
        break;
      case "resources-view-toggle":
        this.animateResourcesViewToggle(element);
        break;
      case "resources-results-info":
        this.animateResourcesResultsInfo(element);
        break;
      case "university-card":
        this.animateUniversityCard(element);
        break;
      case "no-universities-message":
        this.animateNoUniversitiesMessage(element);
        break;

      // Contact page animations
      case "contact-header-title":
        this.animateContactHeaderTitle(element);
        break;
      case "contact-header-description":
        this.animateContactHeaderDescription(element);
        break;
      case "contact-decorative-shape":
        this.animateContactDecorativeShape(element);
        break;
      case "contact-info-heading":
        this.animateContactInfoHeading(element);
        break;
      case "contact-info-description":
        this.animateContactInfoDescription(element);
        break;
      case "contact-detail-item":
        this.animateContactDetailItem(element);
        break;
      case "contact-social-icon":
        this.animateContactSocialIcon(element);
        break;
      case "contact-form-group":
        this.animateContactFormGroup(element);
        break;
      case "contact-form-row":
        this.animateContactFormRow(element);
        break;
      case "contact-radio-item":
        this.animateContactRadioItem(element);
        break;
      case "contact-submit-button":
        this.animateContactSubmitButton(element);
        break;
      case "office-section-heading":
        this.animateOfficeSectionHeading(element);
        break;
      case "office-section-description":
        this.animateOfficeSectionDescription(element);
        break;
      case "office-box":
        this.animateOfficeBox(element);
        break;
      case "google-map-iframe":
        this.animateGoogleMapIframe(element);
        break;
      case "faq-prompt":
        this.animateFaqPrompt(element);
        break;
      case "faq-title":
        this.animateFaqTitle(element);
        break;
      case "faq-subheading":
        this.animateFaqSubheading(element);
        break;
      case "faq-accordion-card":
        this.animateFaqAccordionCard(element);
        break;

      // Gallery page animations
      case "gallery-hero-title":
        this.animateGalleryHeroTitle(element);
        break;
      case "gallery-hero-subtitle":
        this.animateGalleryHeroSubtitle(element);
        break;
      case "gallery-carousel-wrapper":
        this.animateGalleryCarouselWrapper(element);
        break;
      case "gallery-carousel-prev":
        this.animateGalleryCarouselPrev(element);
        break;
      case "gallery-carousel-next":
        this.animateGalleryCarouselNext(element);
        break;
      case "gallery-carousel-indicator":
        this.animateGalleryCarouselIndicator(element);
        break;
      case "gallery-grid-title":
        this.animateGalleryGridTitle(element);
        break;
      case "gallery-grid-subtitle":
        this.animateGalleryGridSubtitle(element);
        break;
      case "gallery-grid-item":
        this.animateGalleryGridItem(element);
        break;

      // Services page animations
      case "services-section-subheading":
        this.animateServicesSectionSubheading(element);
        break;
      case "services-section-heading":
        this.animateServicesSectionHeading(element);
        break;
      case "services-section-description":
        this.animateServicesSectionDescription(element);
        break;
      case "services-service-card":
        this.animateServicesServiceCard(element);
        break;
      case "additional-services-title":
        this.animateAdditionalServicesTitle(element);
        break;
      case "additional-service-item":
        this.animateAdditionalServiceItem(element);
        break;
      case "services-cta-content":
        this.animateServicesCtaContent(element);
        break;
      case "services-cta-title":
        this.animateServicesCtaTitle(element);
        break;
      case "services-cta-description":
        this.animateServicesCtaDescription(element);
        break;
      case "services-cta-button":
        this.animateServicesCtaButton(element);
        break;

      default:
        this.animateDefault(element);
    }
  }

  // Individual animation methods
  animatePartnerItem(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateTitleReveal(element) {
    element.style.transition = "all 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateHighlightCard(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  animateDestinationItem(element) {
    element.style.transition =
      "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  animateServiceSlideRight(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateX(0)";
  }

  animateServiceSlideLeft(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateX(0)";
  }

  animateLogoMatrix(element) {
    element.style.transition =
      "all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "rotateY(0) scale(1)";
  }

  animateBenefitCardFlip(element) {
    element.style.transition =
      "all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "rotateX(0)";
  }

  animateProcessStep(element) {
    element.style.transition = "all 1s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  animateProcessArrow(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "scaleX(1)";
  }

  animateStatCounter(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";

    // Add counter animation
    const numberElement = element.querySelector(".stat-number");
    if (numberElement) {
      this.animateCounter(numberElement);
    }
  }

  animateBlogCard(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) rotateX(0)";
  }

  animateDefault(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  // ========== ABOUT PAGE ANIMATION METHODS ==========

  animateHeroTagline(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateHeroTitle(element) {
    element.style.transition = "all 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateHeroImage(element) {
    element.style.transition = "all 1s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "scale(1) translateY(0)";
  }

  animateHeroStatCounter(element) {
    element.style.transition =
      "all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";

    // Add counter animation for stats
    const numberElement = element.querySelector(".stat-number");
    if (numberElement) {
      this.animateCounter(numberElement);
    }
  }

  animateStoryContent(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";

    const textContent = element.querySelector(".story-text");
    const imageContent = element.querySelector(".story-image");

    if (textContent) {
      textContent.style.transition =
        "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
      textContent.style.opacity = "1";
      textContent.style.transform = "translateX(0)";
    }

    if (imageContent) {
      imageContent.style.transition =
        "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
      imageContent.style.opacity = "1";
      imageContent.style.transform = "translateX(0)";
    }
  }

  animateMvvCard(element) {
    element.style.transition =
      "all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) rotateX(0)";
  }

  animateTeamMember(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";

    // Special image animation for team members
    const memberImage = element.querySelector(".member-image");
    if (memberImage) {
      memberImage.style.transition =
        "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
      memberImage.style.transform = "scale(1)";
    }
  }

  animateValueCard(element) {
    element.style.transition =
      "all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) rotateY(0)";
  }

  animateCultureCard(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  animateCtaImage(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateX(0) scale(1)";
  }

  animateCtaContent(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateX(0)";
  }

  animateCtaButton(element) {
    element.style.transition =
      "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateMediaLogo(element) {
    element.style.transition =
      "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  // ========== DESTINATIONS PAGE ANIMATION METHODS ==========

  animateDestinationsBreadcrumb(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateDestinationsHeroTitle(element) {
    element.style.transition = "all 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateSearchHeader(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateSearchBox(element) {
    element.style.transition = "all 1s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  animateSearchStats(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateSearchFilterTag(element) {
    element.style.transition =
      "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  animateCountryCard(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";

    // Special flag animation
    const flag = element.querySelector(".country-flag");
    if (flag) {
      flag.style.transition = "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
      flag.style.transform = "scale(1) rotate(0deg)";
    }

    // Animate action buttons with stagger
    const actions = element.querySelectorAll(".country-actions .btn");
    actions.forEach((btn, index) => {
      setTimeout(() => {
        btn.style.transition =
          "all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
        btn.style.opacity = "1";
        btn.style.transform = "translateY(0)";
      }, 200 + index * 100);
    });
  }

  animateNoCountriesMessage(element) {
    element.style.transition = "all 1s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  animateDestinationsCtaTitle(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateDestinationsCtaDescription(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateDestinationsCtaButton(element) {
    element.style.transition =
      "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  // ========== RESOURCES PAGE ANIMATION METHODS ==========

  animateResourcesBreadcrumb(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateResourcesHeroTitle(element) {
    element.style.transition = "all 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateResourcesHeroSubtitle(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateResourcesAirplane(element) {
    element.style.transition = "all 1.5s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1) rotate(45deg)";

    // Add floating animation after initial reveal
    setTimeout(() => {
      element.style.animation = "float 6s ease-in-out infinite";
    }, 800);
  }

  animateResourcesSearchWrapper(element) {
    element.style.transition = "all 1s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  animateResourcesFilterTab(element) {
    element.style.transition =
      "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  animateResourcesCountryFilter(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateResourcesSortDropdown(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateResourcesViewToggle(element) {
    element.style.transition =
      "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  animateResourcesResultsInfo(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateUniversityCard(element) {
    element.style.transition = "all 1s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";

    // Animate university image back to normal
    const universityImage = element.querySelector(
      ".university-featured-img, .university-placeholder-img"
    );
    if (universityImage) {
      universityImage.style.transition =
        "all 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
      universityImage.style.transform = "scale(1)";
    }

    // Animate badges with individual timing
    const logoBadge = element.querySelector(".university-logo-badge");
    if (logoBadge) {
      setTimeout(() => {
        logoBadge.style.transition =
          "all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
        logoBadge.style.transform = "scale(1) rotate(0deg)";
        logoBadge.style.opacity = "1";
      }, 300);
    }

    const flagBadge = element.querySelector(".country-flag-badge");
    if (flagBadge) {
      setTimeout(() => {
        flagBadge.style.transition =
          "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
        flagBadge.style.transform = "scale(1) translateX(0)";
        flagBadge.style.opacity = "1";
      }, 200);
    }

    const durationBadge = element.querySelector(".duration-badge");
    if (durationBadge) {
      setTimeout(() => {
        durationBadge.style.transition =
          "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
        durationBadge.style.transform = "scale(1) translateY(0)";
        durationBadge.style.opacity = "1";
      }, 400);
    }

    const actionButton = element.querySelector(".view-details-btn");
    if (actionButton) {
      setTimeout(() => {
        actionButton.style.transition =
          "all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
        actionButton.style.transform = "translateY(0) scale(1)";
        actionButton.style.opacity = "1";
      }, 500);
    }
  }

  animateNoUniversitiesMessage(element) {
    element.style.transition = "all 1s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  // ========== CONTACT PAGE ANIMATION METHODS ==========

  animateContactHeaderTitle(element) {
    element.style.transition = "all 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateContactHeaderDescription(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateContactDecorativeShape(element) {
    element.style.transition = "all 1s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "scale(1) rotate(45deg)";

    // Add floating animation after initial reveal
    setTimeout(() => {
      element.style.animation = "contactShapeFloat 4s ease-in-out infinite";
    }, 600);
  }

  animateContactInfoHeading(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateContactInfoDescription(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateContactDetailItem(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateX(0)";
  }

  animateContactSocialIcon(element) {
    element.style.transition =
      "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "scale(1) translateY(0)";

    // Add hover-ready bounce effect
    setTimeout(() => {
      element.style.transition =
        "all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    }, 600);
  }

  animateContactFormGroup(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateContactFormRow(element) {
    element.style.transition = "all 0.9s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateContactRadioItem(element) {
    element.style.transition =
      "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateX(0) scale(1)";
  }

  animateContactSubmitButton(element) {
    element.style.transition =
      "all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";

    // Add subtle glow effect after animation
    setTimeout(() => {
      element.style.boxShadow = "0 4px 20px rgba(0, 123, 255, 0.3)";
    }, 800);
  }

  animateOfficeSectionHeading(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateOfficeSectionDescription(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateOfficeBox(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";

    // Add hover-ready effect
    setTimeout(() => {
      element.style.transition =
        "all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    }, 800);
  }

  animateGoogleMapIframe(element) {
    element.style.transition = "all 1.2s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "scale(1)";
  }

  animateFaqPrompt(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateFaqTitle(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateFaqSubheading(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateFaqAccordionCard(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";

    // Add subtle shadow effect after animation
    setTimeout(() => {
      element.style.boxShadow = "0 2px 10px rgba(0, 0, 0, 0.08)";
    }, 800);
  }

  // ========== GALLERY PAGE ANIMATION METHODS ==========

  animateGalleryHeroTitle(element) {
    element.style.transition = "all 1.2s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  animateGalleryHeroSubtitle(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateGalleryCarouselWrapper(element) {
    element.style.transition = "all 1.2s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "scale(1) translateY(0)";

    // Add subtle glow effect after animation
    setTimeout(() => {
      element.style.boxShadow = "0 10px 40px rgba(0, 0, 0, 0.1)";
    }, 1200);
  }

  animateGalleryCarouselPrev(element) {
    element.style.transition =
      "all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateX(0) scale(1)";

    // Add hover-ready effect
    setTimeout(() => {
      element.style.transition =
        "all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    }, 800);
  }

  animateGalleryCarouselNext(element) {
    element.style.transition =
      "all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateX(0) scale(1)";

    // Add hover-ready effect
    setTimeout(() => {
      element.style.transition =
        "all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    }, 800);
  }

  animateGalleryCarouselIndicator(element) {
    element.style.transition =
      "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "scale(1) translateY(0)";

    // Add pulse effect for active indicator
    setTimeout(() => {
      if (element.classList.contains("active")) {
        element.style.animation =
          "galleryIndicatorPulse 2s ease-in-out infinite";
      }
    }, 600);
  }

  animateGalleryGridTitle(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateGalleryGridSubtitle(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateGalleryGridItem(element) {
    element.style.transition = "all 1s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1) rotateY(0deg)";

    // Animate overlay
    const overlay = element.querySelector(".grid-overlay");
    if (overlay) {
      setTimeout(() => {
        overlay.style.transition =
          "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
        overlay.style.opacity = "1";
        overlay.style.transform = "scale(1)";
      }, 200);
    }

    // Add hover-ready effects
    setTimeout(() => {
      element.style.transition =
        "all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
      // Add subtle shadow for depth
      element.style.boxShadow = "0 8px 25px rgba(0, 0, 0, 0.15)";
    }, 1000);
  }

  // ========== SERVICES PAGE ANIMATION METHODS ==========

  animateServicesSectionSubheading(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateX(0)";
  }

  animateServicesSectionHeading(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateServicesSectionDescription(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateServicesServiceCard(element) {
    element.style.transition = "all 1s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1) rotateX(0deg)";

    // Animate service image
    const serviceImage = element.querySelector(".service-image");
    if (serviceImage) {
      setTimeout(() => {
        serviceImage.style.transition =
          "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
        serviceImage.style.transform = "scale(1)";
        serviceImage.style.opacity = "1";
      }, 200);
    }

    // Animate service title
    const serviceTitle = element.querySelector(".service-title");
    if (serviceTitle) {
      setTimeout(() => {
        serviceTitle.style.transition =
          "all 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
        serviceTitle.style.transform = "translateY(0)";
        serviceTitle.style.opacity = "1";
      }, 400);
    }

    // Animate service description
    const serviceDescription = element.querySelector(".service-description");
    if (serviceDescription) {
      setTimeout(() => {
        serviceDescription.style.transition =
          "all 0.6s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
        serviceDescription.style.transform = "translateY(0)";
        serviceDescription.style.opacity = "1";
      }, 600);
    }

    // Add hover-ready effects
    setTimeout(() => {
      element.style.transition =
        "all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    }, 1000);
  }

  animateAdditionalServicesTitle(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateAdditionalServiceItem(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";

    // Animate icon with special effect
    const icon = element.querySelector("i");
    if (icon) {
      setTimeout(() => {
        icon.style.transition =
          "all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
        icon.style.transform = "scale(1) rotateY(0deg)";
        icon.style.opacity = "1";
      }, 200);

      // Add icon bounce effect after reveal
      setTimeout(() => {
        icon.style.animation = "serviceIconBounce 1s ease-out";
      }, 800);
    }

    // Add hover-ready effects
    setTimeout(() => {
      element.style.transition =
        "all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    }, 800);
  }

  animateServicesCtaContent(element) {
    element.style.transition = "all 1s cubic-bezier(0.34, 1.56, 0.64, 1)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";
  }

  animateServicesCtaTitle(element) {
    element.style.transition = "all 1s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateServicesCtaDescription(element) {
    element.style.transition = "all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0)";
  }

  animateServicesCtaButton(element) {
    element.style.transition =
      "all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55)";
    element.style.opacity = "1";
    element.style.transform = "translateY(0) scale(1)";

    // Add subtle glow effect after animation
    setTimeout(() => {
      if (element.classList.contains("btn-primary")) {
        element.style.boxShadow = "0 4px 20px rgba(255, 255, 255, 0.2)";
      } else {
        element.style.boxShadow = "0 4px 20px rgba(255, 255, 255, 0.1)";
      }
    }, 800);
  }

  // Counter animation for stats
  animateCounter(element) {
    const target = parseInt(element.textContent.replace(/\D/g, ""));
    const duration = 2000;
    const increment = target / (duration / 16);
    let current = 0;

    const timer = setInterval(() => {
      current += increment;
      if (current >= target) {
        element.textContent =
          target + (element.textContent.includes("+") ? "+" : "");
        clearInterval(timer);
      } else {
        element.textContent =
          Math.floor(current) + (element.textContent.includes("+") ? "+" : "");
      }
    }, 16);
  }
}

// Initialize animations when DOM is ready
document.addEventListener("DOMContentLoaded", () => {
  // Add floating animation CSS for airplanes and contact shapes
  if (!document.getElementById("floating-animation-css")) {
    const style = document.createElement("style");
    style.id = "floating-animation-css";
    style.textContent = `
      @keyframes float {
        0%, 100% {
          transform: translateY(0) scale(1) rotate(45deg);
        }
        50% {
          transform: translateY(-10px) scale(1) rotate(45deg);
        }
      }
      
      @keyframes contactShapeFloat {
        0%, 100% {
          transform: scale(1) rotate(45deg) translateY(0);
        }
        33% {
          transform: scale(1.05) rotate(50deg) translateY(-8px);
        }
        66% {
          transform: scale(0.95) rotate(40deg) translateY(5px);
        }
      }
      
      @keyframes galleryIndicatorPulse {
        0%, 100% {
          transform: scale(1) translateY(0);
          opacity: 1;
        }
        50% {
          transform: scale(1.2) translateY(0);
          opacity: 0.8;
        }
      }
      
      @keyframes serviceIconBounce {
        0%, 100% {
          transform: scale(1) rotateY(0deg);
        }
        25% {
          transform: scale(1.1) rotateY(-5deg);
        }
        50% {
          transform: scale(1.2) rotateY(5deg);
        }
        75% {
          transform: scale(1.05) rotateY(-2deg);
        }
      }
    `;
    document.head.appendChild(style);
  }

  new ScrollAnimations();
});

// Export for external use
window.ScrollAnimations = ScrollAnimations;
