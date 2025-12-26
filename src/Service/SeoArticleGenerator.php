<?php

namespace App\Service;

use Symfony\AI\Chat\ChatInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;

class SeoArticleGenerator
{
    public function __construct(
        private ChatInterface $chat,
    ) {}

    public function generate(string $keyword): string
    {
        $messages = new MessageBag();
        $messages->add(Message::ofSystem('你是专业的 SEO 内容写作助手，擅长写结构清晰、可读性强的文章。'));
        $messages->add(Message::ofUser(<<<TEXT
请围绕关键词「{$keyword}」写一篇 SEO 文章，要求：
1. 包含标题
2. 使用 H2 / H3 结构
3. 语言自然，适合博客发布
4. 字数 800 字左右
TEXT
        ));

        $this->chat->initiate($messages);
        
        $response = $this->chat->submit(Message::ofUser('请开始写作'));
        
        return $response->content;
    }
}