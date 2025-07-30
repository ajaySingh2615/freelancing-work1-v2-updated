<?php
/**
 * PHPMailer Implementation for Reliable Email Delivery
 * Save this file as process-form-phpmailer.php
 * 
 * SETUP INSTRUCTIONS:
 * 1. Download PHPMailer from: https://github.com/PHPMailer/PHPMailer
 * 2. Extract to a 'PHPMailer' folder in your project root
 * 3. Update Gmail credentials below
 * 4. Enable 2-Factor Authentication on Gmail
 * 5. Generate an App Password for this application
 */

// Include PHPMailer files
require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
        
        // Send email using PHPMailer
        $emailSent = sendEmailWithPHPMailer($name, $email, $fullPhone, $country, $state, $city);
        
        if ($emailSent) {
            $response = [
                'status' => 'success',
                'message' => 'Thank you for your inquiry! We will contact you soon.'
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'There was an error sending your inquiry. Please try again later.'
            ];
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        $response = [
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $errors
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}

function sendEmailWithPHPMailer($name, $email, $fullPhone, $country, $state, $city) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your-gmail@gmail.com'; // UPDATE THIS
        $mail->Password   = 'your-app-password';    // UPDATE THIS (use App Password, not regular password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        
        // Recipients
        $mail->setFrom('your-gmail@gmail.com', 'Sunrise Global Education'); // UPDATE THIS
        $mail->addAddress('ajaysingh261526@gmail.com', 'Ajay Singh');
        $mail->addReplyTo($email, $name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Request Free Counselling - New MBBS Inquiry';
        
        $mail->Body = "
        <html>
        <head>
            <title>Request Free Counselling - MBBS Inquiry</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .header { background-color: #003585; color: white; padding: 20px; text-align: center; }
                .content { padding: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                td { padding: 10px; border-bottom: 1px solid #eee; }
                .label { font-weight: bold; background-color: #f8f9fa; width: 30%; }
                .footer { margin-top: 30px; padding: 15px; background-color: #f8f9fa; text-align: center; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>Request Free Counselling</h1>
                <p>New MBBS Inquiry from Sunrise Global Education Website</p>
            </div>
            <div class='content'>
                <h2>Student Details</h2>
                <table>
                    <tr>
                        <td class='label'>Full Name:</td>
                        <td>$name</td>
                    </tr>
                    <tr>
                        <td class='label'>Email Address:</td>
                        <td><a href='mailto:$email'>$email</a></td>
                    </tr>
                    <tr>
                        <td class='label'>Phone Number:</td>
                        <td><a href='tel:$fullPhone'>$fullPhone</a></td>
                    </tr>
                    <tr>
                        <td class='label'>Preferred Country:</td>
                        <td>$country</td>
                    </tr>";
        
        if (!empty($state)) {
            $mail->Body .= "
                    <tr>
                        <td class='label'>State:</td>
                        <td>$state</td>
                    </tr>";
        }
        
        if (!empty($city)) {
            $mail->Body .= "
                    <tr>
                        <td class='label'>City:</td>
                        <td>$city</td>
                    </tr>";
        }
        
        $mail->Body .= "
                    <tr>
                        <td class='label'>Inquiry Date:</td>
                        <td>" . date("d F Y, H:i:s") . "</td>
                    </tr>
                    <tr>
                        <td class='label'>Source:</td>
                        <td>Website - Hero Form (Request Free Counselling)</td>
                    </tr>
                </table>
            </div>
            <div class='footer'>
                <p><strong>Sunrise Global Education</strong></p>
                <p>Leading MBBS Abroad Consultancy</p>
                <p>This inquiry was submitted through the website contact form</p>
            </div>
        </body>
        </html>";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("PHPMailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?> 