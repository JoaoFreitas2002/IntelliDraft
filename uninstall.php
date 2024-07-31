<?php

defined('ABSPATH') || exit;
defined('WP_UNINSTALL_PLUGIN') || exit;

delete_option('intellidraft_api_settings');
delete_option('intellidraft_settings');
