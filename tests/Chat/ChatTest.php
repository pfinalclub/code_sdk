<?php
/**
 * Author: PFinal南丞
 * Date: 2024/8/5
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Tests\Chat;

use CozeSdk\Chat\Chat;
use CozeSdk\Kernel\Exception\HttpException;
use CozeSdk\Tests\TestCase;
use PHPUnit\Framework\Attributes\Group;

class ChatTest extends TestCase
{
//    /**
//     * test_chat_st
//     * @return void
//     * @throws \CozeSdk\Kernel\Exception\HttpException
//     */
//    #[Group("chat_build")]
//    public function test_chat_st()
//    {
//        $chat = new Chat($this->app);
//        $responseClosure = $chat->setUserId("12345678")->Query("你好,用PHP写一个循环")->Build();
//        var_dump($responseClosure);
//        $this->assertIsArray($responseClosure);
////        ob_start();
////        $responseClosure = $chat->setBotId("7388055232066617394")->setUserId("123456789")->Query("你好,写一个首关于风景的诗词")->Build();
////        $responseClosure();
////        $output = ob_get_clean();
////        var_dump($output);
////        $this->assertIsString($output);
//        //        $this->assertIsObject($res);
//        //$this->assertIsArray($res);
////        $chatId = $chat->getChatId();
////        # var_dump($chatId);
////        $this->assertIsString($chatId);
//
//
//    }
//
    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
//    #[Group('chat_status')]
//    public function test_get_chat_status()
//    {
//        $chat = new Chat($this->app);
//        $message_info = $chat->setConversationId("7437376554340024358")->getChatRetrieve("7437376554340040742");
//		var_dump($message_info);
//        $this->assertIsArray($message_info);
//    }
//
    /**
     * @throws \CozeSdk\Kernel\Exception\HttpException
     */
//    #[Group("chat_message_list")]
//    public function test_get_chat_message_list()
//    {
//        $chat = new Chat($this->app);
//        $message_list = $chat->setConversationId("7437376554340024358")->getChatMessageList("7437376554340040742");
//        var_dump($message_list);
//        $this->assertIsArray($message_list);
//    }
//

	#[Group("chat_send_message")]
	public function test_send_message()
	{
		$chat = new Chat($this->app);
		$message_info = $chat->sendMessage("Laravel 路由测试");
		var_dump($message_info);
		$this->assertIsArray($message_info);
	}
}