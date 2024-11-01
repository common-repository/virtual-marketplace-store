<?php
class Vmstore_product extends Vmstore_Taxonomies {

  public function __construct($plugin_name, $version, $config)
  {
    parent::__construct($plugin_name, $version, $config);
  }

  public function register() {
    $labels = array(
      'name'                  => _x( 'Products', 'Post Type General Name', 'text_domain' ),
      'singular_name'         => _x( 'Product', 'Post Type Singular Name', 'text_domain' ),
      'menu_name'             => __( 'VMStore', 'text_domain' ),
      'name_admin_bar'        => __( 'Product', 'text_domain' ),
      'archives'              => __( 'Product Archives', 'text_domain' ),
      'attributes'            => __( 'Product Attributes', 'text_domain' ),
      'parent_item_colon'     => __( 'Parent Product:', 'text_domain' ),
      'all_items'             => __( 'Products', 'text_domain' ),
      'add_new_item'          => __( 'Add New Product', 'text_domain' ),
      'add_new'               => __( 'Add New Product', 'text_domain' ),
      'new_item'              => __( 'New Product', 'text_domain' ),
      'edit_item'             => __( 'Edit Product', 'text_domain' ),
      'update_item'           => __( 'Update Product', 'text_domain' ),
      'view_item'             => __( 'View Product', 'text_domain' ),
      'view_items'            => __( 'View Products', 'text_domain' ),
      'search_items'          => __( 'Search Products', 'text_domain' ),
      'not_found'             => __( 'Not found', 'text_domain' ),
      'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
      'featured_image'        => __( 'Featured Image', 'text_domain' ),
      'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
      'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
      'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
      'insert_into_item'      => __( 'Insert into Product', 'text_domain' ),
      'uploaded_to_this_item' => __( 'Uploaded to this Product', 'text_domain' ),
      'items_list'            => __( 'Products list', 'text_domain' ),
      'items_list_navigation' => __( 'Products list navigation', 'text_domain' ),
      'filter_items_list'     => __( 'Filter Products list', 'text_domain' ),
    );
    $args = array(
      'label'                 => __( 'Product', 'text_domain' ),
      'description'           => __( 'Virtual Marketplace Store', 'text_domain' ),
      'labels'                => $labels,
      'supports'              => array( 'title'),
      'taxonomies'            => array( ),
      'hierarchical'          => false,
      'public'                => false,
      'show_ui'               => true,
      'show_in_menu'          => 'vmstore',
      'menu_position'         => 5,
      'menu_icon'             => 'dashicons-cart',
      'show_in_admin_bar'     => true,
      'show_in_nav_menus'     => true,
      'can_export'            => true,
      'has_archive'           => false,
      'exclude_from_search'   => true,
      'publicly_queryable'    => false,
      'capability_type'       => 'post',
      // 'rewrite'               => array('slug' => $this->_get_option("slug") . '/products'),
      'register_meta_box_cb' => array($this, '_add_metaboxes'),
    );
    register_post_type( 'vmstore-product', $args );
  }

  public function _add_metaboxes() {
    add_meta_box(
        'vmstore_product_details',
        'Details',
        array($this, 'vmstore_product_details'),
        'vmstore-product',
        'normal',
        'default'
    );
    add_meta_box(
        'vmstore_product_selling_points',
        'Key Selling Points',
        array($this, 'vmstore_product_selling_points'),
        'vmstore-product',
        'normal',
        'default'
    );
    add_meta_box(
        'vmstore_product_faqs',
        'Frequently Asked Questions',
        array($this, 'vmstore_product_faqs'),
        'vmstore-product',
        'normal',
        'default'
    );

    add_meta_box(
        'vmstore_product_gallery',
        'Gallery',
        array($this, 'vmstore_product_gallery'),
        'vmstore-product',
        'normal',
        'default'
    );

    add_meta_box(
        'vmstore_product_files',
        'Files',
        array($this, 'vmstore_product_files'),
        'vmstore-product',
        'normal',
        'default'
    );

    add_meta_box(
        'vmstore_product_meta',
        'Product Meta',
        array($this, 'vmstore_product_meta'),
        'vmstore-product',
        'side',
        'default'
    );
  }

