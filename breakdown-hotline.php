<?php

/**
 * Plugin Name: Breakdown Hotline
 * Plugin URI: http://breakdownhotline.co.uk
 * Description: Manage your drivers 
 * Version: 1.0.1
 * Author: Breakdown Hotline
 * Author URI: http://breakdownhotline.co.uk
 * Text Domain: breakdown-hotline
 */


defined('ABSPATH') || exit;

if (!defined('PLUGIN_DIR')) {
    define('PLUGIN_DIR', __DIR__);
}


require_once 'vendor/autoload.php';

use BreakdownHotline\Init;

//Get plugin's current data
require_once ABSPATH . 'wp-admin/includes/plugin.php';
$current_plugin_data = get_plugin_data(__FILE__);
define('BREAKDOWN_PLUGIN_VERSION', $current_plugin_data['Version']);
if (class_exists(Init::class)) {
    $breakdown_hotline = Init::get_instance();
    $breakdown_hotline->load_dependencies();
}
