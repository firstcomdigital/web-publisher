<?php

/*
 * This file is part of the Superdesk Web Publisher Templates System.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace spec\SWP\Bundle\TemplatesSystemBundle\Widget;

use PhpSpec\ObjectBehavior;
use SWP\Component\TemplatesSystem\Gimme\Model\WidgetModelInterface;
use SWP\Bundle\TemplatesSystemBundle\Widget\GoogleAdSenseWidgetHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

/**
 * @mixin GoogleAdSenseWidgetHandler
 */
class GoogleAdSenseWidgetHandlerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(GoogleAdSenseWidgetHandler::class);
    }

    public function let(WidgetModelInterface $widgetModel, EngineInterface $templating)
    {
        $widgetModel->getParameters()->willReturn(['ad_client' => 'client id']);
        $this->beConstructedWith($widgetModel, $templating);
    }

    public function it_should_render(
        ContainerInterface $container,
        EngineInterface $templating,
        Response $response
    ) {
        $templating->render('widgets/adsense.html.twig', [
            'style' => 'display:block',
            'ad_client' => 'client id',
            'ad_test' => 'off',
            'ad_slot' => null,
            'ad_format' => 'auto',
        ])->willReturn($response);

        $this->render()->shouldReturn($response);
    }

    public function it_should_render_defaults_when_no_params_provided(
        ContainerInterface $container,
        EngineInterface $templating,
        Response $response,
        WidgetModelInterface $widgetModel
    ) {
        $widgetModel->getParameters()->willReturn([]);
        $this->beConstructedWith($widgetModel, $templating);

        $templating->render('widgets/adsense.html.twig', [
            'style' => 'display:block',
            'ad_client' => null,
            'ad_test' => 'off',
            'ad_slot' => null,
            'ad_format' => 'auto',
        ])->willReturn($response);

        $this->render()->shouldReturn($response);
    }

    public function it_should_check_if_it_is_visible(WidgetModelInterface $widgetModel)
    {
        $widgetModel->getVisible()->willReturn(true);
        $this->isVisible()->shouldReturn(true);

        $widgetModel->getVisible()->willReturn(false);
        $this->isVisible()->shouldReturn(false);
    }
}
