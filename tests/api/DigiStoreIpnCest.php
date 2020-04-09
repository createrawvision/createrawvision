<?php

function rand_str($len = 16)
{
    return substr(md5(uniqid(mt_rand(), true)), 0, $len);
}

class DigiStoreIpnCest
{
    private const API_URL = '/?listener=digistore';
    private $user_id;
    private $customer_id;
    private $membership_id;
    private $payment_id;
    private $subscription_key = 'rcp_subscription_key';
    private $gateway_subscription_id = 'digistore_order_id';

    public function _before(ApiTester $I)
    {
        $this->set_rcp_sandbox($I);
        $this->create_new_customer($I);
        // sync timezones with application
        date_default_timezone_set("Europe/Berlin");
    }

    /** 
     * Set RCP into sandbox mode
     */
    private function set_rcp_sandbox(ApiTester $I)
    {
        $rcp_settings = $I->grabOptionFromDatabase('rcp_settings');
        $rcp_settings['sandbox'] = 1;
        $I->haveOptionInDatabase('rcp_settings', $rcp_settings);
    }

    /**
     * Create new user and customer
     */
    private function create_new_customer(ApiTester $I)
    {
        $this->user_id = $I->haveUserInDatabase("ApiTester");
        $this->customer_id = $I->haveInDatabase('a2kA1_rcp_customers', array(
            'user_id' => $this->user_id,
            'date_registered' => date("Y-m-d H:i:s"),
            'email_verification' => 'none',
            'last_login' => '0000-00-00 00:00:00',
            'has_trialed' => '0',
            'ips' => '',
            'notes' => '',
            'uuid' => 'customer-uuid'
        ));
    }

    private function create_membership(ApiTester $I, $membership_args = array())
    {
        $default_membership_args = array(
            'customer_id'       => $this->customer_id,
            'object_id'         => 1,
            'object_type'       => 'membership',
            'currency'          => 'EUR',
            'initial_amount'    => '10',
            'recurring_amount'  => '10',
            'status'            => 'active',
            'gateway'           => 'digistore',
            'auto_renew'        => '1',
            'subscription_key'  => $this->subscription_key,
            'gateway_subscription_id' => $this->gateway_subscription_id,
            'created_date' => date("Y-m-d H:i:s", strtotime("-2 minutes")),
            'expiration_date' => date("Y-m-d H:i:s", strtotime("+1 month -2 minutes")),
            'disabled' => 0
        );
        $membership_args = array_merge($default_membership_args, $membership_args);
        $this->membership_id = $I->haveInDatabase('a2kA1_rcp_memberships', $membership_args);
    }

    private function create_payment(ApiTester $I, $payment_args = array())
    {
        $default_payment_args = array(
            'subscription' => 'CreateRawVision Member (monatlich)',
            'object_id' => 1,
            'object_type' => 'subscription',
            'date' => date("Y-m-d H:i:s"),
            'amount' => '10',
            'subtotal' => '10',
            'user_id' => $this->user_id,
            'customer_id' => $this->customer_id,
            'membership_id' => $this->membership_id,
            'payment_type' => '',
            'transaction_type' => 'new',
            'subscription_key' => $this->subscription_key,
            'transaction_id' => '',
            'status' => 'complete',
            'gateway' => 'digistore',
            'credits' => '0',
            'fees' => '0',
            'discount_amount' => '0',
            'discount_code' => ''
        );
        $payment_args = array_merge($default_payment_args, $payment_args);
        $this->payment_id = $I->haveInDatabase('a2kA1_rcp_payments', $payment_args);
    }

    /**
     * Create new pending membership
     * and new pending payment without transaction id
     */
    private function create_pending_membership(ApiTester $I)
    {
        $this->create_membership($I, array(
            'status' => 'pending',
            'gateway_subscription_id' => ''
        ));

        $this->create_payment($I, array(
            'status' => 'pending'
        ));

        $I->haveInDatabase('a2kA1_rcp_membershipmeta', array(
            'rcp_membership_id' => $this->membership_id,
            'meta_key' => 'pending_payment_id',
            'meta_value' => $this->payment_id,
        ));
        $I->haveUserMetaInDatabase($this->user_id, 'rcp_pending_subscription_level', '1');
        $I->haveUserMetaInDatabase($this->user_id, 'rcp_pending_subscription_key', $this->subscription_key);
        $I->haveUserMetaInDatabase($this->user_id, 'rcp_pending_payment_id', $this->payment_id);
        $I->haveUserMetaInDatabase($this->user_id, 'rcp_pending_subscription_amount', '10');
    }

