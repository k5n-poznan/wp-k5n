<div class="wrap">
    <h2><?php _e('Add Subscriber', 'wp-k5n'); ?></h2>
    <form action="" method="post">
        <table>
            <tr>
                <td colspan="2"><h3><?php _e('Subscriber Info:', 'wp-k5n'); ?></h3></td>
            </tr>
            <tr>
                <td><span class="label_td" for="wp_subscribe_name"><?php _e('Name', 'wp-k5n'); ?>:</span></td>
                <td><input type="text" id="wp_subscribe_name" name="wp_subscribe_name"/></td>
            </tr>

            <tr>
                <td><span class="label_td" for="wp_subscribe_mobile"><?php _e('Mobile', 'wp-k5n'); ?>:</span></td>
                <td><input type="text" name="wp_subscribe_mobile" id="wp_subscribe_mobile" class="code"/></td>
            </tr>

            <?php if ($this->subscribe->get_groups()): ?>
                <tr>
                    <td><span class="label_td" for="wpk5n_group_name"><?php _e('Group', 'wp-k5n'); ?>:</span></td>
                    <td>
                        <select name="wpk5n_group_name" id="wpk5n_group_name">
                            <?php foreach ($this->subscribe->get_groups() as $items): ?>
                                <option value="<?php echo $items->ID; ?>"><?php echo $items->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            <?php else: ?>
                <tr>
                    <td><span class="label_td" for="wpk5n_group_name"><?php _e('Group', 'wp-k5n'); ?>:</span></td>
                    <td><?php echo sprintf(__('There is no group! <a href="%s">Add</a>', 'wp-k5n'), 'admin.php?page=wp-k5n-subscribers-group'); ?></td>
                </tr>
            <?php endif; ?>

            <tr>
                <td colspan="2">
                    <a href="admin.php?page=wp-k5n-subscribers" class="button"><?php _e('Back', 'wp-k5n'); ?></a>
                    <input type="submit" class="button-primary" name="wp_add_subscribe"
                           value="<?php _e('Add', 'wp-k5n'); ?>"/>
                </td>
            </tr>
        </table>
    </form>
</div>