<?php

/**
 * (c) FSi sp. z o.o. <info@fsi.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace FSi\Component\Files\Integration\Symfony\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('fsi_files');

        /** @var ArrayNodeDefinition $root */
        $root = $treeBuilder->getRootNode();

        /** @var NodeBuilder $rootChildren */
        $rootChildren = $root->children();

        /** @var ArrayNodeDefinition $adaptersNode */
        $adaptersNode = $rootChildren->arrayNode('adapters')->beforeNormalization()->castToArray()->end();
        $adaptersNode->useAttributeAsKey('filesystem')->prototype('scalar')->end();
        $adaptersNode->end();

        /** @var NodeBuilder $entitiesChildren */
        $entitiesChildren = $rootChildren->arrayNode('entities')
            ->useAttributeAsKey('class')
            ->arrayPrototype()
            ->children()
        ;
        $entitiesChildren->scalarNode('prefix')->cannotBeEmpty()->end();
        $entitiesChildren->scalarNode('filesystem')->cannotBeEmpty()->end();

        /** @var NodeBuilder $fieldsChildren */
        $fieldsChildren = $entitiesChildren->arrayNode('fields')->arrayPrototype()->children();
        $fieldsChildren->scalarNode('name')->cannotBeEmpty()->end();
        $fieldsChildren->scalarNode('filesystem')->defaultNull()->end();
        $fieldsChildren->scalarNode('pathField')->defaultNull()->end();
        $fieldsChildren->scalarNode('prefix')->defaultNull()->end();
        $fieldsChildren->end();

        $entitiesChildren->end();
        $rootChildren->end();
        $root->end();

        return $treeBuilder;
    }
}
