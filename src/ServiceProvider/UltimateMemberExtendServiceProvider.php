<?php

namespace BreakdownHotline\ServiceProvider;

use BreakdownHotline\DependencyInjection\Container;
use BreakdownHotline\DependencyInjection\ServiceProviderInterface;
use BreakdownHotline\UltimateMemberExtend\UltimateMemberExtend;

class UltimateMemberExtendServiceProvider implements ServiceProviderInterface
{

    public function register(Container $container)
    {
        $container['ultimate_member_extend'] = $container->service(function (Container $container) {
            return new UltimateMemberExtend($container['postcodes_api']);
        });
    }
}
