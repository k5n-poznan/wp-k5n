<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Tytuł', 'wp-k5n'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
           name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>">
</p>

<p>
    <label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Opis', 'wp-k5n'); ?></label>
    <textarea class="widefat" id="<?php echo $this->get_field_id('description'); ?>"
              name="<?php echo $this->get_field_name('description'); ?>"><?php echo esc_attr($description); ?></textarea>
<p class="description"><?php _e('HTML jest wspierany.', 'wp-k5n'); ?></p>
</p>

<p>
    <input class="checkbox" id="<?php echo $this->get_field_id('show_group'); ?>"
           name="<?php echo $this->get_field_name('show_group'); ?>" type="checkbox"
           value="1" <?php checked($show_group, 1); ?>>
    <label for="<?php echo $this->get_field_id('show_group'); ?>"><?php _e('Pokaż grupę', 'wp-k5n'); ?></label>
</p>

<p>
    <input class="checkbox" id="<?php echo $this->get_field_id('send_activation_code'); ?>"
           name="<?php echo $this->get_field_name('send_activation_code'); ?>" type="checkbox"
           value="1" <?php checked($send_activation_code, 1); ?>>
    <label for="<?php echo $this->get_field_id('send_activation_code'); ?>"><?php _e('Weryfikacja kodem aktywacyjnym', 'wp-k5n'); ?></label>
</p>

<p>
    <input class="checkbox" id="<?php echo $this->get_field_id('send_welcome_sms'); ?>"
           name="<?php echo $this->get_field_name('send_welcome_sms'); ?>" type="checkbox"
           value="1" <?php checked($send_welcome_k5n, 1); ?>>
    <label for="<?php echo $this->get_field_id('send_welcome_sms'); ?>"><?php _e('Wyślij powitalny SMS', 'wp-k5n'); ?></label>
</p>

<?php if ($send_welcome_sms) : ?>
    <p>
        <label for="<?php echo $this->get_field_id('welcome_sms_template'); ?>"><?php _e('Tekst powitalnego SMS', 'wp-k5n'); ?></label>
        <textarea class="widefat" id="<?php echo $this->get_field_id('welcome_sms_template'); ?>"
                  name="<?php echo $this->get_field_name('welcome_sms_template'); ?>"><?php echo esc_attr($welcome_sms_template); ?></textarea>
    <p class="description">
        <?php echo sprintf(__('Imie: %s, Nazwisko: %s, Nr telefonu: %s', 'wp-k5n'), '<code>%subscribe_name%</code>', '<code>%subscribe_surname%</code>', '<code>%subscribe_mobile%</code>'); ?>
    </p>
    </p>
<?php endif; ?>

<p>
    <input class="checkbox" id="<?php echo $this->get_field_id('mobile_number_terms'); ?>"
           name="<?php echo $this->get_field_name('mobile_number_terms'); ?>" type="checkbox"
           value="1" <?php checked($mobile_number_terms, 1); ?>>
    <label for="<?php echo $this->get_field_id('mobile_number_terms'); ?>"><?php _e('Właściwości nr telefonu', 'wp-k5n'); ?></label>
</p>

<?php if ($mobile_number_terms) : ?>
    <p>
        <label for="<?php echo $this->get_field_id('mobile_field_placeholder'); ?>"><?php _e('Opis w pustym polu telefonu', 'wp-k5n'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('mobile_field_placeholder'); ?>"
               name="<?php echo $this->get_field_name('mobile_field_placeholder'); ?>" type="text"
               value="<?php echo esc_attr($mobile_field_placeholder); ?>">
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('mobile_field_max'); ?>"><?php _e('Maksymalna liczba cyfr', 'wp-k5n'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('mobile_field_max'); ?>"
               name="<?php echo $this->get_field_name('mobile_field_max'); ?>" type="number"
               value="<?php echo esc_attr($mobile_field_max); ?>">
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('mobile_field_min'); ?>"><?php _e('Minimalna liczba cyfr', 'wp-k5n'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('mobile_field_min'); ?>"
               name="<?php echo $this->get_field_name('mobile_field_min'); ?>" type="number"
               value="<?php echo esc_attr($mobile_field_min); ?>">
    </p>
<?php endif; ?>