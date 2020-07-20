<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://seobox.io
 * @since      1.0.0
 *
 * @package    Affiliate_Box
 * @subpackage Affiliate_Box/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Affiliate_Box
 * @subpackage Affiliate_Box/public
 * @author     SeoBox <support@seobox.io>
 */
class Affiliate_Box_Public
{

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @param string $plugin_name The name of the plugin.
     * @param string $version The version of this plugin.
     * @since    1.0.0
     */
    public function __construct($plugin_name, $version)
    {

        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function acf_init()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/fields/acf-field-features.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/fields/acf-field-templates.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/fields/acf-field-template-products.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/fields/acf-field-product-review.php';


        if (function_exists('acf_register_block')) {
            acf_register_block(array(
                'name' => 'roundup-summary',
                'title' => 'Roundup Review Summary',
                'description' => 'A small block to summarize reviewed products',
                'render_template' => plugin_dir_path(__FILE__) . 'block-templates/roundup-summary.php',
                'category' => 'acfpr-blocks',
                'icon' => 'text',
                'post_types' => array('post', 'page'),
                'keywords' => array('roundup', 'review', "product"),
            ));

            $enable_bootstrap_4 = get_field("bootstrap_4", 'option') ?? "true";
            if ($enable_bootstrap_4) {
                $enqueue_style = plugins_url('css/bootstrap.css', __FILE__);
            } else {
                $enqueue_style = plugins_url('css/affiliate-box-public.css', __FILE__);
            }


            acf_register_block(array(
                'name' => 'roundup-reviews',
                'title' => 'Elementor Roundup Reviews',
                'description' => 'Renders products reviews. Visible only when a post is published.',
                'render_template' => plugin_dir_path(__FILE__) . 'block-templates/roundup-reviews.php',
                'category' => 'acfpr-blocks',
                'icon' => 'star-half',
                'post_types' => array('post', 'page'),
                'keywords' => array('roundup', 'review', "product"),
                'enqueue_style' => $enqueue_style,
            ));


            acf_register_block(array(
                'name' => 'roundup-review-templates',
                'title' => 'Roundup Reviews',
                'description' => 'Renders products reviews. Visible only when a post is published.',
                'render_template' => plugin_dir_path(__FILE__) . 'block-templates/roundup-review-templates.php',
                'category' => 'acfpr-blocks',
                'icon' => 'star-half',
                'post_types' => array('post', 'page'),
                'keywords' => array('roundup', 'review', "product"),
                'enqueue_style' => $enqueue_style,
            ));
        }

        if (function_exists('acf_add_options_page') && current_user_can('manage_options')) {

            $parent = acf_add_options_page(array(
                'menu_title' => 'Product Review Settings',
                'menu_slug' => 'product-review-settings',
                'capability' => 'manage_options',
                'redirect' => true,
            ));

            acf_add_options_sub_page(array(
                'page_title' => 'General Settings',
                'menu_title' => 'General Settings',
                'capability' => 'manage_options',
                'parent_slug' => $parent['menu_slug'],
            ));
            acf_add_options_sub_page(array(
                'page_title' => 'Affiliate Settings',
                'menu_title' => 'Affiliate Settings',
                'capability' => 'manage_options',
                'parent_slug' => $parent['menu_slug'],
            ));
        }
    }

    function acf_include_field_types($version = false)
    {
        include_once('fields/acf-field-product-review.php');
        include_once('fields/acf-field-features.php');
        include_once('fields/acf-field-templates.php');
    }

    function register_elementor_widgets()
    {
        include_once('widgets/button.php');
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type(new CTA_Button());
    }

// Add Custom Blocks Panel in Gutenberg
    function block_categories($categories, $post)
    {
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'acfpr-blocks',
                    'title' => __('Product Reviews', 'acfpr-blocks-master'),
                ),
            )
        );
    }


    function acf_settings_json_save_point($acf_product_review_path)
    {

        // update path
        $acf_product_review_path = plugin_dir_path(__FILE__) . 'acf-json';

        // return
        return $acf_product_review_path;

    }

    function acf_settings_json_load_point($acf_product_review_path)
    {
        // remove original path (optional)
        unset($acf_product_review_path[0]);

        // append path
        $acf_product_review_path[] = plugin_dir_path(__FILE__) . 'acf-json';

        // return
        return $acf_product_review_path;

    }

    /**
     * Parse Gutenberg blocks and extract default values for repeater if it's empty
     *
     * @param $value repeater value
     * @param $post_id post id
     * @param $field field
     *
     * @return repeater value array to fill out reviews repeater
     */
    function afc_load_value_reviews($value, $post_id, $field)
    {
        if (get_post_status($post_id) !== 'draft' or $value != false) {
            return $value;
        }

        $blocks = parse_blocks(get_the_content());

        $parsing_logics = array(
            array("description" => true, "pros_cons" => true, "features" => true),
            array("description" => true, "pros_cons" => true, "features" => false),
            array("description" => true, "pros_cons" => false, "features" => true),
            array("description" => true, "pros_cons" => false, "features" => false),
        );

        foreach ($parsing_logics as $parsing_logic) {

            $products = BlocksToProductConverter::convert($blocks, $parsing_logic);

            foreach ($products as $product) {
                $value[] = array(
                    'field_5e0821999c85e' => $product->asin,
                    'field_5e092db33655d' => $product->title,
                    'field_5e084bbd32476' => $product->description,
                    'field_5e5744c7300f8' => $product->bestCategory,
                    'field_5e2bc46c572b4' => implode("\n", $product->specs),
                    'field_5e0908c36812b' => implode("\n", $product->pros),
                    'field_5e0908d56812c' => implode("\n", $product->cons),
                );
            }

            if (!empty($value)) {
                return $value;
            }
        }


        return $value;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Affiliate_Box_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Affiliate_Box_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/affiliate-box-public.css', array(), $this->version, 'all');

    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {

        /**
         * This function is provided for demonstration purposes only.
         *
         * An instance of this class should be passed to the run() function
         * defined in Affiliate_Box_Loader as all of the hooks are defined
         * in that particular class.
         *
         * The Affiliate_Box_Loader will then create the relationship
         * between the defined hooks and the functions defined in this
         * class.
         */

        wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/affiliate-box-public.js', array('jquery'), $this->version, false);

    }

}
