# Runbook de Auditoria Web Transversal - PoC

Objetivo:
- comprovar trilha de auditoria transversal nas rotas web modernas para reforcar o requisito 1.3.21.

## 1. Componentes implementados

- `config/web_audit.php`
- `app/Services/Auth/WebAuditTrailService.php`
- `app/Http/Middleware/WebAuditTrailMiddleware.php`
- `config/logging.php` (canal `web_audit`)
- `routes/web.php` (middleware `webAuditTrail` no grupo autenticado)
- `.env.example` (`WEB_AUDIT_*`)

## 2. Fluxo de validacao

1. Garantir `WEB_AUDIT_ENABLED=true`.
2. Realizar acesso autenticado a pelo menos duas rotas:
- `GET /web/welcome`
- `GET /web/sessions`
3. Executar uma acao `POST` (ex.: revogacao de sessao, quando aplicavel).
4. Verificar geracao de eventos em:
- `storage/logs/web_audit-YYYY-MM-DD.log`

## 3. Evidencias esperadas

- registro com:
  - `request_id` para correlacao ponta a ponta;
  - `user_id`, `login`, `instit`, `session_id`;
  - `method`, `path`, `status`, `duration_ms`, `ip`;
  - `query_keys`/`input_keys` (sem campos sensiveis).
- captura dos logs com pelo menos um GET e um POST.

## 4. Observacoes

- o middleware cobre o escopo web moderno autenticado;
- campos sensiveis (`senha`, `token`, `signature`, `code`) sao filtrados do `input_keys`;
- para ampliar cobertura em legado puro, manter estrategia incremental por modulo.
