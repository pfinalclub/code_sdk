<?php
/**
 * Author: PFinal南丞
 * Date: 2024/8/3
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Kernel\Conversation;

interface Conversation
{
    public function setConversationId(string $conversationId): void;

    public function getConversationId(): string;

    public function createConversation(): void;
}