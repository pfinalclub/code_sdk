<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/18
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);
namespace CozeSdk\Tests\OfficialAccount;

use CozeSdk\OfficialAccount\Account;
use CozeSdk\OfficialAccount\Account as AccountInterface;
use CozeSdk\OfficialAccount\Application;
use CozeSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

class AccountTest extends TestCase
{

    #[Group("account")]
    public function test_application_can_create_account_instance()
    {
        $this->assertInstanceOf(AccountInterface::class, $this->app->getAccount());
    }

    public function test_get_account_kid()
    {
        $config = [
            'kid' => '87H_tatLsKzPKQGxcp8ZRJsENRZZL7oQVbpNBaHmKlw',
            'iss' => '1135933249080'
        ];
        $account = new Account(
            kid: $config['kid'],
            iss: $config['iss'],
        );
        $this->assertSame($config['kid'], $account->getKid());
    }

    #[Group("account_sign")]
    /**
     * @throws \Exception
     */
    public function test_get_account_signature()
    {
        $config = [
            'kid' => '87H_tatLsKzPKQGxcp8ZRJsENRZZL7oQVbpNBaHmKlw',
            'iss' => '1135933249080',
            'key_path' => __DIR__.'/../'
        ];
        $account = new Account(
            kid: $config['kid'],
            iss: $config['iss'],
            key_path: $config['key_path']
        );
        $signature = $account->getSignature();
        $this->assertIsString($signature);

    }
}