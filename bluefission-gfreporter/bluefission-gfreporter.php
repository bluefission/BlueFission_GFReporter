<?php
/**
 * Built from The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that
 * also follow WordPress Coding Standards and PHP best practices.
 *
 * @package   BlueFission_GFReporter
 * @author    Your Name <devon@bluefission.com>
 * @license   GPL-2.0+
 * @link      http://bluefission.com
 * @copyright 2014 Devon Scott, BlueFission.com
 *
 * @wordpress-plugin
 * Plugin Name:       BlueFission Gravity Forms Reporter
 * Plugin URI:        http://bluefission.com/wordpress-plugins
 * Description:       Schedule a monthly report of a given Gravity Formorm to sent via email
 * Version:           1.0.3
 * Author:            Devon Scott, BlueFission.com
 * Author URI:        http://bluefission.com
 * Text Domain:       bluefission-gfreporter-locale
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/<owner>/<repo>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define('BFGFR_OPTIONS_VAR', 'bluefission_gfr_options');
define('MAINTENANCE_INTERVAL', 'daily');

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . '/public/class-bluefission-gfreporter.php' );

register_activation_hook( __FILE__, array( 'BlueFission_GFReporter', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'BlueFission_GFReporter', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'BlueFission_GFReporter', 'get_instance' ) );
add_action( 'scheduled_maintenance', array( 'BlueFission_GFReporter', 'scheduled_report' ) );
add_action( 'trigger_report', array( 'BlueFission_GFReporter', 'send_report' ) );
add_action( 'wp_ajax_trigger_report', array( 'BlueFission_GFReporter', 'send_report_ajax' ) );
/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * TODO:
 *
 * - replace `class-plugin-admin.php` with the name of the plugin's admin file
 * - replace BlueFission_GFReporter_Admin with the name of the class defined in
 *   `class-plugin-name-admin.php`
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
//if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
if ( is_admin() ) {
	require_once( plugin_dir_path( __FILE__ ) . '/admin/class-bluefission-gfreporter-admin.php' );
	add_action( 'plugins_loaded', array( 'BlueFission_GFReporter_Admin', 'get_instance' ) );

}
