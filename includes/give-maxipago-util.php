<?php

function give_maxipago_get_num_ref($formatoNumReferencia, $auxDonation_id)
{
	$aux_numReferencia = '';

	if($formatoNumReferencia == 'id') {
		$aux_numReferencia = $auxDonation_id;
	}
	else if($formatoNumReferencia == 'data') {

		$aux_numReferencia = date("YmdHis");
	}
	else if($formatoNumReferencia == 'id_data') {
		$aux_numReferencia = $auxDonation_id . "_" . date("YmdHis");
	}
	else {
		$aux_numReferencia = date("YmdHis");
	}

	return $aux_numReferencia;
}