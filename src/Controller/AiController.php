<?php

namespace App\Controller;

use App\Service\SeoArticleGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AiController
{
    #[Route('/generate', name: 'ai_generate')]
    public function generate(
        Request $request,
        SeoArticleGenerator $generator
    ): Response {
        $keyword = $request->query->get('keyword');

        if (!$keyword) {
            return new Response(
                '请提供 keyword 参数，例如：/generate?keyword=SEO优化',
                400
            );
        }

        $content = $generator->generate($keyword);

        return new Response(
            $content,
            200,
            ['Content-Type' => 'text/plain; charset=UTF-8']
        );
    }
}
