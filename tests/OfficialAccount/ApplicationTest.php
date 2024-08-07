<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */

declare(strict_types=1);

namespace CozeSdk\Tests\OfficialAccount;

use CozeSdk\OfficialAccount\Contracts\Application as ApplicationInterface;
use CozeSdk\OfficialAccount\Contracts\Account as AccountInterface;
use CozeSdk\Kernel\Contracts\AccessToken as AccessTokenInterface;
use CozeSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

class ApplicationTest extends TestCase
{

    public function test_application_can_create_account_instance()
    {
        $this->assertInstanceOf(ApplicationInterface::class, $this->app);
        $this->assertInstanceOf(AccountInterface::class, $this->app->getAccount());
        $this->assertSame($this->app->getAccount(), $this->app->getAccount());
    }

    #[Group("access_token")]
    public function test_get_and_set_access_token()
    {

        $this->assertInstanceOf(AccessTokenInterface::class, $this->app->getAccessToken());
        $this->assertIsString($this->app->getAccessToken()->getToken());

    }
}