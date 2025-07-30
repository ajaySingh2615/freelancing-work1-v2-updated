<?php
// Enable error reporting for development
// ini_set('display_errors', 1);
// error_reporting(E_ALL);

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = isset($_POST['name']) ? sanitizeInput($_POST['name']) : '';
    $email = isset($_POST['email']) ? sanitizeInput($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $subject = isset($_POST['subject']) ? sanitizeInput($_POST['subject']) : '';
    $message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';
    $consent = isset($_POST['consent']) ? true : false;
    
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
    
    if (empty($subject)) {
        $errors[] = "Subject is required";
    }
    
    if (empty($message)) {
        $errors[] = "Message is required";
    }
    
    if (!$consent) {
        $errors[] = "You must agree to the privacy policy";
    }
    
    // If validation passes, process the form
    if (empty($errors)) {
        // Prepare email content
        $to = "del@ruseducation.in"; // Change this to your email
        $emailSubject = "Contact Form: $subject";
        
        $emailContent = "
        <html>
        <head>
            <title>Contact Form Submission</title>
        </head>
        <body>
            <h2>Contact Form Submission</h2>
            <table>
                <tr>
                    <td><strong>Name:</strong></td>
                    <td>$name</td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td>$email</td>
                </tr>
                <tr>
                    <td><strong>Phone:</strong></td>
                    <td>$phone</td>
                </tr>
                <tr>
                    <td><strong>Subject:</strong></td>
                    <td>$subject</td>
                </tr>
                <tr>
                    <td><strong>Message:</strong></td>
                    <td>" . nl2br($message) . "</td>
                </tr>
                <tr>
                    <td><strong>Date:</strong></td>
                    <td>" . date("Y-m-d H:i:s") . "</td>
                </tr>
            </table>
        </body>
        </html>
        ";
        
        // Set email headers
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: Rus Education <noreply@ruseducation.in>' . "\r\n";
        $headers .= 'Reply-To: ' . $email . "\r\n";
        
        // Send email
        $mailSent = mail($to, $emailSubject, $emailContent, $headers);
        
        // Optional: Save to database
        if ($mailSent) {
            // Save to database (example code)
            // $conn = new mysqli("localhost", "username", "password", "database");
            // $stmt = $conn->prepare("INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            // $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message);
            // $stmt->execute();
            // $stmt->close();
            // $conn->close();
            
            // Return success response for AJAX
            $response = [
                'status' => 'success',
                'message' => 'Thank you for your message! We will get back to you soon.'
            ];
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } else {
            $response = [
                'status' => 'error',
                'message' => 'There was an error sending your message. Please try again later.'
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

// Function to sanitize input data
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?> 