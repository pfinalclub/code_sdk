<?php
/**
 * Author: PFinal南丞
 * Date: 2024/8/5
 * Email: <lampxiezi@163.com>
 */

namespace CozeSdk\Kernel\Support;

class StreamProcessor
{
    private $buffer = '';
    private $events = [];

    public function processChunk(string $chunkContent): void
    {
        // 将新块添加到缓冲区
        $this->buffer .= $chunkContent;

        // 处理缓冲区中的数据
        $this->parseBuffer();
    }

    private function parseBuffer(): void
    {
        // 分割事件块
        $blocks = explode("\n\n", trim($this->buffer));

        foreach ($blocks as $block) {
            if (empty($block)) {
                continue;
            }

            // 解析事件
            $lines = explode("\n", $block);
            $event = null;
            $data  = '';

            foreach ($lines as $line) {
                if (strpos($line, 'event:') === 0) {
                    $event = substr($line, 6);
                } elseif (strpos($line, 'data:') === 0) {
                    $data = substr($line, 5);
                }
            }

            if ($event && $data) {
                $this->events[] = [
                    'event' => $event,
                    'data'  => json_decode($data, true) // 解析 JSON 数据
                ];
            }
        }

        // 清空缓冲区已处理的部分
        $lastNewlinePos = strrpos($this->buffer, "\n\n");
        $this->buffer   = substr($this->buffer, $lastNewlinePos !== false ? $lastNewlinePos + 2 : 0);
    }

    public function getEvents(): array
    {
        return $this->events;
    }
}