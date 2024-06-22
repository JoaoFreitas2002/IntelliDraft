<?php

defined('ABSPATH') || exit;

class IntelliWriter_Frontend_Display
{

    public function __construct()
    {
        add_action('enqueue_block_editor_assets', array($this, 'enqueue_extended_blocks_assets'));
    }

    public function enqueue_extended_blocks_assets()
    {
        wp_enqueue_script('intelliwriter-extended-blocks', plugin_dir_url(__FILE__) . '../../assets/js/blocks.js', array('wp-blocks', 'wp-hooks', 'wp-element', 'wp-compose', 'wp-editor', 'wp-components'), '1.0.0', true);

        wp_localize_script('intelliwriter-extended-blocks', 'extendedHeadingBlock', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('chatgpt_nonce'),
        ]);
    }
}
