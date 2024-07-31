<?php

defined('ABSPATH') || exit;

final class IntelliDraft
{

    public function __construct()
    {
        $this->includes();
    }

    private function includes()
    {
        require_once plugin_dir_path(__FILE__) . 'admin/class-admin-menu.php';
        require_once plugin_dir_path(__FILE__) . 'post-editor/class-editor-display.php';
        require_once plugin_dir_path(__FILE__) . 'api/class-api.php';
    }

    public function run()
    {
        new IntelliDraft_Settings();
        new IntelliDraft_Post_Editor_Display();
        new IntelliDraft_CGPT_Api();
    }
}
