<?php
/**
 * Insert Complete Sample Data
 * This will add all 12 countries and universities as originally planned
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    // Database connection
    $db = new PDO(
        "mysql:host=localhost;dbname=medstudy_blog;charset=utf8mb4",
        'root',
        '',
        array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        )
    );

    echo "<h2>Inserting Complete Sample Data</h2>";

    // Clear existing data (optional - remove if you want to keep current data)
    echo "<p>Clearing existing data...</p>";
    $db->exec("DELETE FROM university_images");
    $db->exec("DELETE FROM universities");  
    $db->exec("DELETE FROM countries");
    $db->exec("ALTER TABLE countries AUTO_INCREMENT = 1");
    $db->exec("ALTER TABLE universities AUTO_INCREMENT = 1");

    // Insert all 12 countries
    echo "<p>Inserting 12 countries...</p>";
    
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
    
    $stmt = $db->prepare("INSERT INTO countries (name, slug, flag_code, description, region, student_count, categories, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, 1, ?)");
    
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
    
    echo "<p>✓ All 12 countries inserted successfully!</p>";

    // Insert universities for each country
    echo "<p>Inserting universities for each country...</p>";
    
    $countryStmt = $db->prepare("SELECT id, name FROM countries");
    $countryStmt->execute();
    $countryList = $countryStmt->fetchAll();
    
    $universities = [];
    
    foreach ($countryList as $country) {
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
                // Add 2 sample universities for remaining countries
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
    
    $stmt = $db->prepare("INSERT INTO universities (country_id, name, slug, about_university, course_duration, language_of_instruction, annual_fees, location, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
    
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
    
    echo "<p>✓ All universities inserted successfully!</p>";

    // Show final counts
    echo "<h3>Final Database Status:</h3>";
    
    $countStmt = $db->query("SELECT COUNT(*) FROM countries");
    $countriesCount = $countStmt->fetchColumn();
    echo "<p>✓ Countries: <strong>$countriesCount</strong></p>";
    
    $uniStmt = $db->query("SELECT COUNT(*) FROM universities");
    $universitiesCount = $uniStmt->fetchColumn();
    echo "<p>✓ Universities: <strong>$universitiesCount</strong></p>";
    
    echo "<h3>Countries by Region:</h3>";
    $regionStmt = $db->query("SELECT region, COUNT(*) as count FROM countries GROUP BY region ORDER BY count DESC");
    $regions = $regionStmt->fetchAll();
    foreach ($regions as $region) {
        echo "<p>✓ " . ucfirst($region['region']) . ": <strong>" . $region['count'] . "</strong> countries</p>";
    }

    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #c3e6cb; border-radius: 5px; margin: 20px 0;'>";
    echo "<h3 style='color: #155724; margin: 0 0 10px 0;'>🎉 Database Setup Complete!</h3>";
    echo "<p style='color: #155724; margin: 0;'>You can now visit <a href='destinations.php' style='color: #155724; font-weight: bold;'>destinations.php</a> to see all countries and test the full system!</p>";
    echo "</div>";

} catch (Exception $e) {
    echo "<h3 style='color: red;'>❌ Error:</h3>";
    echo "<p style='color: red;'>" . $e->getMessage() . "</p>";
}
?> 