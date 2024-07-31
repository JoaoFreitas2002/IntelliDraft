<?php

/**
 * Plugin Name: IntelliDraft
 * Plugin URI: http://intellidraft.joaoffreitas.com/
 * Description: IntelliDraft is a cutting-edge WordPress plugin that leverages the power of AI to revolutionize your content creation process. Whether you're a blogger, marketer, or site administrator, IntelliDraft helps you produce high-quality, engaging content effortlessly. Transform the way you create, manage, and optimize your WordPress site with AI-driven insights and automation.
 * Version: 1.0.0
 * Author: João Freitas
 * Author URI: http://joaoffreitas.com/
 * Text Domain: intellidraft
 */

defined('ABSPATH') || exit;

define('INTELLIDRAFT_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('INTELLIDRAFT_PLUGIN_URL', plugin_dir_url(__FILE__));
define('INTELLIDRAFT_PLUGIN_VERSION', '1.0.0');

require_once __DIR__ . '/vendor/autoload.php';

require_once plugin_dir_path(__FILE__) . 'includes/class-intellidraft.php';

function intellidraft_init()
{
    $plugin = new IntelliDraft();
    $plugin->run();
}
add_action('plugins_loaded', 'intellidraft_init');
