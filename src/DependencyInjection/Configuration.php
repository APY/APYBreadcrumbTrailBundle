<?php
/*
 * This file is part of the APYBreadcrumbTrailBundle.
 *
 * (c) Abhoryo <abhoryo@free.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace APY\BreadcrumbTrailBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('apy_breadcrumb_trail');
        // BC layer for symfony/config < 4.2
        $rootNode = method_exists($treeBuilder, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('apy_breadcrumb_trail');

        $rootNode
            ->children()
                ->scalarNode('template')
                    ->defaultValue('@APYBreadcrumbTrail/breadcrumbtrail.html.twig')
                ->end()
             ->end()
        ;

        return $treeBuilder;
    }
}
