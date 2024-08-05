<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/25
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Tests\Conversation;

use CozeSdk\Conversation\Conversation;
use CozeSdk\OfficialAccount\Application;
use CozeSdk\Tests\TestCase;

class ConversationTest extends TestCase
{

    /**
     * test_create_conversation
     * @return void
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function test_create_conversation()
    {
        $app = new Application(
            config: [
                'kid' => '8v5iOwlXR4QQiPlkId1FcjcbO0Jug7RpfXECW4D-uJA',
                'iss' => '1135933249080',
                'key_path' => __DIR__.'/../'
            ]
        );
        $conversation = new Conversation($app->getAccessToken());
        $conversationId = $conversation->getConversationId();
        $this->assertIsString($conversationId);
    }
}