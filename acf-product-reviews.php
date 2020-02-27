<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://seobox.io
 * @since             1.0.0
 * @package           Acf_Product_Reviews
 *
 * @wordpress-plugin
 * Plugin Name:       ACF Product Review
 * Plugin URI:        https://seobox.io
 * Description:       The Product Reviews ACF Field Plugin enhances the functionality of the “Advanced Custom Fields” plugin.
 * Version:           1.2.5
 * Author:            SeoBox
 * Author URI:        https://seobox.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       acf-product-reviews
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('ACF_PRODUCT_REVIEWS_VERSION', '1.2.5');

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker('https://github.com/Inkvi/acf-product-review/', __FILE__, 'acf-product-review');

//Optional: If you're using a private repository, specify the access token like this:
$myUpdateChecker->setAuthentication('accb0e2a7631eb94e30bc4d4ec540e37cce3dca1');

//Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-acf-product-reviews-activator.php
 */
function activate_acf_product_reviews()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-acf-product-reviews-activator.php';
    Acf_Product_Reviews_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-acf-product-reviews-deactivator.php
 */
function deactivate_acf_product_reviews()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-acf-product-reviews-deactivator.php';
    Acf_Product_Reviews_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_acf_product_reviews');
register_deactivation_hook(__FILE__, 'deactivate_acf_product_reviews');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-acf-product-reviews.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_acf_product_reviews()
{

    $plugin = new Acf_Product_Reviews();
    $plugin->run();

}

run_acf_product_reviews();
