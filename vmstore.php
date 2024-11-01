<?php

/**
 *
 * @link              https://www.vendasta.com
 * @since             1.2.6
 * @package           Vmstore
 *
 * @wordpress-plugin
 * Plugin Name:       Virtual Marketplace Store
 * Plugin URI:        https://www.vendasta.com
 * Description:       Render your products, packages and categories from the Vendasta marketplace and sync them to your website.
 * Version:           1.2.6
 * Author:            Vendasta
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       virtual-marketplace-store
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'vmstore_version', '1.2.6' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-vmstore-activator.php
 */
function activate_vmstore() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vmstore-activator.php';
	Vmstore_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-vmstore-deactivator.php
 */
function deactivate_vmstore() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-vmstore-deactivator.php';
	Vmstore_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_vmstore' );
register_deactivation_hook( __FILE__, 'deactivate_vmstore' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-vmstore.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_vmstore() {

	$plugin = new Vmstore();
	$plugin->run();

}
run_vmstore();