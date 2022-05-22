<?php

namespace BreakdownHotline;

defined('ABSPATH') || exit;

// if (!class_exists('Init')) :
final class Init
{
    private static $instance = null;
    private function __construct()
    {
    }

    private static function get_controllers()
    {
        return [
            Ultimate_Member_Extend_Controller::class,

        ];
    }

    public static function load_dependencies()
    {
        $real_path = realpath(dirname(__FILE__));
        require_once($real_path . '/ultimate-member-extend/init.php');
        require_once($real_path . '/core/init.php');
    }

    public static function get_instance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance   = new self();
        }
        return self::$instance;
    }
}
