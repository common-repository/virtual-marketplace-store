<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.vendasta.com
 * @since      1.0.0
 *
 * @package    Vmstore
 * @subpackage Vmstore/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Vmstore
 * @subpackage Vmstore/includes
 * @author     Adam Bissonnette <adam@mediamanifesto.com>
 */
class Vmstore_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

        register_taxonomy( 'vmstore-tag', 'vmstore-product',
            array()
        );
        register_taxonomy( 'vmstore-package', 'vmstore-product',
            array(
                'rewrite' => array( 'slug' => 'store' ),
            )
        );
        register_taxonomy_for_object_type( 'vmstore-package', 'vmstore-product' );

        flush_rewrite_rules();

        VmstoreHelpers::doHeimdall(array(), "WordPress Admin", "Activation");

        //create basic tags / not needed with custom categories added
        // Vmstore_Activator::add_tag_if_missing("Website", 1);
        // Vmstore_Activator::add_tag_if_missing("Content & Experience", 2);
        // Vmstore_Activator::add_tag_if_missing("Listings", 3);
        // Vmstore_Activator::add_tag_if_missing("Reputation", 4);
        // Vmstore_Activator::add_tag_if_missing("SEO", 5);
        // Vmstore_Activator::add_tag_if_missing("Social", 6);
        // Vmstore_Activator::add_tag_if_missing("Advertising", 7);
	}

	public static function add_tag_if_missing($tag_name, $tag_id) {
		$tag_guid = "vmstore_tag_" . $tag_id;

		$query_args = array(
         'taxonomy' => 'vmstore-tag',
         'hide_empty' => false,
         'name' => $tag_name
        );

        $vmp_search_results = get_terms($query_args);

        $term_ID = -1;
        if (empty($vmp_search_results))
        {
          $term_ID = wp_insert_term( $tag_name, 'vmstore-tag' )["term_id"];
        }
        else
        {
          $term_ID = $vmp_search_results[0]->term_id;
        }

        update_term_meta( $term_ID, "vmp_guid", $tag_guid );
	}
}
