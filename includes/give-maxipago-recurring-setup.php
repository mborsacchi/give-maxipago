<?php

//Credit Card
function give_maxipago_credito_register_gateway( $gateways ) 
{
	if ( class_exists( 'Give_Recurring' ) ) {
		include_once GIVE_MAXIPAGO_PLUGIN_DIR . 'includes/recurring/give-maxipago-recurring-creditcard.php';
		$give_recurring_maxipago_credit = new Give_Recurring_MaxiPago_Credit();
		$gateways['maxipago_credito']    = 'Give_Recurring_MaxiPago_Credit';
	}

	return $gateways;
}

add_action( 'give_recurring_available_gateways', 'give_maxipago_credito_register_gateway' );