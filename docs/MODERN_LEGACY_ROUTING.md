# Sistema de Roteamento Moderno/Legado

Este documento explica como funciona o sistema de roteamento que permite a convivência de código moderno (Laravel) e código legado (PHP procedural) no e-Cidade.

## 📋 Visão Geral

O sistema usa um **Middleware Laravel** (`LegacyProxyMiddleware`) que intercepta todas as requisições HTTP e decide se elas devem ser processadas pelo código moderno ou pelo código legado.

### Arquitetura

```
Requisição HTTP
     ↓
LegacyProxyMiddleware
     ↓
   ┌─────────────┐
   │  Decisão    │
   └──────┬──────┘
          │
     ┌────┴─────┐
     │          │
  Moderno    Legado
  (Laravel)  (PHP files)
```

## 🚀 Como Usar

### 1. Listar Features Disponíveis

```bash
docker exec -it e-cidade-web-1 php artisan feature:flag list
```

Output:
```
Feature Flags:
+----------------+------------+-----------+---------------------------+
| Feature        | Status     | Rollout % | Description               |
+----------------+------------+-----------+---------------------------+
| api_v2         | ✓ Enabled  | N/A       | Nova API REST v2          |
| nova_interface | ✗ Disabled | 0         | Nova interface do usuário |
| dashboard_novo | ✗ Disabled | N/A       | Novo dashboard            |
+----------------+------------+-----------+---------------------------+
```

### 2. Habilitar uma Feature

```bash
docker exec -it e-cidade-web-1 php artisan feature:flag enable nova_interface
```

### 3. Desabilitar uma Feature

```bash
docker exec -it e-cidade-web-1 php artisan feature:flag disable nova_interface
```

### 4. Rollout Gradual (Canary Release)

Liberar para 10% dos usuários:
```bash
docker exec -it e-cidade-web-1 php artisan feature:flag rollout nova_interface 10
```

Liberar para 50% dos usuários:
```bash
docker exec -it e-cidade-web-1 php artisan feature:flag rollout nova_interface 50
```

Liberar para 100% (todos):
```bash
docker exec -it e-cidade-web-1 php artisan feature:flag rollout nova_interface 100
```

### 5. Verificar Status de uma Feature

```bash
docker exec -it e-cidade-web-1 php artisan feature:flag status api_v2
```

## 📁 Estrutura de Arquivos

```
app/
├── Http/
│   ├── Middleware/
│   │   └── LegacyProxyMiddleware.php    # Middleware principal
│   └── Controllers/
│       └── Api/
│           └── V2/
│               └── ExampleController.php # Controllers modernos
├── Services/
│   └── FeatureFlag.php                   # Serviço de feature flags
└── Console/
    └── Commands/
        └── FeatureFlagCommand.php        # Comando CLI

config/
└── modern_routes.php                      # Configuração de rotas

routes/
└── api.php                                # Rotas modernas
```

## 🎯 Criando Novas Rotas Modernas

### Passo 1: Adicionar Padrão no Config

Edite `config/modern_routes.php`:

```php
'modern_patterns' => [
    'api/v2/*',
    'nova-interface/*',
    'empenho/novo/*',  // ← Nova rota
],
```

### Passo 2: Criar Feature Flag (Opcional)

```php
'features' => [
    'empenho_novo' => [
        'enabled' => false,
        'description' => 'Novo módulo de empenho',
        'routes' => ['empenho/novo/*'],
        'rollout_percentage' => 0,
    ],
],
```

### Passo 3: Criar Controller

```bash
docker exec -it e-cidade-web-1 php artisan make:controller Empenho/NovoEmpenhoController
```

### Passo 4: Adicionar Rotas

Em `routes/web.php` ou `routes/api.php`:

```php
Route::prefix('empenho/novo')->group(function () {
    Route::get('/', [NovoEmpenhoController::class, 'index']);
    Route::post('/', [NovoEmpenhoController::class, 'store']);
});
```

### Passo 5: Habilitar Feature

```bash
docker exec -it e-cidade-web-1 php artisan feature:flag enable empenho_novo
```

## 🔄 Fluxo de Migração Recomendado

### 1. Desenvolvimento

```bash
# Crie a nova feature desabilitada
# Em config/modern_routes.php
'novo_modulo' => [
    'enabled' => false,
    'rollout_percentage' => 0,
]
```

### 2. Testes Internos

