<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.vendasta.com
 * @since      1.1.0
 *
 * @package    Vmstore
 * @subpackage Vmstore/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Vmstore
 * @subpackage Vmstore/public
 * @author     Adam Bissonnette <adam@mediamanifesto.com>
 */
class Vmstore_Public {

	public static $packageFieldMap = array("vmp_package_header", "vmp_package_footer", "vmp_cta", "vmp_title", "vmp_tagline", "vmp_banner", "vmp_icon", "vmp_viewurl", "vmp_videourl", "vmp_guid", "vmp_products_content", "vmp_products_header", "vmp_tags", "vmp_tag_classes", "post_id", "term_slug", "vmp_product_ids", "vmp_link", "vmp_annual_price", "vmp_price", "vmp_pricing", "vmp_banner_classes", "vmp_icon_classes", "vmp_banner_style", "vmp_icon_style", "vmp_icon_title");
	public static $productFieldMap = array("vmp_title", "vmp_tagline", "vmp_description", "vmp_selling_points", "vmp_faqs", "vmp_gallery", "vmp_banner", "vmp_icon", "vmp_viewurl", "vmp_videourl", "vmp_guid", "vmp_additional", "vmp_hide_faqs", "vmp_addons", "post_id", "vmp_files", "vmp_tags", "vmp_banner_classes", "vmp_icon_classes", "vmp_banner_style", "vmp_icon_style", "vmp_icon_title");

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

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $config ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
    $this->config = $config;

