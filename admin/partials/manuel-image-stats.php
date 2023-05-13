<?php

namespace Manuel;

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Manuel_Image_Stats extends \WP_List_Table
{
    public function __construct() {
        parent::__construct([
            'singular' => __('Removed Image', 'manuel'),
            'plural'   => __('Removed Images', 'manuel'),
            'ajax'     => false
        ]);

        $this->prepare_items();
    }

    public function get_columns() {
        return [
            'cb'            => '<input type="checkbox" />',
            'post_id'       => __('Post ID', 'manuel'),
            'post_title'    => __('Post Title', 'manuel'),
            'original_image' => __('Original Image', 'manuel'),
            'time_removed'  => __('Time Removed', 'manuel')
        ];
    }

    public function prepare_items() {
        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $manuel_settings = new Manuel_Settings( '1.0' );
        $data = $manuel_settings->get_manuel_cron()->get_images_stats();
        
        $totalItems = count($data);

        $this->set_pagination_args([
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ]);

        $this->items = array_slice($data, (($currentPage-1)*$perPage), $perPage);

        $this->_column_headers = [$this->get_columns(), [], []];
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'post_id':
            case 'post_title':
            case 'time_removed':
                return esc_html($item[$column_name]);
            case 'original_image':
                return '<a href="' . esc_url($item[$column_name]) . '">' . esc_url($item[$column_name]) . '</a>';
            default:
                return print_r($item, true);
        }
    }

    public function column_title($item) {
        $title = '<strong>' . esc_html($item->post_title) . '</strong>';

        $actions = [
            'edit'      => sprintf('<a href="%s">%s</a>', get_edit_post_link($item->ID), __('Edit', 'manuel')),
            'trash'     => sprintf('<a href="%s">%s</a>', get_delete_post_link($item->ID), __('Trash', 'manuel')),
            'view'      => sprintf('<a href="%s">%s</a>', get_permalink($item->ID), __('View', 'manuel'))
        ];

        return $title . $this->row_actions($actions);
    }

    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="bulk-trash[]" value="%s" />', $item->ID);
    }

    public function extra_tablenav($which) {
        if ($which === 'top') {
            ?>
            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-top" class="screen-reader-text">Select bulk action</label>
                <select name="action" id="bulk-action-selector-top">
                    <option value="-1">Bulk Actions</option>
                    <option value="trash">Move to Trash</option>
                </select>
                <input type="submit" id="doaction" class="button action" value                ="Apply">
            </div>
            <div class="alignleft actions">
                <label class="screen-reader-text" for="post-search-input">Search Posts:</label>
                <input type="search" id="post-search-input" name="s" value="">
                <input type="submit" id="search-submit" class="button" value="Search Posts">
            </div>
            <?php
        }
        if ($which === 'bottom'){
            ?>
            <div class="alignleft actions bulkactions">
                <label for="bulk-action-selector-bottom" class="screen-reader-text">Select bulk action</label>
                <select name="action2" id="bulk-action-selector-bottom">
                    <option value="-1">Bulk Actions</option>
                    <option value="trash">Move to Trash</option>
                </select>
                <input type="submit" id="doaction2" class="button action" value="Apply">
            </div>
            <?php
        }
    }
}

$ImagesTable = new Manuel_Image_Stats();
?>

<div class="wrap">
    <h1><?php esc_html_e( 'Removed Images List', 'manuel' ); ?></h1>
    <form id="posts-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
        <?php
        $ImagesTable->display();
        ?>
    </form>
</div>