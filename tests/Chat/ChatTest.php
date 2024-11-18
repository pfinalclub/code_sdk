<?php
/**
 * Author: PFinal南丞
 * Date: 2024/8/5
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Tests\Chat;

use CozeSdk\Chat\Chat;
use CozeSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

class ChatTest extends TestCase
{
    /**
     * test_chat_st
     * @return void
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    #[Group("chat_build")]
    public function test_chat_st()
    {
        $chat = new Chat($this->app);
        $responseClosure = $chat->setUserId("12345678")->Query("你好,用PHP写一个循环")->Build();
        print_r(json_encode($responseClosure, JSON_UNESCAPED_UNICODE).PHP_EOL);
        $this->assertIsArray($responseClosure);
    }
//
    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    #[Group('chat_status')]
    public function test_get_chat_status()
    {
        $chat = new Chat($this->app);
        $message_info = $chat->setConversationId("7437376554340024358")->getChatRetrieve("7437376554340040742");
		var_dump($message_info);
        $this->assertIsArray($message_info);
    }

    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
    #[Group("chat_message_list")]
    public function test_get_chat_message_list()
    {
        $chat = new Chat($this->app);
        $message_list = $chat->setConversationId("7437376554340024358")->getChatMessageList("7437376554340040742");
        print_r(json_encode($message_list, JSON_UNESCAPED_UNICODE).PHP_EOL);
        $this->assertIsArray($message_list);
    }

	/**
	 * 测试发送消息的功能。
	 * 这个测试方法创建了一个新的 `Chat` 类的实例，并通过传递 `$this->app` 参数来调用其构造函数。
	 * 然后，它调用 `Chat` 实例的 `sendMessage` 方法，并传递一个中文消息（"PHP 接口是什么？"）。
	 * 最后，它将 `sendMessage` 的响应打印为 JSON 编码的字符串，并使用 `assertIsArray` 方法断言响应是一个数组。
	 * @return void
	 * @throws \CozeSdk\Kernel\Exception\HttpException
	 */
	#[Group("chat_send_message")]
	public function test_send_message()
	{
		$chat = new Chat($this->app);
		$message_info = $chat->sendMessage("PHP 接口是什么?");
		print_r(json_encode($message_info, JSON_UNESCAPED_UNICODE).PHP_EOL);
		$this->assertIsArray($message_info);
	}

}