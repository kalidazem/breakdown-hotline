<?php

namespace BreakdownHotline;

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
        // require_once($real_path . '/core/init.php');
        UltimateMemberExtend::getInstance();
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
