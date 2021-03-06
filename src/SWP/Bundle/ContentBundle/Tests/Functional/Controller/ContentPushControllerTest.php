<?php

/*
 * This file is part of the Superdesk Web Publisher Content Bundle.
 *
 * Copyright 2016 Sourcefabric z.ú. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2016 Sourcefabric z.ú
 * @license http://www.superdesk.org/license
 */

namespace SWP\Bundle\ContentBundle\Tests\Functional\Controller;

use SWP\Bundle\ContentBundle\Tests\Functional\WebTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\RouterInterface;

class ContentPushControllerTest extends WebTestCase
{
    const TEST_CONTENT = '{"versioncreated":"2016-05-25T11:53:15+0000","firstcreated":"2016-05-25T10:23:15+0000","pubstatus":"usable","guid":"urn:newsml:localhost:2016-05-25T12:41:05.637675:c97f2b4a-5f09-41e0-b73d-b61d7325e5cc","headline":"test package 5","byline":"John Doe","subject":[{"name":"archaeology","code":"01001000"}],"urgency":3,"priority":6,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"ads fsadf sdaf sadf sadf","version":"6","associations":{"main":{"versioncreated":"2016-05-25T11:14:52+0000","pubstatus":"usable","body_html":"<p>sadf sadf sdaf sadg dg sadfasdg sa<\/p><p>df&nbsp;<\/p><p>asd<\/p><p>fsa&nbsp;<\/p><p>f&nbsp;<\/p><p>asd f<\/p>","headline":"sadf sadf sadf\u00a0","byline":"sadfsda fsdf sadf","subject":[{"name":"archaeology","code":"01001000"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 1","priority":6,"type":"composite","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:14:51.697607:9ba267e4-fed7-4d08-9af0-36bc933eb0f4"},"story-1":{"versioncreated":"2016-05-25T11:53:14+0000","pubstatus":"usable","body_html":"<p>asd fsadf sadf sadf sda<\/p><p>fsad<\/p><p>f&nbsp;<\/p><p>sad<\/p><p>f sadf sadfsadf&nbsp;<\/p><p>lorem ipsum 3<\/p>","headline":"lorem ipsum content 3\u00a0","byline":"John Doe","subject":[{"name":"theft","code":"02001003"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 3","priority":6,"type":"composite","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:53:14.419018:b698d547-35a5-4f0f-9167-3dbecb1dae78"},"story-0":{"versioncreated":"2016-05-25T11:35:43+0000","pubstatus":"usable","body_html":"<p>lorem ispum body&nbsp;<\/p>","headline":"cinema film festival item","description_text": "test abstract","byline":"John Doe","subject":[{"name":"film festival","code":"01005001"}],"version":"2","urgency":3,"located":"Porto","place":[{"qcode":"EUR","state":"","group":"Rest Of World","name":"EUR","country":"","world_region":"Europe"}],"service":[{"name":"Advisories","code":"v"}],"slugline":"test item 2 ","priority":6,"type":"composite","language":"en","guid":"urn:newsml:localhost:2016-05-25T12:35:43.450626:91228dd7-853e-41c6-8bea-32b75496c618"}},"type":"composite","language":"en"}';
    const TEST_CONTENT_WITH_MEDIA = '{"slugline": "text item with image", "urgency": 3, "versioncreated": "2016-08-17T17:47:18+0000", "firstcreated":"2016-05-25T10:23:15+0000", "guid": "urn:newsml:localhost:2016-08-17T18:45:49.955085:fd9771ee-a1a7-40d7-8eb6-64c502e5f495", "body_html": "<p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam imperdiet diam enim, vehicula venenatis nunc maximus vitae. Suspendisse ligula turpis, dictum vel mi quis, viverra viverra massa. Nulla vitae enim id sapien efficitur interdum vel sed nisl. Morbi pharetra suscipit pulvinar. Phasellus tincidunt tortor at porttitor blandit. Nulla ac nibh ut arcu tristique sagittis. Cras ac tristique odio. Sed dolor risus, pulvinar dapibus tincidunt nec, elementum nec dui. Fusce vel enim vel diam auctor faucibus id quis erat. Duis eget risus orci. Praesent sit amet diam tristique, egestas nulla quis, sagittis arcu. Suspendisse non facilisis tellus, ac scelerisque dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam molestie fringilla dui dapibus pellentesque.</p><p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Sed imperdiet ante vitae luctus ullamcorper. Nam sit amet rhoncus urna. Integer eget euismod arcu. Pellentesque cursus luctus magna vel porttitor. Nunc fringilla aliquet quam, vel porta enim volutpat eu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam vestibulum auctor purus a consequat.</p>\n<!-- EMBED START Image {id: \"embedded4905430171\"} -->\n<figure><img src=\"http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http\" alt=\"test image\" srcset=\"//localhost:5000/api/upload/1234567890987654321a/raw?_schema=http 800w, //localhost:5000/api/upload/1234567890987654321c/raw?_schema=http 1079w\" /><figcaption>test image</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded4905430171\"} -->\n<p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Sed sit amet ligula imperdiet, finibus tellus consectetur, condimentum mi. Nam eleifend eleifend elit. Donec sit amet molestie lectus. In auctor ullamcorper tortor non ultrices. Morbi id mattis nisl, a placerat quam. Maecenas sed urna in lorem sodales lobortis. Etiam sodales odio vitae risus cursus blandit. Sed consequat gravida justo nec facilisis. Aenean ac erat luctus, posuere neque nec, blandit lacus. Nulla convallis sem quis tristique dictum. Phasellus porta massa sollicitudin, tincidunt nulla ac, pulvinar quam. Nullam a elit magna. Aenean maximus rhoncus lorem, sodales sagittis leo dictum id. Nulla ut interdum turpis.</p>", "located": "Porto", "language": "en", "version": "2", "priority": 6, "type": "text", "byline": "Pawe\u0142 Miko\u0142ajczuk", "service": [{"name": "Australian General News", "code": "a"}], "associations": {"embedded4905430171": {"renditions": {"16-9": {"height": 720, "mimetype": "image/jpeg", "width": 1079, "media": "1234567890987654321a", "href": "http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http"}, "4-3": {"height": 533, "mimetype": "image/jpeg", "width": 800, "media": "1234567890987654321b", "href": "http://localhost:5000/api/upload/1234567890987654321b/raw?_schema=http"}, "original": {"height": 2667, "mimetype": "image/jpeg", "width": 4000, "media": "1234567890987654321c", "href": "http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http"}}, "urgency": 3, "body_text": "test image", "versioncreated": "2016-08-17T17:46:52+0000", "guid": "tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a6", "byline": "Pawe\u0142 Miko\u0142ajczuk", "pubstatus": "usable", "language": "en", "version": "2", "description_text": "test image", "priority": 6, "type": "picture", "service": [{"name": "Australian General News", "code": "a"}], "usageterms": "indefinite-usage", "mimetype": "image/jpeg", "headline": "test image", "located": "Porto"}}, "pubstatus": "usable", "headline": "Text item with image headline\u00a0", "subject": [{"name": "photography", "code": "01013000"}]}';
    const TEST_CONTENT_WITH_MEDIA_MODIFIED = '{"slugline": "text item with image", "urgency": 3, "versioncreated": "2016-08-17T17:47:18+0000", "firstcreated":"2016-05-25T10:23:15+0000", "guid": "urn:newsml:localhost:2016-08-17T18:45:49.955085:fd9771ee-a1a7-40d7-8eb6-64c502e5f495", "body_html": "<p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam imperdiet diam enim, vehicula venenatis nunc maximus vitae. Suspendisse ligula turpis, dictum vel mi quis, viverra viverra massa. Nulla vitae enim id sapien efficitur interdum vel sed nisl. Morbi pharetra suscipit pulvinar. Phasellus tincidunt tortor at porttitor blandit. Nulla ac nibh ut arcu tristique sagittis. Cras ac tristique odio. Sed dolor risus, pulvinar dapibus tincidunt nec, elementum nec dui. Fusce vel enim vel diam auctor faucibus id quis erat. Duis eget risus orci. Praesent sit amet diam tristique, egestas nulla quis, sagittis arcu. Suspendisse non facilisis tellus, ac scelerisque dui. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Etiam molestie fringilla dui dapibus pellentesque.</p><p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Sed imperdiet ante vitae luctus ullamcorper. Nam sit amet rhoncus urna. Integer eget euismod arcu. Pellentesque cursus luctus magna vel porttitor. Nunc fringilla aliquet quam, vel porta enim volutpat eu. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Etiam vestibulum auctor purus a consequat.</p>\n<!-- EMBED START Image {id: \"embedded4905430000\"} -->\n<figure><img src=\"http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http\" alt=\"test image\" srcset=\"//localhost:5000/api/upload/1234567890987654321a/raw?_schema=http 800w, //localhost:5000/api/upload/1234567890987654321c/raw?_schema=http 1079w\" /><figcaption>test image</figcaption></figure>\n<!-- EMBED END Image {id: \"embedded4905430171\"} -->\n<p style=\"margin-bottom: 15px; text-align: justify; color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; line-height: 20px;\">Sed sit amet ligula imperdiet, finibus tellus consectetur, condimentum mi. Nam eleifend eleifend elit. Donec sit amet molestie lectus. In auctor ullamcorper tortor non ultrices. Morbi id mattis nisl, a placerat quam. Maecenas sed urna in lorem sodales lobortis. Etiam sodales odio vitae risus cursus blandit. Sed consequat gravida justo nec facilisis. Aenean ac erat luctus, posuere neque nec, blandit lacus. Nulla convallis sem quis tristique dictum. Phasellus porta massa sollicitudin, tincidunt nulla ac, pulvinar quam. Nullam a elit magna. Aenean maximus rhoncus lorem, sodales sagittis leo dictum id. Nulla ut interdum turpis.</p>", "located": "Porto", "language": "en", "version": "2", "priority": 6, "type": "text", "byline": "Pawe\u0142 Miko\u0142ajczuk", "service": [{"name": "Australian General News", "code": "a"}], "associations": {"embedded4905430000": {"renditions": {"16-10": {"height": 620, "mimetype": "image/jpeg", "width": 1069, "media": "1234567890987654321a", "href": "http://localhost:5000/api/upload/1234567890987654321a/raw?_schema=http"}, "4-3": {"height": 533, "mimetype": "image/jpeg", "width": 800, "media": "1234567890987654321b", "href": "http://localhost:5000/api/upload/1234567890987654321b/raw?_schema=http"}, "original": {"height": 2667, "mimetype": "image/jpeg", "width": 4000, "media": "1234567890987654321c", "href": "http://localhost:5000/api/upload/1234567890987654321c/raw?_schema=http"}}, "urgency": 3, "body_text": "test image", "versioncreated": "2016-08-17T17:46:52+0000", "guid": "tag:localhost:2016:56753145-8d59-4eed-bdd5-387013db97a6", "byline": "Pawe\u0142 Miko\u0142ajczuk", "pubstatus": "usable", "language": "en", "version": "2", "description_text": "test image", "priority": 6, "type": "picture", "service": [{"name": "Australian General News", "code": "a"}], "usageterms": "indefinite-usage", "mimetype": "image/jpeg", "headline": "test image", "located": "Porto"}}, "pubstatus": "usable", "headline": "Text item with image headline\u00a0", "subject": [{"name": "photography", "code": "01013000"}]}';

