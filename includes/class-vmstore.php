<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.vendasta.com
 * @since      1.0.0
 *
 * @package    Vmstore
 * @subpackage Vmstore/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Vmstore
 * @subpackage Vmstore/includes
 * @author     Adam Bissonnette <adam@mediamanifesto.com>
 */
class Vmstore {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Vmstore_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The plugin settings from wp_options
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      array    $config    The plugin settings from wp_options
	 */
	public $config;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'vmstore_version' ) ) {
			$this->version = vmstore_version;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'vmstore';

		$this->load_plugin_config();
		$this->load_dependencies();
		$this->set_locale();
		$this->define_taxonomy_hooks();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	private function load_plugin_config() {
		//Define default values for our plugin
		$config_options = array(
			'pid' => '',
			'mid' => 'default',
			'slug' => 'store',
			'vmproduct_guid_list' => '',			
      'package_header' => '<p><a href="/store">Return to Store</a></p>',
      'package_footer' => '<p><a href="/store">Return to Store</a></p>',
      'cta' => '<a class="package-cta" href="/">Get Started</a>'
		);
		$this->config = get_option($this->plugin_name . '_options');

		//Ensure all defaults are populated
		foreach ($config_options as $key => $value) {
			if (isset($this->config[$key])) {
				$config_options[$key] = $this->config[$key];
			}
		}

		$this->config = $config_options;
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Vmstore_Loader. Orchestrates the hooks of the plugin.
	 * - Vmstore_i18n. Defines internationalization functionality.
	 * - Vmstore_Admin. Defines all hooks for the admin area.
	 * - Vmstore_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * Helper functions for building form inputs
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-form-helpers.php';

		/**
		 * Helper functions for building form inputs
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vmstore-helpers.php';
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vmstore-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-vmstore-i18n.php';

		/**
		 * The class responsible for defining all actions that occur related to our taxonomies
		 * (e.g. "Store" Products, Packages, Categories)
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'taxonomies/class-vmstore-taxonomies.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'taxonomies/class-vmstore-package.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'taxonomies/class-vmstore-product.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'taxonomies/class-vmstore-tag.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-vmstore-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-vmstore-public.php';

		$this->loader = new Vmstore_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Vmstore_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Vmstore_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_taxonomy_hooks() {

		$package_taxonomy = new Vmstore_package( $this->get_plugin_name(), $this->get_version(), $this->config );
		$product_taxonomy = new Vmstore_product( $this->get_plugin_name(), $this->get_version(), $this->config );
		$tag_taxonomy = new Vmstore_tag( $this->get_plugin_name(), $this->get_version(), $this->config );

		$this->loader->add_action('init', $package_taxonomy, 'register');
		$this->loader->add_action('save_post', $package_taxonomy, '_save_meta');

		$this->loader->add_action('init', $product_taxonomy, 'register');
		$this->loader->add_action('save_post', $product_taxonomy, '_save_meta');

		$this->loader->add_action('init', $tag_taxonomy, 'register');
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Vmstore_Admin( $this->get_plugin_name(), $this->get_version(), $this->config );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$this->loader->add_action('admin_menu', $plugin_admin, 'admin_menu_init');
		$this->loader->add_action('admin_init', $plugin_admin, 'admin_page_init');

		$this->loader->add_action('wp_ajax_vmstore', $plugin_admin, 'vmstore_ajax');
		$this->loader->add_action('wp_ajax_nopriv_vmstore', $plugin_admin, 'vmstore_ajax');

		$this->loader->add_action('vmstore_sync_event', $plugin_admin, 'vmstore_sync_func');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Vmstore_Public( $this->get_plugin_name(), $this->get_version(), $this->config );

		// $this->loader->add_action( 'single_template', $plugin_public, 'register_single_templates' );
		$this->loader->add_action( 'template_include', $plugin_public, 'set_template' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Vmstore_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
