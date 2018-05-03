<div class="wrap">
    <h2><?php _e('Edytuj Subskrybenta', 'wp-k5n'); ?></h2>
    <form action="" method="post">
        <table>
            <tr>
                <td colspan="2"><h3><?php _e('Subskrybent Info:', 'wp-k5n'); ?></h3></td>
            </tr>
            <tr>
                <td><span class="label_td" for="wp_subscribe_name"><?php _e('Imie', 'wp-k5n'); ?>:</span></td>
                <td><input type="text" id="wp_subscribe_name" name="wp_subscribe_name"
                           value="<?php echo $get_subscribe->name; ?>"/></td>
            </tr>
            <tr>
                <td><span class="label_td" for="wp_subscribe_surname"><?php _e('Nazwisko', 'wp-k5n'); ?>:</span></td>
                <td><input type="text" id="wp_subscribe_surname" name="wp_subscribe_surname"
                           value="<?php echo $get_subscribe->surname; ?>"/></td>
            </tr>

            <tr>
                <td><span class="label_td" for="wp_subscribe_mobile"><?php _e('Nr telefonu', 'wp-k5n'); ?>:</span></td>
                <td><input type="text" name="wp_subscribe_mobile" id="wp_subscribe_mobile"
                           value="<?php echo $get_subscribe->mobile; ?>" class="code"/></td>
            </tr>

            <?php if ($this->subscribe->get_groups()): ?>
                <tr>
                    <td><span class="label_td" for="wpk5n_group_name"><?php _e('Grupa', 'wp-k5n'); ?>:</span></td>
                    <td>
                        <select name="wpk5n_group_name" id="wpk5n_group_name">
                            <?php foreach ($this->subscribe->get_groups() as $items): ?>
                                <option value="<?php echo $items->ID; ?>" <?php selected($get_subscribe->group_ID, $items->ID); ?>><?php echo $items->name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            <?php else: ?>
                <tr>
                    <td><span class="label_td" for="wpk5n_group_name"><?php _e('Grupa', 'wp-k5n'); ?>:</span></td>
                    <td><?php echo sprintf(__('Nie dopisano grup subskrypcji! <a href="%s">Dodaj</a>', 'wp-k5n'), 'admin.php?page=wp-k5n-subscribers-group'); ?></td>
                </tr>
            <?php endif; ?>

            <tr>
                <td><span class="label_td" for="wpk5n_subscribe_status"><?php _e('Status', 'wp-k5n'); ?>:</span></td>
                <td>
                    <select name="wpk5n_subscribe_status" id="wpk5n_subscribe_status">
                        <option value="0" <?php selected($get_subscribe->status, '0'); ?>><?php _e('Nieaktywny', 'wp-k5n'); ?></option>
                        <option value="1" <?php selected($get_subscribe->status, '1'); ?>><?php _e('Aktywny', 'wp-k5n'); ?></option>
                    </select>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <a href="admin.php?page=wp-k5n-subscribers" class="button"><?php _e('Powrót', 'wp-k5n'); ?></a>
                    <input type="submit" class="button-primary" name="wp_update_subscribe"
                           value="<?php _e('Zapisz', 'wp-k5n'); ?>"/>
                </td>
            </tr>
        </table>
    </form>
</div>