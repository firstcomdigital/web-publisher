<?php

declare(strict_types=1);

/*
 * This file is part of the Superdesk Web Publisher Content List Component.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Component\ContentList\Model;

use SWP\Component\Common\Model\EnableableTrait;
use SWP\Component\Common\Model\SoftDeletableTrait;
use SWP\Component\Common\Model\TimestampableTrait;

class ContentListItem implements ContentListItemInterface
{
    use TimestampableTrait, SoftDeletableTrait, EnableableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var ContentListInterface
     */
    protected $contentList;

    /**
     * @var ListContentInterface
     */
    protected $content;

    /**
     * @var int
     */
    protected $position;

    /**
     * @var bool
     */
    protected $sticky = false;

    /**
     * ContentListItem constructor.
     */
    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getContentList(): ContentListInterface
    {
        return $this->contentList;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentList(ContentListInterface $contentList = null)
    {
        $this->contentList = $contentList;
    }

    /**
     * {@inheritdoc}
     */
    public function getContent(): ListContentInterface
    {
        return $this->content;
    }

    /**
     * {@inheritdoc}
     */
    public function setContent(ListContentInterface $content)
    {
        $this->content = $content;
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition(int $position)
    {
        $this->position = $position;
    }

    /**
     * {@inheritdoc}
     */
    public function isSticky(): bool
    {
        return $this->sticky;
    }

    /**
     * {@inheritdoc}
     */
    public function setSticky(bool $sticky)
    {
        $this->sticky = $sticky;
    }
}
