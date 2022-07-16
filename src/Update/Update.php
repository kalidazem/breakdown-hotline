<?php

namespace BreakdownHotline\Update;


class Update
{
    protected static $instance = null;


    private function __construct()
    {
        $this->setupFilters();
        $this->setupActions();
    }

    public static function getInstance()
    {
        if (!is_null(self::$instance)) {
            return self::$instance;
        }

        self::$instance = new self();
        return self::$instance;
    }


    function setupFilters()
    {
        add_filter('site_transient_update_plugins', [$this, 'siteTransientUpdatePlugins']);
        add_filter('transient_update_plugins', [$this, 'siteTransientUpdatePlugins']);
    }

    function setupActions()
    {
    }



    function siteTransientUpdatePlugins($transient)
    {
        $checkPluginTransient = get_transient(BREAKDOWN_HOTLINE_CHECK_TRANSIENT);


        //if $CheckPluginTransient is true, then $pluginData = true, $fetchData otherwise.
        $pluginData = $checkPluginTransient ?: $this->fetchLatestPluginVersion();

        if (!$pluginData) {
            return $transient;
        }

        if (!$checkPluginTransient) {
            set_transient(BREAKDOWN_HOTLINE_CHECK_TRANSIENT, $pluginData, BREAKDOWN_HOTLINE_TRANSIENT_EXPIRATION);
        }

        if (version_compare(
            $pluginData->new_version,
            BREAKDOWN_PLUGIN_VERSION,
            ">"
        )) {
            $transient->response['breakdown-hotline/breakdown-hotline.php'] = $pluginData;
        } else {
            $transient->no_update['breakdown-hotline/breakdown-hotline.php'] = $pluginData;
        }

        return $transient;
    }


    function fetchLatestPluginVersion()
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
            'url'           => 'https://breakdownhotline.co.uk', //TODO: add url from variable 
            'package'       => $remote_data->package,  // <-- Important!
            'icons'         => [],
            'banners'       => [],
            'banners_rtl'   => [],
            'tested'        => '',
            'requires_php'  => '',
            'compatibility' => new \stdClass(),
        ];
    }
}