		add_shortcode( "list_packages" , array($this, 'list_packages_shortcode'));
	}

	public function set_template($template) {
		if(get_post_type() == 'vmstore-package') {
			$template = plugin_dir_path(__FILE__) . 'partials/single-vmstore-package.php';
		}

		// if(is_tax('vmstore-package')) {
		// 	$template = plugin_dir_path(__FILE__) . 'partials/single-vmstore-package.php';
		// }

		return $template;
	}

  //showfilter="true" sortfilter="custom"
	public function list_packages_shortcode($atts, $content="") {
		extract(shortcode_atts(array(
		       'class' => '',
		       'id' => '',
           'showfilter' => 'true'
		    ), $atts) );

    $showfilter = filter_var($showfilter, FILTER_VALIDATE_BOOLEAN);

		$packages = Vmstore_Public::get_packages();

		$wrapper = '<div class="vmstore-package-list-wrapper">%s<div class="vmstore-card-container">%s</div><div class="vmstore-footer"></div></div>';
		$output = '';

		$template = Vmstore_Public::get_package_card_template();
		// $template = '<a href="%1$s" title="%2$s" id="package_%3$s" class="vmp_package"><div class="vmp_package_icon"><img src="%4$s" title="%2$s" /></div><div class="vmp_package_title">%2$s</div></a>';
    $toggles = "";

    if ($showfilter) {    
      $tags = Vmstore_Public::get_tags();
      $toggles = Vmstore_Public::_format_section($tags,
                                        '',
                                        '_format_package_tag_toggle',
                                        '<ul class="vmstore-tags vmpackage-card-toggles">%s</ul>');

    }

		foreach ($packages as $package) {      
			$output .= Vmstore_Public::apply_fields_to_template($package, $template, Vmstore_Public::$packageFieldMap);
		}

		return sprintf($wrapper, $toggles, $output);
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( "lightbox", plugin_dir_url( __FILE__ ) . 'css/lightbox.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/vmstore-public.css', array(), $this->version, 'all' );
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( "lightbox", plugin_dir_url( __FILE__ ) . 'js/lightbox.min.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/vmstore-public.js', array( 'jquery' ), $this->version, false );
	}

	public static function get_package_card_template() {
		return file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/vmstore-package-card-template.php', true);
	}

	public static function get_product_template() {
		return file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/vmstore-product-article-template.php', true);
	}

	//Add nofollow to links
	public static function save_rseo_nofollow($content) {
	    $content = preg_replace_callback('~<(a\s[^>]+)>~isU', array('Vmstore_Public', 'cb2'), $content);
	    return $content;
	}

	//Identify links without nofollow
	public static function cb2($match) { 
	    list($original, $tag) = $match;

	    if (strpos($tag, "nofollow")) {
	        return $original;
	    }
	    else {
	        return "<$tag rel='nofollow'>";
	    }
	}

	public static function _format_section($objs, $title, $format_func, $wrapper_template, $callback_args=array())
	{
	    $output = "";

	    if (isset($objs) && !empty($objs))
	    {
	        if (!is_array($objs))
	        {
	            $objs = unserialize($objs);            
	        }
	        
	        foreach ($objs as $obj) {
	            $output .= Vmstore_Public::$format_func($obj, $callback_args);
	        }


	        if (count($objs) > 0)
	        {
	            $output = $title . sprintf($wrapper_template, $output);
	        }
	    }

	    return $output;
	}

	public static function _format_ksp($obj)
	{
	    $output = "";

	    if (isset($obj) && !empty($obj))
	    {
	        $output = sprintf('<li>%s</li>', $obj);
	    }
	    return $output;
	}

	public static function _format_file($obj)
	{
	    $output = "";

	    if (isset($obj) && !empty($obj))
	    {
	        $filename = $obj;
	        $re = '/.*\/(.+?)\.([a-zA-Z\d]{3})$/';
	        preg_match($re, $obj, $matches, PREG_OFFSET_CAPTURE, 0);

	        if (isset($matches) && isset($matches[1])) {
	            $filename = $matches[1][0];
	            $filetype = $matches[2][0];
	        }
	        $output = sprintf('<li class="type-%s"><a href="https:%s">%s</a></li>', $filetype, $obj, $filename);
	    }
	    return $output;
	}

	public static function _format_gallery($obj, $gallery)
	{
	    $output = "";
	    if (isset($obj) && !empty($obj))
	    {
	        $screenshot_template = '<a class="vmp_screenshot" data-lightbox="gallery-%1$s" href="%2$s=s800-tmp.png" style="background-image:url(\'%2$s=w600-tmp.png\')"></a>';
	        $output = sprintf($screenshot_template, $gallery, $obj);
	    }
	    return $output;
	}

	public static function _format_faq($obj)
	{
	    $output = "";
	    if (isset($obj) && isset($obj->question) && isset($obj->answer))
	    {
	        $template = '<div class="vmp_faq"><div class="vmp_faq-question">%s</div><div class="vmp_faq-answer">%s</div></div>';
	        $question = $obj->question;
	        $answer = Vmstore_Public::save_rseo_nofollow($obj->answer);
	        $output = sprintf($template, $question, $answer);
	    }
	    return $output;
	}

	public static function _format_product_content($obj, $product_template)
	{
	    $output = "";
	    if (isset($obj)) {
	        $vmproduct_meta = Vmstore_Public::get_product_meta_by_id($obj->ID, Vmstore_Public::$productFieldMap);
	        $output = Vmstore_Public::apply_fields_to_template($vmproduct_meta, $product_template, Vmstore_Public::$productFieldMap);
	    }

	    return $output;
	}

	public static function _format_product_header($obj, $product_template)
	{
	    $output = "";
	    if (isset($obj)) {
	        $vmproduct_meta = Vmstore_Public::get_product_meta_by_id($obj->ID, Vmstore_Public::$productFieldMap);
	        $output = Vmstore_Public::apply_fields_to_template($vmproduct_meta, $product_template, Vmstore_Public::$productFieldMap);
	    }

	    return $output;
	}

	public static function _format_product_tag($obj)
	{
		$output = "";

		if (isset($obj) && !empty($obj))
		{
		    $output = sprintf('<li>%s</li>', $obj);
		}
		return $output;
	}

  public static function _format_package_tag_toggle($obj)
  {
    $output = "";

    if (isset($obj) && !empty($obj))
    {
      $output = sprintf('<li class="vmpackage-card-toggle" data-target="vmstore-tag-%1$s">%2$s</li>', $obj->slug, $obj->name);
    }
    return $output;    
  }

	public static function get_packages() {
    $query_args = array(
      'post_type' => 'vmstore-package',
      'hide_empty' => false,
      'posts_per_page'   => -1,
      'orderby' => 'menu_order',
      'order' => 'ASC'
    );

    $query_result = get_posts($query_args);

    $packages = [];

    foreach ($query_result as $package) {
    	array_push($packages, Vmstore_Public::get_package_meta_by_id($package->ID, false));
    }

    return $packages;
	}

  public static function get_package_tags($post_id) {
    $meta_query = array(
      array(
        'key' => 'vmp_visibleinstore',
        'value' => true
      ),
      array(
        'key' => 'vmp_guid',
        'value' => "__ALL__",
        'compare' => "!="
      )
    );

    $query_args = array(
      'meta_query' => $meta_query,
      'meta_key' => 'vmp_order',
      'orderby' => 'vmp_order'
    );

    return wp_get_post_terms($post_id, 'vmstore-tag', $query_args);
  }

	public static function get_tags() {
    $meta_query = array(
      array(
        'key' => 'vmp_visibleinstore',
        'value' => true
      )
    );

    $query_args = array(
      'taxonomy' => 'vmstore-tag',
      'hide_empty' => true,
      'meta_query' => $meta_query,
      'meta_key' => 'vmp_order',
      'orderby' => 'vmp_order'
    );

    if (isset($post_id)) {
      $query_args["include"] = array($post_id);      
    }

    $terms = get_terms($query_args);

    // if we didn't get any terms then it's possible
    // something wonky happened to custom categories
    // in which case let's fall back to the full list
    if (count($terms) == 0) {
      $query_args = array(
        'taxonomy' => 'vmstore-tag',
        'hide_empty' => true,
        'meta_query' => array(
          array(
            'key' => 'vmp_visibleinstore',
            'value' => true
          )
        )
      );
      $terms = get_terms($query_args);
    }

    error_log(json_encode($terms));

    return $terms;
	}

	public static function get_products_by_package($post_ids) {
    $query_args = array(
     'post__in' => $post_ids,
     'post_type' => 'vmstore-product'
    );

    $query_result = get_posts($query_args);

    $products = [];
    if (isset($query_result) && count($query_result) > 0) {
        $products = $query_result;
    }

    return $products;
	}

	public static function get_package_meta_by_id($post_id, $includeProducts=true, $config=null) {
		$fieldMap = Vmstore_Public::$packageFieldMap;
    $vmp_meta = get_post_meta($post_id);

    //Flatten the meta array to map to a single dimension
    foreach ($vmp_meta as &$v) {
        $v = array_shift($v);
    }

    foreach ($fieldMap as $key) {
      if (!isset($vmp_meta[$key]))
      {
        $vmp_meta[$key] = "";
      }
    }

    $products = Vmstore_Public::get_products_by_package(unserialize($vmp_meta["vmp_product_ids"]));
    $product_header_template = file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/vmstore-product-article-header-template.php', true);
    $product_content_template = file_get_contents(plugin_dir_path( dirname( __FILE__ ) ) . 'public/partials/vmstore-product-article-content-template.php', true);

    $tags = array();
    $tag_classes = "";
    $packageTags = Vmstore_public::get_package_tags($post_id);
    foreach ($packageTags as $tag) {
      if (!in_array($tag->name, $tags)) {
        array_push($tags, $tag->name);
        $tag_classes .= "vmstore-tag-" . $tag->slug . " ";
      }
    }

    $vmp_meta["vmp_tag_classes"] = trim($tag_classes);
    $vmp_meta["vmp_tags"] = Vmstore_Public::_format_section($tags,
                                    '',
                                    '_format_product_tag',
                                    '<ul class="vmstore-tags">%s</ul>');

    if ($includeProducts) {
	    if (count($products) > 1) {
	        $vmp_meta["vmp_products_header"] = Vmstore_Public::_format_section($products,
	                                    '',
	                                    '_format_product_header',
	                                    '%s',
	                                  	$product_header_template);
	    }

	    $vmp_meta["vmp_products_content"] = Vmstore_Public::_format_section($products,
	                                '',
	                                '_format_product_content',
	                                '%s',
                                	$product_content_template);
    }

    $vmp_meta["post_id"] = $post_id;
    $vmp_meta["vmp_link"] = get_permalink($post_id);
    $vmp_meta["vmp_price"] = 'Contact Sales';

    $hasBanner = $vmp_meta["vmp_banner"] !== "";
    $hasIcon = $vmp_meta["vmp_icon"] !== "";

    $vmp_meta["vmp_banner_classes"] = ($hasBanner)?"":" vmstore-graphic-missing";
    $vmp_meta["vmp_icon_classes"] = ($hasIcon)?"":" vmstore-graphic-missing";
    $vmp_meta["vmp_banner_style"] = ($hasBanner)?sprintf(' style="background-image: url(%s)"', $vmp_meta["vmp_banner"]):"";
    $vmp_meta["vmp_icon_style"] = ($hasIcon)?sprintf(' style="background-image: url(%s)"', $vmp_meta["vmp_icon"]):"";
    $vmp_meta["vmp_icon_title"] = isset($vmp_meta["vmp_title"][0])?$vmp_meta["vmp_title"][0]:"";

    $vmp_meta["vmp_package_header"] = do_shortcode(stripcslashes(Vmstore_Public::get_option($config, "package_header")));
    $vmp_meta["vmp_package_footer"] = do_shortcode(stripcslashes(Vmstore_Public::get_option($config, "package_footer")));
    $vmp_meta["vmp_cta"] = do_shortcode(stripcslashes(Vmstore_Public::get_option($config, "cta")));

    if (isset($vmp_meta["vmp_annual_price"]) && $vmp_meta["vmp_annual_price"] > 0) {
    	$price = $vmp_meta["vmp_annual_price"] / 12 / 100;
    	$vmp_meta["vmp_price"] = "$".number_format($price, 2) . "/month";
    }
    // $vmp_meta["term_slug"] = $term_obj->slug;

    return $vmp_meta;
	}

	public static function get_product_meta_by_id($post_id, $fieldMap) {
	    //Get all VMP Meta
	    $vmproduct_meta = get_post_meta($post_id);

	    //Flatten the meta array to map to a single dimension
	    foreach ($vmproduct_meta as &$v) {
	        $v = array_shift($v);
	    }

	    foreach ($fieldMap as $key) {
        if (!isset($vmproduct_meta[$key]))
        {
          $vmproduct_meta[$key] = "";   
        }
	    }

	    $vmproduct_meta["vmp_selling_points"] = Vmstore_Public::_format_section($vmproduct_meta["vmp_selling_points"],
	                                            sprintf('<h3>Key Selling Points for %s</h3>', $vmproduct_meta["vmp_title"]),
	                                            '_format_ksp',
	                                            "<ul>%s</ul>");
	    $vmproduct_meta["vmp_gallery"] = Vmstore_Public::_format_section($vmproduct_meta["vmp_gallery"],
	                                    '<h3>Gallery</h3>',
	                                    '_format_gallery',
	                                    '<div class="vmp_gallery">%s</div>',
	                                  	"vmstore-gallery-" . $post_id); //at some point we could add a dynamic gallery param here
	    $vmproduct_meta["vmp_faqs"] = Vmstore_Public::_format_section($vmproduct_meta["vmp_faqs"],
	                                    sprintf('<h3>Frequently Asked Questions for %s</h3>', $vmproduct_meta["vmp_title"]),
	                                    '_format_faq',
	                                    "%s");
	    $vmproduct_meta["vmp_files"] = Vmstore_Public::_format_section($vmproduct_meta["vmp_files"],
	                                    '<h3>Files</h3>',
	                                    '_format_file',
	                                    "%s");

      $hasBanner = $vmproduct_meta["vmp_banner"] !== "";
      $hasIcon = $vmproduct_meta["vmp_icon"] !== "";

	    $vmproduct_meta["post_id"] = $post_id;
      $vmproduct_meta["vmp_banner_classes"] = ($hasBanner)?"":" vmstore-graphic-missing";
      $vmproduct_meta["vmp_icon_classes"] = ($hasIcon)?"":" vmstore-graphic-missing";
      $vmproduct_meta["vmp_banner_style"] = ($hasBanner)?sprintf(' style="background-image: url(%s)"', $vmproduct_meta["vmp_banner"]):"";
      $vmproduct_meta["vmp_icon_style"] = ($hasIcon)?sprintf(' style="background-image: url(%s)"', $vmproduct_meta["vmp_icon"]):"";
      $vmproduct_meta["vmp_icon_title"] = isset($vmproduct_meta["vmp_title"][0])?$vmproduct_meta["vmp_title"][0]:"";

	    return $vmproduct_meta;
	}

	public static function apply_fields_to_template($meta, $template, $fieldMap) {
	    $vmp_template = $template;
	    foreach ($fieldMap as $field) {
        if (isset($meta[$field]))
        {
          $vmp_template = str_replace(sprintf("{{%s}}", $field), $meta[$field], $vmp_template);
        }
	    }
	    return $vmp_template;
	}

  public static function get_option($config, $key)
  {
    if (isset($config[$key])) {
      return $config[$key];
    }
    return "";
  }

  public static function load_public_plugin_config() {
    //Define default values for our plugin
    $config_options = array(  
      'package_header' => '<p><a href="/store">Return to Store</a></p>',
      'package_footer' => '<p><a href="/store">Return to Store</a></p>',
      'cta' => '<a class="package-cta" href="/">Get Started</a>'
    );
    $config = get_option('vmstore_options');

    //Ensure all defaults are populated
    foreach ($config_options as $key => $value) {
      if (isset($config[$key])) {
        $config_options[$key] = $config[$key];
      }
    }

    return $config_options;
  }

}
