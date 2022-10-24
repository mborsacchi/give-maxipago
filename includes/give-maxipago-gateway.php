<?php

function maxipago_register_payment_gateway_sections( $sections ) {
	
	$sections['maxipago-settings'] = __( 'maxiPago!', 'give-maxipago' );

	return $sections;
}

add_filter( 'give_get_sections_gateways', 'maxipago_register_payment_gateway_sections' );


function maxipago_register_payment_gateway_setting_fields( $settings ) {

	switch ( give_get_current_setting_section() ) {

		case 'maxipago-settings':
			$settings = array(
				array(
					'id'   => 'give_title_maxipago',
					'type' => 'title',
				),
			);

			$settings[] = array(
				'name' => __( 'Merchant ID', 'give-maxipago' ),
				'desc' => __( 'Digite o Merchant ID proporcionado por maxiPago!', 'give-maxipago' ),
				'id'   => 'give_maxipago_merchant_id',
				'type' => 'text',
		    );

			$settings[] = array(
				'name' => __( 'Merchant Key', 'give-maxipago' ),
				'desc' => __( 'Digite o Merchant key proporcionado por maxiPago!', 'give-maxipago' ),
				'id'   => 'give_maxipago_merchant_key',
				'type' => 'text',
		    );

			$settings[] = array(
				'name' => __( 'Adquirente', 'give-maxipago' ),
				'desc' => __( 'Escolha a adquirente que irá processar as transaçoes', 'give-maxipago' ),
				'id'   => 'give_maxipago_adquirente',
				'type' => 'select',
				'default' => '2',
				'options' => [
					'1' => __('SIMULADOR DE TESTES', 'give-maxipago'),
					'2' => __('Rede', 'give-maxipago'),
					'4' => __('Cielo', 'give-maxipago'),
					'5' => __('TEF', 'give-maxipago'),
					'6' => __('Elavon', 'give-maxipago'),
					'8' => __('ChasePaymentech', 'give-maxipago'),
					'3' => __('GetNet', 'give-maxipago'),
				],
		    );

		/*	$settings[] = array(
				'name' => __( 'Formato número de referencia', 'give-maxipago', 'give-maxipago' ),
				'desc' => __( 'Escolha o número de referencia que será enviado a maxiPago!', 'give-maxipago' ),
				'id'   => 'give_maxipago_numreferencia',
				'type' => 'select',
				'default' => 'titulo',
				'options' => [
					'id' => __('Id da doação', 'give-maxipago'),
					'titulo' => __('Título da página', 'give-maxipago'),
					'titulo_id' => __('Título da página + Id da doação', 'give-maxipago'),
				],
		    );*/

			$settings[] = array(
				'name' => __( 'Formato número de referencia', 'give-maxipago' ),
				'desc' => __( 'Escolha o número de referencia que será enviado a maxiPago!', 'give-maxipago' ),
				'id'   => 'give_maxipago_numreferencia',
				'type' => 'radio_inline',
				'row_classes' => 'give-subfield',
				'options' => [
					'id' => __( 'Id da doação', 'give-maxipago' ),
					'data'  => __( 'Data/Hora', 'give-maxipago' ),
					'id_data'  => __( 'Id da doação + Data/Hora', 'give-maxipago' ),
				],
				'default'     => 'titulo',
		    );


			$settings[] = array(
				'name' => __( 'Ambiente de testes', 'give-maxipago' ),
				'desc' => __( 'Quando habilitado nenhuma transação será de fato processada e será utilizada a url de testes da API.', 'give-maxipago' ),
				'id'   => 'give_maxipago_env_test',
				'type' => 'checkbox',
		    );

			$settings[] = array(
				'name' => __( 'Debug', 'give-maxipago' ),
				'desc' => __( 'Quando habilitado mostrará os erros em tela para depuração (debug).', 'give-maxipago' ),
				'id'   => 'give_maxipago_debug_mode',
				'type' => 'checkbox',
		    );

			$settings[] = array(
				'id'   => 'give_title_maxipago',
				'type' => 'sectionend',
			);

			break;

	} // End switch().

	return $settings;
}

add_filter( 'give_get_settings_gateways', 'maxipago_register_payment_gateway_setting_fields' );