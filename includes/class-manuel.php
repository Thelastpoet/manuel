<?php

namespace Manuel;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Manuel {
    private $version;

    public function __construct() {
        $this->version = MANUEL_VERSION;
        $this->set_locale();
    }

    private function set_locale() {
        load_plugin_textdomain( 'manuel', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    private function manuel_admin_hooks() {
        if ( is_admin() ) {
            $settings = new Manuel_Settings( $this->version );
            add_action( 'admin_menu', array( $settings, 'add_settings_page' ) );
            add_action( 'admin_init', array( $settings, 'register_settings' ) );
            add_action( 'admin_enqueue_scripts', array( $settings, 'enqueue_admin_scripts' ) );

            // include the functions file
            require_once MANUEL_PLUGIN_DIR . '/includes/functions.php';
        }

        $cron = new Manuel_Mwene( $this->version );
        add_action( 'manuel_cron_event', array( $cron, 'run_cron' ) );
        add_filter( 'cron_schedules', array( $cron, 'add_custom_cron_intervals' ) );
    }

    public function run() {
        $this->manuel_admin_hooks();
    }
}