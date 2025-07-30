<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->connect();

echo "<h2>Seeding Sample Blog Data</h2>";

try {
    // Sample Categories
    $categories = [
        [
            'name' => 'Medical Education',
            'slug' => 'medical-education',
            'description' => 'Articles about medical education, study tips, and academic guidance',
            'color' => '#e74c3c'
        ],
        [
            'name' => 'University Guide',
            'slug' => 'university-guide', 
            'description' => 'Information about medical universities and admission processes',
            'color' => '#3498db'
        ],
        [
            'name' => 'Visa Guide',
            'slug' => 'visa-guide',
            'description' => 'Student visa information and application processes',
            'color' => '#2ecc71'
        ],
        [
            'name' => 'Scholarships',
            'slug' => 'scholarships',
            'description' => 'Scholarship opportunities and financial aid information',
            'color' => '#f39c12'
        ],
        [
            'name' => 'Student Life',
            'slug' => 'student-life',
            'description' => 'Life as an international medical student',
            'color' => '#9b59b6'
        ],
        [
            'name' => 'Career Guidance',
            'slug' => 'career-guidance',
            'description' => 'Medical career paths and professional development',
            'color' => '#1abc9c'
        ]
    ];

    echo "<h3>Adding Categories...</h3>";
    
    foreach ($categories as $category) {
        // Check if category already exists
        $check_query = "SELECT id FROM blog_categories WHERE slug = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$category['slug']]);
        
        if (!$check_stmt->fetch()) {
            $insert_query = "INSERT INTO blog_categories (name, slug, description, color, status) VALUES (?, ?, ?, ?, 'active')";
            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->execute([$category['name'], $category['slug'], $category['description'], $category['color']]);
            echo "✓ Added category: " . $category['name'] . "<br>";
        } else {
            echo "- Category already exists: " . $category['name'] . "<br>";
        }
    }

    // Create admin user if not exists
    $admin_check = "SELECT id FROM admin_users WHERE username = 'admin'";
    $admin_stmt = $db->prepare($admin_check);
    $admin_stmt->execute();
    
    if (!$admin_stmt->fetch()) {
        $admin_query = "INSERT INTO admin_users (username, email, password, full_name, role, status) VALUES (?, ?, ?, ?, 'admin', 'active')";
        $admin_insert = $db->prepare($admin_query);
        $admin_insert->execute(['admin', 'admin@medstudy.com', password_hash('admin123', PASSWORD_DEFAULT), 'Admin User']);
        echo "✓ Created admin user (username: admin, password: admin123)<br>";
    }

    // Get category IDs for blog posts
    $cat_query = "SELECT id, slug FROM blog_categories";
    $cat_stmt = $db->prepare($cat_query);
    $cat_stmt->execute();
    $category_map = [];
    while ($row = $cat_stmt->fetch()) {
        $category_map[$row['slug']] = $row['id'];
    }

    // Sample Blog Posts
    $blog_posts = [
        [
            'title' => 'Complete Guide to MBBS Admission Abroad: Everything You Need to Know',
            'slug' => 'complete-guide-mbbs-admission-abroad',
            'excerpt' => 'Discover the complete roadmap to securing your MBBS admission in top international universities. From application requirements to visa processes, we cover everything you need to start your medical journey abroad.',
            'content' => '<h2>Introduction</h2><p>Pursuing an MBBS degree abroad has become increasingly popular among students seeking quality medical education and global exposure. This comprehensive guide will walk you through every step of the process, from choosing the right university to completing your application.</p><h3>Why Study MBBS Abroad?</h3><p>There are numerous advantages to pursuing medical education internationally:</p><ul><li>Access to world-class medical facilities and technology</li><li>Exposure to diverse patient populations and medical practices</li><li>International recognition and career opportunities</li><li>Cultural diversity and personal growth</li><li>Often more affordable than domestic options</li></ul><h3>Choosing the Right Country</h3><p>Several countries offer excellent medical programs for international students. Popular destinations include:</p><ul><li><strong>Russia:</strong> Affordable tuition, English-taught programs, globally recognized degrees</li><li><strong>Ukraine:</strong> Low cost of living, quality education, no entrance exams</li><li><strong>Georgia:</strong> Modern infrastructure, European standards, reasonable fees</li><li><strong>Kazakhstan:</strong> Growing medical education sector, affordable options</li></ul><h3>Application Process</h3><p>The application process typically involves the following steps:</p><ol><li>Research and shortlist universities</li><li>Check eligibility requirements</li><li>Prepare required documents</li><li>Submit applications before deadlines</li><li>Attend interviews if required</li><li>Receive admission letters</li><li>Apply for student visa</li></ol><h3>Required Documents</h3><p>Common documents required for MBBS admission include:</p><ul><li>Academic transcripts and certificates</li><li>NEET scorecard (for Indian students)</li><li>Passport copy</li><li>Medical fitness certificate</li><li>Statement of purpose</li><li>Letters of recommendation</li><li>Financial documents</li></ul><h3>Conclusion</h3><p>Studying MBBS abroad is an excellent opportunity for aspiring doctors to receive quality education while gaining international experience. With proper planning and preparation, you can successfully navigate the admission process and embark on your medical career journey.</p>',
            'category' => 'medical-education',
            'author_name' => 'Dr. Sarah Johnson',
            'is_featured' => 1,
            'is_editors_pick' => 1,
            'featured_image' => 'assets/images/media/home-page/hero-section/one.webp'
        ],
        [
            'title' => 'Top 10 Medical Universities in Europe for International Students',
            'slug' => 'top-10-medical-universities-europe',
            'excerpt' => 'Explore the best medical universities in Europe that welcome international students with excellent programs, modern facilities, and global recognition.',
            'content' => '<h2>Introduction</h2><p>Europe hosts some of the world\'s most prestigious medical universities, offering excellent education standards and international recognition. Here\'s our curated list of the top 10 medical universities for international students.</p><h3>1. University of Oxford - United Kingdom</h3><p>Oxford\'s medical program is renowned globally for its academic excellence and research opportunities. The university offers a 6-year course leading to a Bachelor of Medicine, Bachelor of Surgery (BM BCh).</p><h3>2. Karolinska Institute - Sweden</h3><p>Home to the Nobel Prize in Physiology or Medicine, Karolinska Institute is one of Europe\'s largest medical universities. It offers programs in English and has excellent research facilities.</p><h3>3. University of Edinburgh - Scotland</h3><p>One of the oldest medical schools in the English-speaking world, Edinburgh offers a comprehensive medical curriculum with strong clinical training components.</p><h3>4. Charles University - Czech Republic</h3><p>Located in Prague, Charles University\'s Faculty of Medicine offers programs in English at affordable tuition rates, making it popular among international students.</p><h3>5. Semmelweis University - Hungary</h3><p>Budapest\'s Semmelweis University is one of the oldest medical schools in Europe, offering high-quality medical education in English with reasonable fees.</p><h3>Admission Requirements</h3><p>Most European medical universities require:</p><ul><li>Strong academic background in sciences</li><li>English proficiency tests (IELTS/TOEFL)</li><li>Entrance examinations</li><li>Letters of recommendation</li><li>Personal statement</li></ul><h3>Benefits of Studying in Europe</h3><ul><li>High-quality education standards</li><li>Cultural diversity</li><li>Research opportunities</li><li>Career prospects</li><li>Healthcare system exposure</li></ul>',
            'category' => 'university-guide',
            'author_name' => 'Prof. Michael Chen',
            'is_featured' => 0,
            'is_editors_pick' => 1,
            'featured_image' => 'assets/images/media/home-page/blogs-section/1.jpg'
        ],
        [
            'title' => 'Student Visa Application Process: A Step-by-Step Guide',
            'slug' => 'student-visa-application-process-guide',
            'excerpt' => 'Navigate the student visa application process with confidence. Our comprehensive guide covers requirements, documents, and tips for a successful application.',
            'content' => '<h2>Understanding Student Visas</h2><p>A student visa is a legal document that allows you to enter and study in a foreign country. Each country has specific requirements and procedures for student visa applications.</p><h3>General Requirements</h3><p>While requirements vary by country, common prerequisites include:</p><ul><li>Valid passport</li><li>Letter of acceptance from a recognized institution</li><li>Proof of financial support</li><li>Medical examination results</li><li>Police clearance certificate</li><li>Academic transcripts</li><li>Passport-sized photographs</li></ul><h3>Step-by-Step Application Process</h3><ol><li><strong>Research Visa Requirements:</strong> Check the specific requirements for your destination country</li><li><strong>Gather Documents:</strong> Collect all required documents and ensure they are properly authenticated</li><li><strong>Complete Application Form:</strong> Fill out the visa application form accurately</li><li><strong>Schedule Appointment:</strong> Book an appointment at the embassy or consulate</li><li><strong>Attend Interview:</strong> Prepare for and attend the visa interview</li><li><strong>Submit Biometrics:</strong> Provide fingerprints and photographs if required</li><li><strong>Wait for Processing:</strong> Allow sufficient time for visa processing</li><li><strong>Collect Passport:</strong> Retrieve your passport with the visa</li></ol><h3>Common Interview Questions</h3><p>Be prepared to answer questions such as:</p><ul><li>Why did you choose this university and country?</li><li>How will you finance your studies?</li><li>What are your career plans after graduation?</li><li>Do you have any relatives in the destination country?</li><li>Have you traveled abroad before?</li></ul><h3>Tips for Success</h3><ul><li>Apply early to allow sufficient processing time</li><li>Ensure all documents are complete and authentic</li><li>Be honest and consistent in your responses</li><li>Dress professionally for the interview</li><li>Bring original documents and copies</li><li>Practice common interview questions</li></ul>',
            'category' => 'visa-guide',
            'author_name' => 'Emma Rodriguez',
            'is_featured' => 0,
            'is_editors_pick' => 1,
            'featured_image' => 'assets/images/media/home-page/blogs-section/2.jpg'
        ],
        [
            'title' => 'Merit-Based Scholarships for Medical Students: Complete List',
            'slug' => 'merit-based-scholarships-medical-students',
            'excerpt' => 'Discover comprehensive scholarship opportunities for medical students worldwide. Find funding options to support your medical education journey.',
            'content' => '<h2>Introduction to Medical Scholarships</h2><p>Medical education can be expensive, but numerous scholarship opportunities exist to help deserving students pursue their dreams. This guide covers various merit-based scholarships available for medical students.</p><h3>International Scholarships</h3><h4>1. Fulbright Foreign Student Program</h4><p>The Fulbright program provides funding for graduate study, research, and teaching in the United States. Medical students can apply for various Fulbright opportunities.</p><h4>2. Commonwealth Scholarships</h4><p>Available for students from Commonwealth countries to study in the UK, these scholarships cover tuition, living expenses, and travel costs.</p><h4>3. Erasmus+ Programme</h4><p>European students can benefit from this EU program that supports education, training, youth, and sport across Europe.</p><h3>Country-Specific Scholarships</h3><h4>United States</h4><ul><li>National Health Service Corps Scholarship</li><li>Armed Forces Health Professions Scholarship</li><li>AMA Foundation Scholarships</li></ul><h4>United Kingdom</h4><ul><li>Chevening Scholarships</li><li>Rhodes Scholarships</li><li>Gates Cambridge Scholarships</li></ul><h4>Canada</h4><ul><li>Vanier Canada Graduate Scholarships</li><li>Canada Graduate Scholarships</li><li>Provincial health authority scholarships</li></ul><h3>University-Specific Scholarships</h3><p>Many universities offer their own scholarship programs:</p><ul><li>Harvard Medical School Scholarships</li><li>Oxford Medical School Bursaries</li><li>McGill University Medical Scholarships</li><li>University of Melbourne Medical Scholarships</li></ul><h3>Application Tips</h3><ul><li>Start early and research thoroughly</li><li>Maintain excellent academic records</li><li>Develop strong leadership and extracurricular profiles</li><li>Write compelling personal statements</li><li>Secure strong letters of recommendation</li><li>Meet all deadlines</li></ul><h3>Alternative Funding Options</h3><ul><li>Student loans with favorable terms</li><li>Work-study programs</li><li>Research assistantships</li><li>Teaching assistantships</li><li>Part-time employment opportunities</li></ul>',
            'category' => 'scholarships',
            'author_name' => 'Dr. Priya Sharma',
            'is_featured' => 0,
            'is_editors_pick' => 0,
            'featured_image' => 'assets/images/media/home-page/blogs-section/3.jpg'
        ],
        [
            'title' => 'Adapting to Life as an International Medical Student',
            'slug' => 'adapting-life-international-medical-student',
            'excerpt' => 'Essential tips for international students to successfully adapt to new cultures, academic systems, and social environments while pursuing medical education.',
            'content' => '<h2>The Journey Begins</h2><p>Starting medical school in a foreign country is both exciting and challenging. This guide will help you navigate the transition and make the most of your international medical education experience.</p><h3>Cultural Adaptation</h3><p>Adapting to a new culture is one of the biggest challenges international students face:</p><ul><li><strong>Language Barriers:</strong> Even if courses are taught in English, local accents and medical terminology can be challenging</li><li><strong>Social Customs:</strong> Understanding local customs and social norms helps in building relationships</li><li><strong>Food and Lifestyle:</strong> Adjusting to local cuisine and lifestyle changes</li><li><strong>Healthcare System:</strong> Learning about the local healthcare system and patient care practices</li></ul><h3>Academic System Differences</h3><p>Medical education systems vary significantly across countries:</p><ul><li>Teaching methodologies (lectures vs. problem-based learning)</li><li>Assessment methods and grading systems</li><li>Clinical rotation structures</li><li>Research requirements and opportunities</li><li>Professional development expectations</li></ul><h3>Building Support Networks</h3><p>Creating a strong support system is crucial for success:</p><ul><li>Connect with fellow international students</li><li>Join student organizations and clubs</li><li>Find mentors among senior students or faculty</li><li>Participate in cultural exchange programs</li><li>Maintain connections with family and friends back home</li></ul><h3>Academic Success Strategies</h3><ul><li>Develop effective study habits early</li><li>Form study groups with classmates</li><li>Utilize university support services</li><li>Seek help when needed</li><li>Balance academics with personal well-being</li></ul><h3>Managing Finances</h3><p>Financial management is crucial for international students:</p><ul><li>Create and stick to a budget</li><li>Look for part-time work opportunities (if permitted)</li><li>Take advantage of student discounts</li><li>Consider shared accommodation to reduce costs</li><li>Plan for emergency expenses</li></ul><h3>Health and Well-being</h3><ul><li>Register with local healthcare services</li><li>Understand health insurance coverage</li><li>Maintain physical fitness and mental health</li><li>Seek counseling support when needed</li><li>Stay connected with campus health services</li></ul>',
            'category' => 'student-life',
            'author_name' => 'Maria Rodriguez',
            'is_featured' => 0,
            'is_editors_pick' => 0,
            'featured_image' => 'assets/images/media/home-page/hero-section/two.webp'
        ],
        [
            'title' => 'Medical Specializations: Finding Your Perfect Match',
            'slug' => 'medical-specializations-finding-perfect-match',
            'excerpt' => 'Explore different medical specializations and discover which field aligns best with your interests, skills, and career goals in the medical profession.',
            'content' => '<h2>Choosing Your Medical Specialty</h2><p>Selecting a medical specialty is one of the most important decisions in a medical career. This guide will help you explore various specializations and find the one that matches your interests and goals.</p><h3>Primary Care Specialties</h3><h4>Family Medicine</h4><p>Family physicians provide comprehensive healthcare for individuals and families across all ages, genders, diseases, and parts of the body.</p><h4>Internal Medicine</h4><p>Internists specialize in the prevention, diagnosis, and treatment of adult diseases, often serving as primary care physicians or subspecializing further.</p><h4>Pediatrics</h4><p>Pediatricians focus on the medical care of infants, children, and adolescents, typically up to age 18.</p><h3>Surgical Specialties</h3><h4>General Surgery</h4><p>General surgeons perform a wide variety of surgical procedures on various parts of the body, often serving as the foundation for surgical subspecialties.</p><h4>Orthopedic Surgery</h4><p>Orthopedic surgeons specialize in the musculoskeletal system, treating bones, joints, ligaments, tendons, and muscles.</p><h4>Neurosurgery</h4><p>Neurosurgeons operate on the brain, spinal cord, and nervous system, handling some of the most complex surgical procedures.</p><h3>Diagnostic Specialties</h3><h4>Radiology</h4><p>Radiologists use medical imaging techniques to diagnose and sometimes treat diseases and injuries.</p><h4>Pathology</h4><p>Pathologists study the causes and effects of diseases, often working behind the scenes to diagnose conditions through laboratory analysis.</p><h3>Factors to Consider</h3><ul><li><strong>Personal Interests:</strong> What aspects of medicine fascinate you most?</li><li><strong>Lifestyle Preferences:</strong> Consider work-life balance, call schedules, and time commitments</li><li><strong>Patient Population:</strong> Do you prefer working with children, adults, or elderly patients?</li><li><strong>Work Environment:</strong> Hospital, clinic, operating room, or laboratory settings</li><li><strong>Income Potential:</strong> Financial considerations and earning potential</li><li><strong>Length of Training:</strong> Residency duration and fellowship requirements</li></ul><h3>Exploration Strategies</h3><ul><li>Shadow physicians in different specialties</li><li>Participate in research projects</li><li>Attend medical conferences and specialty meetings</li><li>Join specialty-specific student organizations</li><li>Complete rotations in various specialties</li><li>Seek mentorship from practicing physicians</li></ul><h3>Making the Decision</h3><p>Remember that choosing a specialty is a personal decision that should align with your values, interests, and life goals. Take time to explore, ask questions, and reflect on your experiences before making this important choice.</p>',
            'category' => 'career-guidance',
            'author_name' => 'Dr. Rachel Green',
            'is_featured' => 0,
            'is_editors_pick' => 0,
            'featured_image' => 'assets/images/media/home-page/hero-section/three.webp'
        ]
    ];

    echo "<h3>Adding Blog Posts...</h3>";

    foreach ($blog_posts as $post) {
        // Check if post already exists
        $check_query = "SELECT id FROM blogs WHERE slug = ?";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute([$post['slug']]);
        
        if (!$check_stmt->fetch()) {
            $category_id = isset($category_map[$post['category']]) ? $category_map[$post['category']] : null;
            
            $insert_query = "INSERT INTO blogs (title, slug, excerpt, content, category_id, author_name, status, is_featured, is_editors_pick, featured_image, published_at) 
                           VALUES (?, ?, ?, ?, ?, ?, 'published', ?, ?, ?, NOW())";
            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->execute([
                $post['title'],
                $post['slug'], 
                $post['excerpt'],
                $post['content'],
                $category_id,
                $post['author_name'],
                $post['is_featured'],
                $post['is_editors_pick'],
                $post['featured_image']
            ]);
            echo "✓ Added blog post: " . $post['title'] . "<br>";
        } else {
            echo "- Blog post already exists: " . $post['title'] . "<br>";
        }
    }

    echo "<br><h3>✅ Sample data seeded successfully!</h3>";
    echo "<p>You can now test the blog functionality:</p>";
    echo "<ul>";
    echo "<li><a href='blog.php'>View Blog Page</a></li>";
    echo "<li><a href='admin/login.php'>Admin Login</a> (username: admin, password: admin123)</li>";
    echo "<li><a href='admin/manage-categories.php'>Manage Categories</a></li>";
    echo "</ul>";

} catch (Exception $e) {
    echo "<div style='color: red;'>Error: " . $e->getMessage() . "</div>";
}
?> 