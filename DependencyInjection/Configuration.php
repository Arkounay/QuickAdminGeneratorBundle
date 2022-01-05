<?php


namespace Arkounay\Bundle\QuickAdminGeneratorBundle\DependencyInjection;


use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('quick_admin_generator');

        $treeBuilder->getRootNode()
            ->children()
                ->scalarNode('title')
                    ->defaultValue('QAG Bundle')
                    ->info('The title of the admin, displayed on the top bar.')
                ->end()
                ->scalarNode('global_search')
                    ->defaultFalse()
                    ->info('Allow global search')
                ->end()
                ->scalarNode('dashboard_route_redirection')
                    ->defaultNull()
                    ->info('When specified, will disable the dashboard menu item and redirect to given route')
                ->end()
                ->arrayNode('theme')
                    ->children()
                        ->enumNode('default')
                            ->values(['light', 'dark'])
                            ->defaultValue('light')
                            ->info('Default theme color')
                        ->end()
                        ->booleanNode('allow_switch')
                            ->defaultTrue()
                            ->info('Allows user to switch to dark mode or light mode')
                        ->end()
                    ->end()
                    ->addDefaultsIfNotSet()
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