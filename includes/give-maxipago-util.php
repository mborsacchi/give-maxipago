<?php

function give_maxipago_get_num_ref($formatoNumReferencia, $auxDonation_id)
{
	switch ( $formatoNumReferencia ) {
		case 'id':
			return $auxDonation_id;
			break;

		case 'data':
			return date("YmdHis");
			break;
			
		case 'id_data':
			return $auxDonation_id . "_" . date("YmdHis");
			break;

		default:
			return date("YmdHis");
			break;
	}
}

// maxipago! Gateway period format:
// daily = dia(s), weekly = semana(s), monthly = mês(es), biMonthly = bimestral, quarterly = trimestral, semiannual = semestral, annual = anual

function give_maxipago_get_period($periodo) 
{
	switch ( $periodo ) {
		case 'day':
			return 'daily';
			break;

		case 'month':
			return 'monthly';
			break;
			
		case 'year':
			return 'anual';
			break;

		default:
			return "monthly";
			break;
	}
}