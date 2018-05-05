<?php
if (!defined('ABSPATH')) {
    exit;
} // No direct access allowed ;)

class WP_K5N_Settings {

    public $setting_name;
    public $options = array();

    public function __construct() {
        $this->setting_name = 'wpk5n_settings';

        $this->options = get_option($this->setting_name);

        if (empty($this->options)) {
            update_option($this->setting_name, array());
        }

        //add_action('admin_menu', array(&$this, 'add_settings_menu'), 11);

        if (isset($_GET['page']) and ( $_GET['page'] == 'wp-k5n-settings' or $_GET['page'] == 'wp-k5n') or isset($_POST['option_page']) and $_POST['option_page'] == 'wpk5n_settings') {
            add_action('admin_init', array(&$this, 'register_settings'));
        }
    }

    /**
     * Add WP SMS Professional Package admin page settings
     * */
    public function add_settings_menu() {
        add_submenu_page('wp-k5n', __('Ustawienia', 'wp-k5n'), __('Ustawienia', 'wp-k5n'), 'wpk5n_setting', 'wp-k5n-settings', array(
            &$this,
            'render_settings'
        ));
    }

    /**
     * Gets saved settings from WP core
     *
     * @since           2.0
     * @return          array
     */
    public function get_settings() {
        $settings = get_option($this->setting_name);
        if (empty($settings)) {
            update_option($this->setting_name, array(//'admin_lang'	=>  'enable',
            ));
        }

        return apply_filters('wpk5n_get_settings', $settings);
    }

    /**
     * Registers settings in WP core
     *
     * @since           2.0
     * @return          void
     */
    public function register_settings() {
        if (false == get_option($this->setting_name)) {
            add_option($this->setting_name);
        }

        foreach ($this->get_registered_settings() as $tab => $settings) {
            add_settings_section(
                    'wpk5n_settings_' . $tab, __return_null(), '__return_false', 'wpk5n_settings_' . $tab
            );

            if (empty($settings)) {
                return;
            }

            foreach ($settings as $option) {
                $name = isset($option['name']) ? $option['name'] : '';

                add_settings_field(
                        'wpk5n_settings[' . $option['id'] . ']', $name, array(&$this, $option['type'] . '_callback'), 'wpk5n_settings_' . $tab, 'wpk5n_settings_' . $tab, array(
                    'id' => isset($option['id']) ? $option['id'] : null,
                    'desc' => !empty($option['desc']) ? $option['desc'] : '',
                    'name' => isset($option['name']) ? $option['name'] : null,
                    'section' => $tab,
                    'size' => isset($option['size']) ? $option['size'] : null,
                    'options' => isset($option['options']) ? $option['options'] : '',
                    'std' => isset($option['std']) ? $option['std'] : ''
                        )
                );

                register_setting($this->setting_name, $this->setting_name, array(&$this, 'settings_sanitize'));
            }
        }
    }

    /**
     * Gets settings tabs
     *
     * @since               2.0
     * @return              array Tabs list
     */
    public function get_tabs() {
        $tabs = array(
            'general' => __('Podstawowe', 'wp-k5n'),
            'feature' => __('Parametry', 'wp-k5n'),
            'notifications' => __('Powiadomienia', 'wp-k5n'),
        );

        return $tabs;
    }

    /**
     * Sanitizes and saves settings after submit
     *
     * @since               2.0
     *
     * @param               array $input Settings input
     *
     * @return              array New settings
     */
    public function settings_sanitize($input = array()) {

        if (empty($_POST['_wp_http_referer'])) {
            return $input;
        }

        parse_str($_POST['_wp_http_referer'], $referrer);

        $settings = $this->get_registered_settings();
        $tab = isset($referrer['tab']) ? $referrer['tab'] : 'wp';

        $input = $input ? $input : array();
        $input = apply_filters('wpk5n_settings_' . $tab . '_sanitize', $input);

        // Loop through each setting being saved and pass it through a sanitization filter
        foreach ($input as $key => $value) {

            // Get the setting type (checkbox, select, etc)
            $type = isset($settings[$tab][$key]['type']) ? $settings[$tab][$key]['type'] : false;

            if ($type) {
                // Field type specific filter
                $input[$key] = apply_filters('wpk5n_settings_sanitize_' . $type, $value, $key);
            }

            // General filter
            $input[$key] = apply_filters('wpk5n_settings_sanitize', $value, $key);
        }

        // Loop through the whitelist and unset any that are empty for the tab being saved
        if (!empty($settings[$tab])) {
            foreach ($settings[$tab] as $key => $value) {

                // settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
                if (is_numeric($key)) {
                    $key = $value['id'];
                }

                if (empty($input[$key])) {
                    unset($this->options[$key]);
                }
            }
        }

        // Merge our new settings with the existing
        $output = array_merge($this->options, $input);

        add_settings_error('wpk5n-notices', '', __('Ustawienia zostały zmienione.', 'wp-k5n'), 'updated');

        return $output;
    }

