<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\OfficialAccount\Contracts;

interface Account
{
    public function getKid(): string;
    public function getHeaderParams(): string;
    public function getPayload(): string;
    public function getSignature(): string;
    public function getJti(): string;
}