<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/19
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Chat;

use CozeSdk\Kernel\Chat\Chat as ChatInterface;

class Chat implements ChatInterface
{
    protected string|null $botId = null;
    protected string|null $conversationId = null;

    public function setBotId(string $botId): Chat
    {
        $this->botId = $botId;
        return $this;
    }


    public function setConversationId(string $conversationId): ChatInterface
    {
        $this->conversationId = $conversationId;
        return $this;
    }

    public function query(): array
    {
        // TODO: Implement query() method.
    }

}