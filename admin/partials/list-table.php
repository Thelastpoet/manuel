<?php
if ( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Manuel_Stats_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct( array(
            'singular' => __( 'Stat', 'manuel' ),
            'plural'   => __( 'Stats', 'manuel' ),
            'ajax'     => false,
        ) );
    }

    public function get_columns() {
        return array(
            'post_id'      => __( 'Post ID', 'manuel' ),
            'post_title'   => __( 'Post Title', 'manuel' ),
            'original_link' => __( 'Original Link', 'manuel' ),
            'anchor_text'  => __( 'Anchor Text', 'manuel' ),
            'actions'      => __( 'Actions', 'manuel' ),
        );
    }

    public function column_default( $item, $column_name ) {
        switch ( $column_name ) {
            case 'post_id':
            case 'post_title':
            case 'original_link':
            case 'anchor_text':
                return $item[ $column_name ];
            case 'actions':
                return sprintf(
                    '<button class="button edit-link" data-post-id="%s" data-original-link="%s">%s</button>',
                    $item['post_id'],
                    $item['original_link'],
                    __( 'Edit', 'manuel' )
                );
            default:
                return print_r( $item, true );
        }
    }

    public function prepare_items() {
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = array();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $manuel_settings = new Manuel_Settings( '1.0' );
        $this->items = $manuel_settings->get_manuel_cron()->get_stats();
    }

}