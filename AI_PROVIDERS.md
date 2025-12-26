# Symfony AI 支持的 AI 供应商和措施

## 概述

Symfony AI Bundle 提供了一个统一的接口来与多个 AI 供应商交互。它支持多种大型语言模型（LLM）供应商，允许开发者根据地区限制、成本和性能需求灵活切换。

## 支持的 AI 供应商

### 1. OpenAI
- **供应商名称**: OpenAI
- **模型支持**:
  - `gpt-4o`, `gpt-4o-mini`
  - `gpt-4`, `gpt-4-turbo`
  - `gpt-3.5-turbo`
- **配置示例**:
  ```yaml
  ai:
      platform:
          openai:
              api_key: '%env(OPENAI_API_KEY)%'
  ```
- **地区限制**: 在中国大陆等地区可能需要代理访问
- **优势**: 模型质量高，功能丰富
- **劣势**: 网络访问受限，成本较高

### 2. Anthropic (Claude)
- **供应商名称**: Anthropic
- **模型支持**:
  - `claude-3-5-sonnet-20241022`
  - `claude-3-opus-20240229`
  - `claude-3-sonnet-20240229`
  - `claude-3-haiku-20240307`
- **配置示例**:
  ```yaml
  ai:
      platform:
          anthropic:
              api_key: '%env(ANTHROPIC_API_KEY)%'
  ```
- **地区限制**: 在中国大陆等地区可能需要代理访问
- **优势**: 长文本处理能力强，输出质量高
- **劣势**: 网络访问受限，成本较高

### 3. Google AI (Vertex AI / Gemini)
- **供应商名称**: Google
- **模型支持**:
  - `gemini-pro`
  - `gemini-1.5-pro`
  - `gemini-1.5-flash`
- **配置示例**:
  ```yaml
  ai:
      platform:
          google:
              api_key: '%env(GOOGLE_AI_API_KEY)%'
  ```
- **地区限制**: 在中国大陆等地区可能需要代理访问
- **优势**: 多模态能力强，免费额度充足
- **劣势**: 网络访问受限

### 4. Mistral AI
- **供应商名称**: Mistral AI
- **模型支持**:
  - `mistral-large-latest`
  - `mistral-medium`
  - `mistral-small-latest`
- **配置示例**:
  ```yaml
  ai:
      platform:
          mistral:
              api_key: '%env(MISTRAL_API_KEY)%'
  ```
- **地区限制**: 在中国大陆等地区可能需要代理访问
- **优势**: 欧洲供应商，价格合理
- **劣势**: 网络访问受限

### 5. Hugging Face (本地/远程模型)
- **供应商名称**: Hugging Face
- **模型支持**: 众多开源模型
- **配置示例**:
  ```yaml
  ai:
      platform:
          huggingface:
              api_key: '%env(HUGGINGFACE_API_KEY)%'
  ```
- **地区限制**: 在中国大陆等地区可能需要代理访问
- **优势**: 开源模型丰富，可本地部署
- **劣势**: 网络访问受限，性能因模型而异

## 地区网络访问解决方案

### 中国大陆用户解决方案

#### 1. 代理配置
在 `config/packages/framework.yaml` 中配置代理：

```yaml
framework:
    http_client:
        default_options:
            # 中国地区需要配置代理才能访问海外 AI API
            proxy: '127.0.0.1:7890'  # 代理服务器地址和端口
            # 如果代理需要认证，使用 'http://user:password@proxy:port' 格式
            cafile: '%kernel.project_dir%/cacert.pem'  # CA 证书路径
```

#### 2. 替代方案

##### A. 本地模型部署
- **Ollama**: 本地部署开源模型（如 Llama3, Mistral 等）
- **配置示例**:
  ```yaml
  ai:
      platform:
          ollama:
              base_uri: 'http://localhost:11434'
  ```

##### B. 国内 AI 供应商
- **百度文心一言**: 需要注册百度云账号
- **阿里通义千问**: 需要注册阿里云账号
- **腾讯混元**: 需要注册腾讯云账号
- **字节豆包**: 需要注册字节跳动云账号

