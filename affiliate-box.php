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
 * @package           Affiliate_Box
 *
 * @wordpress-plugin
 * Plugin Name:       Affiliate Box
 * Plugin URI:        https://seobox.io
 * Description:       The Affiliate Box Plugin enhances the functionality of the “Advanced Custom Fields” plugin.
 * Version:           1.6.15
 * Author:            SeoBox
 * Author URI:        https://seobox.io
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       affiliate-box
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
define('AFFILIATE_BOX_VERSION', '1.6.15');

require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker('https://github.com/Seobox/Affiliate-Box/', __FILE__, 'Affiliate-Box');

//Optional: Set the branch that contains the stable release.
$myUpdateChecker->setBranch('master');


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-affiliate-box-activator.php
 */
function activate_affiliate_box()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-affiliate-box-activator.php';
    Affiliate_Box_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-affiliate-box-deactivator.php
 */
function deactivate_affiliate_box()
{
    require_once plugin_dir_path(__FILE__) . 'includes/class-affiliate-box-deactivator.php';
    Affiliate_Box_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_affiliate_box');
register_deactivation_hook(__FILE__, 'deactivate_affiliate_box');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-affiliate-box.php';
require plugin_dir_path(__FILE__) . 'acffa/acf-font-awesome.php';
require plugin_dir_path(__FILE__) . 'acf-code-field/acf-code-field.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_affiliate_box()
{

    $plugin = new Affiliate_Box();
    $plugin->run();

}

run_affiliate_box();
