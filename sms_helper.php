<?php
// sms_helper.php - helper functions for sending SMS using provided API

function send_sms($to, $message) {
    $apiKey = 'f4b3d290214b0b674286ba535117952c';
    $apiUrl = 'https://api.example-sms.com/send'; // Replace with actual SMS API endpoint

    $postData = [
        'to' => $to,
        'message' => $message,
        'api_key' => $apiKey
    ];

    $ch = curl_init($apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        error_log("SMS sending error: " . $error);
        return false;
    }

    $result = json_decode($response, true);
    if ($result && isset($result['success']) && $result['success'] === true) {
        return true;
    } else {
        error_log("SMS sending failed: " . $response);
        return false;
    }
}
?>
