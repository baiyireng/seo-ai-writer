# SEO AI Writer

一个基于 Symfony 框架和 OpenAI API 的智能 SEO 文章生成器，能够根据关键词自动生成高质量、结构清晰的 SEO 优化文章。

## 功能特性

- 🤖 基于 OpenAI GPT-4o-mini 模型的内容生成
- 📝 自动创建结构化的 SEO 文章（包含标题、H2/H3 结构等）
- 🔧 集成 SearchTool 用于获取实时搜索结果
- 🌐 支持代理配置（适用于中国大陆等网络受限地区）
- 🛠️ 基于 Symfony 7.4 框架构建
- ⚡ 使用 Symfony AI Bundle 统一管理 AI 平台集成

## 技术栈

- **PHP**: 8.3+
- **Symfony**: 7.4.*
- **Symfony AI Bundle**: 用于集成 OpenAI 平台
- **Doctrine ORM**: 数据库管理
- **Twig**: 模板引擎
- **PostgreSQL**: 默认数据库（可配置）
- **HTTP Client**: 用于 API 请求（支持代理配置）

## 环境要求

- PHP 8.3 或更高版本
- Composer
- PostgreSQL 数据库（或可配置的其他数据库）
- OpenAI API 密钥
- 代理服务器（中国大陆用户）

## 安装步骤

### 1. 克隆项目

```bash
git clone <repository-url>
cd seo-ai-writer
```

### 2. 安装依赖

```bash
composer install
```

### 3. 配置环境变量

复制示例环境文件并配置相关参数：

```bash
cp .env .env.local
```

编辑 `.env.local` 文件，添加以下配置：

```env
###> symfony/framework-bundle ###
APP_ENV=dev
APP_SECRET=your_app_secret_here
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
###< doctrine/doctrine-bundle ###

###> symfony/ai-bundle ###
OPENAI_API_KEY=your_openai_api_key_here
SERPAPI_API_KEY=your_serpapi_key_here
###< symfony/ai-bundle ###
```

### 4. 数据库设置

创建数据库并运行迁移：

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### 5. 中国大陆用户特殊配置

由于网络限制，中国大陆用户需要配置代理才能访问 OpenAI API。请参考 [OPENAI_PROXY_SETUP.md](OPENAI_PROXY_SETUP.md) 文件进行配置。

## 配置说明

### HTTP 客户端配置

项目已配置为使用代理访问 OpenAI API（中国大陆用户需要）：

```yaml
# config/packages/framework.yaml
framework:
    http_client:
        default_options:
            proxy: '127.0.0.1:7890'  # 代理服务器地址和端口
            cafile: '%kernel.project_dir%/cacert.pem'  # CA 证书路径
```

### AI 平台配置

AI 平台配置位于 `config/packages/ai.yaml`：

```yaml
ai:
    platform:
        openai:
            api_key: '%env(OPENAI_API_KEY)%'

    agent:
        default:
            platform: 'ai.platform.openai'
            model: 'gpt-4o-mini'
            prompt: |
                You are a professional SEO content writing assistant, skilled at writing clear, readable articles.
            tools: []

        seo_writer:
            model: 'gpt-4o-mini'
            tools: [ 'App\Tool\SearchTool' ]
```

## 使用方法

### 1. Web 端使用

启动开发服务器：

```bash
symfony server:start
```

或使用 PHP 内置服务器：

```bash
php -S localhost:8000 -t public/
```

访问 `http://localhost:8000/generate?keyword=你的关键词` 来生成 SEO 文章。

### 2. 命令行测试

可以使用 Symfony 命令测试 AI 功能：

```bash
# 设置环境变量
export OPENAI_API_KEY=your_api_key_here

# 测试 AI 平台连接
bin/console ai:platform:invoke openai gpt-4o-mini "Hello, world!"

# 测试 AI 代理
bin/console ai:agent:call seo_writer "请写一篇关于人工智能的文章"
```

### 3. API 使用

项目提供 REST API 接口用于文章生成：

```
GET /generate?keyword={关键词}
```

## 项目结构

```
seo-ai-writer/
├── assets/                 # 前端资源
├── config/                 # 配置文件
│   └── packages/           # 包配置
│       ├── ai.yaml         # AI 平台配置
│       ├── framework.yaml  # 框架配置（包含 HTTP 客户端代理设置）
│       └── ...             # 其他配置
├── public/                 # 公共目录
├── src/                    # 应用源码
│   ├── Controller/         # 控制器
│   │   └── AiController.php # AI 生成控制器
│   ├── Service/            # 服务类
│   │   └── SeoArticleGenerator.php # SEO 文章生成服务
│   └── Tool/               # AI 工具
│       └── SearchTool.php   # 搜索工具
├── tests/                  # 测试文件
├── vendor/                 # Composer 依赖
├── .env.local             # 环境配置
├── composer.json          # 项目依赖
└── README.md              # 项目说明
```

## 主要组件

### SeoArticleGenerator 服务

位于 `src/Service/SeoArticleGenerator.php`，负责调用 AI 平台生成结构化的 SEO 文章。

### SearchTool

位于 `src/Tool/SearchTool.php`，集成 SERP API 提供实时搜索功能，增强 AI 生成内容的准确性。

### AiController

位于 `src/Controller/AiController.php`，提供 Web API 接口用于文章生成。

## 开发指南

### 添加新功能

1. 创建新的服务类在 `src/Service/` 目录
2. 如需新工具，创建工具类在 `src/Tool/` 目录并使用 `#[AsTool]` 注解
3. 创建控制器在 `src/Controller/` 目录
4. 更新路由配置（如需要）

### 测试

```bash
# 运行单元测试
php bin/phpunit

# 运行代码质量检查
php bin/console lint:yaml
php bin/console lint:twig
php bin/console lint:container
```

## 故障排除

### 常见问题

1. **SSL 连接错误**：确保使用了正确的 CA 证书配置
2. **API 访问被拒绝**：检查 OpenAI API 密钥是否正确
3. **代理配置问题**：确保代理服务器正在运行且配置正确
4. **速率限制错误**：这是正常现象，表示连接成功但请求过于频繁

### 调试

```bash
# 查看环境变量
bin/console debug:container --env-vars

# 查看路由
bin/console debug:router

# 查看服务
bin/console debug:container
```

## 部署

### 生产环境部署

1. 设置 `APP_ENV=prod`
2. 运行 `composer install --no-dev --optimize-autoloader`
3. 清除并预热缓存：`php bin/console cache:warmup --env=prod`
4. 配置 Web 服务器（Apache/Nginx）指向 `public/` 目录

## 贡献

欢迎提交 Issue 和 Pull Request 来改进这个项目。

## 许可证

[MIT License](LICENSE)

## 支持

如需帮助，请查看：
- [官方 Symfony 文档](https://symfony.com/doc/current/index.html)
- [Symfony AI Bundle 文档](https://github.com/symfony/ai-bundle)
- [OpenAI API 文档](https://platform.openai.com/docs/api-reference)