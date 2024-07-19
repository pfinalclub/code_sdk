<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/19
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\Kernel\Bot;

interface Chat
{
    public function getBotId(): string;

    public function setBotId(): string;

    public function sendChat(): array;

    public function getAdditionalMessages(): array;

    public function setAdditionalMessages(): array;

    public function getStream(): bool;

    public function setStream(): void;

    public function getAutoSaveHistory(): bool;

    public function setAutoSaveHistory(): void;

    public function getMetaData(): array;
}