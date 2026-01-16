<?php

/******************************************************************************
 *  
 *  PROJECT: Flynax Classifieds Software
 *  VERSION: 4.9.3
 *  LICENSE: FL30UFXTM56M - https://www.flynax.com/flynax-software-eula.html
 *  PRODUCT: General Classifieds
 *  DOMAIN: gmowin.online
 *  FILE: PAYPALREST.GATEWAY.PHP
 *  
 *  The software is a commercial product delivered under single, non-exclusive,
 *  non-transferable license for one domain or IP address. Therefore distribution,
 *  sale or transfer of the file in whole or in part without permission of Flynax
 *  respective owners is considered to be illegal and breach of Flynax License End
 *  User Agreement.
 *  
 *  You are not allowed to remove this information from the file without permission
 *  of Flynax respective owners.
 *  
 *  Flynax Classifieds Software 2024 | All copyrights reserved.
 *  
 *  https://www.flynax.com
 ******************************************************************************/

class paypalREST
{
    public $payment_info;
    public $account_id;
    public $errors;

    public $results;
    public $approved = false;

    public function __construct($payment, $accountId)
    {
        global $config;

        $this->payment_info = $payment;
        $this->account_id = $accountId;
        $this->results = new stdClass();
        $this->api = $config['android_paypal_sandbox'] ? 'https://api.sandbox.paypal.com' : 'https://api.paypal.com';

        $this->getTransaction();
    }

    public function getTransaction()
    {
        global $config;


        $data = array('grant_type' => 'client_credentials');

        // get access token
        $ch = curl_init();

        if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        curl_setopt($ch, CURLOPT_URL, $this->api . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Accept: application/json", "Accept-Language: en_US"));
        curl_setopt($ch, CURLOPT_USERPWD, $config['android_paypal_client_id'] . ':' . $config['android_paypal_secret']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        $response = curl_exec($ch);
        curl_close($ch);

        if (empty($response)) {
            $this->errors[] = "paypal_no_token_received";
            $GLOBALS['rlDebug']->logger("ANDROID BILLING (PayPal REST): " . __FUNCTION__ . "(), no token received for account with id: " . $this->account_id);
        } else {
            $data = json_decode($response);

            $ch = curl_init();

            if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            }

            curl_setopt($ch, CURLOPT_URL, $this->api . '/v1/payments/payment/' . $this->payment_info['tracking_id']);
            curl_setopt($ch, CURLOPT_POST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . $data->access_token));
            $response = curl_exec($ch);
            curl_close($ch);

            if (empty($response)) {
                $this->errors[] = "paypal_no_get_payment_response";
                $GLOBALS['rlDebug']->logger("ANDROID BILLING (PayPal REST): " . __FUNCTION__ . "(), no received any response for payment with ID: " . $this->payment_info['tracking_id']);
            } else {
                $data = json_decode($response);

                ob_start();
                print_r($this->payment_info);
                print_r($response);
                $o = ob_get_clean();
                $GLOBALS['rlDebug']->logger("ANDROID BILLING (PayPal REST):" . $o);

                if ($data->state == 'approved'
                    && $data->transactions[0]->amount->total == $this->payment_info['amount']
                    && strtolower($data->transactions[0]->amount->currency) == strtolower($config['android_billing_currency'])) {

                    $this->results->transaction_id = $data->transactions[0]->related_resources[0]->sale->id;
                    $this->approved = true;
                } else {
                    $this->errors[] = "paypal_transaction_is_not_approved";
                    $GLOBALS['rlDebug']->logger("ANDROID BILLING (PayPal REST): " . __FUNCTION__ . "(), The payment with ID: " . $this->payment_info['tracking_id'] . " isn't approved or amount is wrong");
                }
            }
        }
    }
}
