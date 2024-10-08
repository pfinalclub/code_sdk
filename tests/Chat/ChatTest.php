<?php
/**
 * Author: PFinal南丞
 * Date: 2024/8/5
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Tests\Chat;

use CozeSdk\Chat\Chat;
use CozeSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

class ChatTest extends TestCase
{
    /**
     * test_chat_st
     * @return void
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    #[Group("chat_build")]
    public function test_chat_st()
    {
        $chat = new Chat($this->app->getAccessToken());
        $responseClosure = $chat->setBotId("7388055232066617394")->setUserId("12345678")->Query("你好,用PHP写一个循环")->Build();
        var_dump($responseClosure);
        $this->assertIsArray($responseClosure);
//        ob_start();
//        $responseClosure = $chat->setBotId("7388055232066617394")->setUserId("123456789")->Query("你好,写一个首关于风景的诗词")->Build();
//        $responseClosure();
//        $output = ob_get_clean();
//        var_dump($output);
//        $this->assertIsString($output);
        //        $this->assertIsObject($res);
        //$this->assertIsArray($res);
//        $chatId = $chat->getChatId();
//        # var_dump($chatId);
//        $this->assertIsString($chatId);


    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    #[Group('chat_status')]
    public function test_get_chat_status()
    {
        $chat = new Chat($this->app->getAccessToken());
        $message_info = $chat->setConversationId("7388055232066617394")->getChatRetrieve("7400272900110172212");
        $this->assertIsArray($message_info);
    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    #[Group("chat_message_list")]
    public function test_get_chat_message_list()
    {
        $chat = new Chat($this->app->getAccessToken());
        $message_list = $chat->setConversationId("7400272900110155828")->getChatMessageList("7400272900110172212");
        var_dump($message_list);
        $this->assertIsArray($message_list);
    }
}