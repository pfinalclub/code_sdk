<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/25
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Tests\Conversation;

use CozeSdk\Conversation\Conversation;
use CozeSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

class ConversationTest extends TestCase
{

    /**
     * test_create_conversation
     * @return void
     * @throws \CozeSdk\Kernel\Exception\HttpException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    #[Group("create_cid")]
    public function test_create_conversation()
    {
        $conversation = new Conversation($this->app->getAccessToken());
        $conversationId = $conversation->getConversationId();
        var_dump($conversationId);
        $this->assertIsString($conversationId);
    }
}