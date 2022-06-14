<?php

namespace BreakdownHotline\ServiceProvider;

use BreakdownHotline\API\PostcodesAPI;
use BreakdownHotline\DependencyInjection\ServiceProviderInterface;
use BreakdownHotline\DependencyInjection\Container;

class PostcodesAPIServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['postcodes_api'] = $container->service(function (Container $container) {
            return new PostcodesAPI($container['wordpress.http_transport']);
        });
    }
}