    /**
     * Get settings fields
     *
     * @since           2.0
     * @return          array Fields
     */
    public function get_registered_settings() {
        $options = array(
            'enable' => __('Włączone', 'wp-k5n'),
            'disable' => __('Wyłączone', 'wp-k5n')
        );

        $settings = apply_filters('wp_k5n_registered_settings', array(
            // General tab
            'general' => apply_filters('wp_k5n_general_settings', array(
                'admin_title' => array(
                    'id' => 'admin_title',
                    'name' => __('Administrator', 'wp-k5n'),
                    'type' => 'header'
                ),
                'admin_mobile_number' => array(
                    'id' => 'admin_mobile_number',
                    'name' => __('Admin nr tel.', 'wp-k5n'),
                    'type' => 'text',
                    'desc' => __('Numer telefonu administratora', 'wp-k5n')
                ),
            )),
            // Feature tab
            'feature' => apply_filters('wp_k5n_feature_settings', array(
                'mobile_field' => array(
                    'id' => 'mobile_field',
                    'name' => __('Pole nr telefonu', 'wp-k5n'),
                    'type' => 'header'
                ),
                'add_mobile_field' => array(
                    'id' => 'add_mobile_field',
                    'name' => __('Dodaj pole nr telefonu', 'wp-k5n'),
                    'type' => 'checkbox',
                    'options' => $options,
                    'desc' => __('Dodaje pole numeru telefonu do profilu uzytkownika i formularza rejestracyjnego.', 'wp-k5n')
                ),
                'rest_api' => array(
                    'id' => 'rest_api',
                    'name' => __('REST API', 'wp-k5n'),
                    'type' => 'header'
                ),
                'rest_api_status' => array(
                    'id' => 'rest_api_status',
                    'name' => __('Rest api functions', 'wp-k5n'),
                    'type' => 'checkbox',
                    'options' => $options,
                    'desc' => __('Add WP-K5N endpoints to the WP Rest API', 'wp-k5n')
                ),
                'layout_title' => array(
                    'id' => 'layout_title',
                    'name' => __('Wyświetlanie', 'wp-k5n'),
                    'type' => 'header'
                ),
                'subscribers_page_size' => array(
                    'id' => 'subscribers_page_size',
                    'name' => __('Subscrypcje (ilość)', 'wp-k5n'),
                    'type' => 'number',
                    'desc' => __('Wyświetlana ilośc subscrybentów na stronę', 'wp-k5n'),
                    'size' => '4',
                    'min' => '10',
                    'std' => '15',
                    'options' => array(
                    ),
                ),
                'groups_page_size' => array(
                    'id' => 'groups_page_size',
                    'name' => __('Grupy (ilość)', 'wp-k5n'),
                    'type' => 'number',
                    'desc' => __('Wyświetlana ilośc grup na stronę', 'wp-k5n'),
                    'size' => '4',
                    'min' => '10',
                    'std' => '15',
                    'options' => array(
                    ),
                ),
                'outbox_page_size' => array(
                    'id' => 'outbox_page_size',
                    'name' => __('Powiadomienia (ilość)', 'wp-k5n'),
                    'type' => 'number',
                    'desc' => __('Wyświetlana ilośc powiadomień na stronę', 'wp-k5n'),
                    'size' => '4',
                    'min' => '10',
                    'std' => '15',
                    'options' => array(
                    ),
                ),
            )),
            // Notifications tab
            'notifications' => apply_filters('wp_k5n_notifications_settings', array(
                // Publish new post
                'notif_publish_new_post_title' => array(
                    'id' => 'notif_publish_new_post_title',
                    'name' => __('Publikacja wpisu', 'wp-k5n'),
                    'type' => 'header'
                ),
                'notif_publish_new_post' => array(
                    'id' => 'notif_publish_new_post',
                    'name' => __('Status', 'wp-k5n'),
                    'type' => 'checkbox',
                    'options' => $options,
                    'desc' => __('Wyślij powiadomienie do subskrybentów k5n po publikacji nowego postu.', 'wp-k5n')
                ),
                'notif_publish_new_post_template' => array(
                    'id' => 'notif_publish_new_post_template',
                    'name' => __('Wzór komunikatu', 'wp-k5n'),
                    'type' => 'textarea',
                    'desc' => __('Wprowadź wzór wysyłanego komunikatu.', 'wp-k5n') . '<br>' .
                    sprintf(
                            __('Tytuł wpisu: %s, Treść wpisu: %s, Adres url: %s, Data wpisu: %s', 'wp-k5n'), '<code>%post_title%</code>', '<code>%post_content%</code>', '<code>%post_url%</code>', '<code>%post_date%</code>'
                    )
                ),
            )),
        ));

        return $settings;
    }

