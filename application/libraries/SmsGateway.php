<?php

class SmsGateway {
    private $api_url;
    private $api_key;

    public function __construct($api_url, $api_key) {
        $this->api_url = $api_url;
        $this->api_key = $api_key;
    }

    public function sendSms($to, $message) {
        $data = [
            'to' => $to,
            'message' => $message,
            'api_key' => $this->api_key,
        ];

        // Send an HTTP request to the SMS gateway's API
        $response = $this->sendHttpRequest($this->api_url, 'POST', $data);

        // Handle the response from the SMS gateway
        if ($response === 'SMS_SENT_SUCCESSFULLY') {
            return true;
        } else {
            return false;
        }
    }

    private function sendHttpRequest($url, $method, $data) {
        // Simulate sending an HTTP request
        // In a real-world scenario, you would use curl or a similar library
        // to send HTTP requests to the SMS gateway's API
        // and handle the response accordingly.

        // For this example, we assume a successful response.
        return 'SMS_SENT_SUCCESSFULLY';
    }
}

// Example usage with a hypothetical SMS gateway
$gateway = new SmsGateway('https://api.sms-gateway.com/send', 'your_api_key_here');

$to = 'recipient_phone_number';
$message = 'Hello, this is a test SMS!';

if ($gateway->sendSms($to, $message)) {
    echo "SMS sent successfully!";
} else {
    echo "Failed to send SMS.";
}
