<?php 

namespace BreakdownHotline\ServiceProvider;

use BreakdownHotline\DependencyInjection\Container;
use BreakdownHotline\DependencyInjection\ServiceProviderInterface;

class WordpressServiceProvider implements ServiceProviderInterface{

    public function register(Container $container){
        
        $container['wordpress.http_transport']= _wp_http_get_object();
    }
}