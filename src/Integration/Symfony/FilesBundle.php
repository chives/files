<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\Files\Integration\Symfony;

use FSi\Component\Files\Integration\Symfony\DependencyInjection\CompilerPass\UrlAdapterPass;
use FSi\Component\Files\Integration\Symfony\DependencyInjection\FilesExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use function sprintf;

final class FilesBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new UrlAdapterPass());
        $this->loadExternalBundlesServices($container);
    }

    private function loadExternalBundlesServices(ContainerBuilder $container): void
    {
        $loader = new XmlFileLoader(
            $container,
            new FileLocator(sprintf('%s/Resources/config/services', __DIR__))
        );

        if (true === $container->hasExtension('oneup_flysystem')) {
            $loader->load('flysystem.xml');
        }

        if (true === $container->hasExtension('framework')) {
            $loader->load('symfony.xml');
        }

        if (true === $container->hasExtension('doctrine')) {
            $loader->load('doctrine.xml');
        }
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new FilesExtension();
        }

        return $this->extension;
    }
}
