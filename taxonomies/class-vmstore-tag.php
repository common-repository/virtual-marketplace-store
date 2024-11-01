<?php
class Vmstore_tag extends Vmstore_Taxonomies {

  public function __construct($plugin_name, $version, $config)
  {
    parent::__construct($plugin_name, $version, $config);
  }

  public function register() {
    $labels = array(
      'name'                       => _x( 'Tags', 'Taxonomy General Name', 'text_domain' ),
      'singular_name'              => _x( 'Tag', 'Taxonomy Singular Name', 'text_domain' ),
      'menu_name'                  => __( 'Tags', 'text_domain' ),
      'all_items'                  => __( 'All Tags', 'text_domain' ),
      'parent_item'                => __( 'Parent Tag', 'text_domain' ),
      'parent_item_colon'          => __( 'Parent Tag:', 'text_domain' ),
      'new_item_name'              => __( 'New Tag Name', 'text_domain' ),
      'add_new_item'               => __( 'Add New Tag', 'text_domain' ),
      'edit_item'                  => __( 'Edit Tag', 'text_domain' ),
      'update_item'                => __( 'Update Tag', 'text_domain' ),
      'view_item'                  => __( 'View Tag', 'text_domain' ),
      'separate_items_with_commas' => __( 'Separate tags with commas', 'text_domain' ),
      'add_or_remove_items'        => __( 'Add or remove tags', 'text_domain' ),
      'choose_from_most_used'      => __( 'Choose from the most used', 'text_domain' ),
      'popular_items'              => __( 'Popular Tags', 'text_domain' ),
      'search_items'               => __( 'Search Tags', 'text_domain' ),
      'not_found'                  => __( 'Not Found', 'text_domain' ),
      'no_terms'                   => __( 'No tags', 'text_domain' ),
      'items_list'                 => __( 'Tags list', 'text_domain' ),
      'items_list_navigation'      => __( 'Tags list navigation', 'text_domain' ),
    );
    register_taxonomy( 'vmstore-tag', 'vmstore-package',
        array(
          'hierarchical'          => false,
          'labels'                => $labels,
          'show_ui'               => true,     
          'show_in_menu'          => 'vmstore',
          'show_admin_column'     => true,
          'query_var'             => true,
          'show_tagcloud'         => true,
          'has_archive'           => false,
          'exclude_from_search'   => true,
          'publicly_queryable'    => false,
          'rewrite'           => array( 'slug' => $this->_get_option("slug") . '/tag' ),
        )
    );
    register_taxonomy_for_object_type( 'vmstore-tag', 'vmstore-package' );
  }

  public function _add_metaboxes() {
    //do nothing
  }

  public function _save_meta($post_id) {
    //do nothing
  }
}