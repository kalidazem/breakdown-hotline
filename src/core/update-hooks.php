<?php

namespace BreakdownHotline\Core;

defined('ABSPATH') || exit;
//update expiration
define('BREAKDOWN_HOTLINE_TRANSIENT_EXPIRATION', 43200); // 12 hrs

//transient check
define('BREAKDOWN_HOTLINE_CHECK_TRANSIENT', 'wp_update_check_breakdown_hotline');

//update check endpoint
define(
    'PLUGIN_UPDATE_CHECK_ENDPOINT',
    "https://wp-plugin-update-function.vercel.app/api/breakdown-hotline"
);

add_filter('site_transient_update_plugins', '\BreakdownHotline\Core\breakdown_hotline_site_transient_update_plugins');
add_filter('transient_update_plugins', '\BreakdownHotline\Core\breakdown_hotline_site_transient_update_plugins');


/**
 * Fetch the latest plugin version
 * 
 * @since 1.0.0
 * @return object
 */
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



function breakdown_hotline_site_transient_update_plugins($transient)
{
    $check_plugin_transient = get_transient(BREAKDOWN_HOTLINE_CHECK_TRANSIENT);


    $plugin_data = $check_plugin_transient ?: fetch_data();

    if (!$plugin_data) {
        return $transient;
    }

    if (!$check_plugin_transient) {
        set_transient(BREAKDOWN_HOTLINE_CHECK_TRANSIENT, $plugin_data, BREAKDOWN_HOTLINE_TRANSIENT_EXPIRATION);
    }

    if (version_compare(
        $plugin_data->new_version,
        BREAKDOWN_PLUGIN_VERSION,
        ">"
    )) {
        $transient->response['breakdown-hotline/breakdown-hotline.php'] = $plugin_data;
    } else {
        $transient->no_update['breakdown-hotline/breakdown-hotline.php'] = $plugin_data;
    }

    return $transient;
}
