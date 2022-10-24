<?php

	function register_maxipago_payment_credit( $gateways ) {
	
		$gateways['maxipago_credito'] = array(
		'admin_label'    => __( 'Credit card - maxiPago!', 'give-maxipago' ),
		'checkout_label' => __( 'Credit card - maxiPago!', 'give-maxipago' ),
		);
		
		return $gateways;
	}

	add_filter( 'give_payment_gateways', 'register_maxipago_payment_credit' );


	//Process checkout submission. 
	function maxipago_process_credit_donation( $posted_data ) {
		func_credit_donation_maxipago($posted_data, false);
	}


	function func_credit_donation_maxipago( $donation_data, $isRecurrence = false ) 
	{
		//THE RECURRENCE FEATURE IS NOT AVAILABLE IN THIS VERSION

		// Make sure we don't have any left over errors present.
		give_clear_errors();
	
		// Any errors?
		$errors = give_get_errors();
	
		// No errors, proceed.
		if ( ! $errors ) 
		{
			$form_id         = intval( $donation_data['post_data']['give-form-id'] );
			$price_id        = ! empty( $donation_data['post_data']['give-price-id'] ) ? $donation_data['post_data']['give-price-id'] : 0;
			$donation_amount = ! empty( $donation_data['price'] ) ? $donation_data['price'] : 0;
	
			// Setup the payment details.
			$donation_args = array(
				'price'           => $donation_amount,
				'give_form_title' => $donation_data['post_data']['give-form-title'],
				'give_form_id'    => $form_id,
				'give_price_id'   => $price_id,
				'date'            => $donation_data['date'],
				'user_email'      => $donation_data['user_email'],
				'purchase_key'    => $donation_data['purchase_key'],
				'currency'        => give_get_currency(),
				'user_info'       => $donation_data['user_info'],
				'status'          => 'pending',
				'gateway'         => 'maxipago_credito'

				//Cartão de Crédito
			);
	
			// Record the pending donation.
			$donation_id = give_insert_payment( $donation_args );

			if ( ! $donation_id ) 
			{
				// Record Gateway Error as Pending Donation in Give is not created.
				give_record_gateway_error(__( 'maxiPago! Error', 'give-maxipago' ), sprintf(__( 'Unable to create a pending donation with Give.', 'give-maxipago' )));
	
				give_send_back_to_checkout( '?payment-mode=maxipago_credito' );
				return;
			}
			else 
			{
				try {

					#region Integração Maxipago

					$maxiPago = new maxiPago;
					$maxiPago->setLogger(GIVE_MAXIPAGO_PLUGIN_DIR .'/logs','INFO');
		
					$merchantId 	 =  give_get_option('give_maxipago_merchant_id');
					$merchantKey	 =  give_get_option('give_maxipago_merchant_key');
					$sandboxEnabled  =  give_get_option('give_maxipago_env_test');
					$debugEnabled    =  give_get_option('give_maxipago_debug_mode');
					$processorID 	 =  give_get_option('give_maxipago_adquirente' );
					$formatNumRef    =  give_get_option('give_maxipago_numreferencia' );
					
					$maxiPago->setCredentials($merchantId , $merchantKey);
					
					if($debugEnabled)
						$maxiPago->setDebug(true);

					if($sandboxEnabled)
						$maxiPago->setEnvironment("TEST");
					else
						$maxiPago->setEnvironment("LIVE");


					$maxipago_data = array(
						"processorID"		=> $processorID,
						"referenceNum" 		=> give_maxipago_get_num_ref($formatNumRef, $donation_id), 
						"fraudCheck"		=> "N",
						"number" 			=> $donation_data['card_info']['card_number'], 
						"expMonth" 			=> sprintf( '%02d', $donation_data['card_info']['card_exp_month'] ), 
						"expYear" 			=> $donation_data['card_info']['card_exp_year'],
						"cvvNumber" 		=> $donation_data['card_info']['card_cvc'],
						"currencyCode"  	=> give_get_currency( $form_id ),
						"chargeTotal" 		=> $donation_amount, 
						"billingName"		=> $donation_data['card_info']['card_name'], 
						"billingAddress"  	=> $donation_data['card_info']['card_address'],
						"billingAddress2" 	=> $donation_data['card_info']['card_address_2'],
						//"billingDistrict" 	=> '', // I don't have this information on GiveWP
						"billingCity" 		=> $donation_data['card_info']['card_city'], 
						"billingState" 		=> $donation_data['card_info']['card_state'],
						"billingPostalCode" => $donation_data['card_info']['card_zip'], 
						"billingCountry" 	=> $donation_data['card_info']['card_country'], 
						"billingEmail" 		=> $donation_data['post_data']['give_email']
					);

					if($isRecurrence) 
					{
						$maxipago_data["action"] = "new";
						$maxipago_data["startDate"] = date("Y-m-d");
						$maxipago_data["frequency"] = "1";
						$maxipago_data["period"] = "monthly"; //Interval of payment: 'daily', 'weekly', 'monthly'
						$maxipago_data["installments"] = "-1"; //-1 = Infinite
						$maxipago_data["failureThreshold"] = "2"; //Number of declines before email notification
						
						$maxiPago->createRecurring($maxipago_data);
					}
					else 
					{
						$maxiPago->creditCardSale($maxipago_data);
					}


					if ($maxiPago->isErrorResponse()) 
					{
						give_update_payment_status( $donation_id, 'failed' );						
						give_insert_payment_note( $donation_id, $isRecurrence ? "ER_RC01" : "ER_PC01");
						
						$responseResult = wp_json_encode($maxiPago->getResult(),  JSON_PRETTY_PRINT);
						give_insert_payment_note( $donation_id, "ERROR RESPONSE DETAILS: {$responseResult }" );
						
						give_set_error( 'maxipago_response_error', "Transaction has failed: ".$maxiPago->getMessage());
						give_send_back_to_checkout( '?payment-mode=maxipago_credito' );
					}
					elseif ($maxiPago->isTransactionResponse()) 
					{
						if ($maxiPago->getResponseCode() == "0") 
						{ 
							$transaction_id = $maxiPago->getOrderID();
							
							$auxCreditinfo = $maxipago_data;
							
							$auxCreditinfo['number'] = "****************";
							$auxCreditinfo['cvvNumber'] = "***";

							$data_sent = wp_json_encode($auxCreditinfo, JSON_PRETTY_PRINT);

							give_set_payment_transaction_id( $donation_id, $transaction_id );
							give_insert_payment_note( $donation_id, "DATA SENT: {$data_sent}" );

							$transResult = wp_json_encode($maxiPago->getResult(), JSON_PRETTY_PRINT);
							give_insert_payment_note( $donation_id, "RESULT: {$transResult }" );

							if ( !empty( $transaction_id ) ) 
							{
								// Set status to completed.
								give_update_payment_status( $donation_id );

								// All done. Send to success page.
								give_send_to_success_page();
							}
							else 
							{
								give_insert_payment_note( $donation_id, $isRecurrence ? "ER_RC02" : "ER_PC02");
								give_insert_payment_note( $donation_id, "TRANSACTION ID VACIO" );

								give_set_error( 'maxipago_transaction_error', "Internal error - Transaction Id empty.");
								give_send_back_to_checkout( '?payment-mode=maxipago_credito' );
							}
						}
						else 
						{ 
							//DECLINED
							give_update_payment_status( $donation_id, 'failed' );
							give_insert_payment_note( $donation_id, $isRecurrence ? "ER_RC03" : "ER_PC03");

							$declinedResult = wp_json_encode($maxiPago->getResult(),  JSON_PRETTY_PRINT);
							give_insert_payment_note( $donation_id, "ERROR DETAILS: {$declinedResult }" );

							give_set_error( 'maxipago_transaction_error', $maxiPago->getMessage());
							give_send_back_to_checkout( '?payment-mode=maxipago_credito' );
						}    
					}

					#endregion

				}
				catch (Exception $e) 
				{ 
					give_update_payment_status( $donation_id, 'failed' );
					give_insert_payment_note( $donation_id, $isRecurrence ? "ER_RC04" : "ER_PC04");

					give_insert_payment_note( $donation_id, "EXCEPTION DETAILS: {$e}" );

					//give_set_error( 'maxipago_catch_error', $e->getMessage()." in ".$e->getFile()." on line ".$e->getLine());
					give_set_error( 'maxipago_catch_error',   "Fatal error: {$e->getMessage()}" );
					give_send_back_to_checkout( '?payment-mode=maxipago_credito' );
				}
			}
		} 
		else 
		{
			give_send_back_to_checkout( '?payment-mode=maxipago_credito' );
		} //End if().
	}



	add_action( 'give_gateway_maxipago_credito', 'maxipago_process_credit_donation' );