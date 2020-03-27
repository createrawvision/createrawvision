<?php

class DigiStoreIpnCest
{
    public function _before(ApiTester $I)
    {
        // create a new user
        $I->cli(['user', 'create', 'user', 'user@mail.com', '--role=subscriber', '--user_pass=password']);
        $I->loginAs('user', 'password');

        // Set RCP into sandbox mode
        $rcp_settings = $I->grabOptionFromDatabase('rcp_settings');
        $rcp_settings['sandbox'] = 1;
        $I->haveOptionInDatabase('rcp_settings', $rcp_settings);

        // Create Pending membership
        $I->cli(['rcp', 'memberships', '--level=1']);
        // new user 
        // a2kA1_rcp_memberships -> pending membership without gateway_id
        // a2kA1_rcp_payments -> pending payment without transaction_id
        // a2kA1_rcp_membershipmeta ->  pending_payment_id
    }


    // test that user can sign up for a new membership (should be acceptance test?)

    // IPN Tests
    // test that invalid signature gets rejected
    public function test_digistore_ipn_signature(ApiTester $I)
    {
        $I->sendPOST('/?listener=digistore');
        $I->seeResponseContains("ERROR: invalid sha signature");
        $I->seeResponseCodeIs(200);
    }

    // test that valid signature gets accepted

    // test that initial payment activates pending membership

    // test that renewal payment extends membership successfully
    // test renewal payment on expired membership

    // test refund only undoes the payment and expires membership only when neccessary

    // test cancelling membership, so it does not expire immediatly

    // test resuming membership undoes cancel when done before missed payment
    // test resuming membership when payment was missed -> Add note that rebilling was resumed

    // test missing payment only logs it 

    // when you can't do anything with the transaction, refund it after checking, maybe add a note for the customer

}
