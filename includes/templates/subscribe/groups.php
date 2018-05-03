<div class="wrap">
    <h2><?php _e( 'Grupy', 'wp-k5n' ); ?></h2>

    <div class="wpsms-button-group">
        <a href="admin.php?page=wp-k5n-subscribers-group&action=add" class="button"><span
                    class="dashicons dashicons-groups"></span> <?php _e( 'Dodaj Grupę', 'wp-k5n' ); ?></a>
    </div>

    <form id="subscribers-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
		<?php $list_table->search_box( __( 'Wyszukaj', 'wp-k5n' ), 'search_id' ); ?>
		<?php $list_table->display(); ?>
    </form>
</div>