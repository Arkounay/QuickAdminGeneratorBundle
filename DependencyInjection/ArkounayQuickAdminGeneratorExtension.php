<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\DependencyInjection;


use Arkounay\Bundle\QuickAdminGeneratorBundle\Controller\Crud;
use Arkounay\Bundle\QuickAdminGeneratorBundle\Extension\FieldService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

class ArkounayQuickAdminGeneratorExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('quick_admin_generator', $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $container->registerForAutoconfiguration(Crud::class)
            ->addTag('quickadmin.crud')
            ->addTag('controller.service_arguments')
            ->setBindings(['$entity' => expr("service('Arkounay\\\Bundle\\\QuickAdminGeneratorBundle\\\Crud\\\CrudListener').guessEntity()")])
            ->addMethodCall('setInternalDependencies', [ new Reference(EntityManagerInterface::class), new Reference(FieldService::class), new Reference(RequestStack::class), new Reference(EventDispatcherInterface::class), new Reference(TranslatorInterface::class)]);

    }

}