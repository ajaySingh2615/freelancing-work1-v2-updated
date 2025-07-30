<?php
/**
 * Process University Inquiry Form
 * Handles form validation and email sending to Gmail
 * MedStudy Global - Universities System
 */

// Include required files
require_once 'includes/functions.php';

// Set content type
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Ensure POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

/**
 * Send JSON response
 */
function sendResponse($success, $message, $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Validate required fields
 */
function validateRequiredFields($data, $required) {
    $missing = [];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            $missing[] = $field;
        }
    }
    return $missing;
}

/**
 * Send email using PHP mail function
 */
function sendInquiryEmail($data) {
    // Email configuration
    $to = 'info@medstudy.global'; // Change to your Gmail address
    $subject = 'New University Inquiry - ' . ($data['university_name'] ?? 'General');
    
    // Prepare email content
    $emailContent = generateEmailContent($data);
    
    // Email headers
    $headers = [
        'From: noreply@medstudy.global',
        'Reply-To: ' . $data['email'],
        'X-Mailer: PHP/' . phpversion(),
        'MIME-Version: 1.0',
        'Content-Type: text/html; charset=UTF-8'
    ];
    
    // Send email
    $success = mail($to, $subject, $emailContent, implode("\r\n", $headers));
    
    // Log email attempt
    error_log("Email send attempt - Success: " . ($success ? 'Yes' : 'No') . " - To: $to");
    
    return $success;
}

/**
 * Generate HTML email content
 */
function generateEmailContent($data) {
    $html = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>New University Inquiry</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: #003585; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .field { margin-bottom: 15px; }
            .field strong { display: inline-block; width: 120px; }
            .footer { background: #003585; color: white; padding: 15px; text-align: center; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h2>New University Inquiry</h2>
            </div>
            <div class="content">
                <p><strong>A new inquiry has been received through the university website:</strong></p>
                
                <div class="field">
                    <strong>Name:</strong> ' . htmlspecialchars($data['name']) . '
                </div>
                
                <div class="field">
                    <strong>Email:</strong> <a href="mailto:' . htmlspecialchars($data['email']) . '">' . htmlspecialchars($data['email']) . '</a>
                </div>
                
                <div class="field">
                    <strong>Phone:</strong> ' . htmlspecialchars($data['phone'] ?? 'Not provided') . '
                </div>
                
                <div class="field">
                    <strong>Country:</strong> ' . htmlspecialchars($data['country']) . '
                </div>
                
                ' . (!empty($data['state']) ? '<div class="field"><strong>State:</strong> ' . htmlspecialchars($data['state']) . '</div>' : '') . '
                
                ' . (!empty($data['city']) ? '<div class="field"><strong>City:</strong> ' . htmlspecialchars($data['city']) . '</div>' : '') . '
                
                ' . (!empty($data['university_name']) ? '<div class="field"><strong>University:</strong> ' . htmlspecialchars($data['university_name']) . '</div>' : '') . '
                
                ' . (!empty($data['country_name']) ? '<div class="field"><strong>Interested Country:</strong> ' . htmlspecialchars($data['country_name']) . '</div>' : '') . '
                
                ' . (!empty($data['source_page']) ? '<div class="field"><strong>Source Page:</strong> ' . htmlspecialchars($data['source_page']) . '</div>' : '') . '
                
                ' . (!empty($data['message']) ? '<div class="field"><strong>Message:</strong><br>' . nl2br(htmlspecialchars($data['message'])) . '</div>' : '') . '
                
                <div class="field">
                    <strong>Submitted:</strong> ' . date('F j, Y \a\t g:i A') . '
                </div>
            </div>
            <div class="footer">
                <p>This inquiry was submitted through MedStudy Global website.</p>
                <p>Please respond to the student at: <strong>' . htmlspecialchars($data['email']) . '</strong></p>
            </div>
        </div>
    </body>
    </html>';
    
    return $html;
}

// Get POST data
$postData = $_POST;

// If JSON data is sent
$jsonData = json_decode(file_get_contents('php://input'), true);
if ($jsonData) {
    $postData = array_merge($postData, $jsonData);
}

// Required fields validation
$requiredFields = ['name', 'email', 'country'];
$missingFields = validateRequiredFields($postData, $requiredFields);

if (!empty($missingFields)) {
    sendResponse(false, 'Missing required fields: ' . implode(', ', $missingFields));
}

// Sanitize and validate input data
$data = [
    'name' => sanitizeInput($postData['name']),
    'email' => validateEmail($postData['email']),
    'phone' => !empty($postData['phone']) ? sanitizeInput($postData['phone']) : '',
    'country' => sanitizeInput($postData['country']),
    'state' => !empty($postData['state']) ? sanitizeInput($postData['state']) : '',
    'city' => !empty($postData['city']) ? sanitizeInput($postData['city']) : '',
    'message' => !empty($postData['message']) ? sanitizeInput($postData['message']) : '',
    'university_name' => !empty($postData['university_name']) ? sanitizeInput($postData['university_name']) : '',
    'country_name' => !empty($postData['country_name']) ? sanitizeInput($postData['country_name']) : '',
    'source_page' => !empty($postData['source_page']) ? sanitizeInput($postData['source_page']) : 'unknown'
];

// Validate email
if (!$data['email']) {
    sendResponse(false, 'Invalid email address provided');
}

// Validate name length
if (strlen($data['name']) < 2) {
    sendResponse(false, 'Name must be at least 2 characters long');
}

// Basic spam protection - check for suspicious patterns
$suspiciousPatterns = [
    'http://',
    'https://',
    'www.',
    '<script',
    'javascript:',
    '[url=',
    '[link='
];

$allText = strtolower($data['name'] . ' ' . $data['message']);
foreach ($suspiciousPatterns as $pattern) {
    if (strpos($allText, $pattern) !== false) {
        sendResponse(false, 'Invalid content detected');
    }
}

// Rate limiting (simple implementation)
$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimitFile = sys_get_temp_dir() . '/inquiry_rate_limit_' . md5($clientIP);

if (file_exists($rateLimitFile)) {
    $lastSubmission = (int)file_get_contents($rateLimitFile);
    if (time() - $lastSubmission < 60) { // 1 minute rate limit
        sendResponse(false, 'Please wait before submitting another inquiry');
    }
}

// Update rate limit file
file_put_contents($rateLimitFile, time());

// Send email
try {
    $emailSent = sendInquiryEmail($data);
    
    if ($emailSent) {
        // Log successful submission (for analytics)
        error_log("INQUIRY_SUCCESS: " . json_encode([
            'email' => $data['email'],
            'university' => $data['university_name'],
            'country' => $data['country'],
            'source' => $data['source_page'],
            'timestamp' => date('Y-m-d H:i:s')
        ]));
        
        sendResponse(true, 'Thank you for your inquiry! We will contact you soon.');
    } else {
        // Log email failure
        error_log("INQUIRY_EMAIL_FAILED: " . json_encode($data));
        sendResponse(false, 'There was an issue sending your inquiry. Please try again later.');
    }
    
} catch (Exception $e) {
    error_log("INQUIRY_ERROR: " . $e->getMessage());
    sendResponse(false, 'An unexpected error occurred. Please try again later.');
}
?> 