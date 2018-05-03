<div id="wpk5n-subscribe">
    <div id="wpk5n-result"></div>
    <div id="wpk5n-step-1">
        <p><?php echo $instance['description']; ?></p>
        <div class="wpk5n-subscribe-form">
            <label><?php _e('Your name', 'wp-k5n'); ?>:</label>
            <input id="wpk5n-name" type="text" placeholder="<?php _e('Your name', 'wp-k5n'); ?>" class="wpk5n-input"/>
        </div>

        <div class="wpk5n-subscribe-form">
            <label><?php _e('Your mobile', 'wp-k5n'); ?>:</label>
            <input id="wpk5n-mobile" type="text" placeholder="<?php echo $instance['mobile_field_placeholder']; ?>"
                   class="wpk5n-input"/>
        </div>

        <?php if ($instance['show_group']) { ?>
            <div class="wpk5n-subscribe-form">
                <label><?php _e('Group', 'wp-k5n'); ?>:</label>
                <select id="wpk5n-groups" class="wpk5n-input">
                    <?php foreach ($get_group as $items): ?>
                        <option value="<?php echo $items->ID; ?>"><?php echo $items->name; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php } ?>

        <div class="wpk5n-subscribe-form">
            <label>
                <input type="radio" name="subscribe_type" id="wpk5n-type-subscribe" value="subscribe"
                       checked="checked"/>
                       <?php _e('Subscribe', 'wp-k5n'); ?>
            </label>

            <label>
                <input type="radio" name="subscribe_type" id="wpk5n-type-unsubscribe" value="unsubscribe"/>
                <?php _e('Unsubscribe', 'wp-k5n'); ?>
            </label>
        </div>

        <button class="wpk5n-button" id="wpk5n-submit"><?php _e('Subscribe', 'wp-k5n'); ?></button>
    </div>

    <div id="wpk5n-step-2">
        <div class="wpk5n-subscribe-form">
            <label><?php _e('Activation code:', 'wp-k5n'); ?></label>
            <input type="text" id="wpk5n-ativation-code" placeholder="<?php _e('Activation code:', 'wp-k5n'); ?>"
                   class="wpk5n-input"/>
        </div>
        <button class="wpk5n-button" id="activation"><?php _e('Activation', 'wp-k5n'); ?></button>
    </div>
    <input type="hidden" id="wpk5n-widget-id" value="<?php echo $widget_id; ?>">
</div>