    public function header_callback($args) {
        echo '<hr/>';
    }

    public function html_callback($args) {
        echo $args['options'];
    }

    public function notice_callback($args) {
        echo $args['desc'];
    }

    public function checkbox_callback($args) {
        $checked = isset($this->options[$args['id']]) ? checked(1, $this->options[$args['id']], false) : '';
        $html = '<input type="checkbox" id="wpk5n_settings[' . $args['id'] . ']" name="wpk5n_settings[' . $args['id'] . ']" value="1" ' . $checked . '/>';
        $html .= '<label for="wpk5n_settings[' . $args['id'] . ']"> ' . __('Active', 'wp-k5n') . '</label>';
        $html .= '<p class="description"> ' . $args['desc'] . '</p>';

        echo $html;
    }

    public function multicheck_callback($args) {
        $html = '';
        foreach ($args['options'] as $key => $value) {
            $option_name = $args['id'] . '-' . $key;
            $this->checkbox_callback(array(
                'id' => $option_name,
                'desc' => $value
            ));
            echo '<br>';
        }

        echo $html;
    }

    public function radio_callback($args) {
        foreach ($args['options'] as $key => $option) :
            $checked = false;

            if (isset($this->options[$args['id']]) && $this->options[$args['id']] == $key) {
                $checked = true;
            } elseif (isset($args['std']) && $args['std'] == $key && !isset($this->options[$args['id']])) {
                $checked = true;
            }

            echo '<input name="wpk5n_settings[' . $args['id'] . ']"" id="wpk5n_settings[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>';
            echo '<label for="wpk5n_settings[' . $args['id'] . '][' . $key . ']">' . $option . '</label>&nbsp;&nbsp;';
        endforeach;

        echo '<p class="description">' . $args['desc'] . '</p>';
    }

    public function text_callback($args) {
        if (isset($this->options[$args['id']]) and $this->options[$args['id']]) {
            $value = $this->options[$args['id']];
        } else {
            $value = isset($args['std']) ? $args['std'] : '';
        }

        $size = ( isset($args['size']) && !is_null($args['size']) ) ? $args['size'] : 'regular';
        $html = '<input type="text" class="' . $size . '-text" id="wpk5n_settings[' . $args['id'] . ']" name="wpk5n_settings[' . $args['id'] . ']" value="' . esc_attr(stripslashes($value)) . '"/>';
        $html .= '<p class="description"> ' . $args['desc'] . '</p>';

        echo $html;
    }

    public function number_callback($args) {
        if (isset($this->options[$args['id']])) {
            $value = $this->options[$args['id']];
        } else {
            $value = isset($args['std']) ? $args['std'] : '';
        }

        $max = isset($args['max']) ? $args['max'] : 999999;
        $min = isset($args['min']) ? $args['min'] : 0;
        $step = isset($args['step']) ? $args['step'] : 1;

        $size = ( isset($args['size']) && !is_null($args['size']) ) ? $args['size'] : 'regular';
        $html = '<input type="number" step="' . esc_attr($step) . '" max="' . esc_attr($max) . '" min="' . esc_attr($min) . '" class="' . $size . '-text" id="wpk5n_settings[' . $args['id'] . ']" name="wpk5n_settings[' . $args['id'] . ']" value="' . esc_attr(stripslashes($value)) . '"/>';
        $html .= '<p class="description"> ' . $args['desc'] . '</p>';

        echo $html;
    }

