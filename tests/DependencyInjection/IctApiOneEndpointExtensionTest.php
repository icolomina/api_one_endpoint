<?php

namespace Ict\ApiOneEndpoint\tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Reference;

class IctApiOneEndpointExtensionTest extends TestCase
{
    /**
     * @throws \Exception
     */
    public function testContainerServices()
    {
        $container = $this->getContainer();
        $loader = new XmlFileLoader($container, new FileLocator( __DIR__ .'/../../Resources/config/'));
        $loader->load('services.xml');

        $definitions = array_filter($container->getDefinitions(), fn(Definition $definition) => str_starts_with($definition->getClass(), 'Ict'));
        foreach ($definitions as $definition) {
            $this->assertTrue(class_exists($definition->getClass()));
            $refClass = new \ReflectionClass($definition->getClass());
            if($refClass->getConstructor()){
                $this->assertTrue(count($refClass->getConstructor()->getParameters()) === count($definition->getArguments()));
            }

            foreach ($definition->getArguments() as $argument) {
                if($argument instanceof Reference && str_starts_with($argument, 'Ict')) {
                    $this->assertTrue(class_exists($argument));
                }
            }
        }
    }

    private function getContainer(): ContainerBuilder
    {
        return new ContainerBuilder(new ParameterBag([
            'kernel.debug'       => false,
            'kernel.bundles'     => [],
            'kernel.cache_dir'   => sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir'    => __DIR__ . '/../../',
        ]));
    }
}
