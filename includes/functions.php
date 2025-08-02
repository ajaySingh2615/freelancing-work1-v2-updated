<?php
/**
 * Helper Functions for Universities System
 * MedStudy Global - Universities & Countries Management
 */

// Include database connection
require_once __DIR__ . '/../config/database.php';

/**
 * Get countries by region with optional filtering
 * @param string|null $region - Optional region filter
 * @return array - Array of countries
 */
function getCountriesByRegion($region = null) {
    global $db;
    
    try {
        if ($region) {
            $stmt = $db->prepare("
                SELECT * FROM countries 
                WHERE region = ? AND is_active = 1 
                ORDER BY sort_order ASC, name ASC
            ");
            $stmt->execute([$region]);
        } else {
            $stmt = $db->prepare("
                SELECT * FROM countries 
                WHERE is_active = 1 
                ORDER BY sort_order ASC, name ASC
            ");
            $stmt->execute();
        }
        
        $countries = $stmt->fetchAll();
        
        // Decode JSON categories for each country
        foreach ($countries as &$country) {
            $country['categories'] = json_decode($country['categories'], true) ?? [];
        }
        
        return $countries;
        
    } catch (PDOException $e) {
        error_log("Error fetching countries: " . $e->getMessage());
        return [];
    }
}

/**
 * Get universities by country ID
 * @param int $country_id - Country ID
 * @return array - Array of universities
 */
function getUniversitiesByCountry($country_id) {
    global $db;
    
    try {
        $stmt = $db->prepare("
            SELECT u.*, c.name as country_name, c.flag_code 
            FROM universities u 
            LEFT JOIN countries c ON u.country_id = c.id 
            WHERE u.country_id = ? AND u.is_active = 1 
            ORDER BY u.name ASC
        ");
        $stmt->execute([$country_id]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error fetching universities: " . $e->getMessage());
        return [];
    }
}

/**
 * Get university by slug with country information
 * @param string $slug - University slug
 * @return array|null - University data or null
 */
function getUniversityBySlug($slug) {
    global $db;
    
    try {
        $stmt = $db->prepare("
            SELECT u.*, c.name as country_name, c.flag_code, c.region 
            FROM universities u 
            LEFT JOIN countries c ON u.country_id = c.id 
            WHERE u.slug = ? AND u.is_active = 1 
            LIMIT 1
        ");
        $stmt->execute([$slug]);
        
        return $stmt->fetch();
        
    } catch (PDOException $e) {
        error_log("Error fetching university: " . $e->getMessage());
        return null;
    }
}

/**
 * Get university images by university ID
 * @param int $university_id - University ID
 * @return array - Array of image URLs
 */
function getUniversityImages($university_id) {
    global $db;
    
    try {
        $stmt = $db->prepare("
            SELECT * FROM university_images 
            WHERE university_id = ? 
            ORDER BY created_at ASC
        ");
        $stmt->execute([$university_id]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error fetching university images: " . $e->getMessage());
        return [];
    }
}

/**
 * Generate unique slug for university
 * @param string $name - University name
 * @param string $country_name - Country name
 * @return string - Generated slug
 */
function generateUniversitySlug($name, $country_name) {
    global $db;
    
    // Create base slug
    $baseSlug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name . '-' . $country_name)));
    $baseSlug = preg_replace('/-+/', '-', $baseSlug);
    $baseSlug = trim($baseSlug, '-');
    
    // Check if slug exists
    $counter = 0;
    $slug = $baseSlug;
    
    while (true) {
        try {
            $stmt = $db->prepare("SELECT id FROM universities WHERE slug = ? LIMIT 1");
            $stmt->execute([$slug]);
            
            if (!$stmt->fetch()) {
                break; // Slug is unique
            }
            
            $counter++;
            $slug = $baseSlug . '-' . $counter;
            
        } catch (PDOException $e) {
            error_log("Error checking slug: " . $e->getMessage());
            return $baseSlug . '-' . time(); // Fallback to timestamp
        }
    }
    
    return $slug;
}

/**
 * Get country by slug
 * @param string $slug - Country slug
 * @return array|null - Country data or null
 */
function getCountryBySlug($slug) {
    global $db;
    
    try {
        $stmt = $db->prepare("
            SELECT * FROM countries 
            WHERE slug = ? AND is_active = 1 
            LIMIT 1
        ");
        $stmt->execute([$slug]);
        
        $country = $stmt->fetch();
        
        if ($country) {
            // Decode JSON categories
            $country['categories'] = json_decode($country['categories'], true) ?? [];
        }
        
        return $country;
        
    } catch (PDOException $e) {
        error_log("Error fetching country: " . $e->getMessage());
        return null;
    }
}

/**
 * Get country by ID
 * @param int $country_id - Country ID
 * @return array|null - Country data or null
 */
function getCountryById($country_id) {
    global $db;
    
    try {
        $stmt = $db->prepare("
            SELECT * FROM countries 
            WHERE id = ? AND is_active = 1 
            LIMIT 1
        ");
        $stmt->execute([$country_id]);
        
        $country = $stmt->fetch();
        
        if ($country) {
            // Decode JSON categories
            $country['categories'] = json_decode($country['categories'], true) ?? [];
        }
        
        return $country;
        
    } catch (PDOException $e) {
        error_log("Error fetching country by ID: " . $e->getMessage());
        return null;
    }
}

/**
 * Get total universities count for a country
 * @param int $country_id - Country ID
 * @return int - Count of active universities
 */
function getUniversitiesCountByCountry($country_id) {
    global $db;
    
    try {
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM universities 
            WHERE country_id = ? AND is_active = 1
        ");
        $stmt->execute([$country_id]);
        
        $result = $stmt->fetch();
        return (int) $result['count'];
        
    } catch (PDOException $e) {
        error_log("Error counting universities: " . $e->getMessage());
        return 0;
    }
}

/**
 * Search universities by name across all countries
 * @param string $query - Search query
 * @param int|null $limit - Optional limit
 * @return array - Array of matching universities
 */
function searchUniversities($query, $limit = null) {
    global $db;
    
    try {
        $searchTerm = '%' . $query . '%';
        $sql = "
            SELECT u.*, c.name as country_name, c.flag_code 
            FROM universities u 
            LEFT JOIN countries c ON u.country_id = c.id 
            WHERE (u.name LIKE ? OR u.location LIKE ?) 
            AND u.is_active = 1 
            ORDER BY u.name ASC
        ";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error searching universities: " . $e->getMessage());
        return [];
    }
}

/**
 * Get featured universities across all countries
 * @param int $limit - Number of featured universities to return
 * @return array - Array of featured universities
 */
function getFeaturedUniversities($limit = 6) {
    global $db;
    
    try {
        $stmt = $db->prepare("
            SELECT u.*, c.name as country_name, c.flag_code 
            FROM universities u 
            LEFT JOIN countries c ON u.country_id = c.id 
            WHERE u.is_active = 1 
            ORDER BY RAND() 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll();
        
    } catch (PDOException $e) {
        error_log("Error fetching featured universities: " . $e->getMessage());
        return [];
    }
}

/**
 * Format currency amount with proper symbol
 * @param float $amount - Amount to format
 * @param string $currency - Currency code (default: USD)
 * @return string - Formatted currency string
 */
function formatCurrency($amount, $currency = 'USD') {
    $symbols = [
        'USD' => '$',
        'EUR' => '€',
        'GBP' => '£',
        'RUB' => '₽'
    ];
    
    $symbol = $symbols[$currency] ?? '$';
    return $symbol . number_format($amount, 0);
}

/**
 * Sanitize and validate email
 * @param string $email - Email to validate
 * @return string|false - Sanitized email or false if invalid
 */
function validateEmail($email) {
    $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Sanitize input data
 * @param string $input - Input to sanitize
 * @return string - Sanitized input
 */
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

/**
 * Generate breadcrumb navigation
 * @param array $items - Array of breadcrumb items [['title' => 'Home', 'url' => '/']]
 * @return string - HTML breadcrumb
 */
function generateBreadcrumb($items) {
    if (empty($items)) {
        return '';
    }
    
    $breadcrumb = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
    
    $total = count($items);
    foreach ($items as $index => $item) {
        $isLast = ($index === $total - 1);
        
        if ($isLast) {
            $breadcrumb .= '<li class="breadcrumb-item active" aria-current="page">' . htmlspecialchars($item['title']) . '</li>';
        } else {
            $breadcrumb .= '<li class="breadcrumb-item"><a href="' . htmlspecialchars($item['url']) . '">' . htmlspecialchars($item['title']) . '</a></li>';
        }
    }
    
    $breadcrumb .= '</ol></nav>';
    return $breadcrumb;
}

/**
 * Log activity for debugging (development only)
 * @param string $message - Message to log
 * @param array $context - Additional context data
 */
function logActivity($message, $context = []) {
    if (defined('DEBUG') && DEBUG) {
        $logData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'message' => $message,
            'context' => $context
        ];
        
        error_log("ACTIVITY: " . json_encode($logData));
    }
}

/**
 * Get all universities with country information
 * @return array - Array of universities with country data
 */
function getAllUniversities() {
    global $db;
    
    try {
        $stmt = $db->prepare("
            SELECT u.*, c.name as country_name, c.flag_code,
                   (SELECT COUNT(*) FROM university_images ui WHERE ui.university_id = u.id) as images_count
            FROM universities u 
            LEFT JOIN countries c ON u.country_id = c.id
            WHERE u.is_active = 1 AND c.is_active = 1
            ORDER BY u.name ASC
        ");
        $stmt->execute();
        
        $universities = $stmt->fetchAll();
        
        return $universities;
        
    } catch (PDOException $e) {
        error_log("Error fetching all universities: " . $e->getMessage());
        return [];
    }
}

/**
 * Get all countries
 * @return array - Array of all active countries
 */
function getAllCountries() {
    global $db;
    
    try {
        $stmt = $db->prepare("
            SELECT * FROM countries 
            WHERE is_active = 1 
            ORDER BY name ASC
        ");
        $stmt->execute();
        
        $countries = $stmt->fetchAll();
        
        return $countries;
        
    } catch (PDOException $e) {
        error_log("Error fetching all countries: " . $e->getMessage());
        return [];
    }
}

/**
 * Convert timestamp to "time ago" format
 * @param string $timestamp - MySQL timestamp
 * @return string - Formatted time ago string
 */
function timeAgo($timestamp) {
    if (empty($timestamp) || $timestamp === null || $timestamp === '0000-00-00 00:00:00') {
        return 'Recently';
    }
    
    $time = strtotime($timestamp);
    if ($time === false) {
        return 'Recently';
    }
    
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $minutes = floor($diff / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    } else {
        $years = floor($diff / 31536000);
        return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
    }
}

/**
 * Format date in a readable format
 * @param string $timestamp - MySQL timestamp
 * @param string $format - Date format (default: 'M j, Y')
 * @return string - Formatted date string
 */
function formatDate($timestamp, $format = 'M j, Y') {
    if (empty($timestamp) || $timestamp === null || $timestamp === '0000-00-00 00:00:00') {
        return 'Recently';
    }
    
    $time = strtotime($timestamp);
    if ($time === false) {
        return 'Recently';
    }
    
    return date($format, $time);
}

?> 