    public function textarea_callback($args) {
        if (isset($this->options[$args['id']])) {
            $value = $this->options[$args['id']];
        } else {
            $value = isset($args['std']) ? $args['std'] : '';
        }

        $size = ( isset($args['size']) && !is_null($args['size']) ) ? $args['size'] : 'regular';
        $html = '<textarea class="large-text" cols="50" rows="5" id="wpk5n_settings[' . $args['id'] . ']" name="wpk5n_settings[' . $args['id'] . ']">' . esc_textarea(stripslashes($value)) . '</textarea>';
        $html .= '<p class="description"> ' . $args['desc'] . '</p>';

        echo $html;
    }

    public function password_callback($args) {
        if (isset($this->options[$args['id']])) {
            $value = $this->options[$args['id']];
        } else {
            $value = isset($args['std']) ? $args['std'] : '';
        }

        $size = ( isset($args['size']) && !is_null($args['size']) ) ? $args['size'] : 'regular';
        $html = '<input type="password" class="' . $size . '-text" id="wpk5n_settings[' . $args['id'] . ']" name="wpk5n_settings[' . $args['id'] . ']" value="' . esc_attr($value) . '"/>';
        $html .= '<p class="description"> ' . $args['desc'] . '</p>';

        echo $html;
    }

    public function missing_callback($args) {
        echo '&ndash;';

        return false;
    }

    public function select_callback($args) {
        if (isset($this->options[$args['id']])) {
            $value = $this->options[$args['id']];
        } else {
            $value = isset($args['std']) ? $args['std'] : '';
        }

        $html = '<select id="wpk5n_settings[' . $args['id'] . ']" name="wpk5n_settings[' . $args['id'] . ']"/>';

        foreach ($args['options'] as $option => $name) :
            $selected = selected($option, $value, false);
            $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
        endforeach;

        $html .= '</select>';
        $html .= '<p class="description"> ' . $args['desc'] . '</p>';

        echo $html;
    }

    public function advancedselect_callback($args) {
        if (isset($this->options[$args['id']])) {
            $value = $this->options[$args['id']];
        } else {
            $value = isset($args['std']) ? $args['std'] : '';
        }

        if (is_rtl()) {
            $class_name = 'chosen-select chosen-rtl';
        } else {
            $class_name = 'chosen-select';
        }

        $html = '<select class="' . $class_name . '" id="wpk5n_settings[' . $args['id'] . ']" name="wpk5n_settings[' . $args['id'] . ']"/>';

        foreach ($args['options'] as $key => $v) {
            $html .= '<optgroup label="' . ucfirst(str_replace('_', ' ', $key)) . '">';

            foreach ($v as $option => $name) :
                $disabled = ( $key == 'pro_pack_gateways' ) ? $disabled = ' disabled' : '';
                $selected = selected($option, $value, false);
                $html .= '<option value="' . $option . '" ' . $selected . ' ' . $disabled . '>' . ucfirst($name) . '</option>';
            endforeach;

            $html .= '</optgroup>';
        }

        $html .= '</select>';
        $html .= '<p class="description"> ' . $args['desc'] . '</p>';

        echo $html;
    }

    public function color_select_callback($args) {
        if (isset($this->options[$args['id']])) {
            $value = $this->options[$args['id']];
        } else {
            $value = isset($args['std']) ? $args['std'] : '';
        }

        $html = '<select id="wpk5n_settings[' . $args['id'] . ']" name="wpk5n_settings[' . $args['id'] . ']"/>';

        foreach ($args['options'] as $option => $color) :
            $selected = selected($option, $value, false);
            $html .= '<option value="' . $option . '" ' . $selected . '>' . $color['label'] . '</option>';
        endforeach;

        $html .= '</select>';
        $html .= '<p class="description"> ' . $args['desc'] . '</p>';

        echo $html;
    }

