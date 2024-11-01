<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class-vmstore-public.php';

get_header();

global $post;
$vmproduct_meta = Vmstore_Public::get_product_meta_by_id($post->ID, Vmstore_Public::$productFieldMap);
?>

<section id="primary" class="content-area">
    <main id="main" class="site-main">

    <?php
        $template = file_get_contents('vmstore-product-article-template.php', true);
        echo Vmstore_Public::apply_fields_to_template($vmproduct_meta, $template, Vmstore_Public::$productFieldMap);
    ?>

    </main>
</section>

<?php get_footer(); ?>