<?php

namespace Manuel;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Manuel_Mwene {
    private $version;
    private $batch_size = 2;

    // Add a user aggent for the HTTP request
    private $user_agent = 'Manuel Mwene';

    public function __construct( $version ) {
        $this->version = $version;
    }

    public function run_cron() {
        // Find and remove broken links and images in posts
        $this->manuel_main();
    }

    public function add_custom_cron_intervals( $schedules ) {
        $schedules['manuel_five_times_daily'] = array(
            'interval' => 86400 / 4,
            'display'  => __( 'Five times daily', 'manuel' ),
        );

        return $schedules;
    }

    private function manuel_main() {
        global $wpdb;
        $offset = 0;
    
        while ( true ) {
            // Query the posts from the database using the offset and batch size
            $query_args = array(
                'post_type'      => 'post',
                'post_status'    => 'publish',
                'posts_per_page' => $this->batch_size,
                'offset'         => $offset,
            );
            $posts = get_posts( $query_args );
    
            // If there are no more posts, exit the loop
            if ( empty( $posts ) ) {
                break;
            }
    
            // For each post, search for broken links and images
            foreach ( $posts as $post ) {
                // Get the post content
                $content = $post->post_content;
    
                // Remove broken links
                $content = $this->remove_broken_links( $content, $post );
    
                // Remove broken images
                $content = $this->remove_broken_images( $content, $post );
    
                // Update the post content if changes were made
                if ( $content !== $post->post_content ) {
                    wp_update_post( array(
                        'ID'            => $post->ID,
                        'post_content'  => $content,
                    ) );
                }
            }
    
            // Increment the offset for the next batch
            $offset += $this->batch_size;
        }
    }    

    public function manuel_db() {
        global  $wpdb;
        $links_table_name = $wpdb->prefix . 'manuel_removed_links';
        $images_table_name = $wpdb->prefix . 'manuel_removed_images';

        // Create the removed links table if it doesn't exist
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$links_table_name'" ) != $links_table_name ) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $links_table_name (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                post_id bigint(20) UNSIGNED NOT NULL,
                original_link text NOT NULL,
                anchor_text text NOT NULL,
                time_removed datetime NOT NULL,
                PRIMARY KEY (id)
            )   $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }

        // Create the removed images table if it doesn't exist
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$images_table_name'" ) != $images_table_name ) {
            $charset_collate = $wpdb->get_charset_collate();
            $sql = "CREATE TABLE $images_table_name (
                id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
                post_id bigint(20) UNSIGNED NOT NULL,
                original_image text NOT NULL,
                time_removed datetime NOT NULL,
                PRIMARY KEY (id)
            )   $charset_collate;";

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $sql );
        }
    }

    public function get_stats() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'manuel_removed_links';
        $posts_table = $wpdb->prefix . 'posts';
    
        $results = $wpdb->get_results(
            "SELECT l.*, p.post_title
             FROM {$table_name} l
             INNER JOIN {$posts_table} p ON l.post_id = p.ID", ARRAY_A
        );
    
        return $results;
    }   
    
    public function get_images_stats() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'manuel_removed_images';
        $posts_table = $wpdb->prefix . 'posts';

        $results = $wpdb->get_results(
            "SELECT i.*, p.post_title
             FROM {$table_name} i
             INNER JOIN {$posts_table} p ON i.post_id = p.ID", ARRAY_A
        );

        return $results;
    }

    private function remove_broken_links( $content, $post ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'manuel_removed_links';
    
        // Find and remove broken links
        $dom = new \DOMDocument();
        if (!empty($content)) {
            @$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
        }
    
        $links = $dom->getElementsByTagName( 'a' );
        foreach ( $links as $link ) {
            $href = $link->getAttribute( 'href' );
            if ( ! $this->is_valid_url( $href ) && $href !== '#' ) {
                $anchor_text = $link->nodeValue;
                $time_removed = current_time( 'mysql' );
    
                // Check if the broken link record is in the database
                $broken_link = $wpdb->get_row( $wpdb->prepare(
                    "SELECT * FROM {$table_name} WHERE post_id = %d AND original_link = %s AND anchor_text = %s", $post->ID, $href, $anchor_text ) );
    
                // If the broken link record doesn't exist in the database, insert the removed link details into the db
                if ( ! $broken_link ) {
                    $text = $dom->createTextNode( $link->nodeValue );
                    $link->parentNode->replaceChild( $text, $link );
    
                    $wpdb->insert($table_name, array(
                        'post_id' => $post->ID,
                        'original_link' => $href,
                        'anchor_text' => $anchor_text,
                        'time_removed' => $time_removed,
                    ));
                }
            }
        }
    
        $content = $dom->saveHTML();
        return $content;
    }

    private function remove_broken_images( $content, $post ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'manuel_removed_images';

        // Find and remove broken images
        $dom = new \DOMDocument();
        if (!empty($content)) {
            @$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );
        }

        $images = $dom->getElementsByTagName( 'img' );
        foreach ( $images as $image ) {
            $src = $image->getAttribute( 'src' );
            if ( ! $this->is_valid_url( $src ) ) {
                $image->parentNode->removeChild( $image );

                // Insert the removed image details into the db
                $wpdb->insert($table_name, array(
                    'post_id' => $post->ID,
                    'original_image' => $src,
                    'time_removed' => current_time( 'mysql' ),
                ));
            }
        }

        $content = $dom->saveHTML();
        return $content;        
    }

    private function is_valid_url( $url ) {
        // Chek if the URL is well formed
        if ( filter_var( $url, FILTER_VALIDATE_URL ) === false ) {
            return false;
        }

        // Check if the URL is valid
        if ( empty( $url ) ) {
            return false;
        }
    
        // Initialize curl
        $ch = curl_init( $url );

        // Set options
        curl_setopt( $ch, CURLOPT_NOBODY, true );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
        curl_setopt( $ch, CURLOPT_USERAGENT, $this->user_agent );

        // Execute
        if ( !curl_exec( $ch )) {
            // Close the cURL resource
            curl_close( $ch );
        
            // If cURL operation fails, return false
            return false;
        }

        // Get the HTTP Response Code
        $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

        // Close the cURL resource
        curl_close( $ch );

        // Check if hte HTTP response code is successful
        if ( $httpCode >= 200 && $httpCode < 300 ) {
            return true;
        }

        return false;
    }
    
}