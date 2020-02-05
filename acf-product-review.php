<?php

/*
Plugin Name: Advanced Custom Fields: Product Reviews
Plugin URI: https://github.com/Inkvi/acf-amazon-image
Description: ACF Custom Field Type for Product Reviews
Version: 1.0.27
Author: Alexander Eliseev
*/

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker('https://github.com/Inkvi/acf-product-review/', __FILE__, 'acf-product-review');

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication('accb0e2a7631eb94e30bc4d4ec540e37cce3dca1');

//Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');

// exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

function get_amazon_url($asin)
{
    $affiliate_settings = get_field('amazon_affiliate_settings', 'option');
    $tag = $affiliate_settings['associate_id'] ?? '';

    return "http://www.amazon.com/dp/" . $asin . "/ref=nosim?tag=" . $tag;
}

include_once('acf-product-review-blocks.php');


/**
 * Copy predefined AALB templates to a WP upload folder.
 */
function aalb_copy_templates_to_uploads_dir()
{
    require_once(ABSPATH . 'wp-admin/includes/file.php');
    try {
        WP_Filesystem();
        global $wp_filesystem;

        $upload_dir = wp_upload_dir();
        $upload_dir = $wp_filesystem->find_folder($upload_dir['basedir']);

        require_once ABSPATH . "wp-content/plugins/amazon-associates-link-builder/amazon-associates-link-builder.php";
        $template_upload_path = $upload_dir . AALB_TEMPLATE_UPLOADS_FOLDER;

        if (!$wp_filesystem->is_dir($template_upload_path) && !aalb_create_dir($template_upload_path)) {
            return false;
        }
        copy_dir(plugin_dir_path(__FILE__) . "aalb-templates", $template_upload_path);
    } catch (Exception $e) {
        error_log('Unable to remove templates uploads directory. Failed with the Exception ' . $e->getMessage());
    }
}

function startsWith($string, $startString)
{
    $len = strlen($startString);

    return (substr($string, 0, $len) === $startString);
}

/**
 * Parse Gutenberg blocks and extract default values for repeater if it's empty
 *
 * @param $value repeater value
 * @param $post_id post id
 * @param $field field
 *
 * @return array value array to fill out reviews repeater
 */
function afc_load_reviews($value, $post_id, $field)
{
    if (get_post_status($post_id) !== 'draft' or $value != false) {
        return $value;
    }

    $products = array();
    $current_product = new ACFProductReviewMeta();
    $prev_block = "";

    $blocks = parse_blocks(get_the_content());
    foreach ($blocks as $block) {
        $blockName = $block['blockName'];
        if (is_null($blockName)) {
            continue;
        }

        if (!in_array($blockName, array(
            "core/heading",
            "core/list",
            "core/image",
            "core/paragraph"
        ))) {
            $prev_block = '';
            continue;
        }


        $html = trim($block['innerHTML']);

        if (startsWith($html, "<h3") or startsWith($html, "<h2")) {

	        if ($current_product->isComplete()) {
		        array_push($products, $current_product);
	        }

	        $current_product = new ACFProductReviewMeta();

            // extract asin and title from the link
            $pattern = "/<a href=\".*amazon\.com.*?\/([A-Z0-9]{10}).*?>(.*?)(<br.*>)*<\/a>/";
            if (preg_match($pattern, $html, $matches)) {
                $current_product->asin = $matches[1];
                $current_product->title = $matches[2];
            }
            continue;
        }

        if (!$current_product->hasTitle()) {
//        	skip blocks until we have a title; title indicates that a product review has been started
        	continue;
        }

        if (in_array(strip_tags($html), array("Pros", "Pro"))) {
            $prev_block = "pros";
            continue;
        }

        if (in_array(strip_tags($html), array("Cons", "Con"))) {
            $prev_block = "cons";
            continue;
        }

        if (in_array(strip_tags($html), array("Specs", "Features", "Tech Specs", "Specifications"))) {
            $prev_block = "specs";
            continue;
        }

        if ($blockName == "core/list" && $prev_block == "pros") {
            $current_product->pros = getListElements($html);
            $prev_block = "core/list";
            continue;
        }

        if ($blockName == "core/list" && $prev_block == "cons") {
            $current_product->cons = getListElements($html);
            $prev_block = "core/list";
            continue;
        }

        if ($blockName == "core/list" && $prev_block == "specs") {
            $current_product->specs = getListElements($html);
            $prev_block = "core/list";
            continue;
        }

        if ($blockName == "core/paragraph" && $prev_block == "core/list") {
            # embed youtube url
            $pattern = '@(https?://)?(?:www\.)?(youtu(?:\.be/([-\w]+)|be\.com/watch\?v=([-\w]+)))\S*@im';
            if (preg_match($pattern, $html, $matches)) {
                $url = "https://youtube.com/embed/" . trim($matches[4], '"');
                $html = "<div class='embed-container'><iframe src='$url' frameborder='0' allowfullscreen></iframe></div>";
            }
            $current_product->description .= $html;
            continue;
        }
    }

    if ($current_product->isComplete()) {
        array_push($products, $current_product);
    }

    foreach ($products as $product) {
        $value[] = array(
            'field_5e0821999c85e' => $product->asin,
            'field_5e092db33655d' => $product->title,
            'field_5e084bbd32476' => $product->description,
            'field_5e2bc46c572b4' => implode("\n", $product->specs),
            'field_5e0908c36812b' => implode("\n", $product->pros),
            'field_5e0908d56812c' => implode("\n", $product->cons),
        );

    }

    return $value;
}

