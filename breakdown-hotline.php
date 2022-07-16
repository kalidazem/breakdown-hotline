<?php

/**
 * Plugin Name: Breakdown Hotline
 * Plugin URI: http://breakdownhotline.co.uk
 * Description: Manage your drivers 
 * Version: 1.0.2
 * Author: Breakdown Hotline
 * Author URI: http://breakdownhotline.co.uk
 * Text Domain: breakdown-hotline
 */


defined('ABSPATH') || exit;

if (!defined('PLUGIN_DIR')) {
    define('PLUGIN_DIR', __DIR__);
}

//To use just append the desired postcode to it.
if (!defined('BOROUGH_VIA_POST_LOOKUP_ENDPOINT')) {
    define('BOROUGH_VIA_POST_LOOKUP_ENDPOINT', 'https://api.postcodes.io/postcodes/');
}

//update expiration
if (!defined('BREAKDOWN_HOTLINE_TRANSIENT_EXPIRATION')) {
    define('BREAKDOWN_HOTLINE_TRANSIENT_EXPIRATION', 43200); // 12 hrs
}

//transient check
if (!defined('BREAKDOWN_HOTLINE_CHECK_TRANSIENT')) {
    define('BREAKDOWN_HOTLINE_CHECK_TRANSIENT', 'wp_update_check_breakdown_hotline');
}

//update check endpoint
if (!defined('PLUGIN_UPDATE_CHECK_ENDPOINT')) {
    define(
        'PLUGIN_UPDATE_CHECK_ENDPOINT',
        "https://wp-plugin-update-function.vercel.app/api/breakdown-hotline"
    );
}

require_once 'vendor/autoload.php';

use BreakdownHotline\Init;

//Get plugin's current data
require_once ABSPATH . 'wp-admin/includes/plugin.php';

$current_plugin_data = get_plugin_data(__FILE__);
define('BREAKDOWN_PLUGIN_VERSION', $current_plugin_data['Version']);


global $breakdown_hotline;
$breakdown_hotline = new BreakdownHotline\Init();
add_action('after_setup_theme', array($breakdown_hotline, 'load_dependencies'));
