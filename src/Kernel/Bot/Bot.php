<?php
/**
 * Author: PFinal南丞
 * Date: 2024/7/19
 * Email: <lampxiezi@163.com>
 */
declare(strict_types=1);

namespace CozeSdk\Kernel\Bot;

interface Bot
{
    /**
     * 获取bot ID
     * getBotId
     * @return string
     */
    public function getBotId(): string;

    /**
     * 获取 空间ID
     * getSpaceId
     * @return string
     */
    public function getSpaceId(): string;

    /**
     * 获取bot列表
     * getBotList
     * @return array
     */
    public function getBotList(): array;

    /**
     * getBotDetail 获取 bot 配置
     * @return array
     */
    public function getBotDetail(): array;
}