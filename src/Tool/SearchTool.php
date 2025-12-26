<?php

namespace App\Tool;

use Symfony\AI\Agent\Toolbox\Attribute\AsTool;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

/**
 * Web search tool for Symfony AI Agent.
 * Uses SerpAPI (https://serpapi.com) to fetch real-time search results.
 */
#[AsTool(
    name: 'web_search',
    description: 'Search the web for up-to-date and relevant information about a given topic or query. Returns top organic results with title, snippet, and URL.'
)]
class SearchTool
{
    public function __construct(
        private HttpClientInterface $httpClient,
        #[\SensitiveParameter]
        private string $serpApiKey = '',
    ) {}

    /**
     * Perform a web search via SerpAPI.
     *
     * @param string $query The search query (e.g., "2025年最佳电动SUV")
     * @return array List of search results or error info
     */
    public function __invoke(string $query): array
    {
        // 验证 API 密钥
        if (!$this->serpApiKey) {
            return [
                ['error' => 'SERPAPI_API_KEY is not configured. Please set it in .env.local']
            ];
        }

        try {
            $response = $this->httpClient->request('GET', 'https://serpapi.com/search', [
                'query' => [
                    'engine' => 'google',  // 明确指定搜索引擎
                    'q' => trim($query),
                    'api_key' => $this->serpApiKey,
                    'num' => 5,            // 返回前5条结果
                    'hl' => 'en',          // 使用英文界面语言，避免可能的编码问题
                    'gl' => 'us',          // 地区：美国
                ],
                'timeout' => 15,
            ]);

            $data = $response->toArray();

            // 检查是否有错误返回
            if (isset($data['error'])) {
                return [['error' => 'SerpAPI Error: ' . $data['error']]];
            }

            // 提取有机搜索结果
            $results = [];
            foreach ($data['organic_results'] ?? [] as $item) {
                $results[] = [
                    'title' => $item['title'] ?? '',
                    'snippet' => $item['snippet'] ?? '',
                    'link' => $item['link'] ?? '',
                ];
            }

            return $results ?: [['info' => 'No organic results found for this query.']];

        } catch (HttpExceptionInterface $e) {
            // HTTP 错误（如 403、429、500）
            return [['error' => 'SerpAPI HTTP Error: ' . $e->getMessage()]];
        } catch (TransportExceptionInterface $e) {
            // 网络错误（超时、DNS 失败等）
            return [['error' => 'Network Error: ' . $e->getMessage()]];
        } catch (\Throwable $e) {
            // 其他异常
            return [['error' => 'Unexpected error: ' . $e->getMessage()]];
        }
    }
}