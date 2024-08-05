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

class ChatTest extends TestCase
{
    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    public function test_chat()
    {
        $app = new Application(
            config: [
                'kid' => '8v5iOwlXR4QQiPlkId1FcjcbO0Jug7RpfXECW4D-uJA',
                'iss' => '1135933249080',
                'key_path' => __DIR__.'/../'
            ]
        );
        $chat = new Chat($app->getAccessToken());
        $res = $chat->setBotId("7381736405354971163")->setConversationId("7399479380332003338")->setUserId("smm_1")->Query()->Build();
        # var_dump($res);
        $this->assertIsArray($res);
        $chatId = $chat->getChatId();
        $this->assertIsString($chatId);
        $message_status = $chat->getChatDetail();
        var_dump($message_status);
        $this->assertIsArray($message_status);
    }
}