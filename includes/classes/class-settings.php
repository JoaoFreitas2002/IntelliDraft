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
        register_setting('intellidraft_settings', 'intellidraft_integration');
        register_setting('intellidraft_settings', 'intellidraft_title_prompt');
        register_setting('intellidraft_settings', 'intellidraft_content_prompt');
        register_setting('intellidraft_settings', 'intellidraft_excerpt_prompt');
        register_setting('intellidraft_settings', 'intellidraft_cgpt_api_key');
        register_setting('intellidraft_settings', 'intellidraft_cgpt_model');
        register_setting('intellidraft_settings', 'intellidraft_cgpt_temperature');
        register_setting('intellidraft_settings', 'intellidraft_cgpt_max_tokens');

        if (get_option('intellidraft_title_prompt', '') == '') {

            update_option("intellidraft_title_prompt", 'Create a catchy and concise title for a article about "{{topics}}" in {{language}} language, using a {{writing_style}} style and a {{writing_tone}} tone.');
        }

        if (get_option('intellidraft_content_prompt', '') == '') {
            update_option("intellidraft_content_prompt", 'Write a detailed article about "{{topics}}" with the title "{{title}}" in {{language}} language. Use a {{writing_style}} style and maintain a {{writing_tone}} tone throughout. Include an introduction, at least three main sections with clear headings, and a conclusion.');
        }

        if (get_option('intellidraft_excerpt_prompt', '') == '') {
            update_option("intellidraft_excerpt_prompt", 'Write a brief summary (50-75 words) of a article about {{topics}} titled "{{title}}" in {{language}} language. Use a {{writing_style}} style and a {{writing_tone}} tone to capture the key points engagingly.');
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
        include plugin_dir_path(__FILE__) . '/../views/settings-page.php';
    }

    public function enqueue_admin_assets($hook)
    {
        if ($hook == 'settings_page_intellidraft') {

            wp_enqueue_style('intellidraft_settings', plugin_dir_url(__FILE__) . '../../assets/css/admin.css', null, '1.0.0');
        }
    }
}
