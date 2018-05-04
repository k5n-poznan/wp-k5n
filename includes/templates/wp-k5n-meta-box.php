<script type="text/javascript">
    jQuery(document).ready(function () {
        if (jQuery('#wps-send-subscribe').val() == 'yes') {
            jQuery('#wpk5n-select-subscriber-group').show();
            jQuery('#wpk5n-custom-text').show();
        }

        jQuery("#wps-send-subscribe").change(function () {
            if (this.value == 'yes') {
                jQuery('#wpk5n-select-subscriber-group').show();
                jQuery('#wpk5n-custom-text').show();
            } else {
                jQuery('#wpk5n-select-subscriber-group').hide();
                jQuery('#wpk5n-custom-text').hide();
            }

        });
    })
</script>

<div>
    <label>
        <?php _e('Czy wysłać powiadomienie do subskrybentów K5N?', 'wp-k5n'); ?><br/>
        <select name="wps_send_subscribe" id="wps-send-subscribe">
            <option value="0" selected><?php _e('Please select', 'wp-k5n'); ?></option>
            <option value="yes"><?php _e('Tak'); ?></option>
            <option value="no"><?php _e('Nie'); ?></option>
        </select>
    </label>
</div>

<div id="wpk5n-select-subscriber-group">
    <label>
        <?php _e('Wybierz grupę', 'wp-k5n'); ?><br/>
        <select name="wps_subscribe_group">
            <option value="all"><?php echo sprintf(__('Wszyscy (%s aktywnych subskrybentów)', 'wp-k5n'), $username_active); ?></option>
            <?php foreach ($get_group_result as $items): ?>
                <option value="<?php echo $items->ID; ?>"><?php echo $items->name; ?></option><?php endforeach; ?>
        </select>
    </label>
</div>

<div id="wpk5n-custom-text">
    <label>
        <?php _e('Wzór powiadomienia', 'wp-k5n'); ?><br/>
        <textarea id="wpk5n-text-template" name="wpk5n_text_template"><?php
            global $wpk5n_option;
            echo $wpk5n_option['notif_publish_new_post_template'];
            ?></textarea>
        <p class="description data"><?php _e('Wprowadź dane:', 'wp-k5n'); ?>
            <br/><?php _e('Tytuł wpisu', 'wp-k5n'); ?>: <code>%post_title%</code>
            <br/><?php _e('Treść wpisu', 'wp-k5n'); ?>: <code>%post_content%</code>
            <br/><?php _e('Adres url', 'wp-k5n'); ?>: <code>%post_url%</code>
            <br/><?php _e('Data wpisu', 'wp-k5n'); ?>: <code>%post_date%</code>
        </p>
    </label>
</div>