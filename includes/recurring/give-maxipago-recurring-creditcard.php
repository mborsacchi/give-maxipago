<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Give_Recurring_Gateway' ) ) {
	return;
}

global $give_recurring_maxipago_credit;

class Give_Recurring_MaxiPago_Credit extends Give_Recurring_Gateway {

	public function init() {

		$this->id = 'maxipago_credito';

		// create as pending.
		$this->offsite = false;
	}

	public function create_payment_profiles() {
		// Creates a payment profile and then sets the profile ID.
		$this->subscriptions['profile_id'] = 'maxipago_credito-' . $this->purchase_data['purchase_key'];
	}

	public function can_cancel( $ret, $subscription ) {

		$ret = false;
		if ( 'active' === $subscription->status ) {
			$ret = true;
		}
		return $ret;
	}

	
	public function complete_signup() {
		$subscription = new Give_Subscription( $this->subscriptions['profile_id'], true );
		maxipago_credit_process_payment( $this->purchase_data, true, $subscription );
	}
}
