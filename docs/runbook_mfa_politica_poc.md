# Runbook de MFA por Perfil/Grupo/Usuario - PoC

Objetivo:
- demonstrar a ampliacao da politica de MFA para cobertura por perfil, grupo e usuario (requisito 1.3.15.d).

## 1. Componentes implementados

- `config/mfa.php`
- `app/Services/Auth/MfaService.php`
- `app/Http/Middleware/AuthEcidadeUser.php`
- `.env.example` (variaveis `MFA_*`)

## 2. Parametros de politica

- `MFA_ENABLED=true`
- `MFA_ADMINS_ONLY=true` (modo legado)
- `MFA_REQUIRED_USERS=7,8,9`
- `MFA_REQUIRED_GROUPS=financeiro,licitacao`
- `MFA_USER_GROUPS_JSON={"33":["financeiro"]}`
- `MFA_ALLOW_ADMIN_BYPASS=false`

Observacoes:
- `MFA_REQUIRED_USERS` e `MFA_REQUIRED_GROUPS` permitem rollout gradual por risco;
- quando `MFA_REQUIRED_GROUPS` estiver preenchido, somente usuarios desses grupos exigem MFA;
- sem regras especificas, o fallback permanece no comportamento atual (`MFA_ADMINS_ONLY`).

## 3. Cenario de validacao para PoC

1. Habilitar MFA no ambiente.
2. Configurar `MFA_REQUIRED_USERS` com um usuario de teste.
3. Realizar login desse usuario:
- esperado: redirecionamento para desafio MFA.
4. Configurar `MFA_REQUIRED_GROUPS` e mapear usuario em `MFA_USER_GROUPS_JSON`.
5. Realizar login:
- esperado: desafio MFA exigido para membro do grupo.
6. Testar usuario fora do grupo:
- esperado: sem desafio MFA quando regra de grupo estiver ativa.
7. Registrar evidencias de tela e log.

## 4. Evidencias esperadas

- captura de tela do desafio MFA;
- evidencias de sucesso/falha de validacao do codigo;
- log `MFA code issued` com identificador do usuario;
- aceite funcional do avaliador da PoC.
