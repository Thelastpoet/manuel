<?php

namespace Manuel;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class Manuel_Links_Stats extends \WP_List_Table {
    public function __construct()
    {
        parent::__construct([
            'singular' => __('Stat', 'manuel'),
            'plural'   => __('Stats', 'manuel'),
            'ajax'     => false
        ]);
    }

    public function get_columns() {
        return [
            'cb'           => '<input type="checkbox" />',
            'post_title'   => __('Post Title', 'manuel'),
            'original_link' => __('Original Link', 'manuel'),
            'anchor_text'  => __('Anchor Text', 'manuel'),
            'time_removed' => __('Time Removed', 'manuel')
        ];
    }

    public function column_cb($item) {
        return sprintf('<input type="checkbox" name="bulk-delete[]" value="%s" />', $item['ID']);
    }

    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();

        $this->_column_headers = array($columns, $hidden, $sortable);

        $manuel_settings = new Manuel_Settings('1.0');
        $data = $manuel_settings->get_manuel_cron()->get_stats();

        $perPage = 20;
        $currentPage = $this->get_pagenum();
        $totalItems = count($data);

        $this->set_pagination_args([
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ]);

        $data = array_slice($data, (($currentPage-1)*$perPage), $perPage);

        $this->items = $data;
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'post_id':
            case 'post_title':
            case 'anchor_text':
            case 'time_removed':
                return esc_html($item[$column_name]);
            case 'original_link':
                return '<a href="' . esc_url($item[$column_name]) . '">' . esc_url($item[$column_name]) . '</a>';
            default:
                return print_r($item, true);
        }
    }

    public function column_post_title($item) {
        $actions = [
            'edit'      => sprintf('<a href="%s">%s</a>', get_edit_post_link($item['post_id']), __('Edit', 'manuel')),
            'trash'     => sprintf('<a href="%s">%s</a>', get_delete_post_link($item['post_id']), __('Trash', 'manuel')),
            'view'      => sprintf('<a href="%s">%s</a>', get_permalink($item['post_id']), __('View', 'manuel'))
        ];

        return sprintf('%1$s %2$s', $item['post_title'], $this->row_actions($actions));
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

$linksTable = new Manuel_Links_Stats();
?>

<div class="wrap">
    <h2><?php esc_html_e('Manuel Links Stats', 'manuel') ?></h2>
    <form method="post">
        <?php
        $linksTable->prepare_items();
        $linksTable->display();
        ?>
    </form>
</div>