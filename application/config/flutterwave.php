<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');


// ------------------------------------------------------------------------
// Flutter Wave library configuration
// ------------------------------------------------------------------------

// Flutter Wave environment, Sandbox or Live

$config['sandbox'] = TRUE; //TRUE for Sandbox - FALSE for live environment

// Flutter Wave API endpoints for Sandbox & Live
$config['payment_endpoint'] = ($config['sandbox']) ? 'flutterwave_webhook_url' : 'flutterwave_webhook_url';
$config['verify_endpoint'] = ($config['sandbox']) ? 'flutterwave_webhook_url' : 'flutterwave_webhook_url';

/* Configuration stars from here */
// Flutter Wave Credentials 
$config['PBFPubKey'] = ($config['sandbox']) ? 'TEST_PUBLIC_KEY' : 'LIVE_PUBLIC_KEY'; /* Public Key for Sandbox : Live */
$config['SECKEY'] = ($config['sandbox']) ? 'TEST_SECRET_KEY' : 'LIVE_SECRET_KEY'; /* Secret Key for Sandbox : Live */
$config['encryption_key'] = ($config['sandbox']) ? 'TEST_ENCRYPTION_KEY' : 'LIVE_ENCRYPTION_KEY'; /* Encryption Key for Sandbox : Live */

// Webhook Secret Hash 
$config['secret_hash'] = ($config['sandbox']) ? 'TEST_SECRET_HASH' : 'LIVE_SECRET_HASH$'; /* Secret HASH for Sandbox : Live */

// What is the default currency?
// $config['currency'] = 'USD';  /* Store Currency Code */
$config['currency'] = 'NGN';  /* Store Currency Code */

// Transaction Prefix if any
$config['txn_prefix'] = 'TXN_PREFIX';  /* Transaction ID Prefix if any */