    private function create_active_membership(ApiTester $I)
    {
        $this->create_membership($I, array(
            'created_date' => date("Y-m-d H:i:s", strtotime("-15 days")),
            'expiration_date' => date("Y-m-d H:i:s", strtotime("+15 days"))
        ));

        $this->create_payment($I, array(
            'date' => date("Y-m-d H:i:s", strtotime("-15 days"))
        ));
    }

    private function create_expired_membership(ApiTester $I)
    {
        $this->create_membership($I, array(
            'created_date' => date("Y-m-d H:i:s", strtotime("-45 days")),
            'expiration_date' => date("Y-m-d H:i:s", strtotime("-15 days")),
            'status' => 'expired'
        ));

        $this->create_payment($I, array(
            'date' => date("Y-m-d H:i:s", strtotime("-45 days"))
        ));
    }

    private function get_expiration_timestamp(ApiTester $I)
    {
        $expiration_date = $I->grabFromDatabase('a2kA1_rcp_memberships', 'expiration_date', array('id' => $this->membership_id));
        return DateTime::createFromFormat("Y-m-d H:i:s", $expiration_date)->getTimestamp();
    }

    private function get_some_transaction_id(ApiTester $I)
    {
        return $I->grabFromDatabase('a2kA1_rcp_payments', 'id', array('membership_id' => $this->membership_id));
    }

    private function receive_ipn(ApiTester $I, $body)
    {
        $default_body = array(
            'event' => 'on_payment',
            'order_id' => $this->gateway_subscription_id,
            'transaction_id' => rand_str(),
            'transaction_date' => date("Y-m-d H:i:s"),
            'next_payment_at' => date("Y-m-d H:i:s", strtotime('+1 month')),
            'parent_transaction_id' => '',
            'transaction_amount' => '10.00',
            'pay_sequence_no' => '1',
            'custom' => '',
            'buyer_email' => 'buyer@mail.com',
            'buyer_first_name' => 'FirstName',
            'buyer_last_name' => 'LastName',
            'country' => 'DE',
            'currency' => 'EUR',
            'pay_method' => 'digistore_pay_method',
            'invoice_url' => 'https://crv.test/invoice_url',
            'rebilling_stop_url' => 'https://crv.test/rebilling_stop_url',
            'receipt_url' => 'https://crv.test/receipt_url',
            'renew_url' => 'https://crv.test/renew_url',
            'request_refund_url' => 'https://crv.test/request_refund_url',
        );
        $body = array_merge($default_body, $body);
        $I->sendPOST(self::API_URL, $body);
    }

    private function receive_initial_payment_ipn(ApiTester $I, $transaction_id = null)
    {
        $this->receive_ipn($I, array(
            'transaction_id' => $transaction_id ?? rand_str(),
            'pay_sequence_no' => 1,
            'custom' => $this->user_id . '|' . $this->membership_id
        ));
    }

    private function receive_renewal_payment_ipn(ApiTester $I, $pay_sequence_no, $transaction_id = null)
    {
        $this->receive_ipn($I, array(
            'transaction_id' => $transaction_id ?? rand_str(),
            'pay_sequence_no' => $pay_sequence_no
        ));
    }

    private function receive_payment_missed_ipn(ApiTester $I, $transaction_id = null)
    {
        $this->receive_ipn($I, array(
            'event' => 'on_payment_missed',
            'transaction_id' => $transaction_id ?? rand_str(),
        ));
    }

    private function receive_refund_ipn(ApiTester $I, $parent_transaction_id)
    {
        $this->receive_ipn($I, array(
            'event' => 'on_refund',
            'parent_transaction_id' => $parent_transaction_id
        ));
    }

    private function receive_cancel_ipn(ApiTester $I)
    {
        $this->receive_ipn($I, array(
            'event' => 'on_rebill_cancelled'
        ));
    }

