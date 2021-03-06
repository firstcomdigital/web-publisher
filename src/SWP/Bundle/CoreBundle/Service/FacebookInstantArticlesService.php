<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Core Bundle.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\CoreBundle\Service;

use Facebook\InstantArticles\Client\Client;
use Facebook\InstantArticles\Client\InstantArticleStatus;
use Facebook\InstantArticles\Elements\InstantArticle;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesArticle;
use SWP\Bundle\CoreBundle\Model\FacebookInstantArticlesFeedInterface;
use SWP\Bundle\CoreBundle\Repository\FacebookInstantArticlesArticleRepositoryInterface;
use SWP\Bundle\FacebookInstantArticlesBundle\Manager\FacebookInstantArticlesManagerInterface;
use SWP\Bundle\StorageBundle\Doctrine\ORM\EntityRepository;
use SWP\Component\Storage\Factory\FactoryInterface;

class FacebookInstantArticlesService implements FacebookInstantArticlesServiceInterface
{
    /**
     * @var FacebookInstantArticlesManagerInterface
     */
    protected $facebookInstantArticlesManager;

    /**
     * @var FactoryInterface
     */
    protected $instantArticlesArticleFactory;

    /**
     * @var EntityRepository
     */
    protected $facebookInstantArticlesArticleRepository;

    /**
     * FacebookInstantArticlesService constructor.
     *
     * @param FacebookInstantArticlesManagerInterface           $facebookInstantArticlesManager
     * @param FactoryInterface                                  $instantArticlesArticleFactory
     * @param FacebookInstantArticlesArticleRepositoryInterface $facebookInstantArticlesArticleRepository
     */
    public function __construct(
        FacebookInstantArticlesManagerInterface $facebookInstantArticlesManager,
        FactoryInterface $instantArticlesArticleFactory,
        FacebookInstantArticlesArticleRepositoryInterface $facebookInstantArticlesArticleRepository
    ) {
        $this->facebookInstantArticlesManager = $facebookInstantArticlesManager;
        $this->instantArticlesArticleFactory = $instantArticlesArticleFactory;
        $this->facebookInstantArticlesArticleRepository = $facebookInstantArticlesArticleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function pushInstantArticle(
        FacebookInstantArticlesFeedInterface $feed,
        InstantArticle $instantArticle,
        ArticleInterface $article
    ) {
        $submissionId = $this->getClient($feed)->importArticle($instantArticle, true);

        /** @var FacebookInstantArticlesArticle $instantArticleEntity */
        $instantArticleEntity = $this->facebookInstantArticlesArticleRepository->findInFeed($feed, $article);

        if (null === $instantArticleEntity) {
            $instantArticleEntity = $this->instantArticlesArticleFactory->create();
            $instantArticleEntity->setArticle($article);
            $instantArticleEntity->setFeed($feed);
            $instantArticleEntity->setStatus('new');
        }

        $instantArticleEntity->setSubmissionId((string) $submissionId);
        $this->facebookInstantArticlesArticleRepository->add($instantArticleEntity);
    }

    /**
     * {@inheritdoc}
     */
    public function updateSubmissionStatus(string $submissionId)
    {
        /** @var FacebookInstantArticlesArticle $instantArticleEntity */
        $instantArticle = $this->facebookInstantArticlesArticleRepository->findSubmission($submissionId);

        if (null === $instantArticle) {
            throw new \Exception('Instant Article with provided submission ID does not exists.');
        }

        $submissionStatus = $this->getClient($instantArticle->getFeed())->getSubmissionStatus($submissionId);
        $instantArticle->setStatus($submissionStatus->getStatus());
        $instantArticle->setErrors($this->getSubmissionErrors($submissionStatus));
        $this->facebookInstantArticlesArticleRepository->flush();

        return $instantArticle;
    }

    /**
     * @param $feed
     *
     * @return Client
     *
     * @throws \Exception
     */
    protected function getClient($feed)
    {
        $facebookPage = $feed->getFacebookPage();

        if (null === $facebookPage->getApplication()) {
            throw new \Exception('Page is not authorized to publish Instant Articles', 403);
        }

        $facebook = $this->facebookInstantArticlesManager->getFacebookManager()->createForApp($facebookPage->getApplication());
        $facebook->setDefaultAccessToken($facebookPage->getAccessToken());

        return new Client($facebook, $facebookPage->getPageId(), $feed->isDevelopment());
    }

    /**
     * @param InstantArticleStatus $submissionStatus
     *
     * @return array
     */
    private function getSubmissionErrors(InstantArticleStatus $submissionStatus): array
    {
        $errors = [];
        foreach ($submissionStatus->getMessages() as $serverMessage) {
            $errors[] = [$serverMessage->getLevel() => $serverMessage->getMessage()];
        }

        return $errors;
    }
}
