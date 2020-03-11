<?php

/**
 * Mailchimp Subscription Digistore Coupon
 * @author Josef Wittmann
 * @version 1.0
 *
 * This code generates a unique Digistore24 coupon code and subscribes the user to Mailchimp with the coupon code included.
 * It requires the Digistore24 API PHP file to be in the same folder. See @link https://docs.digistore24.com/api-de/
 */

function generate_random_string($length = 6)
{
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
    $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

/**
 * @return string - the digistore coupon
 */
function generate_coupon_code($ds_api, $email)
{

  $data = array(
    'code'              => generate_random_string(),
    'product_ids'       => 'all',
    'expires_at'        => '14d',
    'first_amount'      => 5,
    'is_count_limited'  => true,
    'count_left'        => 1,
    'upgrade_policy'    => 'not_valid',
    'note'              => $email
  );

  $response = $ds_api->createVoucher($data);
  $coupon_code = $response->code;

  return $coupon_code;
}

/**
 * Adds a subscriber to the Mailchimp List
 * 
 * @return mixed - the response
 */
function add_subscriber($api_key, $list_id, $coupon_code, $email, $fname)
{
  $mc_key_split = explode('-', $api_key);
  $mc_datacenter = end($mc_key_split);
  $mc_api_url = 'https://' . $mc_datacenter . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members';

  $user_data = array(
    "email_address" => $email,
    "status" => "pending",
    "merge_fields" => array(
      "FNAME" => $fname,
      "COUPON" => $coupon_code
    )
  );

  $ch = curl_init();
  curl_setopt_array($ch, array(
    CURLOPT_URL => $mc_api_url,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json'
    ),
    CURLOPT_POST => TRUE,
    CURLOPT_POSTFIELDS => json_encode($user_data),
    CURLOPT_USERPWD => 'coupon_generator:' . $api_key,
    CURLOPT_HTTPAUTH => CURLAUTH_BASIC
  ));

  $response = curl_exec($ch);
  $json_res = json_decode($response, TRUE);
  return $json_res;
}

define('DIGISTORE_API_KEY', '300226-aKsbLBQbmF3PbsxUekQEGRdPj6iEKeLfCdgNgA6t'); // https://www.digistore24.com/vendor/settings/account_access/api
define('MAILCHIMP_API_KEY', '4aba98320716ec778689ef590d27ff4e-us13'); // https://us13.admin.mailchimp.com/account/api/
define('MAILCHIMP_LIST_ID', 'a04c23f499');

require_once __DIR__ . '/ds24_api.php';

function subscribe_with_coupon()
{
  header('Content-Type: application/json');
  $email = $_POST["EMAIL"];
  $fname = $_POST["FNAME"];
  if (empty($email) || empty($fname)) {
    return array("error" => 'email and name required');
  }
  try {
    $ds_api = DigistoreApi::connect(DIGISTORE_API_KEY);
    $coupon_code = generate_coupon_code($ds_api, $email);
    $response = add_subscriber(MAILCHIMP_API_KEY, MAILCHIMP_LIST_ID, $coupon_code, $email, $fname);
    if (array_key_exists('id', $response)) {
      return array("success" => true);
    } else {
      $ds_api->deleteVoucher($coupon_code);
      return array("error" => $response["title"]);
    }
  } catch (DigistoreApiException $e) {
    $error_message = $e->getMessage();
    return array("error" => $error_message);
  } finally {
    $ds_api->disconnect();
  }
}
