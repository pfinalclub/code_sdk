<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */

declare(strict_types=1);

namespace CozeSdk\Tests\OfficialAccount;

use CozeSdk\OfficialAccount\Application;
use CozeSdk\OfficialAccount\Contracts\Application as ApplicationInterface;
use CozeSdk\OfficialAccount\Contracts\Account as AccountInterface;
use CozeSdk\Kernel\Contracts\AccessToken as AccessTokenInterface;
use CozeSdk\Tests\TestCase;

class ApplicationTest extends TestCase
{
    public function test_application_can_create_account_instance()
    {
        $app = new Application(
            config: [
                'kid' => 'HC3N9VQD48ADZwrMD_uv8tQZxZ-E4eVVDHKO1XjXUNU',
                'iss' => '1135933249080'
            ]
        );

        $this->assertInstanceOf(ApplicationInterface::class, $app);
        $this->assertInstanceOf(AccountInterface::class, $app->getAccount());
        $this->assertSame($app->getAccount(), $app->getAccount());
    }

    public function test_get_and_set_access_token()
    {
        $app = new Application(
            config: [
                'kid' => 'HC3N9VQD48ADZwrMD_uv8tQZxZ-E4eVVDHKO1XjXUNU',
                'iss' => '1135933249080',
                'key_path' => __DIR__.'/../'
            ]
        );

        $this->assertInstanceOf(AccessTokenInterface::class, $app->getAccessToken());
        $this->assertIsString($app->getAccessToken()->getToken());

    }
}