<?php

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2016 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\DependencyInjection;

use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesArticle;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesArticleInterface;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeed;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeedInterface;
use SWP\Bundle\CoreBundle\Repository\ApiKeyRepository;
use SWP\Bundle\CoreBundle\Factory\ApiKeyFactory;
use SWP\Bundle\CoreBundle\Model\ApiKey;
use SWP\Bundle\CoreBundle\Model\User;
use SWP\Bundle\CoreBundle\Repository\FacebookInstantArticlesArticleRepository;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Storage\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $treeBuilder->root('swp_core')
            ->children()
                ->arrayNode('device_listener')
                    ->canBeEnabled()
                    ->info('Enable device detection in templates loader')
                ->end()
                ->arrayNode('persistence')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('orm')
                            ->addDefaultsIfNotSet()
                            ->canBeEnabled()
                            ->children()
                                ->arrayNode('classes')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->arrayNode('user')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->cannotBeEmpty()->defaultValue(User::class)->end()
                                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('api_key')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->cannotBeEmpty()->defaultValue(ApiKey::class)->end()
                                            ->scalarNode('repository')->defaultValue(ApiKeyRepository::class)->end()
                                            ->scalarNode('factory')->defaultValue(ApiKeyFactory::class)->end()
                                            ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('facebook_instant_articles_feed')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->cannotBeEmpty()->defaultValue(FacebookInstantArticlesFeed::class)->end()
                                            ->scalarNode('interface')->defaultValue(FacebookInstantArticlesFeedInterface::class)->end()
                                            ->scalarNode('repository')->defaultValue(EntityRepository::class)->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                        ->end()
                                    ->end()
                                    ->arrayNode('facebook_instant_articles_article')
                                        ->addDefaultsIfNotSet()
                                        ->children()
                                            ->scalarNode('model')->cannotBeEmpty()->defaultValue(FacebookInstantArticlesArticle::class)->end()
                                            ->scalarNode('interface')->defaultValue(FacebookInstantArticlesArticleInterface::class)->end()
                                            ->scalarNode('repository')->defaultValue(FacebookInstantArticlesArticleRepository::class)->end()
                                            ->scalarNode('factory')->defaultValue(Factory::class)->end()
                                            ->scalarNode('object_manager_name')->defaultValue(null)->end()
                                        ->end()
                                    ->end()
                                ->end() // classes
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
