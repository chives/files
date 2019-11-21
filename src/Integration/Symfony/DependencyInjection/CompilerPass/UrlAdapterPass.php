<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\Files\Integration\Symfony\DependencyInjection\CompilerPass;

use Assert\Assertion;
use FSi\Component\Files\FileUrlResolver;
use FSi\Component\Files\UrlAdapter;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use function array_reduce;
use function sprintf;

final class UrlAdapterPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->getDefinition(FileUrlResolver::class)->replaceArgument(
            '$adapters',
            array_reduce(
                $container->getExtensionConfig('fsi_files')[0]['adapters'],
                function (array $accumulator, array $configuration) use ($container): array {
                    $filesystem = $configuration['filesystem'];
                    Assertion::keyNotExists(
                        $accumulator,
                        $filesystem,
                        "Duplicate entry for filesystem \"{$filesystem}\"."
                    );

                    $definition = $container->getDefinition($configuration['service']);
                    $this->validateAdapterServiceDefinition(
                        $definition,
                        $accumulator,
                        $configuration['service'],
                        $filesystem
                    );

                    $accumulator[$filesystem] = $definition;
                    return $accumulator;
                },
                []
            )
        );
    }

    private function validateAdapterServiceDefinition(
        Definition $definition,
        array $usedAdapters,
        string $id,
        string $filesystem
    ): void {
        Assertion::notNull($definition->getClass(), "Service \"{$id}\" has no class.");
        Assertion::subclassOf(
            $definition->getClass(),
            UrlAdapter::class,
            sprintf(
                'Service "%s" for filesystem "%s" does not implement "%s".',
                $id,
                $filesystem,
                UrlAdapter::class
            )
        );

        Assertion::notInArray(
            $definition,
            $usedAdapters,
            "Service \"{$id}\" is used more than one time."
        );
    }
}