<?php

/**
 * The taxonomy-specific functionality of the plugin.
 *
 * @link       https://www.vendasta.com
 * @since      1.0.0
 *
 * @package    Vmstore
 * @subpackage Vmstore/Taxonomies
 */

/**
 * The taxonomy-specific functionality of the plugin.
 *
 * Registers package, product and category taxonomies
 *
 * @package    Vmstore
 * @subpackage Vmstore/admin
 * @author     Adam Bissonnette <adam@mediamanifesto.com>
 */
abstract class Vmstore_Taxonomies {
  private $plugin_name;
  private $version;
  private $config;

  public function __construct($plugin_name, $version, $config)
  {
    $this->plugin_name = $plugin_name;
    $this->version = $version;
    $this->config = $config;
  }

  protected abstract function register();
  protected abstract function _save_meta( $post_id );
  protected abstract function _add_metaboxes();

  protected function _get_option($key)
  {
    if (isset($this->config[$key])) {
      return $this->config[$key];
    }
    return "";
  }

}