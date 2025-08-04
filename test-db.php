<?php
/**
 * Database Connection Test
 * This file tests the database connection and table creation
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Test</h2>";

// Test database connection first
try {
    // Database Configuration
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'medstudy_blog');
    define('DB_USER', 'root');
    define('DB_PASS', '');

    echo "<p>✓ Database constants defined</p>";

    // Test connection
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        )
    );

    echo "<p>✓ Database connection successful!</p>";

    // Check if tables exist
    $tables = ['countries', 'universities', 'university_images'];
    
    echo "<h3>Checking Tables:</h3>";
    foreach ($tables as $table) {
        try {
            $stmt = $db->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "<p>✓ Table '$table' exists with $count records</p>";
        } catch (Exception $e) {
            echo "<p>❌ Table '$table' does NOT exist - " . $e->getMessage() . "</p>";
        }
    }

    // If tables don't exist, try to create them
    echo "<h3>Creating Tables:</h3>";
    
    // Create countries table
    try {
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
        
        echo "<p>✓ Countries table created successfully</p>";
    } catch (Exception $e) {
        echo "<p>❌ Error creating countries table: " . $e->getMessage() . "</p>";
    }

    // Create universities table
    try {
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
        
        echo "<p>✓ Universities table created successfully</p>";
    } catch (Exception $e) {
        echo "<p>❌ Error creating universities table: " . $e->getMessage() . "</p>";
    }

    // Create university_images table
    try {
        $db->exec("CREATE TABLE IF NOT EXISTS university_images (
            id INT AUTO_INCREMENT PRIMARY KEY,
            university_id INT NOT NULL,
            image_url VARCHAR(500) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            
            FOREIGN KEY (university_id) REFERENCES universities(id) ON DELETE CASCADE,
            
            INDEX idx_university (university_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        
        echo "<p>✓ University images table created successfully</p>";
    } catch (Exception $e) {
        echo "<p>❌ Error creating university_images table: " . $e->getMessage() . "</p>";
    }

    // Insert sample data
    echo "<h3>Inserting Sample Data:</h3>";
    
    // Check if countries table has data
    $stmt = $db->query("SELECT COUNT(*) FROM countries");
    $countryCount = $stmt->fetchColumn();
    
    if ($countryCount == 0) {
        echo "<p>Inserting sample countries...</p>";
        
        $countries = [
            ['Russia', 'russia', 'ru', 'Russia offers world-class medical education at affordable costs with internationally recognized degrees.', 'europe', 15000, '["popular", "budget"]'],
            ['Georgia', 'georgia', 'ge', 'Georgia provides quality medical education in English with modern facilities and clinical training.', 'asia', 8000, '["popular", "budget"]'],
            ['Kazakhstan', 'kazakhstan', 'kz', 'Kazakhstan offers excellent medical programs with state-of-the-art infrastructure and research opportunities.', 'asia', 12000, '["budget"]']
        ];
        
        $stmt = $db->prepare("INSERT INTO countries (name, slug, flag_code, description, region, student_count, categories, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)");
        
        foreach ($countries as $index => $country) {
            $stmt->execute([
                $country[0], $country[1], $country[2], $country[3], 
                $country[4], $country[5], $country[6], $index + 1
            ]);
        }
        
        echo "<p>✓ Sample countries inserted successfully</p>";
    } else {
        echo "<p>✓ Countries table already has $countryCount records</p>";
    }

    // Insert sample universities (DISABLED - No automatic seeding)
    $stmt = $db->query("SELECT COUNT(*) FROM universities");
    $universityCount = $stmt->fetchColumn();
    
    // DISABLED: Automatic university seeding to prevent recreation after deletion
    if (false && $universityCount == 0) {
        echo "<p>Inserting sample universities...</p>";
        
        // Get country IDs
        $countryStmt = $db->prepare("SELECT id FROM countries WHERE slug = ?");
        
        $universities = [
            ['russia', 'First Moscow State Medical University', 'first-moscow-state-medical-university-russia', 'First Moscow State Medical University (Sechenov University) is one of the oldest and most prestigious medical universities in Russia.', '6 years', 'English/Russian', 6500.00, 'Moscow, Russia'],
            ['georgia', 'Tbilisi State Medical University', 'tbilisi-state-medical-university-georgia', 'Tbilisi State Medical University is the leading medical institution in Georgia, offering internationally recognized medical degrees.', '6 years', 'English', 8000.00, 'Tbilisi, Georgia'],
            ['kazakhstan', 'Al-Farabi Kazakh National University', 'al-farabi-kazakh-national-university-kazakhstan', 'Al-Farabi Kazakh National University is the premier medical institution in Kazakhstan.', '6 years', 'English/Russian', 4000.00, 'Almaty, Kazakhstan']
        ];
        
        $uniStmt = $db->prepare("INSERT INTO universities (country_id, name, slug, about_university, course_duration, language_of_instruction, annual_fees, location, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        
        foreach ($universities as $uni) {
            $countryStmt->execute([$uni[0]]);
            $countryId = $countryStmt->fetchColumn();
            
            if ($countryId) {
                $uniStmt->execute([
                    $countryId, $uni[1], $uni[2], $uni[3], $uni[4], $uni[5], $uni[6], $uni[7]
                ]);
            }
        }
        
        echo "<p>✓ Sample universities inserted successfully</p>";
    } else {
        echo "<p>✓ Universities table has $universityCount records (auto-seeding disabled)</p>";
    }

    echo "<h3>Final Status:</h3>";
    foreach ($tables as $table) {
        $stmt = $db->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "<p>✓ Table '$table': $count records</p>";
    }

} catch (PDOException $e) {
    echo "<h3>❌ Database Connection Error:</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>XAMPP MySQL service is running</li>";
    echo "<li>Database name 'medstudy_blog' exists in phpMyAdmin</li>";
    echo "<li>Database credentials are correct (username: root, password: empty)</li>";
    echo "</ul>";
} catch (Exception $e) {
    echo "<h3>❌ General Error:</h3>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
}
?> 