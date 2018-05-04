<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery(".wpk5n-value").hide();
        jQuery(".wpk5n-group").show();

        jQuery("select#select_sender").change(function () {
            var get_method = "";
            jQuery("select#select_sender option:selected").each(
                    function () {
                        get_method += jQuery(this).attr('id');
                    }
            );
            if (get_method == 'wp_subscribe_username') {
                jQuery(".wpk5n-value").hide();
                jQuery(".wpk5n-group").fadeIn();
            } else if (get_method == 'wp_users') {
                jQuery(".wpk5n-value").hide();
                jQuery(".wpk5n-users").fadeIn();
            } else if (get_method == 'wp_tellephone') {
                jQuery(".wpk5n-value").hide();
                jQuery(".wpk5n-numbers").fadeIn();
                jQuery("#wp_get_number").focus();
            }
        });

        jQuery("#wp_get_message").counter({
            count: 'up',
            goal: 'sky',
            msg: '<?php _e('characters', 'wp-k5n'); ?>'
        })
    });
</script>

<div class="wrap">
    <h2><?php _e('Wyślij powiadomienie SMS K5N', 'wp-k5n'); ?></h2>
    <form method="post" action="">
        <table class="form-table">
            <?php wp_nonce_field('update-options'); ?>

            <tr>
                <td><?php _e('Wyślij do', 'wp-k5n'); ?>:</td>
                <td>
                    <select name="wp_send_to" id="select_sender">
                        <option value="wp_subscribe_username"
                                id="wp_subscribe_username"><?php _e('Subskrydenci', 'wp-k5n'); ?></option>
                        <option value="wp_users" id="wp_users"><?php _e('Użytkownicy Wordpress', 'wp-k5n'); ?></option>
                        <option value="wp_tellephone" id="wp_tellephone"><?php _e('Numer(y)', 'wp-k5n'); ?></option>
                    </select>

                    <select name="wpk5n_group_name" class="wpk5n-value wpk5n-group">
                        <option value="all">
                            <?php
                            global $wpdb, $table_prefix;
                            $username_active = $wpdb->query("SELECT * FROM {$table_prefix}k5n_subscribes WHERE status = '1'");
                            echo sprintf(__('Wszyscy (%s subskrybentów aktywnych)', 'wp-k5n'), $username_active);
                            ?>
                        </option>
                        <?php foreach ($get_group_result as $items): ?>
                            <option value="<?php echo $items->ID; ?>"><?php echo $items->name; ?></option>
                        <?php endforeach; ?>
                    </select>

                    <span class="wpk5n-value wpk5n-users">
                        <span><?php echo sprintf(__('<b>%s</b> użytkowników z nr telefonu.', 'wp-k5n'), count($get_users_mobile)); ?></span>
                    </span>

                    <span class="wpk5n-value wpk5n-numbers">
                        <div class="clearfix"></div>
                        <textarea cols="80" rows="2" style="direction:ltr;" id="wp_get_number" name="wp_get_number"></textarea>
                    </span>
                </td>
            </tr>

            <tr>
                <td><?php _e('Tresć', 'wp-k5n'); ?>:</td>
                <td>
                    <textarea dir="auto" cols="80" rows="5" name="wp_get_message" id="wp_get_message"></textarea><br/>
                </td>
            </tr>
            <?php if ($this->k5n->flash == "enable") { ?>
                <tr>
                    <td><?php _e('Wyślij Flash', 'wp-k5n'); ?>:</td>
                    <td>
                        <input type="radio" id="flash_yes" name="wp_flash" value="true"/>
                        <label for="flash_yes"><?php _e('Yes', 'wp-k5n'); ?></label>
                        <input type="radio" id="flash_no" name="wp_flash" value="false" checked="checked"/>
                        <label for="flash_no"><?php _e('No', 'wp-k5n'); ?></label>
                        <br/>
                        <p class="description"><?php _e('Flash umożliwia wysyłanie wiadomości bez pytania, otwiera się', 'wp-k5n'); ?></p>
                    </td>
                </tr>
            <?php } ?>
            <tr>
                <td>
                    <p class="submit">
                        <input type="submit" class="button-primary" name="SendSMS"
                               value="<?php _e('Wyślij', 'wp-k5n'); ?>"/>
                    </p>
                </td>
            </tr>
        </table>
    </form>
</div>