<?php
/**
 * Plugin Name:       Manuel
 * Plugin URI:        https://github.com/Thelastpoet/manuel/
 * Description:       A WordPress plugin that searches and removes broken links and images in WordPress posts, and updates the modified dates of these posts.
 * Version:           1.0.0
 * Author:            Ammanulah Emmanuel
 * Author URI:        https://nabaleka.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       manuel
 * Domain Path:       /languages
 */

namespace Manuel;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Manuel constants
define( 'MANUEL_VERSION', '1.0.0' );
define( 'MANUEL_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'MANUEL_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once MANUEL_PLUGIN_DIR . 'includes/class-manuel.php';
require_once MANUEL_PLUGIN_DIR . 'includes/class-manuel-activator.php';
require_once MANUEL_PLUGIN_DIR . 'includes/class-manuel-deactivator.php';
require_once MANUEL_PLUGIN_DIR . 'includes/class-manuel-mwene.php';
require_once MANUEL_PLUGIN_DIR . 'admin/manuel-settings.php';

// Register activation and uninstall hooks.
register_activation_hook( __FILE__, [ __NAMESPACE__ . '\Manuel_Activator', 'activate' ]);
register_uninstall_hook( __FILE__, [ __NAMESPACE__ . '\Manuel_Deactivator', 'deactivate' ]);

// Initialize the Manuel.
$manuel = new Manuel();
$manuel->run();