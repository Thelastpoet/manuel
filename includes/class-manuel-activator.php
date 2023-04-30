<?php

require_once MANUEL_PLUGIN_DIR . '/includes/class-manuel-cron.php';

class Manuel_Activator {
    public static function activate( $version ) {
        // Unschedule the existing cron event
        $timestamp = wp_next_scheduled( 'manuel_cron_event' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'manuel_cron_event' );
        }
    
        // Schedule the new cron job
        $cron_interval = get_option( 'manuel_cron_interval', 'manuel_five_times_daily' );
        wp_schedule_event( time(), $cron_interval, 'manuel_cron_event' );

        // Create Manuel db table here
        $manuel_cron = new Manuel_Cron( $version );
        $manuel_cron->manuel_db();
    }
    
}
