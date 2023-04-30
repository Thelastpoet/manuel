<?php

function manuel_update_link() {
    // Check the nonce for security
    check_ajax_referer( 'manuel_update_link', 'nonce' );

    $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    $original_link = isset( $_POST['original_link'] ) ? sanitize_text_field( $_POST['original_link'] ) : '';
    $new_link = isset( $_POST['new_link'] ) ? sanitize_text_field( $_POST['new_link'] ) : '';
    $anchor_text = isset( $_POST['anchor_text'] ) ? sanitize_text_field( $_POST['anchor_text'] ) : '';
    $time_removed = isset( $_POST['time_removed'] ) ? sanitize_text_field( $_POST['time_removed'] ) : '';

    if ( empty( $post_id ) || empty( $original_link ) || empty( $new_link ) || empty( $anchor_text ) || empty( $time_removed ) ) {
        wp_send_json_error( 'Invalid request data' );
    }

    $post = get_post( $post_id );
    if ( ! $post ) {
        wp_send_json_error( 'Post not found' );
    }

    $content = str_replace( $original_link, $new_link, $post->post_content );

    $result = wp_update_post( array(
        'ID'           => $post_id,
        'post_content' => $content,
    ) );

    if ( $result ) {
        wp_send_json_success();
    } else {
        wp_send_json_error( 'Failed to update the link' );
    }
}
add_action( 'wp_ajax_manuel_update_link', 'manuel_update_link' );