function getListElements(string $html): array
{
    $values = array();

    $dom = new DOMDocument();
    $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
    $points = $dom->getElementsByTagName('ul');
    foreach ($points->item(0)->getElementsByTagName('li') as $points) {
        array_push($values, $points->nodeValue);
    }

    return $values;
}


// check if class already exists
if (!class_exists('acf_product_review')) :

    class ACFProductReviewMeta
    {
        public $asin;
        public $title;
        public $pros = array();
        public $cons = array();
        public $specs = array();
        public $description = '';

        public function isComplete()
        {
            $pros_cons_exist = !empty($this->pros) && !empty($this->cons);

            return isset($this->asin) && isset($this->title) && isset($this->description) && (!empty($this->specs) || $pros_cons_exist);
        }

        public function hasTitle() {
        	return isset($this->title);
        }

    }

    class acf_product_review
    {

        // vars
        var $settings;


        function __construct()
        {

            // settings
            // - these will be passed into the field class.
            $this->settings = array(
                'url' => plugin_dir_url(__FILE__),
                'path' => plugin_dir_path(__FILE__)
            );

            //			TODO: throws warnings in prod
            //			include_once( 'includes/plugin.php' );
            //			rest api needs a different path
            //			include_once( 'wp-admin/includes/plugin.php' );
            //			if ( is_plugin_inactive( "amazon-associates-link-builder/amazon-associates-link-builder.php" ) ) {
            //				return;
            //			}

            aalb_copy_templates_to_uploads_dir();


            // include field
            add_action('acf/include_field_types', array($this, 'include_field'));
            add_action('acf/init', array($this, 'acf_init'));
            add_filter('acf/load_value/name=reviews', 'afc_load_reviews', 10, 3);
        }


        function acf_init()
        {
            if (function_exists('acf_add_options_page')) {

                $parent = acf_add_options_page(array(
                    'menu_title' => 'Product Review Settings',
                    'menu_slug' => 'product-review-settings',
                    'capability' => 'manage_options',
                    'redirect' => true,
                ));

                acf_add_options_sub_page(array(
                    'page_title' => 'General Settings',
                    'menu_title' => 'General Settings',
                    'parent_slug' => $parent['menu_slug'],
                ));
                acf_add_options_sub_page(array(
                    'page_title' => 'Affiliate Settings',
                    'menu_title' => 'Affiliate Settings',
                    'parent_slug' => $parent['menu_slug'],
                ));
            }
        }

        function include_field($version = false)
        {
            include_once('fields/acf-field-product-review.php');
            include_once('fields/acf-field-features.php');
        }

    }


    // initialize
    new acf_product_review();

    // class_exists check
endif;

?>