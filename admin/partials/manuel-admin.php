<?php
// Check if the user has the necessary permissions
if ( ! current_user_can( 'manage_options' ) ) {
    return;
}
?>

<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

    <form method="post" action="options.php">
        <?php
        // Output security fields for the registered setting
        settings_fields( 'manuel_settings' );

        // Output the settings sections and fields
        do_settings_sections( 'manuel-settings' );

        // Output the submit button
        submit_button();
        ?>
    </form>
</div>
