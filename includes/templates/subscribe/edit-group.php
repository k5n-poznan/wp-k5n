<div class="wrap">
    <h2><?php _e( 'Edytuj Grupę', 'wp-k5n' ); ?></h2>
    <form action="" method="post">
        <table>
            <tr>
                <td colspan="2"><h3><?php _e( 'Grupa Info:', 'wp-k5n' ); ?></h3></td>
            </tr>
            <tr>
                <td><span class="label_td" for="wp_group_name"><?php _e( 'Nazwa', 'wp-k5n' ); ?>:</span></td>
                <td><input type="text" id="wp_group_name" name="wp_group_name" value="<?php echo $get_group->name; ?>"/>
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <a href="admin.php?page=wp-sms-subscribers-group" class="button"><?php _e( 'Back', 'wp-k5n' ); ?></a>
                    <input type="submit" class="button-primary" name="wp_update_group"
                           value="<?php _e( 'Zapisz', 'wp-k5n' ); ?>"/>
                </td>
            </tr>
        </table>
    </form>
</div>