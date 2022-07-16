<?php

namespace BreakdownHotline;

use BreakdownHotline\DependencyInjection\Container;
use BreakdownHotline\ServiceProvider;
use BreakdownHotline\Update\Update;

defined('ABSPATH') || exit;


final class Init
{
    /**
     * Instance of this class
     *
     * @var self
     * @deprecated 1.0.0
     */
    private static $instance = null;

    /**
     *
     * @var boolean
     */
    private $loaded;

    /**
     * Dependency injection container
     *
     * @var Container
     * @since 1.0.1
     */
    private $container;


    public function __construct()
    {
        $this->container = new Container();
        $this->loaded = false;
    }

    /**
     * Loads required dependencies for plugin to function
     *
     * @return void
     */
    public function load_dependencies()
    {
        if ($this->loaded) {
            return;
        }
        $real_path = realpath(dirname(__FILE__));
        require_once($real_path . '/UltimateMemberExtend/callbacks.php');

        $this->container->configure([
            ServiceProvider\PostcodesAPIServiceProvider::class,
            ServiceProvider\WordpressServiceProvider::class,
            ServiceProvider\UltimateMemberExtendServiceProvider::class,
        ]);


        /**
         * Update instantiation with its dependencies 
         */
        //TODO: stupidly I added it to gitignore but still called it
        Update::getInstance();
        $this->loaded = true;
    }

    /**
     * Get instance of this class
     *
     * @return self
     * @deprecated 1.0.0
     */
    public static function get_instance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance   = new self();
        }
        return self::$instance;
    }
}
