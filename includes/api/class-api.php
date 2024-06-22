<?php

defined('ABSPATH') || exit;

require_once plugin_dir_path(__FILE__) . 'class-cgpt-api.php';

final class IntelliWriter_Api
{

    public function __construct()
    {
        $this->inicialize_cgpt_api();
    }

    public function inicialize_cgpt_api()
    {
        add_action('wp_ajax_chatgpt_generate_content', array($this, 'chatgpt_generate_content'));
    }

    public function chatgpt_generate_content()
    {
        check_ajax_referer('chatgpt_nonce', 'nonce');

        if (!isset($_POST['prompt'])) {
            wp_send_json_error('No input text provided');
        }

        $prompt = sanitize_text_field($_POST['prompt']);

        $chatgpt = new IntelliWriter_CGPT_Api();
        $content = $chatgpt->generate_content($prompt);

        wp_send_json_success($content);
    }
}