  public function vmstore_product_details() {
    global $post;
    wp_nonce_field( basename( __FILE__ ), 'vmp_fields' );
    $vmp_title = get_post_meta( $post->ID, 'vmp_title', true );
    $vmp_tagline = get_post_meta( $post->ID, 'vmp_tagline', true );
    $vmp_description = get_post_meta( $post->ID, 'vmp_description', true );

    echo '<div class="vmstore-form">';
    echo vwformtools\FormHelpers::gen_field("Title", "vmp_title", $vmp_title, "text");
    echo vwformtools\FormHelpers::gen_field("Tagline", "vmp_tagline", $vmp_tagline, "text");
    echo vwformtools\FormHelpers::gen_field("Description", "vmp_description", $vmp_description, "editor");
    echo '</div>';
  }

  public function vmstore_product_selling_points() {
    global $post;
    $vmp_faqs = get_post_meta( $post->ID, 'vmp_faqs', true );
    $vmp_selling_points = get_post_meta( $post->ID, 'vmp_selling_points', true );

    echo '<div class="vmstore-form">';
    echo vwformtools\FormHelpers::gen_field("Key Selling Points", "vmp_selling_points", $vmp_selling_points, "multiinput");
    echo '</div>';
  }

  public function vmstore_product_faqs() {
    global $post;
    $vmp_faqs = get_post_meta( $post->ID, 'vmp_faqs', true );

    echo '<div class="vmstore-form">';
    echo vwformtools\FormHelpers::gen_field("Frequently Asked Questions", "vmp_faqs", $vmp_faqs, "multifaq");
    echo '</div>';
  }

  public function vmstore_product_meta() {
    global $post;
    $vmp_banner = get_post_meta( $post->ID, 'vmp_banner', true );
    $vmp_icon = get_post_meta( $post->ID, 'vmp_icon', true );
    $vmp_locked = get_post_meta( $post->ID, 'vmp_locked', true );

    echo '<div class="vmstore-form">';
    echo vwformtools\FormHelpers::gen_field("Banner Url", "vmp_banner", $vmp_banner, "text");
    echo vwformtools\FormHelpers::gen_field("Icon Url", "vmp_icon", $vmp_icon, "text");
    echo vwformtools\FormHelpers::gen_field("Disable Updates", "vmp_locked", $vmp_locked, "toggle");
    echo '</div>';
  }

  public function vmstore_product_gallery() {
    global $post;
    $vmp_gallery = get_post_meta( $post->ID, 'vmp_gallery', true );

    echo '<div class="vmstore-form">';
    echo vwformtools\FormHelpers::gen_field("Gallery Image Urls", "vmp_gallery", $vmp_gallery, "multiinput");
    echo '</div>';
  }

  public function vmstore_product_files() {
    global $post;
    $vmp_files = get_post_meta( $post->ID, 'vmp_files', true );

    echo '<div class="vmstore-form">';
    echo vwformtools\FormHelpers::gen_field("Files", "vmp_files", $vmp_files, "multiinput");
    echo '</div>';
  }

  public function _save_meta( $post_id ) {
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return $post_id;
    }

    if ( 'vmstore-product' !== get_post_type($post_id) || ! isset( $_POST['vmp_title'] ) || ! wp_verify_nonce( $_POST['vmp_fields'], basename(__FILE__) ) ) {
        return $post_id;
    }

    $vmp_meta['vmp_title'] = esc_textarea($_POST['vmp_title']);
    $vmp_meta['vmp_tagline'] = esc_textarea($_POST['vmp_tagline']);
    $vmp_meta['vmp_description'] = ($_POST['vmp_description']);
    $vmp_meta['vmp_banner'] = esc_textarea($_POST['vmp_banner']);
    $vmp_meta['vmp_icon'] = esc_textarea($_POST['vmp_icon']);
    $vmp_meta['vmp_locked'] = isset($_POST['vmp_locked']);
    // $vmp_meta['vmp_gallery'] = esc_textarea($_POST['vmp_gallery']);
    // $vmp_meta['vmp_selling_points'] = esc_textarea($_POST['vmp_selling_points']);
    // $vmp_meta['vmp_faqs'] = esc_textarea($_POST['vmp_faqs']);

    foreach ( $vmp_meta as $key => $value ) {
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
    }
  }
}