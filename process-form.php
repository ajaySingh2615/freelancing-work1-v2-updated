<?php
// Enable error reporting for development
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// Include PHPMailer (we'll install via Composer or download manually)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// For now, we'll use a simple alternative without PHPMailer
// If you want to use PHPMailer, uncomment the lines above and install it

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $country = isset($_POST['country']) ? sanitizeInput($_POST['country']) : '';
    $state = isset($_POST['state']) ? sanitizeInput($_POST['state']) : '';
    $city = isset($_POST['city']) ? sanitizeInput($_POST['city']) : '';
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $country_code = isset($_POST['country-code']) ? sanitizeInput($_POST['country-code']) : '+91';
    
    // Validate form data
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($phone)) {
        $errors[] = "Phone number is required";
    }
    
    if (empty($country)) {
        $errors[] = "Country is required";
    }
    
    // If validation passes, process the form
    if (empty($errors)) {
        // Format the phone number with country code
        $fullPhone = $country_code . $phone;
        
        // Alternative method: Save to file and send via cURL to a mail service
        $emailSent = sendEmailAlternative($name, $email, $fullPhone, $country, $state, $city);
        
        if ($emailSent) {
            // Return success response for AJAX
            $response = [
                'status' => 'success',
                'message' => 'Thank you for your inquiry! We will contact you soon.'
            ];
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } else {
            $response = [
                'status' => 'error',
                'message' => 'There was an error sending your inquiry. Please try again later.'
            ];
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    } else {
        // Return error response for AJAX
        $response = [
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
} else {
    // If not a POST request, redirect to home page
    header("Location: index.php");
    exit;
}

// Alternative email function that saves to file for now
function sendEmailAlternative($name, $email, $fullPhone, $country, $state, $city) {
    // Create email content
    $emailContent = "
NEW INQUIRY - REQUEST FREE COUNSELLING
=====================================
Date: " . date("d F Y, H:i:s") . "
Source: Website Hero Form

STUDENT DETAILS:
- Name: $name
- Email: $email
- Phone: $fullPhone
- Preferred Country: $country";

    if (!empty($state)) {
        $emailContent .= "\n- State: $state";
    }
    
    if (!empty($city)) {
        $emailContent .= "\n- City: $city";
    }

    $emailContent .= "\n
INSTRUCTIONS:
Please contact this student as soon as possible for free counselling.

Contact Email: ajaysingh261526@gmail.com
=====================================
";

    // Save to a file (you can manually check this file and send emails)
    $filename = 'inquiries/inquiry_' . date('Y-m-d_H-i-s') . '_' . sanitizeForFilename($name) . '.txt';
    
    // Create directory if it doesn't exist
    if (!is_dir('inquiries')) {
        mkdir('inquiries', 0755, true);
    }
    
    // Save inquiry to file
    $fileSaved = file_put_contents($filename, $emailContent);
    
    // Also try sending via simple mail with error suppression
    $to = "ajaysingh261526@gmail.com";
    $subject = "Request Free Counselling - New MBBS Inquiry";
    $message = nl2br($emailContent);
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Sunrise Global Education <noreply@sunriseglobaleducation.com>\r\n";
    
    // Try to send email, but don't fail if it doesn't work
    @mail($to, $subject, $message, $headers);
    
    // Return true if file was saved (so form doesn't show error)
    return $fileSaved !== false;
}

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to sanitize filename
function sanitizeForFilename($string) {
    return preg_replace('/[^a-zA-Z0-9_-]/', '_', $string);
}
?> 