```bash
# Habilite apenas para você (dev)
php artisan feature:flag enable novo_modulo
```

### 3. Beta (10% dos usuários)

```bash
php artisan feature:flag rollout novo_modulo 10
```

### 4. Rollout Gradual

```bash
php artisan feature:flag rollout novo_modulo 25  # 25%
php artisan feature:flag rollout novo_modulo 50  # 50%
php artisan feature:flag rollout novo_modulo 75  # 75%
```

### 5. Release Completo

```bash
php artisan feature:flag rollout novo_modulo 100  # 100%
```

### 6. Remover Feature Flag

Quando estável, remova o feature flag e deixe apenas o código moderno.

## 🧪 Testando

### Testar API Moderna

```bash
# Dentro do container
curl http://localhost/api/v2/example

# De fora
curl http://localhost:8282/api/v2/example
```

Output esperado:
```json
{
    "message": "API v2 funcionando!",
    "type": "modern",
    "data": {
        "version": "2.0.0",
        "timestamp": "2025-11-04 18:00:00"
    }
}
```

### Testar Integração com Banco Legado

```bash
curl http://localhost:8282/api/v2/legacy-integration
```

### Testar Rota Legada

```bash
curl http://localhost:8282/login.php
```

Deve retornar a página de login do sistema legado.

## 📊 Logs e Debugging

### Habilitar Logs de Roteamento

Em `.env`:
```env
LOG_ROUTE_DECISIONS=true
```

### Ver Logs

```bash
docker exec -it e-cidade-web-1 tail -f storage/logs/laravel.log
```

Você verá logs como:
```
[2025-11-04 18:00:00] debug: Route decision
{
    "path": "api/v2/example",
    "type": "modern",
    "reason": "Matches modern pattern: api/v2/*"
}
```

## ⚙️ Configurações Avançadas

### Compartilhamento de Sessão

O middleware automaticamente compartilha a sessão Laravel com o código legado PHP:

```php
// Em config/modern_routes.php
'session' => [
    'share_with_legacy' => true,
    'legacy_session_name' => 'PHPSESSID',
],
```

### Desabilitar o Sistema Temporariamente

```env
MODERN_ROUTES_ENABLED=false
```

Isso faz com que todas as requisições sejam processadas normalmente pelo Laravel/legado sem interceptação.

## 🚨 Troubleshooting

### Erro: "Legacy file not found"

**Problema**: O middleware não encontrou o arquivo PHP legado.

**Solução**: Verifique se o arquivo existe em:
- `/path/to/file.php`
- `/resources/legacy/path/to/file.php`

### Feature Flag Não Funciona

**Solução**: Limpe o cache:
```bash
docker exec -it e-cidade-web-1 php artisan cache:clear
docker exec -it e-cidade-web-1 php artisan config:clear
```

### Sessão Não Compartilhada

**Problema**: Dados da sessão não aparecem no código legado.

**Solução**: Verifique se o middleware `StartSession` está rodando antes do `LegacyProxyMiddleware` no `app/Http/Kernel.php`.

## 📚 Exemplos Práticos

### Exemplo 1: Migrar Módulo de Relatórios

```php
// 1. Adicionar em config/modern_routes.php
'relatorios_novo' => [
    'enabled' => true,
    'routes' => ['relatorios/novo/*'],
],

// 2. Criar controller
php artisan make:controller Relatorios/NovoRelatorioController

// 3. Adicionar rotas em routes/web.php
Route::prefix('relatorios/novo')->group(function () {
    Route::get('/financeiro', [NovoRelatorioController::class, 'financeiro']);
});
```

### Exemplo 2: API REST para Mobile

```php
// Em routes/api.php
Route::prefix('v2/mobile')->group(function () {
    Route::get('/dashboard', [MobileController::class, 'dashboard']);
    Route::post('/sync', [MobileController::class, 'sync']);
});
```

## 🎓 Próximos Passos

1. **Criar Testes**: Adicione testes automatizados para suas rotas modernas
2. **Monitoramento**: Implemente métricas para acompanhar o uso de rotas modernas vs legadas
3. **Documentação API**: Use Swagger/OpenAPI para documentar sua nova API
4. **Frontend Moderno**: Integre Vue.js, React ou outra framework moderna

## 📞 Suporte

Para dúvidas ou problemas, consulte:
- Documentação do Laravel: https://laravel.com/docs
- Issues do e-Cidade: https://github.com/e-cidade/e-cidade/issues
