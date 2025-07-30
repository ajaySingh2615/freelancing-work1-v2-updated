# MedStudy Global - MBBS Abroad Consultancy Website

This is the codebase for MedStudy Global, a consultancy website for medical education abroad.

## CSS Structure

The CSS has been organized into separate files for better maintainability:

1. **variables.css** - Contains all CSS variables including colors, spacing, and transitions
2. **header.css** - Contains styles for the header, navigation, and mobile menu
3. **footer.css** - Contains styles for the footer and floating elements (back-to-top, WhatsApp button, etc.)
4. **style.css** - Main stylesheet that imports all other CSS files and contains general styles

### How CSS Files are Organized

- **variables.css**: Contains all the design system variables like colors, typography, spacing, etc.
- **header.css**: Contains all styles related to the header area, including:
  - Top header (contact bar)
  - Main navigation
  - Mobile menu
  - Search popup
  - Sticky header behavior
  
- **footer.css**: Contains all styles related to the footer area, including:
  - Footer widgets
  - Copyright section
  - Floating elements (back-to-top button, WhatsApp button, consultation button)
  
- **style.css**: Contains general styles and imports all other CSS files:
  - General typography
  - Button styles
  - Hero section
  - Feature tabs
  - Section headings
  - Responsive styles for content areas

## Design System

The design system follows the MedStudy Global Design System with the following key colors:

- Primary Color: #003585 (Dark Blue)
- Accent Color: #FEBA02 (Yellow/Gold)
- Secondary Color: #149DE1 (Light Blue)

All measurements use rem units for better responsiveness.

## JavaScript Features

- Sticky header
- Mobile menu toggle
- Search popup
- Back to top button
- Smooth scrolling
- Animation on scroll
- Counter animations

## Responsive Design

The website is fully responsive with breakpoints at:
- 1200px (75rem) - Large desktops
- 992px (62rem) - Small desktops and tablets
- 768px (48rem) - Tablets and large phones
- 576px (36rem) - Mobile phones

## Project Overview

The website is an educational consultancy platform that helps students pursue MBBS (medical) education in Russia. The clone includes the following pages:

- Home page
- About page
- Contact page
- Form processing capabilities

## Technology Stack

- **HTML5**: For the website structure
- **CSS3**: For custom styling
- **Bootstrap 4.6**: For responsive layout and UI components
- **PHP**: For server-side processing and form handling
- **JavaScript**: For interactive elements and form validation
- **jQuery**: For DOM manipulation and AJAX form submissions

## Features

- Responsive design that works on all devices (mobile, tablet, desktop)
- Clean and modern UI matching the original website's aesthetics
- Contact and inquiry forms with validation
- Dynamic content sections
- Interactive elements like sliders, counters, and popups

## Project Structure

```
Project-1/
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── images/
│   │   ├── icons/
│   │   ├── media/
│   │   └── universities/
│   └── js/
│       └── main.js
├── includes/
│   ├── header.php
│   └── footer.php
├── index.php
├── about.php
├── contact.php
├── process-form.php
├── process-contact.php
└── README.md
```

## Setup and Installation

1. Clone the repository or download the zip file
2. Place the files in your local server directory (e.g., htdocs for XAMPP, www for WAMP)
3. Start your local server
4. Access the website at http://localhost/Project-1/

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Opera (latest)

## Future Enhancements

- Adding more pages (Universities, Countries, Resources, etc.)
- Integrating a database for storing form submissions
- Adding a blog/news management system
- Implementing user accounts and authentication
- Adding a dashboard for admin management

## Credits

This project was created by [Your Name] as a clone of the [Rus Education website](https://www.ruseducation.in/). It's intended for educational and demonstration purposes only. 