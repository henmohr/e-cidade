# Runbook de Identidade Externa (GOVBR/Google/A1-A3) - PoC

Objetivo:
- demonstrar integracao incremental de identidade externa com vinculo ao usuario interno por CPF/login (requisito 1.3.14).

## 1. Componentes implementados

- `config/external_identity.php`
- `app/Services/Auth/ExternalIdentityService.php`
- `app/Http/Controllers/Auth/ExternalIdentityController.php`
- `routes/web.php` (`/web/idp/providers` e `/web/idp/callback`)
- `app/Http/Middleware/VerifyCsrfToken.php` (excecao para callback externo)
- `.env.example` (`AUTH_EXTERNAL_*`)

## 2. Parametros principais

- `AUTH_EXTERNAL_ENABLED=true`
- `AUTH_EXTERNAL_ALLOWED_PROVIDERS=govbr,google,a1,a3`
- `AUTH_EXTERNAL_PROVIDER_SECRETS_JSON={"govbr":"segredo","google":"segredo2"}`
- `AUTH_EXTERNAL_ALLOW_UNSIGNED=false` (apenas PoC controlada pode usar `true`)
- `AUTH_EXTERNAL_DEFAULT_INSTIT=1`
- `AUTH_EXTERNAL_REDIRECT_PATH=/web/welcome`

## 3. Contrato de callback (PoC)

Endpoint:
- `POST /web/idp/callback`

Protecoes ativas:
- rate limit por provedor+IP (`throttle:external-idp`);
- validacao de assinatura (quando habilitada);
- validacao de expiracao (`expires_at`);
- protecao anti-replay por `nonce`.

Campos esperados:
- `provider` (ex.: `govbr`)
- `payload` (JSON string com claims)
- `signature` (opcional se enviada em header)

Header alternativo:
- `X-Identity-Signature: <hmac_sha256>`

Claims minimas no payload:
- `provider`
- `cpf` ou `login`
- `subject` (id externo)
- `expires_at` (obrigatorio quando `AUTH_EXTERNAL_ENFORCE_CLAIMS_EXPIRATION=true`)
- `nonce` (obrigatorio quando `AUTH_EXTERNAL_ENFORCE_NONCE=true`)

## 4. Cenario de validacao

1. Habilitar integracao externa no ambiente.
2. Consultar `GET /web/idp/providers`.
3. Enviar callback assinado para usuario conhecido por CPF/login.
4. Confirmar redirecionamento para `/web/welcome`.
5. Confirmar sessao legado preenchida (`DB_id_usuario`, `DB_login`).
6. Confirmar logs de sucesso/negacao.
7. Repetir callback com mesmo `nonce`:
- esperado: bloqueio por replay (`409`).
8. Disparar varias chamadas consecutivas acima do limite:
- esperado: bloqueio por rate limit (`429`).

## 5. Evidencias esperadas

- chamada de callback com assinatura valida;
- retorno de sucesso e redirecionamento;
- log `External identity login succeeded`;
- log de negacao para assinatura invalida (teste negativo).
