<?php

namespace BreakdownHotline;

use BreakdownHotline\API\PostcodesAPI;
use BreakdownHotline\UltimateMemberExtend\UltimateMemberExtend;
use BreakdownHotline\Update\Update;

defined('ABSPATH') || exit;


final class Init
{
    private static $instance = null;
    private function __construct()
    {
    }

    /**
     * Loads required dependencies for plugin to function
     *
     * @return void
     */
    public static function load_dependencies()
    {

        $real_path = realpath(dirname(__FILE__));
        require_once($real_path . '/UltimateMemberExtend/callbacks.php');

        //TODO: make dependency injection automatic
        /**
         * UltimateMemberExtend instantiation with its dependencies 
         */
        $http_transport = new \WP_Http();
        $postcodes_client_api = new PostcodesAPI($http_transport);
        new UltimateMemberExtend($postcodes_client_api);


        /**
         * Update instantiation with its dependencies 
         */
        Update::getInstance();
    }

    public static function get_instance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance   = new self();
        }
        return self::$instance;
    }
}
