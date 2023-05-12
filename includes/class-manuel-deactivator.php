<?php

namespace Manuel;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Manuel_Deactivator {
    public static function deactivate() {
        // Unschedule the cron job
        $timestamp = wp_next_scheduled( 'manuel_cron_event' );
        if ( $timestamp ) {
            wp_unschedule_event( $timestamp, 'manuel_cron_event' );
        }
    }
}
