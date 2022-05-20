<?php

namespace BreakdownHotline;

use BreakdownHotline\Controllers\Ultimate_Member_Extend_Controller;

// if (!class_exists('Init')) :
final class Init
{
    private static $instance = null;
    // private static $services = 
    private function __construct()
    {
    }

    private static function get_controllers()
    {
        return [
            Ultimate_Member_Extend_Controller::class,

        ];
    }

    public static function register_controllers()
    {
        $controllers = self::get_controllers();

        foreach ($controllers as $controller_class) {
            $controller = self::init($controller_class);

            if (method_exists($controller, 'register')) {
                $controller->register();
            }
        }
    }

    private static function init($class)
    {
        $controller_class =  $class::get_instance();
        return $controller_class;
    }

    public static function get_instance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance   = new self();
        }
        return self::$instance;
    }
}

// endif;
