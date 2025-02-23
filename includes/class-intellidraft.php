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
        require_once plugin_dir_path(__FILE__) . 'classes/class-settings.php';
        require_once plugin_dir_path(__FILE__) . 'classes/class-chatGPT-api.php';
        require_once plugin_dir_path(__FILE__) . 'classes/class-generate-post.php';
    }

    public function run()
    {
        new IntelliDraft_Settings();
        new IntelliDraft_CGPT_Api();
        new IntelliDraft_Generate_Post();
    }
}
