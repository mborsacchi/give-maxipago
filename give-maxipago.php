<?php
/**
 * Plugin Name:          Give - maxiPago! Gateway
 * Plugin URI:        	 https://github.com/mborsacchi/give-maxipago
 * Description:          Adds the maxiPago! payment gateway to the available GiveWP payment methods.
 * Version:              2.0.0
 * Author:               Marcelo Borsacchi
 * Author URI:           https://github.com/mborsacchi
 * Text Domain:          give-maxipago
 * License:              General Public Licence v3 or later
 * Domain Path:          /languages
 *
 * @package             Give
 * @subpackage          maxiPago!
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'GIVE_MAXIPAGO_PLUGIN_DIR' ) ) {
    define( 'GIVE_MAXIPAGO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

require_once GIVE_MAXIPAGO_PLUGIN_DIR . 'includes/lib/maxiPago.php';
require_once GIVE_MAXIPAGO_PLUGIN_DIR . 'includes/give-maxipago-util.php';

require_once GIVE_MAXIPAGO_PLUGIN_DIR . 'includes/give-maxipago-gateway.php';
require_once GIVE_MAXIPAGO_PLUGIN_DIR . 'includes/payment-methods/give-maxipago-creditcard.php';

require_once GIVE_MAXIPAGO_PLUGIN_DIR . 'includes/give-maxipago-recurring-setup.php';
