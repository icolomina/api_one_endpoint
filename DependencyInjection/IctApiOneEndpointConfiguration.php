<?php

namespace Ict\ApiOneEndpoint\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class IctApiOneEndpointConfiguration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $tb = new TreeBuilder('ict_api_one_endpoint');
        return $tb;
    }
}
