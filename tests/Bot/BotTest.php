<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/19
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Tests\Bot;

use CozeSdk\Bot\Bot;
use CozeSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

class BotTest extends TestCase
{
    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    #[Group("bot_list")]
    public function testGetBotList()
    {
        $bot = new Bot($this->app->getAccessToken());
        $bot_list = $bot->setSpaceId(spaceId:"7374606142925733940")->getBotList();
        $this->assertIsArray($bot_list);
    }

    /**
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     */
    #[Group("bot_info")]
    public function testGetBotInfo()
    {
        $bot = new Bot($this->app->getAccessToken());
        $bot_info = $bot->getBotDetail("7381736405354971163");
        $this->assertIsArray($bot_info);
    }

    #[Group("bot_ids")]
    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function testGetBotIdList()
    {
        $bot = new Bot($this->app->getAccessToken());
        $botIds = $bot->setSpaceId(spaceId:"7374606142925733940")->getBotIdList();
        var_dump($botIds);
        $this->assertIsArray($botIds);
    }
}