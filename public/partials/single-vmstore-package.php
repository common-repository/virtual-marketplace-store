<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class-vmstore-public.php';

get_header();
$public_config = Vmstore_Public::load_public_plugin_config();
$product_template = file_get_contents('vmstore-product-article-template.php', true);
$vmp_meta = Vmstore_Public::get_package_meta_by_id($post->ID, $product_template, $public_config);
?>
<div class="wrap">
  <section id="primary" class="content-area">
      <div id="main-content" class="site-main">

      <?php
          $template = file_get_contents('vmstore-package-article-template.php', true);

          echo Vmstore_Public::apply_fields_to_template($vmp_meta, $template, Vmstore_Public::$packageFieldMap);
      ?>

      </div>
  </section>
</div>
<?php get_footer(); ?>