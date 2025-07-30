<?php
session_start();

// Destroy all session variables
session_unset();
session_destroy();

// Regenerate session ID for security
session_regenerate_id(true);

// Redirect to login page
header("Location: login.php");
exit();
?> 