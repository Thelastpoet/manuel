<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Manuel_Settings {
    private $version;

    public function __construct( $version ) {
        $this->version = $version;
    }

    public function add_settings_page() {
        add_menu_page(
            __( 'Manuel Settings', 'manuel' ),
            __( 'Manuel Settings', 'manuel' ),
            'manage_options',
            'manuel-settings',
            array( $this, 'display_settings_page' )
        );

        add_submenu_page(
            'manuel-settings',
            __( 'Manuel Link Stats', 'manuel' ),
            __( 'Manuel Link Stats', 'manuel' ),
            'manage_options',
            'manuel-stats',
            array( $this, 'display_stats_page' )
        );

        add_submenu_page(
            'manuel-settings',
            __( 'Manuel Image Stats', 'manuel' ),
            __( 'Manuel Image Stats', 'manuel' ),
            'manage_options',
            'manuel-image-stats',
            array( $this, 'display_image_stats_page' )
        );
            
    }

    public function display_settings_page() {
        include_once MANUEL_PLUGIN_DIR . 'admin/partials/manuel-admin.php';
    }

    public function display_stats_page() {
        include_once MANUEL_PLUGIN_DIR . 'admin/partials/manuel-stats.php';
    }

    public function display_image_stats_page() {
        include_once MANUEL_PLUGIN_DIR . 'admin/partials/manuel-image-stats.php';
    }
    
    public function get_manuel_cron() {
        if ( ! isset( $this->manuel_cron ) ) {
            require_once MANUEL_PLUGIN_DIR . 'includes/class-manuel-cron.php';
            $this->manuel_cron = new Manuel_Cron( $this->version );
        }
    
        return $this->manuel_cron;
    }    

    public function register_settings() {
        register_setting( 'manuel_settings', 'manuel_cron_interval' );

        add_settings_section(
            'manuel_cron_settings_section',
            __( 'Cron Settings', 'manuel' ),
            null,
            'manuel-settings'
        );

        add_settings_field(
            'manuel_cron_interval',
            __( 'Cron Interval', 'manuel' ),
            array( $this, 'render_cron_interval_field' ),
            'manuel-settings',
            'manuel_cron_settings_section'
        );
    }

    public function render_cron_interval_field() {
        $option = get_option( 'manuel_cron_interval', 'manuel_five_times_daily' );
        ?>
        <select name="manuel_cron_interval">
            <option value="manuel_five_times_daily" <?php selected( $option, 'manuel_five_times_daily' ); ?>><?php _e( 'Five times daily', 'manuel' ); ?></option>
            <!-- we will Add more options if needed -->
        </select>
        <?php
    }

    public function enqueue_admin_scripts() {
        wp_enqueue_style( 'manuel-admin', MANUEL_PLUGIN_URL . 'assets/css/manuel-admin.css', array(), $this->version );
        wp_enqueue_script( 'manuel-admin', MANUEL_PLUGIN_URL . 'assets/js/manuel-admin.js', array( 'jquery' ), $this->version, true );
        wp_enqueue_script( 'manuel-stats', MANUEL_PLUGIN_URL . 'assets/js/manuel-stats.js', array( 'jquery' ), $this->version, true );
    
        // Localize the 'manuel-stats' script with the 'manuelStats' object.
        wp_localize_script( 'manuel-stats', 'manuelStats', array(
            'nonce' => wp_create_nonce( 'manuel_update_link' ),
        ) );
    }
    
}