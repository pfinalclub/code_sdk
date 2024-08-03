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

    public function getMetaData(): array;

    public function getMessage(): array;

    public function getRole(): string;

    public function query(): array;
}