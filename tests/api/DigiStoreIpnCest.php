<?php

class DigiStoreIpnCest
{
    const API_URL = '/?listener=digistore';

    public function _before(ApiTester $I)
    {
        /** 
         * Set RCP into sandbox mode
         */
        $rcp_settings = $I->grabOptionFromDatabase('rcp_settings');
        $rcp_settings['sandbox'] = 1;
        $I->haveOptionInDatabase('rcp_settings', $rcp_settings);

        /**
         * New user and customer
         */
        $user_id = $I->haveUserInDatabase("ApiTester");
        $customer_id = $I->haveInDatabase('a2kA1_rcp_customers', array(
            'user_id' => $user_id,
            'date_registered' => '2020-03-27 14:48:22',
            'email_verification' => 'none',
            'last_login' => '0000-00-00 00:00:00',
            'has_trialed' => '0',
            'ips' => '',
            'notes' => '',
            'uuid' => 'urn:uuid:9298637e-468b-4641-81fd-9ef69d53f8b8'
        ));
        /**
         * New pending membership
         */
        $subscriptionKey = 'rcp-subscription-key';
        $membership_id = $I->haveInDatabase('a2kA1_rcp_memberships', array(
            'customer_id' => $customer_id,
            'object_id' => 1,
            'object_type' => 'membership',
            'currency' => 'EUR',
            'initial_amount' => '10',
            'recurring_amount' => '10',
            'created_date' => date("Y-m-d H:i:s"),
            'activated_date' => '0000-00-00 00:00:00',
            'trial_end_date' => '0000-00-00 00:00:00',
            'renewed_date' => '0000-00-00 00:00:00',
            'cancellation_date' => '0000-00-00 00:00:00',
            'expiration_date' => date("Y-m-d H:i:s", strtotime("+1 month")),
            'payment_plan_completed_date' => '0000-00-00 00:00:00',
            'auto_renew' => '1',
            'times_billed' => '0',
            'maximum_renewals' => '0',
            'status' => 'pending',
            'gateway_customer_id' => '',
            'gateway_subscription_id' => '',
            'gateway' => 'digistore',
            'signup_method' => 'live',
            'subscription_key' => $subscriptionKey,
            'notes' => '',
            'upgraded_from' => '0',
            'date_modified' => date("Y-m-d H:i:s"),
            'disabled' => '0',
            'uuid' => 'urn:uuid:1aa1a6c8-3ee6-45b4-a4c1-e22e7284cb95'
        ));
        /**
         * New pending payment without transaction id
         */
        $paymentId = $I->haveInDatabase('a2kA1_rcp_payments', array(
            'subscription' => 'CreateRawVision Member (monatlich)',
            'object_id' => 1,
            'object_type' => 'subscription',
            'date' => date("Y-m-d H:i:s"),
            'amount' => '10',
            'user_id' => $user_id,
            'customer_id' => $customer_id,
            'membership_id' => $membership_id,
            'payment_type' => '',
            'transaction_type' => 'new',
            'subscription_key' => $subscriptionKey,
            'transaction_id' => '',
            'status' => 'pending',
            'gateway' => 'digistore',
            'subtotal' => '10',
            'credits' => '0',
            'fees' => '0',
            'discount_amount' => '0',
            'discount_code' => ''
        ));
        $I->haveInDatabase('a2kA1_rcp_membershipmeta', array(
            'rcp_membership_id' => $membership_id,
            'meta_key' => 'pending_payment_id',
            'meta_value' => $paymentId,
        ));
        $I->haveUserMetaInDatabase($user_id, 'rcp_pending_subscription_level', '1');
        $I->haveUserMetaInDatabase($user_id, 'rcp_pending_subscription_key', $subscriptionKey);
        $I->haveUserMetaInDatabase($user_id, 'rcp_pending_payment_id', $paymentId);
        $I->haveUserMetaInDatabase($user_id, 'rcp_pending_subscription_amount', '10');

        /**
         * Set HTTP headers
         */
        $I->haveHttpHeader('content-type', 'application/x-www-form-urlencoded; charset=utf-8');
        $I->haveHttpHeader('user-agent', 'DigiStore-API/1.0');
    }

    /**
     * an empty ipn call should have a 200 status code, with "no membership found" response
     */
    public function test_empty_ipn_call(ApiTester $I)
    {
        $I->sendPOST(self::API_URL);
        $I->seeResponseContains("no membership found");
        $I->seeResponseCodeIs(200);
    }

    /**
     * Initial payment for pending membership should activate the membership
     */
    public function test_initial_payment(ApiTester $I)
    {
        $I->sendPOST(self::API_URL, 'foo=bar&this=raw');
    }

    /**
     * Renewal payment for active payment should extend membership expiration over the next payment date
     */

    /**
     * Renewal payment for expired membership should extend membership expiration
     */

    /**
     * @todo Initial payment for active membership should be ignored?
     */

    /**
     * Refund only undoes the payment (status refunded) and expires membership only when neccessary
     */

    /**
     * cancelling membership only works for active memberships and does not expire immediatly
     */

    /**
     * test resuming membership undoes cancel only when in cancelled state
     * an expired membership just gets ignored
     */

    /**
     * test resuming membership when payment was missed -> Add note that rebilling was resumed
     */

    /**
     * test missing payment only logs it -> Gets expired automatically
     */

    /**
     * Refunding an active/cancelled membership sets expiry time back
     */

    /**
     * when you can't do anything with the transaction, refund it after checking, maybe add a note for the customer
     */
}