    public function rich_editor_callback($args) {
        global $wp_version;

        if (isset($this->options[$args['id']])) {
            $value = $this->options[$args['id']];
        } else {
            $value = isset($args['std']) ? $args['std'] : '';
        }

        if ($wp_version >= 3.3 && function_exists('wp_editor')) {
            $html = wp_editor(stripslashes($value), 'wpk5n_settings[' . $args['id'] . ']', array('textarea_name' => 'wpk5n_settings[' . $args['id'] . ']'));
        } else {
            $html = '<textarea class="large-text" rows="10" id="wpk5n_settings[' . $args['id'] . ']" name="wpk5n_settings[' . $args['id'] . ']">' . esc_textarea(stripslashes($value)) . '</textarea>';
        }

        $html .= '<p class="description"> ' . $args['desc'] . '</p>';

        echo $html;
    }

    public function upload_callback($args) {
        if (isset($this->options[$args['id']])) {
            $value = $this->options[$args['id']];
        } else {
            $value = isset($args['std']) ? $args['std'] : '';
        }

        $size = ( isset($args['size']) && !is_null($args['size']) ) ? $args['size'] : 'regular';
        $html = '<input type="text" class="' . $size . '-text wpk5n_upload_field" id="wpk5n_settings[' . $args['id'] . ']" name="wpk5n_settings[' . $args['id'] . ']" value="' . esc_attr(stripslashes($value)) . '"/>';
        $html .= '<span>&nbsp;<input type="button" class="wpk5n_settings_upload_button button-secondary" value="' . __('Upload File', 'wpk5n') . '"/></span>';
        $html .= '<p class="description"> ' . $args['desc'] . '</p>';

        echo $html;
    }

    public function color_callback($args) {
        if (isset($this->options[$args['id']])) {
            $value = $this->options[$args['id']];
        } else {
            $value = isset($args['std']) ? $args['std'] : '';
        }

        $default = isset($args['std']) ? $args['std'] : '';

        $size = ( isset($args['size']) && !is_null($args['size']) ) ? $args['size'] : 'regular';
        $html = '<input type="text" class="wpk5n-color-picker" id="wpk5n_settings[' . $args['id'] . ']" name="wpk5n_settings[' . $args['id'] . ']" value="' . esc_attr($value) . '" data-default-color="' . esc_attr($default) . '" />';
        $html .= '<p class="description"> ' . $args['desc'] . '</p>';

        echo $html;
    }

    public function render_settings() {
        $active_tab = isset($_GET['tab']) && array_key_exists($_GET['tab'], $this->get_tabs()) ? $_GET['tab'] : 'general';

        ob_start();
        ?>
        <div class="wrap wpk5n-settings-wrap">
            <?php do_action('wp_k5n_settings_page'); ?>
            <h2><?php _e('Ustawienia', 'wp-k5n') ?></h2>
            <div class="wpk5n-tab-group">
                <ul class="wpk5n-tab">
                    <li id="wpk5n-logo">
                        <img src="<?php echo WP_K5N_DIR_PLUGIN; ?>assets/images/logo-200.png"/>
                        <p><?php echo sprintf(__('WP-K5N v%s', 'wp-k5n'), WP_K5N_VERSION); ?></p>
                        <?php do_action('wp_k5n_after_setting_logo'); ?>
                    </li>
                    <?php
                    foreach ($this->get_tabs() as $tab_id => $tab_name) {

                        $tab_url = add_query_arg(array(
                            'settings-updated' => false,
                            'tab' => $tab_id
                        ));

                        $active = $active_tab == $tab_id ? 'active' : '';

                        echo '<li><a href="' . esc_url($tab_url) . '" title="' . esc_attr($tab_name) . '" class="' . $active . '">';
                        echo $tab_name;
                        echo '</a></li>';
                    }
                    ?>
                </ul>
                <?php echo settings_errors('wpk5n-notices'); ?>
                <div class="wpk5n-tab-content">
                    <form method="post" action="options.php">
                        <table class="form-table">
                            <?php
                            settings_fields($this->setting_name);
                            do_settings_fields('wpk5n_settings_' . $active_tab, 'wpk5n_settings_' . $active_tab);
                            ?>
                        </table>
                        <?php submit_button(); ?>
                    </form>
                </div>
            </div>
        </div>
        <?php
        echo ob_get_clean();
    }

}
