<?php
/**
 * Author: PFinal南丞
 * Date: 2024/8/5
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Tests\Chat;

use CozeSdk\Chat\Chat;
use CozeSdk\OfficialAccount\Application;
use CozeSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

class ChatTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }
    /**
     * test_chat_st
     * @return void
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    #[Group("chat_build")]
    public function test_chat_st()
    {
        $chat = new Chat($this->app->getAccessToken());
        ob_start();
        $responseClosure = $chat->setBotId("7381736405354971163")->setConversationId("7399479380332003338")->setUserId("123456789")->Query("MySql是什么？")->Build(true);
        $responseClosure();
        $output = ob_get_clean();
        var_dump($output);
        $this->assertIsString($output);
        //        $this->assertIsObject($res);
        //$this->assertIsArray($res);
//        $chatId = $chat->getChatId();
//        # var_dump($chatId);
//        $this->assertIsString($chatId);


    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    public function test_get_chat_status()
    {
        $app = new Application(
            config: [
                'kid' => '8v5iOwlXR4QQiPlkId1FcjcbO0Jug7RpfXECW4D-uJA',
                'iss' => '1135933249080',
                'key_path' => __DIR__.'/../'
            ]
        );
        $chat = new Chat($app->getAccessToken());
        $message_info = $chat->setConversationId("7399479380332003338")->getChatRetrieve("7399534772378157110");
        $this->assertIsArray($message_info);
    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    public function test_get_chat_message_list()
    {
        $app = new Application(
            config: [
                'kid' => '8v5iOwlXR4QQiPlkId1FcjcbO0Jug7RpfXECW4D-uJA',
                'iss' => '1135933249080',
                'key_path' => __DIR__.'/../'
            ]
        );
        $chat = new Chat($app->getAccessToken());
        $message_list = $chat->setConversationId("7399479380332003338")->getChatMessageList("7399534772378157110");
        var_dump($message_list);
        $this->assertIsArray($message_list);
    }
}