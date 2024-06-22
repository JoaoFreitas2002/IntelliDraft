<?php

defined('ABSPATH') || exit;

class IntelliWriter_Settings
{

    public function __construct()
    {
        add_action('admin_init', array($this, 'settings_init'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }

    public function settings_init()
    {
        register_setting('intelliwriter_settings_group', 'intelliwriter_api_settings');

        add_settings_section(
            'intelliwriter_api_settings_section',
            '',
            null,
            'intelliwriter_api_cgpt'
        );

        add_settings_field(
            'intelliwriter_cgpt_api_key',
            'API Key',
            array($this, 'cgpt_api_key_render'),
            'intelliwriter_api_cgpt',
            'intelliwriter_api_settings_section'
        );

        add_settings_field(
            'intelliwriter_cgpt_model',
            'Model',
            array($this, 'cgpt_model_crender'),
            'intelliwriter_api_cgpt',
            'intelliwriter_api_settings_section'
        );

        register_setting('intelliwriter_settings_group', 'intelliwriter_settings');

        add_settings_section(
            'intelliwriter_settings_section',
            '',
            null,
            'intelliwriter'
        );

        add_settings_field(
            'remove_options_on_uninstall',
            'Remove options on uninstall?',
            array($this, 'remove_options_render'),
            'intelliwriter',
            'intelliwriter_settings_section'
        );
    }

    public function cgpt_api_key_render()
    {
        $options = get_option('intelliwriter_api_settings');
?>
        <input type='text' id="intelliwriter_cgpt_api_key" name='intelliwriter_api_settings[intelliwriter_cgpt_api_key]' value='<?php echo isset($options['intelliwriter_cgpt_api_key']) ? $options['intelliwriter_cgpt_api_key'] : '' ?>'>
    <?php
    }

    public function cgpt_model_crender()
    {
        $options = get_option('intelliwriter_api_settings');

        $chatgpt = new IntelliWriter_CGPT_Api();
        $models = $chatgpt->get_models();
        echo '<select id="intelliwriter_cgpt_model" name="intelliwriter_api_settings[intelliwriter_cgpt_model]" style="width: 400px;">';
        foreach ($models->data as $model) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($model->id),
                isset($options['intelliwriter_cgpt_model']) && $options['intelliwriter_cgpt_model'] === $model->id ? 'selected="selected"' : '',
                esc_html($model->id)
            );
        }
        echo '</select>';
    }

    public function remove_options_render()
    {
        $options = get_option('intelliwriter_settings');
    ?>
        <input type="checkbox" name="intelliwriter_settings[remove_options_on_uninstall]" value="1" <?php checked(1, isset($options['remove_options_on_uninstall']) ? $options['remove_options_on_uninstall'] : 0, true); ?> />
<?php
    }

    public function add_admin_menu()
    {
        add_options_page(
            'IntelliWriter Settings',
            'IntelliWriter',
            'manage_options',
            'intelliwriter',
            array($this, 'create_admin_page'),
            20
        );
    }

    public function create_admin_page()
    {
        include plugin_dir_path(__FILE__) . '/settings-page.php';
    }

    public function enqueue_admin_assets($hook)
    {
        if ($hook != 'settings_page_intelliwriter') {
            return;
        }

        wp_enqueue_style('intelliwriter_settings', plugin_dir_url(__FILE__) . '../../assets/css/admin.css', array(), '1.0.0');
        wp_enqueue_script('intelliwriter_settings', plugin_dir_url(__FILE__) . '../../assets/js/admin.js', array(), '1.0.0', true);
    }
}
