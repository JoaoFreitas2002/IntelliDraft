<?php

/**
 * Plugin Name: IntelliWriter
 * Plugin URI: http://intelliwriter.joaoffreitas.com/
 * Description: IntelliWriter is your AI-powered writing assistant for WordPress. Seamlessly integrated into the Block Editor, IntelliWriter harnesses the power of OpenAI's ChatGPT to effortlessly generate engaging content based on your prompts. Say goodbye to writer's block and tedious brainstorming sessions – with IntelliWriter, creating captivating content is as easy as typing a few words and clicking a button. Whether you're a blogger, marketer, or content creator, let IntelliWriter elevate your writing experience to new heights.
 * Version: 1.0.0
 * Author: João Freitas
 * Author URI: http://joaoffreitas.com/
 * Text Domain: intelliwriter
 */

defined('ABSPATH') || exit;

require_once __DIR__ . '/vendor/autoload.php';

require_once plugin_dir_path(__FILE__) . 'includes/class-intelliwriter.php';

function intelliwriter_init()
{
    $plugin = new IntelliWriter();
    $plugin->run();
}
add_action('plugins_loaded', 'intelliwriter_init');
