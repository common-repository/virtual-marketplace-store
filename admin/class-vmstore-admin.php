<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.vendasta.com
 * @since      1.0.0
 *
 * @package    Vmstore
 * @subpackage Vmstore/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Vmstore
 * @subpackage Vmstore/admin
 * @author     Adam Bissonnette <adam@mediamanifesto.com>
 */
class Vmstore_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	private $config;

  private static $human_lmis = array(
    1 => "Website",
    2 => "Content & Experience",
    3 => "Listings",
    4 => "Reputation",
    5 => "SEO",
    6 => "Social",
    7 => "Advertising"
  );

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $config ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->config = $config;
	}

  public function vmstore_ajax() {
    if (is_admin())
    {
      switch($_REQUEST['fn'])
      {
        case 'save':
          $params = array(
            'pid',
            'mid',
            'slug',
            'package_header',
            'package_footer',
            'cta'
          );

          $this->_update_config($params, $_REQUEST);
          die;
        break;
        case 'sync_all':
          $this->vmstore_sync_func(false);
          die;
        break;
        case 'sync_packages':
          $this->_do_heimdall("WordPress Admin", "Manual Packages Sync");
          $response = $this->_sync_packages();
          echo $response;
          die;
        break;
        case 'sync_products':
          $this->_do_heimdall("WordPress Admin", "Manual Products Sync");
          $response = $this->_sync_products();
          echo $response;
          die;
        break;
        case 'sync_custom_categories':
          $this->_do_heimdall("WordPress Admin", "Manual Cateogories Sync");
          $response = $this->_sync_custom_categories();
          echo $response;
          die;
        break;
        case 'package_products':
          $response = $this->_package_products();
          echo $response;
          die;
        break;
        case 'delete_store_data':
          $response = $this->_delete_store_data();
          echo $response;
          die;
        break;
        case 'toggle_cron':
          $cronTime  = wp_next_scheduled ( 'vmstore_sync_event' );

          if (! $cronTime) {
            wp_schedule_event(time(), 'hourly', 'vmstore_sync_event');
            $cronTime  = wp_next_scheduled ( 'vmstore_sync_event' );
            $tz = date_default_timezone_get();
            date_default_timezone_set(get_option('timezone_string'));
            echo "Your store will resync hourly at: " . date_i18n(get_option('date_format') . " " . get_option('time_format'), $cronTime);
            date_default_timezone_set($tz);
          }
          else {
            wp_clear_scheduled_hook('vmstore_sync_event');
            echo "Your store will not resync automatically.";
          }
          die;
        break;
        case 'flush_wipe':
          //this gets wordpress to clear the permalink rules - essential if we change the store slug
          flush_rewrite_rules();
          echo "1";
          die;
        break;
      }
    }
    die;
  }

  public function vmstore_sync_func($automatedSync=true) {
    $action = ($automatedSync)?"Cron Sync":"Manual Sync";
    $this->_do_heimdall("WordPress Admin", $action);
    $response = $this->_sync_packages();

    if ($response == 1) {
      $response = $this->_sync_products();
    }
    if ($response == 1) {
      $response = $this->_package_products();
    }
    if ($response == 1) {
      $response = $this->_sync_custom_categories();
    }

    echo $response;    
  }

  function _update_config($params=array(), $values=array())
  {
    $args = array();

    foreach ($params as $key) {
      if (!is_array($values[$key]))
      {
        $args[$key] = urldecode($values[$key]);
      }
      else
      {
        $args[$key] = ($values[$key]);
      }
    }

    $new_config = array_merge($this->config, $args);
    update_option( $this->plugin_name . '_options', $new_config );
  }

	public function admin_menu_init() {
    add_menu_page(
      'VMStore',
      'VMStore',
      'read',
      'vmstore',
      '',
      'dashicons-cart','40'
    );

		add_submenu_page(
      'vmstore',
      'Tags', 
      'Tags',
      'manage_categories', 
      'edit-tags.php?taxonomy=vmstore-tag&post_type=vmstore-package'
    );

    add_submenu_page(
      'vmstore',
      'VMStore Settings', 
      'Settings', 
      'manage_options', 
      'vmstore-product-admin', 
      array( $this, 'create_admin_page' )
    );
	}

	public function create_admin_page()
	{
	  require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/vmstore-admin-display.php';
	}

	public function admin_page_init() {
		register_setting(
        $this->plugin_name . '_options',        
        'vmstore_user',
        'vmstore_url'
    );

    add_settings_section(
        'public_section',
        'Virtual Marketplace Store Settings', // Page Title
        array( $this, 'print_public_info' ), // Callback to populate content
        $this->plugin_name . '-admin' // Settings Group
    );
	}

	public function print_public_info()
  {
      $vmstore_pid = $this->_get_option("pid");
      $vmstore_mid = $this->_get_option("mid");
      $vmstore_slug = $this->_get_option("slug");
      $vmstore_package_header = $this->_get_option("package_header");
      $vmstore_package_footer = $this->_get_option("package_footer");
      $vmstore_cta = $this->_get_option("cta");

      require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/vmstore-admin-form.php';
  }

  private function _get_option($key)
  {
  	if (isset($this->config[$key])) {
  		return $this->config[$key];
  	}
  	return "";
  }

  private function _do_heimdall($category, $action, $label="")
  {
    VmstoreHelpers::doHeimdall($payload, $category, $action, $label);
  }

  private function _sync_packages()
  {
    $partnerId = $this->_get_option("pid");
    $marketId = $this->_get_option("mid");
    $output = -1;

    if (!empty($marketId) && !empty($partnerId))
    {
      $url = "https://marketplace-packages-api-prod.apigateway.co/marketplace_packages.v1.MarketplacePackages/ListPackages";
    
      $bodyArgs = json_encode(array('partnerId' => $partnerId,
        'pageSize' => 300,
        'marketId' => $marketId,
        'statuses' => array(1),
        'lmiCategory' => null,
        'cursor' => "MA==",
        'sort' => true
      ));

      $args = array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array('content-type' => 'application/json'),
        'body' => $bodyArgs
      );

      $output = $this->_do_rpc($url, $args, array($this, "_sync_packages_cb"));
    }

    return $output;
  }

  private function _sync_packages_cb($response_body)
  {
    $fieldMap = array(
      "name" => "vmp_title",
      "tagline" => "vmp_tagline",
      // "screenshotUrls" => "vmp_gallery",
      "headerImageUrl" => "vmp_banner",
      "icon" => "vmp_icon",
      "productOrder" => "vmp_products",
      "packageId" => "vmp_guid",
      "normalizedAnnualPrice" => "vmp_annual_price",
      "pricing" => "vmp_pricing",
    );

    $json = json_decode($response_body);

    $catalog = $json->packages;

    $product_guids = array();
    $package_guids = array();
    $menu_order = 1;
    foreach ($catalog as $package) {
      if (strtolower($package->status) == "published" && isset($package->products))
      {
        $this->_update_package($package, $fieldMap, true, 'vmstore-package', $menu_order++);
        $product_guids = array_unique(array_merge($product_guids, $package->products));
      }

      $package_guids[] = $package->packageId;
    }


    $vmproduct_guid_list = array("vmproduct_guid_list" => $product_guids);

    $this->_update_config(array("vmproduct_guid_list"), $vmproduct_guid_list);
    $this->_unpublish_missing_packages($package_guids);

    return 1;
  }

  private function _unpublish_missing_packages($vmp_guids)
  {
    $query_args = array(
     'posts_per_page' => -1,
     'post_type' => 'vmstore-package',
     'meta_query'  => array(
      array(
        'key' => 'vmp_guid',
        'value' => $vmp_guids,
        'compare' => 'NOT IN'
        )
      )
    );

    $post_search_results = query_posts($query_args);

    foreach ($post_search_results as $post) {
      $post->post_status = "pending";
      wp_update_post($post);
    }
  }

  private function _sync_products()
  {
    $partnerId = $this->_get_option("pid");
    $marketId = $this->_get_option("mid");
    $output = -1;

    if (!empty($marketId) && !empty($partnerId))
    {
      $url = sprintf("https://vbc-prod.appspot.com/ajax/v1/account/products/?marketId=%s&partnerId=%s",
        $marketId,
        $partnerId);
      $args = array();

      $output = $this->_do_rpc($url, $args, array($this, "_sync_products_cb"));
    }
    return $output;
  }

  private function _sync_products_cb($response_body)
  {
    $fieldMap = array(
      "name" => "vmp_title",
      "tagline" => "vmp_tagline",
      "description" => "vmp_description",
      "keySellingPoints" => "vmp_selling_points",
      "faqs" => "vmp_faqs",
      "screenshotUrls" => "vmp_gallery",
      "headerImageUrl" => "vmp_banner",
      "iconUrl" => "vmp_icon",
      // "" => "vmp_viewurl",
      "lmiCategories" => "vmp_lmis",
      "serviceModel" => "vmp_fulfilment_model",
      "pdfUploadUrls" => "vmp_files",
      "productId" => "vmp_guid"
    );

    $json = json_decode($response_body);

    $catalog = $json->data;

    foreach ($catalog as $product) {
      $this->_update_product($product, $fieldMap, true);
    }

    return 1;
  }

  private function _update_product($product, $fieldMap, $allowCreate=false, $type="vmstore-product")
  {
    $reverseMap = array_flip($fieldMap);

    $arrProduct = (array) $product;

    if (in_array($arrProduct[$reverseMap["vmp_guid"]], $this->config["vmproduct_guid_list"]))
    {
      $vmp_search_results = $this->_query_products($type, $arrProduct[$reverseMap["vmp_guid"]]);

      $post_id = -1;
      if (empty($vmp_search_results) && $allowCreate)
      {
        $postArgs = array(
          'post_title' => $arrProduct[$reverseMap["vmp_title"]],
          'post_type' => $type,
          'post_status' => 'publish'
        );
        $post_id = wp_insert_post( $postArgs );
      }
      else
      {
        $post_id = $vmp_search_results[0]->ID;
      }

      $lockable_fields = array("vmp_title", "vmp_description", "vmp_tagline");

      $field_editable = get_post_meta($post_id, "vmp_locked", true) != 1;
      foreach ($fieldMap as $cur_key => $new_key) {
        if ($field_editable || !in_array($new_key, $lockable_fields))
        {
          update_post_meta( $post_id, $new_key, $arrProduct[$cur_key] );
        }
      }
    }
  }

  private function _update_package($package, $fieldMap, $allowCreate=false, $type="vmstore-package", $menu_order=0)
  {
    $reverseMap = array_flip($fieldMap);

    $arrPackage = (array) $package;

    $vmp_search_results = $this->_query_products($type, $arrPackage[$reverseMap["vmp_guid"]]);

    $post_id = -1;
    if (empty($vmp_search_results) && $allowCreate)
    {
      $postArgs = array(
        'post_title' => $arrPackage[$reverseMap["vmp_title"]],
        'post_type' => $type,
        'post_status' => 'publish'
      );
      $post_id = wp_insert_post( $postArgs );
    }
    else
    {
      $post = $vmp_search_results[0];
      $post_id = $post->ID;

      if ($post->menu_order != $menu_order || $post->post_status != "publish") {
        $post->menu_order = $menu_order;
        $post->post_status = "publish";
        $post = wp_update_post($post);
      }
    }

    $lockable_fields = array("vmp_title", "vmp_description", "vmp_tagline");

    $field_editable = get_post_meta($post_id, "vmp_locked", true) != 1;
    foreach ($fieldMap as $cur_key => $new_key) {
      if ($field_editable || !in_array($new_key, $lockable_fields))
      {
        update_post_meta( $post_id, $new_key, $arrPackage[$cur_key] );
      }
    }
  }

  private function _package_products() {
    $vmp_search_results = $this->_get_packages();

    foreach ($vmp_search_results as $package) {
      $guids = get_post_meta($package->ID, "vmp_products", true);
      $productids = array();

      foreach ($guids as $vmp_guid) {
        $product = $this->_query_products('vmstore-product', $vmp_guid);
        if (!empty($product))
        {
          array_push($productids, $product[0]->ID);

          // $lmis = get_post_meta($product[0]->ID, "vmp_lmis", true);
          // foreach ($lmis as $lmi) {
          //   wp_set_post_terms($package->ID, Vmstore_Admin::$human_lmis[$lmi], 'vmstore-tag', true);
          // }
        }
      }

      update_post_meta($package->ID, 'vmp_product_ids', $productids);
    }

    return 1;
  }

  private function _sync_custom_categories()
  {
    $partnerId = $this->_get_option("pid");
    $marketId = $this->_get_option("mid");
    $output = -1;

    if (!empty($marketId) && !empty($partnerId))
    {
      $url = "https://marketplace-packages-api-prod.apigateway.co/marketplace_packages.v1.MarketplaceStore/GetStoreWithCategories";
      
      $bodyArgs = json_encode(
        array(
          "storeId" => array(
            "partnerId" => $partnerId,
            "marketId" => $marketId
          )
        )
      );

      $args = array(
        'method' => 'POST',
        'timeout' => 45,
        'redirection' => 5,
        'httpversion' => '1.0',
        'blocking' => true,
        'headers' => array('content-type' => 'application/json'),
        'body' => $bodyArgs
      );

      $output = $this->_do_rpc($url, $args, array($this, "_sync_custom_categories_cb"));
    }
    return $output;
  }

  private function _sync_custom_categories_cb($response_body) {
    $json = json_decode($response_body);

    $categories = $json->categories;
    $categoryIds = array();
    $order = 0;

    foreach ($categories as $category) {
      $this->add_tag_if_missing($category->categoryName, $category->categoryId, $order++, $category->visibleInStore);

      foreach ($category->packageIds as $packageId) {
        $categoryIds[] = $category->categoryId;
        $vmp_search_results = $this->_query_products('vmstore-package', $packageId);

        if (!empty($vmp_search_results))
        {
          $post = $vmp_search_results[0];
          wp_set_post_terms($post->ID, $category->categoryName, 'vmstore-tag', true);
        }
      }
    }

    $lmi_tags = array(
      'vmstore_tag_1',
      'vmstore_tag_2',
      'vmstore_tag_3',
      'vmstore_tag_4',
      'vmstore_tag_5',
      'vmstore_tag_6',
      'vmstore_tag_7'
    );

    $categoryIds = array_merge($categoryIds, $lmi_tags);
    $this->remove_excess_categories($categoryIds);
    return 1;
  }

  private function _do_rpc($url, $args, $success_callback=null, $error_callback=null)
  {
    $output = -1;
    $response = wp_remote_post($url, $args);

    $response_code = wp_remote_retrieve_response_code( $response );
    $response_body = wp_remote_retrieve_body( $response );

    if ( !in_array( $response_code, array(200,201) ) || is_wp_error( $response_body ) )
    {
      if (isset($error_callback))
      {
        $output = $error_callback($response);
      }
      else
      {
        $output = 0;
      }
    } else {
      if (isset($success_callback))
      {
        $output = $success_callback($response_body);
      }
      else
      {
        $output = 1;
      }
    }

    return $output;
  }

  private static function remove_excess_categories($categoryIds) {
    $query_args = array(
     'taxonomy' => 'vmstore-tag',
     'hide_empty' => false,
     'meta_query' => array(
        array(
          'key' => 'vmp_guid',
          'value' => $categoryIds,
          'compare' => 'NOT IN'
        ),
        array(
          'key' => 'vmp_guid',
          'compare' => 'EXISTS'
        )
      )
    );
    $vmp_search_results = get_terms($query_args);

    if (!empty($vmp_search_results)) {
      foreach ($vmp_search_results as $term) {
        wp_delete_term($term->term_id, 'vmstore-tag');
      }
    }
  }

  private static function add_tag_if_missing($tag_name, $tag_id, $order, $showinstore) {
    $tag_guid = $tag_id;

    $query_args = array(
     'taxonomy' => 'vmstore-tag',
     'hide_empty' => false,
     'name' => $tag_name
    );

    $vmp_search_results = get_terms($query_args);

    if (empty($vmp_search_results))
    {
      $query_args = array(
       'taxonomy' => 'vmstore-tag',
       'hide_empty' => false,
       'meta_query' => array(
          array(
            'key' => 'vmp_guid',
            'value' => $tag_id
          )
        )
      );
      $vmp_search_results = get_terms($query_args);
    }

    $term_ID = -1;
    if (empty($vmp_search_results))
    {
      $term = wp_insert_term( $tag_name, 'vmstore-tag', array('slug' => $tag_guid) );
      $term_ID = (is_array($term))?$term["term_id"]:-1;
    }
    else
    {
      $term_ID = $vmp_search_results[0]->term_id;
      wp_update_term($term_ID, 'vmstore-tag', array('name' => $tag_name, 'slug' => $tag_guid));
    }

    update_term_meta( $term_ID, "vmp_guid", $tag_guid );
    update_term_meta( $term_ID, "vmp_visibleinstore", $showinstore );
    update_term_meta( $term_ID, "vmp_order", $order );
  }

  private function _delete_store_data() {
    $products = $this->_get_products();

    foreach ($products as $product) {
      wp_delete_post($product->ID, true);
    }

    $packages = $this->_get_packages();

    foreach ($packages as $package) {
      wp_delete_post($package->ID, 'vmstore-package');
    }

    echo "1";
  }

  private function _get_packages() {
    $query_args = array(
     'posts_per_page' => -1,
     'post_type' => 'vmstore-package'
    );

    return query_posts($query_args);
  }

  private function _get_package_by_guid($guid) {
    $query_args = array(
     'posts_per_page' => -1,
     'post_type' => $type,
     'meta_query' => array(
        array(
          'key' => 'vmp_guid',
          'value' => $vmp_guid
        )
      )
    );

    return query_posts($query_args);
  }

  private function _get_products() {
    $query_args = array(
     'posts_per_page' => -1,
     'post_type' => 'vmstore-product'
    );

    return query_posts($query_args);
  }

  private function _query_products($type, $vmp_guid) {
    $query_args = array(
     'posts_per_page' => -1,
     'post_type' => $type,
     'meta_query' => array(
        array(
          'key' => 'vmp_guid',
          'value' => $vmp_guid
        )
      )
    );

    return query_posts($query_args);
  }

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Vmstore_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Vmstore_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vmstore-admin.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Vmstore_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Vmstore_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vmstore-admin.js', array( 'jquery' ), $this->version, false );

	}

}
