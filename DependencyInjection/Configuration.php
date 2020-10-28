<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('quick_admin_generator');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('title')
                    ->defaultValue('QAG Bundle')
                    ->info('The title of the admin, displayed on the top bar.')
                ->end()
                ->arrayNode('menu')
                    ->children()
                        ->enumNode('theme')
                            ->values(['vertical', 'horizontal'])
                            ->defaultValue('horizontal')
                            ->info('How the menu is displayed, either "horizontal" or "vertical"')
                        ->end()
                        ->variableNode('items')->end()
                    ->end()
                    ->addDefaultsIfNotSet()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}