<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/19
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\Kernel\Chat;

interface Chat
{

    public function setBotId(string $botId): self;

    public function setConversationId(string $conversationId): self;
    public function query(): array;
}