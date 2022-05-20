<?php

/**
 * Plugin Name: Breakdown Hotline
 * Plugin URI: http://breakdownhotline.co.uk
 * Description: Manage your drivers 
 * Version: 1.0.0
 * Author: Breakdown Hotline
 * Author URI: http://breakdownhotline.co.uk
 * Text Domain: breakdown-hotline
 */


defined('ABSPATH') || exit;

if (!defined('PLUGIN_DIR')) {
    define('PLUGIN_DIR', __DIR__);
}

if (!defined('PLUGIN_UPDATE_CHECK_ENDPOINT')) {
    define(
        'PLUGIN_UPDATE_CHECK_ENDPOINT',
        "https://wp-plugin-update-function.vercel.app/api/breakdown-hotline"
    );
}


require_once 'vendor/autoload.php';



use BreakdownHotline\Init;

if (class_exists(Init::class)) {
    $breakdown_hotline = Init::get_instance();
    $breakdown_hotline->register_controllers();
}


add_action('wp_enqueue_scripts',  "enqueue_test");

function enqueue_test()
{
    wp_enqueue_script("test", plugin_dir_url(__FILE__) . '/test.js', [], false, true);
}

function get_cities()
{
    $post_data = $_POST;
    $data = [];
    if (!empty($post_data)) {
        foreach ($post_data as $test_data_key => $post_data_value) {
            $data[$test_data_key] = $post_data_value;
        }
    }
    $data  = array_merge($data, ["LN" => "London", "GM" => "Greater Manchester"]);
    return $data;
}


function custom_um_state_list_dropdown()
{
    return ["LN" => "London", "GM" => "Greater Manchester"];
}


// UPDATE PLUGIN FUNCTIONALITY

function fetch_data()
{
    $remote_data = wp_remote_get(PLUGIN_UPDATE_CHECK_ENDPOINT, [
        'headers' => [
            'Accept' => 'application/json'
        ]
    ]);

    if (
        is_wp_error($remote_data) ||
        wp_remote_retrieve_response_code($remote_data) !== 200
    ) {

        return null;
    }

    $remote_data = json_decode($remote_data['body']);


    return (object)[
        'id'            => 'breakdown-hotline/breakdown-hotline.php',
        'slug'          => 'breakdown-hotline',
        'plugin'        => 'breakdown-hotline/breakdown-hotline.php',
        'new_version'   => $remote_data->version,  // <-- Important!
        'url'           => 'https://breakdownhotline.co.uk', //TODO: add url
        'package'       => $remote_data->package,  // <-- Important!
        'icons'         => [],
        'banners'       => [],
        'banners_rtl'   => [],
        'tested'        => '',
        'requires_php'  => '',
        'compatibility' => new \stdClass(),
    ];
}


require_once ABSPATH . 'wp-admin/includes/plugin.php';


add_filter('site_transient_update_plugins', 'breakdown_hotline_site_transient_update_plugins');
function breakdown_hotline_site_transient_update_plugins($transient)
{
    $current_plugin_data = get_plugin_data(__FILE__);
    $plugin_data = fetch_data();

    if (version_compare(
        $plugin_data->new_version,
        $current_plugin_data['Version'],
        ">"
    )) {
        $transient->response['breakdown-hotline/breakdown-hotline.php'] = $plugin_data;
    } else {
        $transient->no_update['breakdown-hotline/breakdown-hotline.php'] = $plugin_data;
    }

    return $transient;
}
