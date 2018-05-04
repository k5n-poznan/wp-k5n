<div id="wpk5n-subscribe">
    <div id="wpk5n-subscribe-result"></div>
    <div id="wpk5n-subscribe-step-1">
        <p><?php echo $instance['description']; ?></p>
        <div class="wpk5n-subscribe-form">
            <label><?php _e('Imie', 'wp-k5n'); ?>:</label>
            <input id="wpk5n-subscribe-name" type="text" placeholder="<?php _e('Proszę wpisać imie', 'wp-k5n'); ?>" class="wpk5n-input"/>
        </div>
        <div class="wpk5n-subscribe-form">
            <label><?php _e('Nazwisko', 'wp-k5n'); ?>:</label>
            <input id="wpk5n-subscribe-surname" type="text" placeholder="<?php _e('Proszę wpisać nazwisko', 'wp-k5n'); ?>" class="wpk5n-input"/>
        </div>

        <div class="wpk5n-subscribe-form">
            <label><?php _e('Nr telefonu', 'wp-k5n'); ?>:</label>
            <input id="wpk5n-subscribe-mobile" type="text" placeholder="<?php echo $instance['mobile_field_placeholder']; ?>"
                   class="wpk5n-input"/>
        </div>

        <?php if ($instance['show_group']) { ?>
            <div class="wpk5n-subscribe-form">
                <label><?php _e('Grupa', 'wp-k5n'); ?>:</label>
                <select id="wpk5n-subscribe-groups" class="wpk5n-input">
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
                       <?php _e('Wiadomości K5N', 'wp-k5n'); ?>
            </label>

            <label>
                <input type="radio" name="subscribe_type" id="wpk5n-type-unsubscribe" value="unsubscribe"/>
                <?php _e('Rezygnuję', 'wp-k5n'); ?>
            </label>
        </div>

        <button class="wpk5n-button" id="wpk5n-subscribe-submit"><?php _e('Rejestruj', 'wp-k5n'); ?></button>
    </div>

    <div id="wpk5n-subscribe-step-2">
        <div class="wpk5n-subscribe-form">
            <label><?php _e('Kod aktywacyjny:', 'wp-k5n'); ?></label>
            <input type="text" id="wpk5n-ativation-code" placeholder="<?php _e('Kod aktywacyjny:', 'wp-k5n'); ?>"
                   class="wpk5n-input"/>
        </div>
        <button class="wpk5n-button" id="activation"><?php _e('Aktywacja', 'wp-k5n'); ?></button>
    </div>
    <input type="hidden" id="wpk5n-widget-id" value="<?php echo $widget_id; ?>">
</div>