    const TEST_ITEM_CONTENT = '{"language": "en", "slugline": "abstract-html-test", "body_html": "<p>some html body</p>", "versioncreated": "2016-09-23T13:57:28+0000", "firstcreated":"2016-05-25T10:23:15+0000", "description_text": "some abstract text", "place": [{"country": "Australia", "world_region": "Oceania", "state": "Australian Capital Territory", "qcode": "ACT", "name": "ACT", "group": "Australia"}], "version": "2", "byline": "ADmin", "keywords": [], "guid": "urn:newsml:localhost:2016-09-23T13:56:39.404843:56465de4-0d5c-495a-8e36-3b396def3cf0", "priority": 6, "subject": [{"name": "lawyer", "code": "02002001"}], "urgency": 3, "type": "text", "headline": "Abstract html test", "service": [{"name": "Australian General News", "code": "a"}], "description_html": "<p><b><u>some abstract text</u></b></p>", "located": "Warsaw", "pubstatus": "usable"}';

    private $router;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->initDatabase();

        /* @var RouterInterface router */
        $this->router = $this->getContainer()->get('router');
    }

    public function testContentPush()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 1]), [
            'route' => [
                'content' => 1,
            ],
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'ads-fsadf-sdaf-sadf-sadf']), [
            'article' => [
                'status' => 'published',
                'route' => 1,
            ],
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testContentPushTwice()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
            ],
        ]);

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
    }

    public function testIfRouteAndPublishStatusIsNotChanged()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
            ],
        ]);

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'ads-fsadf-sdaf-sadf-sadf']), [
            'article' => [
                'status' => 'published',
                'route' => 1,
            ],
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'ads-fsadf-sdaf-sadf-sadf'])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content1 = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset(['route' => ['id' => 1]], $content1);
        self::assertEquals('published', $content1['status']);
        self::assertTrue($content1['is_publishable']);

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'ads-fsadf-sdaf-sadf-sadf'])
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $content2 = json_decode($client->getResponse()->getContent(), true);

        self::assertArraySubset(['route' => ['id' => 1]], $content2);
        self::assertEquals('published', $content2['status']);
        self::assertTrue($content2['is_publishable']);
        self::assertEquals($content1['published_at'], $content2['published_at']);
    }

    public function testContentPushWithMedia()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');

        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '1234567890987654321a',
                'media' => new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '1234567890987654321b',
                'media' => new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '1234567890987654321c',
                'media' => new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT_WITH_MEDIA
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'text-item-with-image'])
        );
        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('media', $content);
        self::assertCount(1, $content['media']);
        self::assertArrayHasKey('renditions', $content['media'][0]['image']);
        self::assertArrayHasKey('renditions', $content['media'][0]);
        self::assertCount(3, $content['media'][0]['renditions']);

        self::assertArraySubset(['id' => 3, 'asset_id' => '1234567890987654321c', 'file_extension' => 'png'], $content['media'][0]['image']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // test resending this same content with media
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT_WITH_MEDIA
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'text-item-with-image'])
        );

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('media', $content);
        self::assertCount(1, $content['media']);
        self::assertArrayHasKey('renditions', $content['media'][0]['image']);
        self::assertArrayHasKey('renditions', $content['media'][0]);
        self::assertCount(3, $content['media'][0]['renditions']);
        self::assertArraySubset(['asset_id' => '1234567890987654321c', 'file_extension' => 'png'], $content['media'][0]['image']);

        // test resending modified content with media
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT_WITH_MEDIA_MODIFIED
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'text-item-with-image'])
        );

        $content = json_decode($client->getResponse()->getContent(), true);
        self::assertArrayHasKey('media', $content);
        self::assertCount(1, $content['media']);
        self::assertArrayHasKey('renditions', $content['media'][0]['image']);
        self::assertArrayHasKey('renditions', $content['media'][0]);
        self::assertCount(3, $content['media'][0]['renditions']);
        self::assertArraySubset(['asset_id' => '1234567890987654321c', 'file_extension' => 'png'], $content['media'][0]['image']);
        self::assertArraySubset(['name' => '16-10'], $content['media'][0]['renditions'][0]);
    }

    public function testRenderingContentWithMedia()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');

        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '1234567890987654321a',
                'media' => new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '1234567890987654321b',
                'media' => new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '1234567890987654321c',
                'media' => new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT_WITH_MEDIA
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 1]), [
            'article' => [
                'status' => 'published',
                'route' => 1,
            ],
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testAssigningContentToCollectionRouteWithParentRoute()
    {
        $client = static::createClient();
        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'site',
                'type' => 'content',
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'news',
                'type' => 'collection',
                'parent' => 1,
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
            ],
        ]);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT);
        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_routes', ['id' => 3]), [
            'route' => [
                'content' => 'ads-fsadf-sdaf-sadf-sadf',
            ],
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request('PATCH', $this->router->generate('swp_api_content_update_articles', ['id' => 'ads-fsadf-sdaf-sadf-sadf']), [
            'article' => [
                'status' => 'published',
            ],
        ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    /*
     * @covers SWP\Bundle\ContentBundle\Controller\ContentPushController::pushAssetsAction
     */
    public function testAssetsPush()
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getContainer()->getParameter('kernel.cache_dir').'/uploads');

        $client = static::createClient();
        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => 'asdgsadfvasdf4w35qwetasftest',
                'media' => new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            [
                'media_id' => 'asdgsadfvasdf4w35qwetasftest',
                'URL' => 'http://localhost/media/asdgsadfvasdf4w35qwetasftest.png',
                'media' => base64_encode(file_get_contents(__DIR__.'/../app/Resources/test_file.png')),
                'mime_type' => 'image/png',
                'filemeta' => [],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );

        // Test amazon mediaId format
        $client->request(
            'POST',
            $this->router->generate('swp_api_assets_push'),
            [
                'media_id' => '2016083108080/6c182d783f51c4654c5feb8491600917ec38dc8675d44b886d7e03a897d9bee7.jpg',
                'media' => new UploadedFile(__DIR__.'/../app/Resources/test_file.png', 'test_file.png', 'image/png', 3992, null, true),
            ]
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());
        $this->assertEquals(
            [
                'media_id' => '2016083108080/6c182d783f51c4654c5feb8491600917ec38dc8675d44b886d7e03a897d9bee7.jpg',
                'URL' => 'http://localhost/media/6c182d783f51c4654c5feb8491600917ec38dc8675d44b886d7e03a897d9bee7.png',
                'media' => base64_encode(file_get_contents(__DIR__.'/../app/Resources/test_file.png')),
                'mime_type' => 'image/png',
                'filemeta' => [],
            ],
            json_decode($client->getResponse()->getContent(), true)
        );
    }

    public function testIfArticleHasLead()
    {
        $client = static::createClient();

        $client->request('POST', $this->router->generate('swp_api_content_create_routes'), [
            'route' => [
                'name' => 'articles',
                'type' => 'collection',
                'content' => null,
            ],
        ]);

        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_CONTENT
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'ads-fsadf-sdaf-sadf-sadf'])
        );

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArrayHasKey('lead', $content);
        self::assertEquals('test abstract', $content['lead']);

        // create article from text item
        $client->request(
            'POST',
            $this->router->generate('swp_api_content_push'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            self::TEST_ITEM_CONTENT
        );

        $this->assertEquals(201, $client->getResponse()->getStatusCode());

        $client->request(
            'GET',
            $this->router->generate('swp_api_content_show_articles', ['id' => 'abstract-html-test'])
        );

        $content = json_decode($client->getResponse()->getContent(), true);

        self::assertArrayHasKey('lead', $content);
        self::assertEquals('some abstract text', $content['lead']);
    }
}
