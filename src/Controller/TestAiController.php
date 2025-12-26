<?php

namespace App\Controller;

use Symfony\AI\Agent\AgentInterface;
use Symfony\AI\Platform\Message\Message;
use Symfony\AI\Platform\Message\MessageBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire; // ← 新增导入
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test')]
class TestAiController extends AbstractController
{
    #[Route('/ai', name: 'test_ai_generate', methods: ['GET'])]
    public function generate(
        Request $request,
        #[Autowire(service: 'ai.agent.seo_writer')] // ← 关键修复
        AgentInterface $seoWriter
    ): JsonResponse {
        $keyword = $request->query->get('keyword', '2025年人工智能趋势');

        if (empty(trim($keyword))) {
            return $this->json(['error' => 'Missing keyword'], 400);
        }

        $systemPrompt = <<<PROMPT
你是一位专业的中文SEO内容专家。请根据最新网络信息，围绕关键词“{$keyword}”撰写一篇结构清晰、信息丰富、语言流畅的文章。

要求：
- 标题必须包含关键词
- 正文 800–1200 字
- 使用 H2/H3 小标题组织内容
- 自然融入关键词 3–5 次
- 结尾附带 3 个常见问题（FAQ）
- 仅输出 Markdown 格式内容，不要任何解释或前缀
PROMPT;

        $messages = new MessageBag(
            Message::forSystem($systemPrompt),
            Message::ofUser("请写一篇关于“{$keyword}”的SEO文章。")
        );

        try {
            $response = $seoWriter->call($messages);

            return $this->json([
                'keyword' => $keyword,
                'generated_content' => $response->getContent()
            ]);
        } catch (\Throwable $e) {
            return $this->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
