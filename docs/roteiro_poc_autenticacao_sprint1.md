# Roteiro PoC - Autenticacao (Sprint 1)

Objetivo:
- demonstrar aderencia minima de autenticacao para os requisitos 1.3.14 e 1.3.15.d;
- apresentar evidencias tecnicas replicaveis para avaliacao da PoC.

## 1. Preparacao de Ambiente

1. Configurar variaveis no `.env`:
   - `AUTH_USERS_PROVIDER=legacy`
   - `MFA_ENABLED=true`
   - `MFA_ADMINS_ONLY=true`
2. Garantir usuario de teste:
   - um usuario admin com CPF em `protocolo.cgm.z01_cgccpf`;
   - e-mail valido em `configuracoes.db_usuarios.email` ou `protocolo.cgm.z01_email`.
3. Executar verificacao rapida:

```bash
php -l app/Providers/Auth/LegacyUserProvider.php
php -l app/Http/Controllers/Auth/LoginController.php
php -l app/Http/Controllers/Auth/ForgotPasswordController.php
php -l app/Http/Controllers/Auth/ResetPasswordController.php
php -l app/Services/Auth/MfaService.php
```

## 2. Cenario A - Login por CPF

1. Efetuar login usando CPF (com ou sem mascara) no campo `login`.
2. Confirmar autenticao bem-sucedida.

Evidencias esperadas:
- usuario localizado por CPF via `LegacyUserProvider`;
- log `Authentication succeeded` com `user_id`, `login`, `ip`.

## 3. Cenario B - MFA Obrigatorio (perfil critico)

1. Com usuario admin autenticado e `MFA_ENABLED=true`, acessar rota protegida `/web/welcome`.
2. Confirmar redirecionamento para `/web/mfa/challenge`.
3. Informar codigo MFA valido e concluir autenticacao.

Evidencias esperadas:
- log `MFA code issued`;
- bloqueio de acesso ate validacao do segundo fator;
- acesso liberado apos `verify` bem-sucedido.

## 4. Cenario C - Bloqueio Progressivo

1. Realizar multiplas tentativas de login com senha invalida para o mesmo `login+ip`.
2. Confirmar bloqueios temporarios crescentes.

Evidencias esperadas:
- log `Authentication failed` por tentativa;
- resposta de validacao: `Acesso temporariamente bloqueado...`;
- parametros controlados por:
  - `AUTH_LOGIN_BLOCK_PRIMARY_SECONDS`
  - `AUTH_LOGIN_BLOCK_SECONDARY_SECONDS`
  - `AUTH_LOGIN_BLOCK_TERTIARY_SECONDS`

## 5. Cenario D - Recuperacao de Senha sem Exposicao

1. Solicitar reset com identificador valido.
2. Solicitar reset com identificador inexistente.
3. Comparar resposta da interface/API.

Evidencias esperadas:
- mesma mensagem funcional para ambos os casos (sem enumeracao de usuarios);
- log tecnico para tentativas sem conta valida.

## 6. Cenario E - Rehash Seguro de Senha Legada

1. Preparar usuario com hash legado `md5(sha1(...))`.
2. Autenticar com senha correta.
3. Conferir que o hash passou a formato moderno.

Evidencias esperadas:
- autenticacao bem-sucedida;
- atualizacao de `senha` para hash moderno;
- formatos desconhecidos de hash nao autenticam.

## 7. Evidencias para Entrega

1. Capturas de tela:
   - login com CPF;
   - tela de MFA;
   - bloqueio progressivo.
2. Trecho de logs com:
   - `Authentication failed`
   - `Authentication succeeded`
   - `MFA code issued`
3. Lista de arquivos alterados na sprint:
   - `app/Providers/Auth/LegacyUserProvider.php`
   - `app/Http/Controllers/Auth/LoginController.php`
   - `app/Http/Controllers/Auth/ForgotPasswordController.php`
   - `app/Http/Controllers/Auth/ResetPasswordController.php`
   - `app/Services/Auth/MfaService.php`
   - `docs/sprint1_checklist_execucao.md`

## 8. Limites Conhecidos da Sprint 1

- Integracoes externas (Google, GOVBR, A1/A3) permanecem para fase seguinte.
- MFA atual cobre fluxo principal com politica por perfil; expansoes de canais de entrega ficam para sprint posterior.
