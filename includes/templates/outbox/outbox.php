<div class="wrap">
    <h2><?php _e('Powiadomienia K5N', 'wp-k5n'); ?></h2>

    <div class="wpk5n-outbox">
        <form id="outbox-filter" method="get">
            <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
            <?php $list_table->search_box(__('Wyszukaj', 'wp-k5n'), 'search_id'); ?>
            <?php $list_table->display(); ?>
        </form>
    </div> 
</div>