#### 3. 代理工具推荐
- **Clash**: 支持多种代理协议
- **v2ray**: 专业代理工具
- **Shadowsocks**: 经典代理工具

## 配置切换示例

### 1. 开发环境配置 (`config/packages/dev/ai.yaml`)
```yaml
ai:
    platform:
        openai:
            api_key: '%env(OPENAI_API_KEY)%'
```

### 2. 生产环境配置 (`config/packages/prod/ai.yaml`)
```yaml
ai:
    platform:
        openai:
            api_key: '%env(OPENAI_API_KEY)%'
        # 或者使用备用供应商
        mistral:
            api_key: '%env(MISTRAL_API_KEY)%'
```

### 3. 环境特定配置 (`config/packages/ai.yaml`)
```yaml
ai:
    platform:
        # 根据环境变量选择供应商
        # 可以同时配置多个供应商作为备用
        openai:
            api_key: '%env(OPENAI_API_KEY)%'
        anthropic:
            api_key: '%env(ANTHROPIC_API_KEY)%'
        mistral:
            api_key: '%env(MISTRAL_API_KEY)%'
```

## 供应商切换策略

### 1. 故障转移策略
```yaml
ai:
    platform:
        # 优先使用 OpenAI，失败时使用 Anthropic
        openai:
            api_key: '%env(OPENAI_API_KEY)%'
        anthropic:
            api_key: '%env(ANTHROPIC_API_KEY)%'
        mistral:
            api_key: '%env(MISTRAL_API_KEY)%'
```

### 2. 地区化策略
- **中国用户**: 优先使用本地模型或国内供应商
- **海外用户**: 可直接使用国际供应商
- **混合环境**: 根据网络可达性自动切换

## 本地部署方案

### 1. Ollama 本地部署
- 安装 Ollama: https://ollama.ai
- 拉取模型: `ollama pull llama3`
- 配置:
  ```yaml
  ai:
      platform:
          ollama:
              base_uri: 'http://localhost:11434'
  ```

### 2. 本地模型优势
- 无需网络访问
- 数据隐私安全
- 成本可控
- 无速率限制

## 环境变量设置

### 推荐的 `.env.local` 配置
```env
# OpenAI
OPENAI_API_KEY=your_openai_api_key_here

# Anthropic
ANTHROPIC_API_KEY=your_anthropic_api_key_here

# Google AI
GOOGLE_AI_API_KEY=your_google_ai_api_key_here

# Mistral AI
MISTRAL_API_KEY=your_mistral_api_key_here

# Hugging Face
HUGGINGFACE_API_KEY=your_huggingface_api_key_here

# Ollama (本地)
OLLAMA_BASE_URI=http://localhost:11434
```

## 供应商选择建议

### 根据地区选择
- **中国大陆**: 优先考虑本地模型或国内供应商
- **海外**: 可使用国际供应商
- **混合环境**: 配置多个供应商作为备用

### 根据需求选择
- **成本敏感**: 本地模型或 Mistral AI
- **质量优先**: OpenAI 或 Anthropic
- **长文本处理**: Anthropic Claude
- **多模态**: Google Gemini

## 故障排除

### 常见问题
1. **网络访问受限**: 使用代理或切换到本地模型
2. **API 密钥无效**: 检查密钥是否正确且有权限
3. **模型不可用**: 检查模型名称和供应商权限
4. **速率限制**: 实现重试逻辑或切换到备用供应商

### 调试方法
```bash
# 检查环境变量
bin/console debug:container --env-vars

# 测试供应商连接
bin/console ai:platform:invoke openai gpt-3.5-turbo "Hello"
```

## 总结

Symfony AI Bundle 提供了灵活的多供应商支持，允许根据地区限制和需求选择最适合的 AI 供应商。对于中国大陆用户，建议优先考虑本地模型部署或国内供应商，以确保服务的稳定性和可访问性。