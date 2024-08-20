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

        $prompt = "Please generate a blog post based on the following title and topics, Title: $title ; Topics: $topics . Use the following tone and language, Tone: $tone ; Language: $language . Return the content in standard HTML format with appropriate tags without the need of head tag. Dont't use images/videos, only text.";

        $chatgpt = new IntelliDraft_CGPT_Api();
        $content = $chatgpt->generate_content($prompt);

        if (isset($content->choices[0]->message->content)) {
            $content = trim($content->choices[0]->message->content);
            wp_send_json_success(array('body' => $content));
        } else {
            wp_send_json_error(array('message' => 'Invalid response from API'));
        }
    }
}
