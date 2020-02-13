<?php

/**
 * Fired during plugin activation
 *
 * @link       https://seobox.io
 * @since      1.0.0
 *
 * @package    Acf_Product_Reviews
 * @subpackage Acf_Product_Reviews/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Acf_Product_Reviews
 * @subpackage Acf_Product_Reviews/includes
 * @author     SeoBox <support@seobox.io>
 */
class Acf_Product_Reviews_Activator
{

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function activate()
    {
        //			TODO: throws warnings in prod
        //			include_once( 'includes/plugin.php' );
        //			rest api needs a different path
        //			include_once( 'wp-admin/includes/plugin.php' );
        //			if ( is_plugin_inactive( "amazon-associates-link-builder/amazon-associates-link-builder.php" ) ) {
        //				return;
        //			}

        self::aalb_copy_templates_to_uploads_dir();
    }

    /**
     * Copy predefined AALB templates to a WP upload folder.
     */
    public static function aalb_copy_templates_to_uploads_dir()
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
}
