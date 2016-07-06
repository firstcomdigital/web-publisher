<?php

namespace spec\SWP\Bundle\ContentBundle\Factory;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use SWP\Bundle\ContentBundle\Doctrine\ODM\PHPCR\Article;
use SWP\Bundle\ContentBundle\Factory\ArticleFactory;
use SWP\Bundle\ContentBundle\Model\ArticleInterface;
use SWP\Bundle\ContentBundle\Model\RouteInterface;
use SWP\Bundle\ContentBundle\Provider\ArticleProviderInterface;
use SWP\Bundle\ContentBundle\Provider\RouteProviderInterface;
use SWP\Component\Bridge\Model\Item;
use SWP\Component\Bridge\Model\PackageInterface;
use SWP\Component\Storage\Factory\FactoryInterface;

/**
 * @mixin ArticleFactory
 */
class ArticleFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, RouteProviderInterface $routeProvider, ArticleProviderInterface $articleProvider)
    {
        $this->beConstructedWith($factory, $routeProvider, $articleProvider, 'test');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ArticleFactory::class);
    }

    function it_creates_new_article_object(FactoryInterface $factory, ArticleInterface $article)
    {
        $factory->create()->willReturn($article);

        $this->create()->shouldReturn($article);
    }

    function it_creates_article_from_package(
        FactoryInterface $factory,
        PackageInterface $package,
        Article $article,
        ArticleInterface $parent,
        ArticleProviderInterface $articleProvider,
        RouteInterface $route,
        RouteProviderInterface $routeProvider
    ) {
        $factory->create()->willReturn($article);

        $item = new Item();
        $item->setBody('some body');

        $package->getHeadline()->shouldBeCalled()->willReturn('item headline');
        $package->getItems()->shouldBeCalled()->willReturn(new ArrayCollection([$item]));
        $package->getLanguage()->shouldBeCalled()->willReturn('en');

        $article->setParentDocument($parent)->shouldBeCalled();
        $article->setTitle('item headline')->shouldBeCalled();
        $article->setBody('some body')->shouldBeCalled();
        $article->setLocale('en')->shouldBeCalled();
        $article->setRoute($route)->shouldBeCalled();

        $articleProvider->findOneById('test')->willReturn($parent);
        $routeProvider->getRouteForArticle($article)->willReturn($route);

        $this->createFromPackage($package)->shouldReturn($article);
    }
}
