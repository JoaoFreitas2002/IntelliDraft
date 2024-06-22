<?php

defined('ABSPATH') || exit;

class IntelliWriter
{

    public function __construct()
    {
        $this->includes();
    }

    private function includes()
    {
        require_once plugin_dir_path(__FILE__) . 'admin/class-admin-menu.php';
        require_once plugin_dir_path(__FILE__) . 'frontend/class-frontend-display.php';
        require_once plugin_dir_path(__FILE__) . 'api/class-api.php';
    }

    public function run()
    {
        new IntelliWriter_Settings();
        new IntelliWriter_Frontend_Display();
        new IntelliWriter_Api();
    }
}
