<?php
/**
 * Database Configuration
 * MedStudy Global - Blog System
 */

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'medstudy_blog');
define('DB_USER', 'sunrise');
define('DB_PASS', 'medstudy_blog_Password123#');

// Cloudinary Configuration
define('CLOUDINARY_CLOUD_NAME', 'dswzvbhix');
define('CLOUDINARY_API_KEY', '443489439765691');
define('CLOUDINARY_API_SECRET', 'QQqfhuPJ_mv5L3u3ikvvA_DsZy4');

// Site Configuration
define('SITE_URL', 'http://localhost/Project-1/');
define('ADMIN_URL', SITE_URL . 'admin/');

// Database Connection Class
class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn;

    public function connect() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
                )
            );
        } catch(PDOException $e) {
            echo "Connection Error: " . $e->getMessage();
        }
        
        return $this->conn;
    }
    
    public function disconnect() {
        $this->conn = null;
    }
}

// Global Database Instance
$database = new Database();
$db = $database->connect();

// Check if tables exist, create if not
function createTables($db) {
    try {
        // Admin Users Table
        $db->exec("CREATE TABLE IF NOT EXISTS admin_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role VARCHAR(20) DEFAULT 'admin',
            status ENUM('active', 'inactive') DEFAULT 'active',
            last_login TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Blog Categories Table
        $db->exec("CREATE TABLE IF NOT EXISTS blog_categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            description TEXT,
            color VARCHAR(7) DEFAULT '#003585',
            status ENUM('active', 'inactive') DEFAULT 'active',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Blogs Table
        $db->exec("CREATE TABLE IF NOT EXISTS blogs (
            id INT AUTO_INCREMENT PRIMARY KEY,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) NOT NULL UNIQUE,
            excerpt TEXT,
            content LONGTEXT NOT NULL,
            featured_image VARCHAR(500),
            category_id INT,
            author_id INT,
            author_name VARCHAR(100),
            author_avatar VARCHAR(500),
            status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
            is_featured BOOLEAN DEFAULT FALSE,
            is_editors_pick BOOLEAN DEFAULT FALSE,
            views INT DEFAULT 0,
            
            -- SEO Fields
            meta_title VARCHAR(255),
            meta_description TEXT,
            meta_keywords VARCHAR(500),
            canonical_url VARCHAR(500),
            og_title VARCHAR(255),
            og_description TEXT,
            og_image VARCHAR(500),
            twitter_title VARCHAR(255),
            twitter_description TEXT,
            twitter_image VARCHAR(500),
            
            -- Schema.org structured data
            schema_type VARCHAR(50) DEFAULT 'Article',
            schema_data JSON,
            
            -- Timestamps
            published_at TIMESTAMP NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            -- Foreign Keys
            FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL,
            FOREIGN KEY (author_id) REFERENCES admin_users(id) ON DELETE SET NULL,
            
            -- Indexes
            INDEX idx_slug (slug),
            INDEX idx_status (status),
            INDEX idx_category (category_id),
            INDEX idx_featured (is_featured),
            INDEX idx_published (published_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Blog Tags Table (Many-to-Many relationship)
        $db->exec("CREATE TABLE IF NOT EXISTS blog_tags (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(50) NOT NULL UNIQUE,
            slug VARCHAR(50) NOT NULL UNIQUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        $db->exec("CREATE TABLE IF NOT EXISTS blog_tag_relations (
            blog_id INT,
            tag_id INT,
            PRIMARY KEY (blog_id, tag_id),
            FOREIGN KEY (blog_id) REFERENCES blogs(id) ON DELETE CASCADE,
            FOREIGN KEY (tag_id) REFERENCES blog_tags(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Countries Table
        $db->exec("CREATE TABLE IF NOT EXISTS countries (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) NOT NULL UNIQUE,
            flag_code VARCHAR(5),
            description TEXT,
            featured_image VARCHAR(500),
            meta_title VARCHAR(255),
            meta_description TEXT,
            is_active BOOLEAN DEFAULT TRUE,
            sort_order INT DEFAULT 0,
            student_count INT DEFAULT 0,
            region ENUM('asia', 'europe', 'africa', 'americas', 'oceania'),
            categories JSON,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            INDEX idx_slug (slug),
            INDEX idx_active (is_active),
            INDEX idx_region (region),
            INDEX idx_sort (sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Universities Table
        $db->exec("CREATE TABLE IF NOT EXISTS universities (
            id INT AUTO_INCREMENT PRIMARY KEY,
            country_id INT NOT NULL,
            name VARCHAR(200) NOT NULL,
            slug VARCHAR(200) NOT NULL UNIQUE,
            featured_image VARCHAR(500),
            logo_image VARCHAR(500),
            about_university LONGTEXT,
            course_duration VARCHAR(50),
            language_of_instruction VARCHAR(100),
            annual_fees DECIMAL(10,2),
            location VARCHAR(200),
            is_active BOOLEAN DEFAULT TRUE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            
            FOREIGN KEY (country_id) REFERENCES countries(id) ON DELETE CASCADE,
            
            INDEX idx_country (country_id),
            INDEX idx_slug (slug),
            INDEX idx_active (is_active),
            INDEX idx_fees (annual_fees)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // University Images Table
        $db->exec("CREATE TABLE IF NOT EXISTS university_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            university_id INT NOT NULL,
            image_url VARCHAR(500) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
            
            INDEX idx_university (university_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        // Insert default admin user (password: admin123)
        $defaultAdmin = $db->prepare("INSERT IGNORE INTO admin_users (username, email, password, full_name) VALUES (?, ?, ?, ?)");
        $defaultAdmin->execute([
            'admin',
            'admin@medstudy.global',
            password_hash('admin123', PASSWORD_DEFAULT),
            'System Administrator'
        ]);
        
        // Insert default categories
        $categories = [
            ['Medical Education', 'medical-education', 'Articles about medical education and career guidance'],
            ['University Guide', 'university-guide', 'Information about medical universities worldwide'],
            ['Visa Guide', 'visa-guide', 'Student visa processes and requirements'],
            ['Scholarships', 'scholarships', 'Scholarship opportunities and application guides'],
            ['Student Life', 'student-life', 'Life as a medical student abroad'],
            ['Career Guidance', 'career-guidance', 'Medical career advice and specialization guides']
        ];
        
        $categoryStmt = $db->prepare("INSERT IGNORE INTO blog_categories (name, slug, description) VALUES (?, ?, ?)");
        foreach ($categories as $category) {
            $categoryStmt->execute($category);
        }
        
        // Insert sample countries data
        insertSampleCountries($db);
        
        // Insert sample universities data  
        insertSampleUniversities($db);
        
        return true;
        
    } catch(PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

// Sample Data Insertion Functions
function insertSampleCountries($db) {
    $countries = [
        [
            'name' => 'Russia',
            'slug' => 'russia', 
            'flag_code' => 'ru',
            'description' => 'Russia offers world-class medical education at affordable costs with internationally recognized degrees.',
            'region' => 'europe',
            'student_count' => 15000,
            'categories' => json_encode(['popular', 'budget'])
        ],
        [
            'name' => 'Georgia',
            'slug' => 'georgia',
            'flag_code' => 'ge', 
            'description' => 'Georgia provides quality medical education in English with modern facilities and clinical training.',
            'region' => 'asia',
            'student_count' => 8000,
            'categories' => json_encode(['popular', 'budget'])
        ],
        [
            'name' => 'Kazakhstan',
            'slug' => 'kazakhstan',
            'flag_code' => 'kz',
            'description' => 'Kazakhstan offers excellent medical programs with state-of-the-art infrastructure and research opportunities.',
            'region' => 'asia', 
            'student_count' => 12000,
            'categories' => json_encode(['budget'])
        ],
        [
            'name' => 'Kyrgyzstan',
            'slug' => 'kyrgyzstan',
            'flag_code' => 'kg',
            'description' => 'Kyrgyzstan provides affordable medical education with international curriculum and practical training.',
            'region' => 'asia',
            'student_count' => 6000,
            'categories' => json_encode(['budget'])
        ],
        [
            'name' => 'Ukraine',
            'slug' => 'ukraine',
            'flag_code' => 'ua',
            'description' => 'Ukraine has a rich tradition in medical education with experienced faculty and comprehensive programs.',
            'region' => 'europe',
            'student_count' => 10000,
            'categories' => json_encode(['popular', 'budget'])
        ],
        [
            'name' => 'Philippines',
            'slug' => 'philippines',
            'flag_code' => 'ph',
            'description' => 'Philippines offers medical education in English with American-style curriculum and clinical exposure.',
            'region' => 'asia',
            'student_count' => 5000,
            'categories' => json_encode(['popular'])
        ],
        [
            'name' => 'Bangladesh',
            'slug' => 'bangladesh',
            'flag_code' => 'bd',
            'description' => 'Bangladesh provides quality medical education with affordable tuition and extensive clinical training.',
            'region' => 'asia',
            'student_count' => 7000,
            'categories' => json_encode(['budget'])
        ],
        [
            'name' => 'China',
            'slug' => 'china',
            'flag_code' => 'cn',
            'description' => 'China offers world-class medical education with cutting-edge research facilities and global recognition.',
            'region' => 'asia',
            'student_count' => 20000,
            'categories' => json_encode(['popular', 'premium'])
        ],
        [
            'name' => 'Nepal',
            'slug' => 'nepal',
            'flag_code' => 'np',
            'description' => 'Nepal provides medical education with emphasis on community health and practical training.',
            'region' => 'asia',
            'student_count' => 4000,
            'categories' => json_encode(['budget'])
        ],
        [
            'name' => 'Armenia',
            'slug' => 'armenia',
            'flag_code' => 'am',
            'description' => 'Armenia offers quality medical education with modern facilities and international recognition.',
            'region' => 'asia',
            'student_count' => 3000,
            'categories' => json_encode(['budget'])
        ],
        [
            'name' => 'Poland',
            'slug' => 'poland',
            'flag_code' => 'pl',
            'description' => 'Poland provides excellent medical education within the European Union with English-taught programs.',
            'region' => 'europe',
            'student_count' => 8000,
            'categories' => json_encode(['popular', 'premium'])
        ],
        [
            'name' => 'Germany',
            'slug' => 'germany',
            'flag_code' => 'de',
            'description' => 'Germany offers world-renowned medical education with advanced research opportunities and clinical excellence.',
            'region' => 'europe',
            'student_count' => 12000,
            'categories' => json_encode(['premium'])
        ]
    ];
    
    $stmt = $db->prepare("INSERT IGNORE INTO countries (name, slug, flag_code, description, region, student_count, categories, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)");
    
    foreach ($countries as $index => $country) {
        $stmt->execute([
            $country['name'],
            $country['slug'], 
            $country['flag_code'],
            $country['description'],
            $country['region'],
            $country['student_count'],
            $country['categories'],
            $index + 1
        ]);
    }
}

function insertSampleUniversities($db) {
    // Get country IDs first
    $countryStmt = $db->prepare("SELECT id, name FROM countries");
    $countryStmt->execute();
    $countries = $countryStmt->fetchAll();
    
    $universities = [];
    
    foreach ($countries as $country) {
        switch ($country['name']) {
            case 'Russia':
                $universities = array_merge($universities, [
                    [
                        'country_id' => $country['id'],
                        'name' => 'First Moscow State Medical University',
                        'slug' => 'first-moscow-state-medical-university-russia',
                        'about_university' => 'First Moscow State Medical University (Sechenov University) is one of the oldest and most prestigious medical universities in Russia, established in 1758. The university offers world-class medical education with state-of-the-art facilities.',
                        'course_duration' => '6 years',
                        'language_of_instruction' => 'English/Russian',
                        'annual_fees' => 6500.00,
                        'location' => 'Moscow, Russia'
                    ],
                    [
                        'country_id' => $country['id'],
                        'name' => 'Kazan Federal University',
                        'slug' => 'kazan-federal-university-russia',
                        'about_university' => 'Kazan Federal University is a leading medical institution in Russia with over 200 years of history. It offers comprehensive medical programs with modern teaching methods.',
                        'course_duration' => '6 years',
                        'language_of_instruction' => 'English',
                        'annual_fees' => 5500.00,
                        'location' => 'Kazan, Russia'
                    ],
                    [
                        'country_id' => $country['id'],
                        'name' => 'Crimea Federal University',
                        'slug' => 'crimea-federal-university-russia',
                        'about_university' => 'Crimea Federal University offers quality medical education with experienced faculty and modern infrastructure in a beautiful location.',
                        'course_duration' => '6 years',
                        'language_of_instruction' => 'English',
                        'annual_fees' => 4500.00,
                        'location' => 'Simferopol, Crimea'
                    ]
                ]);
                break;
                
            case 'Georgia':
                $universities = array_merge($universities, [
                    [
                        'country_id' => $country['id'],
                        'name' => 'Tbilisi State Medical University',
                        'slug' => 'tbilisi-state-medical-university-georgia',
                        'about_university' => 'Tbilisi State Medical University is the leading medical institution in Georgia, offering internationally recognized medical degrees with modern facilities.',
                        'course_duration' => '6 years',
                        'language_of_instruction' => 'English',
                        'annual_fees' => 8000.00,
                        'location' => 'Tbilisi, Georgia'
                    ],
                    [
                        'country_id' => $country['id'],
                        'name' => 'Georgian American University',
                        'slug' => 'georgian-american-university-georgia',
                        'about_university' => 'Georgian American University provides American-style medical education with English instruction and international curriculum.',
                        'course_duration' => '4 years (MD)',
                        'language_of_instruction' => 'English',
                        'annual_fees' => 12000.00,
                        'location' => 'Tbilisi, Georgia'
                    ],
                    [
                        'country_id' => $country['id'],
                        'name' => 'Batumi Shota Rustaveli State University',
                        'slug' => 'batumi-shota-rustaveli-state-university-georgia',
                        'about_university' => 'Located in the beautiful coastal city of Batumi, this university offers quality medical education with modern teaching methods.',
                        'course_duration' => '6 years',
                        'language_of_instruction' => 'English',
                        'annual_fees' => 6500.00,
                        'location' => 'Batumi, Georgia'
                    ]
                ]);
                break;
                
            case 'Kazakhstan':
                $universities = array_merge($universities, [
                    [
                        'country_id' => $country['id'],
                        'name' => 'Al-Farabi Kazakh National University',
                        'slug' => 'al-farabi-kazakh-national-university-kazakhstan',
                        'about_university' => 'Al-Farabi Kazakh National University is the premier medical institution in Kazakhstan, offering world-class education with research opportunities.',
                        'course_duration' => '6 years',
                        'language_of_instruction' => 'English/Russian',
                        'annual_fees' => 4000.00,
                        'location' => 'Almaty, Kazakhstan'
                    ],
                    [
                        'country_id' => $country['id'],
                        'name' => 'Astana Medical University',
                        'slug' => 'astana-medical-university-kazakhstan',
                        'about_university' => 'Astana Medical University provides comprehensive medical education in the capital city with state-of-the-art facilities.',
                        'course_duration' => '6 years',
                        'language_of_instruction' => 'English',
                        'annual_fees' => 4500.00,
                        'location' => 'Nur-Sultan, Kazakhstan'
                    ]
                ]);
                break;
                
            default:
                // Add 2-3 sample universities for remaining countries
                $universities = array_merge($universities, [
                    [
                        'country_id' => $country['id'],
                        'name' => $country['name'] . ' Medical University',
                        'slug' => strtolower(str_replace(' ', '-', $country['name'])) . '-medical-university-' . strtolower(str_replace(' ', '-', $country['name'])),
                        'about_university' => 'A leading medical institution in ' . $country['name'] . ' offering quality medical education with international standards.',
                        'course_duration' => '6 years',
                        'language_of_instruction' => 'English',
                        'annual_fees' => rand(3000, 10000),
                        'location' => 'Capital City, ' . $country['name']
                    ],
                    [
                        'country_id' => $country['id'],
                        'name' => 'International Medical College of ' . $country['name'],
                        'slug' => 'international-medical-college-' . strtolower(str_replace(' ', '-', $country['name'])),
                        'about_university' => 'International Medical College provides world-class medical education with modern facilities in ' . $country['name'] . '.',
                        'course_duration' => '5-6 years',
                        'language_of_instruction' => 'English',
                        'annual_fees' => rand(4000, 12000),
                        'location' => 'Major City, ' . $country['name']
                    ]
                ]);
                break;
        }
    }
    
    $stmt = $db->prepare("INSERT IGNORE INTO universities (country_id, name, slug, about_university, course_duration, language_of_instruction, annual_fees, location, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    
    foreach ($universities as $university) {
        $stmt->execute([
            $university['country_id'],
            $university['name'],
            $university['slug'],
            $university['about_university'],
            $university['course_duration'],
            $university['language_of_instruction'],
            $university['annual_fees'],
            $university['location']
        ]);
    }
}

// Create tables on first run (DISABLED - Use setup-production.php instead)
// createTables($db);
?> 