<?php
// Fetch statistics from the database.
$manuel_settings = new Manuel_Settings( '1.0' );
$removed_images = $manuel_settings->get_manuel_cron()->get_images_stats();
?>

<div class="wrap">
    <h1><?php _e( 'Removed Images List', 'manuel' ); ?></h1>

    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e( 'Post ID', 'manuel' ); ?></th>
                <th><?php _e( 'Post Title', 'manuel' ); ?></th>
                <th><?php _e( 'Original Image', 'manuel' ); ?></th>
                <th><?php _e( 'Time Removed', 'manuel' ); ?></th>
                <th><?php _e( 'Actions', 'manuel' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $removed_images as $stat ) : ?>
                <tr>
                    <td><?php echo $stat['post_id']; ?></td>
                    <td><?php echo $stat['post_title']; ?></td>
                    <td><?php echo $stat['original_image']; ?></td>
                    <td><?php echo $stat['time-removed']; ?></td>
                    <td>
                        <button class="button edit-image-stats" data-post-id="<?php echo $stat['post_id']; ?>" data-original-image="<?php echo $stat['original_image']; ?>">
                            <?php _e( 'Edit', 'manuel' ); ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
