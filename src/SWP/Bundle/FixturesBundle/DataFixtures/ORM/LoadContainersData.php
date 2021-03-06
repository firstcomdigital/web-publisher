<?php

/*
 * This file is part of the Superdesk Web Publisher Fixtures Bundle.
 *
 * Copyright 2015 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use SWP\Bundle\CoreBundle\Model\Container;
use SWP\Bundle\FixturesBundle\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use SWP\Bundle\TemplatesSystemBundle\Model\ContainerWidget;

class LoadContainersData extends AbstractFixture implements FixtureInterface, OrderedFixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $env = $this->getEnvironment();
        if ('test' !== $env) {
            $container1 = new Container();
            $container1->setName('mainNav');
            $container1->setType(1);
            $container1->setCssClass('container');
            $container1->setVisible(true);
            $container1->setTenantCode('123abc');

            $containerWidget1 = new ContainerWidget($container1, $this->getReference('menu_widget_main'));
            $manager->persist($containerWidget1);
            $container1->addWidget($containerWidget1);
            $manager->persist($container1);

            $container2 = new Container();
            $container2->setName('footerNav');
            $container2->setType(1);
            $container2->setCssClass('container');
            $container2->setVisible(true);
            $container2->setTenantCode('123abc');

            $containerWidget2 = new ContainerWidget($container2, $this->getReference('menu_widget_footer'));
            $manager->persist($containerWidget2);
            $container2->addWidget($containerWidget2);
            $manager->persist($container2);

            $manager->flush();
        } else {
            $this->loadFixtures(
                [
                    '@SWPFixturesBundle/Resources/fixtures/ORM/'.$env.'/Container.yml',
                ],
                $manager
            );
        }
    }

    public function getOrder()
    {
        return 2;
    }
}
