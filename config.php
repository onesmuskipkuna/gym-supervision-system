<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gym_supervision');

// Check if mysqli class exists before creating connection
if (!class_exists('mysqli')) {
    die("MySQLi extension is not installed or enabled in your PHP environment.");
}

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SMS API configuration for textsms.co.ke
define('SMS_API_URL', 'https://api.textsms.co.ke/api/v1/send');
define('SMS_API_KEY', 'YOUR_TEXTSMS_API_KEY_HERE'); // Replace with your actual API key

/**
 * Send SMS using textsms.co.ke API
 * @param string $to Recipient phone number
 * @param string $message Message content
 * @return bool True on success, false on failure
 */
function send_sms($to, $message) {
    $data = array(
        'apikey' => SMS_API_KEY,
        'to' => $to,
        'message' => $message,
        'sender' => 'GymSupervision'
    );

    $ch = curl_init(SMS_API_URL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode == 200) {
        return true;
    } else {
        return false;
    }
}
?>