    private function receive_resume_ipn(ApiTester $I)
    {
        $this->receive_ipn($I, array(
            'event' => 'on_rebill_resumed'
        ));
    }

    /**
     * Initial payment for pending membership should activate the membership
     */
    public function test_initial_payment(ApiTester $I)
    {
        $this->create_pending_membership($I);

        $this->receive_initial_payment_ipn($I);

        $I->seeInDatabase('a2kA1_rcp_memberships', array(
            'id' => $this->membership_id,
            'status' => 'active'
        ));
    }

    /**
     * Initial payment for expired membership should undo the expiration
     */
    public function test_initial_payment_undoes_expiration(ApiTester $I)
    {
        $this->create_expired_membership($I);

        $this->receive_initial_payment_ipn($I);

        // See active membership with expiration in one month
        $I->seeInDatabase('a2kA1_rcp_memberships', array(
            'id' => $this->membership_id,
            'status' => 'active',
            'expiration_date >=' => date("Y-m-d H:i:s", strtotime("+1 month -1 day"))
        ));
    }

    /**
     * Renewal payment for expired membership should undo the expiration
     */
    public function test_renewal_payment_undoes_expiration(ApiTester $I)
    {
        $this->create_expired_membership($I);

        $this->receive_renewal_payment_ipn($I, 2);

        // See active membership with expiration in one month
        $I->seeInDatabase('a2kA1_rcp_memberships', array(
            'id' => $this->membership_id,
            'status' => 'active',
            'expiration_date >=' => date("Y-m-d H:i:s", strtotime("+1 month -1 day"))
        ));
    }

    /**
     * Renewal payment for active or cancelled membership should renew the membership
     */
    public function test_renewal_payment_extends_expiration(ApiTester $I)
    {
        $this->create_active_membership($I);

        $this->receive_renewal_payment_ipn($I, 2);

        $I->seeInDatabase('a2kA1_rcp_memberships', array(
            'id' => $this->membership_id,
            'status' => 'active',
            'expiration_date >=' => date("Y-m-d H:i:s", strtotime("+1 month -1 day"))
        ));
    }

    /**
     * test missing payment gets expired
     */
    public function test_missing_payment_expires(ApiTester $I)
    {
        $this->create_active_membership($I);

        $this->receive_payment_missed_ipn($I);

        $I->seeInDatabase('a2kA1_rcp_memberships', array(
            'id' => $this->membership_id,
            'status' => 'expired'
        ));
    }

    /**
     * Refund undoes one payment (status refunded), and expires membership
     */
    public function test_refund_expires_membership(ApiTester $I)
    {
        $this->create_active_membership($I);

        $parent_transaction_id = $this->get_some_transaction_id($I);

        $this->receive_refund_ipn($I, $parent_transaction_id);

        $I->seeInDatabase('a2kA1_rcp_memberships', array(
            'id' => $this->membership_id,
            'status' => 'expired'
        ));
    }

    /**
     * cancelling membership does not expire immediatly
     * test resuming membership undoes cancel
     */
    public function test_cancel_and_resume_membership(ApiTester $I)
    {
        $this->create_active_membership($I);

        $this->receive_cancel_ipn($I);

        $I->seeInDatabase('a2kA1_rcp_memberships', array(
            'id' => $this->membership_id,
            'status' => 'cancelled'
        ));

        $this->receive_resume_ipn($I);

        $I->seeInDatabase('a2kA1_rcp_memberships', array(
            'id' => $this->membership_id,
            'status' => 'active'
        ));
    }

    /**
     * one day after the `next_payment_at` ipn-parameter is the new expiration date
     * set the next_payment_at to 100 days later and check that expiration is in 101 days midnight
     */
    public function test_ipn_determines_expiration(ApiTester $I)
    {
        $this->create_active_membership($I);

        $this->receive_ipn($I, array(
            'next_payment_at' => date('Y-m-d', strtotime('+100 days'))
        ));

        $I->seeInDatabase('a2kA1_rcp_memberships', array(
            'id' => $this->membership_id,
            'expiration_date' => date('Y-m-d', strtotime('+101 days')) . ' 00:00:00'
        ));
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
}
