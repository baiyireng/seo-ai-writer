# OpenAI API 代理配置说明

## 问题描述

在中国大陆地区，由于网络限制，无法直接访问 OpenAI API。需要通过代理服务器才能正常访问。

## 解决方案

### 1. HTTP 客户端配置

在 `config/packages/framework.yaml` 中添加了代理配置：

```yaml
framework:
    http_client:
        default_options:
            # 中国地区需要配置代理才能访问 OpenAI API
            proxy: '127.0.0.1:7890'
            # 如果代理需要认证，可以使用 'http://user:password@proxy:port' 格式
            cafile: '%kernel.project_dir%/cacert.pem'
```

### 2. 证书配置

下载了最新的 CA 证书包以确保 SSL 连接正常工作：

- 从 https://curl.se/ca/cacert.pem 下载证书包
- 配置 Symfony 使用该证书包进行 SSL 验证

### 3. Doctrine 配置修复

移除了无效的 `ssl_config` 选项，该选项会导致容器编译错误。

## 使用方法

### 环境变量设置

确保在 `.env.local` 文件中正确配置了 API 密钥：

```env
OPENAI_API_KEY=your_api_key_here
```

### 命令行测试

在使用命令行工具时，需要设置环境变量：

```bash
export OPENAI_API_KEY=your_api_key_here
bin/console ai:platform:invoke openai gpt-4o-mini "Your message here"
```

### Web 请求测试

对于 Web 请求，确保代理服务器（如 Clash 或其他代理工具）在 `127.0.0.1:7890` 端口运行。

## 注意事项

1. 确保代理服务器正在运行
2. 根据实际使用的代理软件调整端口号
3. 如果代理需要认证，使用完整 URL 格式：`http://username:password@proxy:port`
4. 定期更新 CA 证书包以确保 SSL 连接安全