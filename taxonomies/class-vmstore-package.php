<?php
class Vmstore_package extends Vmstore_Taxonomies {

  public function __construct($plugin_name, $version, $config)
  {
    parent::__construct($plugin_name, $version, $config);
  }

  public function register() {
    $labels = array(
        'name'                  => _x( 'Packages', 'Post Type General Name', 'text_domain' ),
        'singular_name'         => _x( 'Package', 'Post Type Singular Name', 'text_domain' ),
        'menu_name'             => __( 'VMStore', 'text_domain' ),
        'name_admin_bar'        => __( 'Package', 'text_domain' ),
        'archives'              => __( 'Package Archives', 'text_domain' ),
        'attributes'            => __( 'Package Attributes', 'text_domain' ),
        'parent_item_colon'     => __( 'Parent Package:', 'text_domain' ),
        'all_items'             => __( 'Packages', 'text_domain' ),
        'add_new_item'          => __( 'Add New Package', 'text_domain' ),
        'add_new'               => __( 'Add New Package', 'text_domain' ),
        'new_item'              => __( 'New Package', 'text_domain' ),
        'edit_item'             => __( 'Edit Package', 'text_domain' ),
        'update_item'           => __( 'Update Package', 'text_domain' ),
        'view_item'             => __( 'View Package', 'text_domain' ),
        'view_items'            => __( 'View Packages', 'text_domain' ),
        'search_items'          => __( 'Search Packages', 'text_domain' ),
        'not_found'             => __( 'Not found', 'text_domain' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
        'featured_image'        => __( 'Featured Image', 'text_domain' ),
        'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
        'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
        'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
        'insert_into_item'      => __( 'Insert into Package', 'text_domain' ),
        'uploaded_to_this_item' => __( 'Uploaded to this Package', 'text_domain' ),
        'items_list'            => __( 'Packages list', 'text_domain' ),
        'items_list_navigation' => __( 'Packages list navigation', 'text_domain' ),
        'filter_items_list'     => __( 'Filter Packages list', 'text_domain' ),
    );
    $args = array(
        'label'                 => __( 'Package', 'text_domain' ),
        'description'           => __( 'Virtual Marketplace Store', 'text_domain' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'page-attributes'),
        'taxonomies'            => array( ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => 'vmstore',
        'menu_position'         => 5,
        'menu_icon'             => 'dashicons-cart',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false,
        'exclude_from_search'   => false,
        'publicly_queryable'    => true,
        'capability_type'       => 'post',
        'rewrite'               => array('slug' => $this->_get_option("slug")),
        'register_meta_box_cb' => array($this, '_add_metaboxes'),
        'taxonomies'    => array(
            'vmstore-tag'
        )
    );
    register_post_type( 'vmstore-package', $args );
  }

  public function _add_metaboxes() {
    add_meta_box(
        'vmstore_package_details',
        'Details',
        array($this, 'vmstore_package_details'),
        'vmstore-package',
        'normal',
        'default'
    );

    add_meta_box(
        'vmstore_package_selling_points',
        'Products',
        array($this, 'vmstore_package_products'),
        'vmstore-package',
        'normal',
        'default'
    );

    add_meta_box(
        'vmstore_package_meta',
        'Product Meta',
        array($this, 'vmstore_package_meta'),
        'vmstore-package',
        'side',
        'default'
    );
  }

  public function vmstore_package_details() {
    global $post;
    wp_nonce_field( basename( __FILE__ ), 'vmp_fields' );
    $vmp_title = get_post_meta( $post->ID, 'vmp_title', true );
    $vmp_tagline = get_post_meta( $post->ID, 'vmp_tagline', true );

    echo '<div class="vmstore-form">';
    echo vwformtools\FormHelpers::gen_field("Title", "vmp_title", $vmp_title, "text");
    echo vwformtools\FormHelpers::gen_field("Tagline", "vmp_tagline", $vmp_tagline, "text");
    echo '</div>';
  }

  public function vmstore_package_products() {
    global $post;
    $vmp_products = get_post_meta( $post->ID, 'vmp_products', true );
    $vmp_product_ids = get_post_meta( $post->ID, 'vmp_product_ids', true );

    echo '<div class="vmstore-form">';
    echo vwformtools\FormHelpers::gen_field("Product GUIDs", "vmp_products", $vmp_products, "multiinput");
    echo vwformtools\FormHelpers::gen_field("Product IDs", "vmp_product_ids", $vmp_product_ids, "multiinput");
    echo '</div>';
  }

  public function vmstore_package_meta() {
    global $post;
    $vmp_banner = get_post_meta( $post->ID, 'vmp_banner', true );
    $vmp_icon = get_post_meta( $post->ID, 'vmp_icon', true );
    $vmp_guid = get_post_meta( $post->ID, 'vmp_guid', true );
    $vmp_locked = get_post_meta( $post->ID, 'vmp_locked', true );

    echo '<div class="vmstore-form">';
    echo vwformtools\FormHelpers::gen_field("Banner Url", "vmp_banner", $vmp_banner, "text");
    echo vwformtools\FormHelpers::gen_field("Icon Url", "vmp_icon", $vmp_icon, "text");
    echo vwformtools\FormHelpers::gen_field("GUID", "vmp_guid", $vmp_guid, "text");
    echo vwformtools\FormHelpers::gen_field("Disable Updates", "vmp_locked", $vmp_locked, "toggle");
    echo '</div>';
  }

  public function _save_meta( $post_id ) {

    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }

    if ( 'vmstore-package' !== get_post_type($post_id) || ! isset( $_POST['vmp_title'] ) || ! wp_verify_nonce( $_POST['vmp_fields'], basename(__FILE__) ) ) {
        return $post_id;
    }
    $vmp_meta = array();
    $vmp_meta['vmp_title'] = esc_textarea($_POST['vmp_title']);
    $vmp_meta['vmp_tagline'] = esc_textarea($_POST['vmp_tagline']);
    $vmp_meta['vmp_banner'] = esc_textarea($_POST['vmp_banner']);
    $vmp_meta['vmp_icon'] = esc_textarea($_POST['vmp_icon']);
    $vmp_meta['vmp_locked'] = isset($_POST['vmp_locked']);

    foreach ( $vmp_meta as $key => $value ) :
        if ( 'revision' === get_post_type($post_id) ) {
            return;
        }
        if ( get_post_meta( $post_id, $key, false ) ) {
            update_post_meta( $post_id, $key, $value );
        } else {
            add_post_meta( $post_id, $key, $value);
        }
        if ( ! $value ) {
            delete_post_meta( $post_id, $key );
        }
    endforeach;
  }
}