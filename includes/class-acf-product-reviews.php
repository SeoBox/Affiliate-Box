<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://seobox.io
 * @since      1.0.0
 *
 * @package    Acf_Product_Reviews
 * @subpackage Acf_Product_Reviews/includes
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
 * @package    Acf_Product_Reviews
 * @subpackage Acf_Product_Reviews/includes
 * @author     SeoBox <support@seobox.io>
 */
class Acf_Product_Reviews
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Acf_Product_Reviews_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('ACF_PRODUCT_REVIEWS_VERSION')) {
            $this->version = ACF_PRODUCT_REVIEWS_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = 'acf-product-reviews';

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Acf_Product_Reviews_Loader. Orchestrates the hooks of the plugin.
     * - Acf_Product_Reviews_i18n. Defines internationalization functionality.
     * - Acf_Product_Reviews_Admin. Defines all hooks for the admin area.
     * - Acf_Product_Reviews_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        require_once(path_join(plugin_dir_path(dirname(__FILE__)), 'vendor/autoload.php'));

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-acf-product-reviews-loader.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-acf-product-reviews-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-acf-product-reviews-public.php';

        require_once plugin_dir_path(dirname(__FILE__)) . 'public/BlocksToProductConverter.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/ACFProductReviewMeta.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/Amazon.php';

        $this->loader = new Acf_Product_Reviews_Loader();

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        $plugin_admin = new Acf_Product_Reviews_Admin($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts');

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public = new Acf_Product_Reviews_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
        $this->loader->add_action('acf/init', $plugin_public, 'acf_init');
        $this->loader->add_action('acf/include_field_types', $plugin_public, 'acf_include_field_types');


        $this->loader->add_filter('block_categories', $plugin_public, 'block_categories', 10, 2);
        $this->loader->add_filter('acf/settings/save_json', $plugin_public, 'acf_settings_json_save_point');
        $this->loader->add_filter('acf/settings/load_json', $plugin_public, 'acf_settings_json_load_point');
        $this->loader->add_filter('acf/load_value/name=reviews', $plugin_public, 'afc_load_value_reviews', 10, 3);
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Acf_Product_Reviews_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

}
