<?php

defined('ABSPATH') || exit;

class IntelliDraft_Settings
{

    public function __construct()
    {
        add_action('admin_init', array($this, 'settings_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function settings_init()
    {
        register_setting('intellidraft_settings_group', 'intellidraft_api_settings', array('sanitize_callback' => array($this, 'intellidraft_api_sanitize_settings')));

        add_settings_section(
            'intellidraft_api_settings_section',
            '',
            null,
            'intellidraft_api_cgpt'
        );

        add_settings_field(
            'intellidraft_cgpt_api_key',
            'Chat-GPT API Key',
            array($this, 'cgpt_api_key_render'),
            'intellidraft_api_cgpt',
            'intellidraft_api_settings_section'
        );

        add_settings_field(
            'intellidraft_cgpt_model',
            'Chat-GPT Model',
            array($this, 'cgpt_model_render'),
            'intellidraft_api_cgpt',
            'intellidraft_api_settings_section'
        );
    }

    function intellidraft_api_sanitize_settings($settings)
    {
        if (isset($settings['intellidraft_cgpt_api_key'])) {
            $settings['intellidraft_cgpt_api_key'] = IntelliDraft_CGPT_Api::api_key_encrypt(sanitize_text_field($settings['intellidraft_cgpt_api_key']));
        }
        return $settings;
    }

    public function cgpt_api_key_render()
    {
        $options = get_option('intellidraft_api_settings');
        $api_key = isset($options['intellidraft_cgpt_api_key']) ? IntelliDraft_CGPT_Api::api_key_decrypt($options['intellidraft_cgpt_api_key']) : '';
?>
        <input type='text' class="input-wide" name='intellidraft_api_settings[intellidraft_cgpt_api_key]' value='<?php echo $api_key ? $api_key : '' ?>'>
<?php
    }

    public function cgpt_model_render()
    {
        $options = get_option('intellidraft_api_settings');
        $chatgpt = new IntelliDraft_CGPT_Api();
        $models = $chatgpt->get_models();
        echo '<select id="intellidraft_cgpt_model" name="intellidraft_api_settings[intellidraft_cgpt_model]" class="input-wide">';
        foreach ($models->data as $model) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($model->id),
                isset($options['intellidraft_cgpt_model']) && $options['intellidraft_cgpt_model'] === $model->id ? 'selected="selected"' : '',
                esc_html($model->id)
            );
        }
        echo '</select>';
        if (!isset($options['intellidraft_cgpt_api_key'])) {
            echo '<div class="info-text">Fill in your api key and save the settings to see the list of models.</div>';
        }
    }

    public function add_admin_menu()
    {
        add_options_page(
            'IntelliDraft Settings',
            'IntelliDraft',
            'manage_options',
            'intellidraft',
            array($this, 'create_admin_page'),
            99
        );
    }

    public function create_admin_page()
    {
        include plugin_dir_path(__FILE__) . '/settings-page.php';
    }

    public function enqueue_admin_assets($hook)
    {
        if ($hook != 'toplevel_page_intellidraft') {
            return;
        }

        wp_enqueue_style('intellidraft_settings', plugin_dir_url(__FILE__) . '../../assets/css/admin.css', array(), '1.0.0');
    }
}
