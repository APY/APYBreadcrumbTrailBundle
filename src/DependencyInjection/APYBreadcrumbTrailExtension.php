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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\HttpKernel\Kernel;

class APYBreadcrumbTrailExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('apy_breadcrumb_trail.template', $config['template']);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $this->deprecateService($container, 'apy_breadcrumb_trail');
        $this->deprecateService($container, 'apy_breadcrumb_trail.annotation.listener');
    }

    private function deprecateService(ContainerBuilder $container, string $id)
    {
        $alias = $container->getAlias($id);
        if (version_compare(Kernel::VERSION, '5.1.0', '>=')) {
            $alias->setDeprecated('APY/BreadcrumbTrailBundle', '1.7', 'The service is deprecated, use "%alias_id%" FQCN as service id instead.');
        } elseif (version_compare(Kernel::VERSION, '4.3.0', '>=')) {
            /* @phpstan-ignore-next-line */
            $alias->setDeprecated();
        }
    }
}
