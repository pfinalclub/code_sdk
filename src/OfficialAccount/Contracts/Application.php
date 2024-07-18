<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\OfficialAccount\Contracts;

use CozeSdk\Kernel\Contracts\AccessToken;
use CozeSdk\Kernel\Contracts\Config;
interface Application
{
    public function getAccount(): Account;
    public function getConfig(): Config;
    public function getAccessToken(): AccessToken;
}