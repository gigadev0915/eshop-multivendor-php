<?php
/* 
    Strip Payments Library v1.0 for codeigniter 
    by Jaydeep Goswami
*/

/* 
    1. get_credentials()
    2. create_customer($customer_data)
    3. construct_event($request_body, $sigHeader, $secret,$tolerance = DEFAULT_TOLERANCE)
    4. create_payment_intent($c_data)
    5. curl($url, $method = 'GET', $data = [])
*/
class SMS_gateway
{
    private $secret_key = "";
    private $publishable_key = "";
    private $webhook_secret_key = "";
    private $currency_code = "";
    private $url = "";

    function __construct()
    {
        // $this->CI = &get_instance();
        // $this->CI->load->helper('url');
        // $this->CI->load->helper('form');
        $settings = get_settings('payment_method', true);
        $system_settings = get_settings('system_settings', true);

        // $this->secret_key = (isset($settings['stripe_secret_key'])) ? $settings['stripe_secret_key'] : "";
        // $this->publishable_key = (isset($settings['stripe_publishable_key'])) ? $settings['stripe_publishable_key'] : "";
        // $this->webhook_secret_key = (isset($settings['stripe_webhook_secret_key'])) ? $settings['stripe_webhook_secret_key'] : "";
        // $this->currency_code = (isset($settings['stripe_currency_code'])) ? strtolower($settings['stripe_currency_code']) : "usd";
        // $this->url = "https://api.stripe.com/";
    }

    public function parse_sms(string $string, string $country_code, string $mobile, string $sms)
    {
        $search = array($country_code, $mobile, $sms);

        $replace = array('{country_code}', '{only_mobile_number}', '{message}');

        $parsedString = str_replace($search, $replace, $string);

        return $parsedString;
    }

    public function send_sms()
    {
        $data['sms_gateway'] = get_settings('system_settings',true);
        return $data;
    }
}
