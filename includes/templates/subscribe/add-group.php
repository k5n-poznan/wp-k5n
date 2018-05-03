<div class="wrap">
    <h2><?php _e( 'Dodanie grupy subskrypcji', 'wp-k5n' ); ?></h2>
    <form action="" method="post">
        <table>
            <tr>
                <td colspan="2"><h3><?php _e( 'Grupa Info:', 'wp-k5n' ); ?></h3></td>
            </tr>
            <tr>
                <td><span class="label_td" for="wp_group_name"><?php _e( 'Nazwa', 'wp-k5n' ); ?>:</span></td>
                <td><input type="text" id="wp_group_name" name="wp_group_name"/></td>
            </tr>

            <tr>
                <td colspan="2">
                    <a href="admin.php?page=wp-k5n-subscribers-group" class="button"><?php _e( 'Powrót', 'wp-k5n' ); ?></a>
                    <input type="submit" class="button-primary" name="wp_add_group"
                           value="<?php _e( 'Dodaj', 'wp-k5n' ); ?>"/>
                </td>
            </tr>
        </table>
    </form>
</div>