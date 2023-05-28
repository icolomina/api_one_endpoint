<?php

namespace Ict\ApiOneEndpoint\DependencyInjection;

use Ict\ApiOneEndpoint\Contract\Operation\OperationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class IctApiOneEndpointExtension extends Extension
{

    /**
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader($container, new FileLocator(dirname(__DIR__).'/Resources/config'));
        $loader->load('services.xml');

        $configuration = new IctApiOneEndpointConfiguration();
        $this->processConfiguration($configuration, $configs);

        $container->registerForAutoconfiguration(OperationInterface::class)->addTag('ict.api_one_endpoint.operation');

        if(!$container->hasAlias('Symfony\Component\Serializer\SerializerInterface')) {
            throw new \RuntimeException('This bundle requires Symfony serializer. Install it using "composer require symfony/serializer-pack" ');
        }

        if(!$container->hasAlias('Symfony\Component\Validator\Validator\ValidatorInterface')) {
            throw new \RuntimeException('This bundle requires Symfony validator. Install it using "composer require symfony/validator" ');
        }

        if(!$container->hasAlias('Symfony\Component\Messenger\MessageBusInterface')) {
            throw new \RuntimeException('This bundle requires Symfony messenger. Install it using "composer require symfony/messenger" ');
        }
    }
}
