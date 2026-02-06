<?php
/**
 * Plugin Name: Quick Contact Form
 * Plugin URI: https://wpexperts.io/
 * Description: A really, really simple GDPR compliant contact form. There is nothing to configure, just add your email address and it's ready to go. But you then have access to a huge range of easy to use features.
 * Version: 8.2.7
 * Author: Quick Contact Form
 * Author URI: https://wpexperts.io/
 * Requires PHP: 5.6
 * Requires at least: 4.6
 * Text Domain: quick-contact-form
 * Domain Path: /languages
 *
 * Original Author: Aerin
 *
  *
*/

namespace Quick_Contact_Form;

use \Quick_Contact_Form\Control\Plugin;
use \Quick_Contact_Form\Control\Freemius_Config;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


if ( ! function_exists( 'Quick_Contact_Form\run_Quick_Contact_Form' ) ) {
	define( 'QUICK_CONTACT_FORM_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
	define( 'QUICK_CONTACT_FORM_PLUGIN_FILE', plugin_basename( __FILE__ ) );
	define( 'QUICK_CONTACT_FORM_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
	define( 'QUICK_CONTACT_FORM_PLUGIN_NAME', 'quick-contact-form' );

// Include the autoloader so we can dynamically include the classes.
	require_once QUICK_CONTACT_FORM_PLUGIN_DIR . 'control/autoloader.php';


	function run_Quick_Contact_Form() {
		$freemius = new Freemius_Config();
		$freemius = $freemius->init();
		// Signal that SDK was initiated.
		do_action( 'quick_contact_form_fs_loaded' );

		register_activation_hook( __FILE__, array( '\Quick_Contact_Form\Control\Activator', 'activate' ) );

		register_deactivation_hook( __FILE__, array( '\Quick_Contact_Form\Control\Deactivator', 'deactivate' ) );

		/**
		 * @var \Freemius $freemius freemius SDK.
		 */
		$freemius->add_action( 'after_uninstall', array( '\Quick_Contact_Form\Control\Uninstall', 'uninstall' ) );

		$plugin = new Plugin( 'quick-contact-form',
			'8.2.7',
			$freemius );
		$plugin->run();
		
	}

	run_Quick_Contact_Form();
} else {
	die( esc_html__( 'Cannot execute as the plugin already exists, if you have a free version installed deactivate that and try again', 'quick-contact-form' ) );
}