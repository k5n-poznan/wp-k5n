<div class="wrap">
    <h2><?php _e('Subskrybenci', 'wp-k5n'); ?></h2>

    <div class="wpk5n-button-group">
        <a href="admin.php?page=wp-k5n-subscribers&action=add" class="button"><span
                class="dashicons dashicons-admin-users"></span> <?php _e('Dodaj Subskrybenta', 'wp-k5n'); ?></a>
        <a href="admin.php?page=wp-k5n-subscribers-group" class="button"><span
                class="dashicons dashicons-category"></span> <?php _e('Zarządzaj grupą', 'wp-k5n'); ?></a>
        <a href="admin.php?page=wp-k5n-subscribers&action=import" class="button"><span
                class="dashicons dashicons-undo"></span> <?php _e('Importuj', 'wp-k5n'); ?></a>
        <a href="admin.php?page=wp-k5n-subscribers&action=export" class="button"><span
                class="dashicons dashicons-redo"></span> <?php _e('Eksportuj', 'wp-k5n'); ?></a>
    </div>

    <form id="subscribers-filter" method="get">
        <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
        <?php $list_table->search_box(__('Search', 'wp-k5n'), 'search_id'); ?>
        <?php $list_table->display(); ?>
    </form>
</div>