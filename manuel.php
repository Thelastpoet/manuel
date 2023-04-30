<?php
/**
 * Plugin Name:       Manuel
 * Plugin URI:        https://example.com/manuel
 * Description:       A WordPress plugin that searches for and removes broken links and images in WordPress posts, and updates the modified dates of these posts.
 * Version:           1.0.0
 * Author:            Ammanulah Emmanuel
 * Author URI:        https://nabaleka.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       manuel
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

// Define the plugin's version and path for easy reference.
define( 'MANUEL_VERSION', '1.0.0' );
define( 'MANUEL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MANUEL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

// Include the core plugin classes and functions.
require_once MANUEL_PLUGIN_DIR . 'includes/class-manuel.php';
require_once MANUEL_PLUGIN_DIR . 'includes/class-manuel-activator.php';
require_once MANUEL_PLUGIN_DIR . 'includes/class-manuel-deactivator.php';
require_once MANUEL_PLUGIN_DIR . 'includes/class-manuel-cron.php';
require_once MANUEL_PLUGIN_DIR . 'includes/class-manuel-settings.php';

// Register the activation and deactivation hooks.
register_activation_hook( __FILE__, function() {
    Manuel_Activator::activate( MANUEL_VERSION );
});
register_deactivation_hook( __FILE__, array( 'Manuel_Deactivator', 'deactivate' ) );

// Initialize the plugin.
$manuel = new Manuel();
$manuel->run();
