<?php

defined('ABSPATH') || exit;
defined('WP_UNINSTALL_PLUGIN') || exit;

$options = get_option('intelliwriter_settings');

if (isset($options['remove_options_on_uninstall']) && $options['remove_options_on_uninstall']) {
    delete_option('intelliwriter_api_settings');
}

delete_option('intelliwriter_settings');
