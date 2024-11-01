<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.vendasta.com
 * @since      1.0.0
 *
 * @package    Vmstore
 * @subpackage Vmstore/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Vmstore
 * @subpackage Vmstore/includes
 * @author     Adam Bissonnette <adam@mediamanifesto.com>
 */
class Vmstore_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
    wp_clear_scheduled_hook('vmstore_sync_event');
    VmstoreHelpers::doHeimdall(array(), "WordPress Admin", "Deactivation");
	}

}
