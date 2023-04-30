<?php
// Fetch statistics from the database.
$manuel_settings = new Manuel_Settings( '1.0' );
$stats = $manuel_settings->get_manuel_cron()->get_stats();
?>

<div class="wrap">
    <h1><?php _e( 'Manuel Stats', 'manuel' ); ?></h1>

    <table class="widefat">
        <thead>
            <tr>
                <th><?php _e( 'Post ID', 'manuel' ); ?></th>
                <th><?php _e( 'Post Title', 'manuel' ); ?></th>
                <th><?php _e( 'Original Link', 'manuel' ); ?></th>
                <th><?php _e( 'Anchor Text', 'manuel' ); ?></th>
                <th><?php _e( 'Actions', 'manuel' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $stats as $stat ) : ?>
                <tr>
                    <td><?php echo $stat['post_id']; ?></td>
                    <td><?php echo $stat['post_title']; ?></td>
                    <td><?php echo $stat['original_link']; ?></td>
                    <td><?php echo $stat['anchor_text']; ?></td>
                    <td>
                        <button class="button edit-link" data-post-id="<?php echo $stat['post_id']; ?>" data-original-link="<?php echo $stat['original_link']; ?>">
                            <?php _e( 'Edit', 'manuel' ); ?>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
