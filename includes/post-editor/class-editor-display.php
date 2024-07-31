<?php

defined('ABSPATH') || exit;

class IntelliDraft_Post_Editor_Display
{

    public function __construct()
    {
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_extended_blocks_assets'));

        add_action('wp_ajax_intellidraft_generate_content', array($this, 'ajax_generate_content'));

        add_action('enqueue_block_editor_assets', array($this, 'enqueue_editor_assets'));
    }

    public function enqueue_extended_blocks_assets()
    {

        wp_enqueue_script('intellidraft-sidebar', plugin_dir_url(__FILE__) . '../../assets/js/sidebar.js', array('wp-plugins', 'wp-edit-post', 'wp-element', 'wp-components'), '1.0.1', true);

        wp_localize_script('intellidraft-sidebar', 'intellidraft', [
            'iconSvgUrl' => plugin_dir_url(__FILE__) . '../../assets/imgs/icon.svg',
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('chatgpt_nonce'),
        ]);
    }

    function enqueue_editor_assets()
    {
        wp_enqueue_style(
            'intellidraft-editor-style',
            plugin_dir_url(__FILE__) . '../../assets/css/sidebar.css',
            array(),
            '1.0.1'
        );
    }

    public function ajax_generate_content()
    {
        check_ajax_referer('chatgpt_nonce', 'nonce');

        if (!isset($_POST['title']) && !isset($_POST['topics']) && !isset($_POST['tone']) && !isset($_POST['language'])) {
            wp_send_json_error('No input text provided.');
        }

        $title = sanitize_text_field($_POST['title']);
        $topics = sanitize_text_field($_POST['topics']);
        $tone = sanitize_text_field($_POST['tone']);
        $language = sanitize_text_field($_POST['language']);

        $prompt = "Please generate a blog post based on the following content:\n\n" .
            "Title: $title\n" .
            "Topics: $topics\n" .
            "Tone: $tone\n\n" .
            "Language: $language\n\n" .
            "Return the response in the following format:\n" .
            "{ \"Title\": \"<title>\", \"Body\": \"<body>\" }\n\n" .
            "Make sure to follow this format exactly.";

        $chatgpt = new IntelliDraft_CGPT_Api();
        $content = $chatgpt->generate_content($prompt);

        if (isset($content->choices[0]->message->content)) {
            $content = trim($content->choices[0]->message->content);
            $parsed_content = json_decode($content, true);

            if (json_last_error() === JSON_ERROR_NONE && isset($parsed_content['Title']) && isset($parsed_content['Body'])) {
                $title = sanitize_text_field($parsed_content['Title']);
                $body = sanitize_textarea_field($parsed_content['Body']);
                wp_send_json_success(array('title' => $title, 'body' => $body));
            } else {
                wp_send_json_error(array('message' => 'Response format is invalid or not in JSON format'));
            }
        } else {
            wp_send_json_error(array('message' => 'Invalid response from API'));
        }
    }
}
