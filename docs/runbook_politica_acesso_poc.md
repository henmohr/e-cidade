# Runbook de Politica de Acesso (Dia/Horario/Grupo/Expiracao) - PoC

Objetivo:
- demonstrar o controle de acesso por janela de tempo e perfil, conforme requisito 1.3.28.

## 1. Componentes implementados

- `config/auth_access.php`
- `app/Services/Auth/AccessPolicyService.php`
- `app/Http/Middleware/AuthEcidadeUser.php`
- `.env.example` (variaveis `AUTH_ACCESS_*`)

## 2. Parametros principais

- `AUTH_ACCESS_POLICY_ENABLED=true`
- `AUTH_ACCESS_POLICY_TIMEZONE=America/Sao_Paulo`
- `AUTH_ACCESS_POLICY_ALLOW_ADMIN_BYPASS=false`
- `AUTH_ACCESS_DEFAULT_ALLOWED_WEEKDAYS=1,2,3,4,5`
- `AUTH_ACCESS_DEFAULT_START_TIME=08:00`
- `AUTH_ACCESS_DEFAULT_END_TIME=18:00`
- `AUTH_ACCESS_GROUP_RULES_JSON={...}`
- `AUTH_ACCESS_USER_RULES_JSON={...}`
- `AUTH_ACCESS_USER_GROUPS_JSON={...}`

Observacoes:
- dias da semana aceitos: `1..7` (domingo pode ser `0` ou `7`);
- suporte a janela que cruza madrugada (`22:00` ate `06:00`);
- regra por usuario tem precedencia sobre regra de grupo.

## 3. Cenario de validacao para PoC

1. Habilitar politica no ambiente de homologacao.
2. Configurar regra default de horario comercial.
3. Tentar acesso dentro do horario permitido:
- esperado: acesso autorizado.
4. Tentar acesso fora do horario permitido:
- esperado: bloqueio com mensagem de politica e log de negacao.
5. Configurar expiracao para um usuario de teste em `AUTH_ACCESS_USER_RULES_JSON`.
6. Tentar acesso apos a data de expiracao:
- esperado: bloqueio por expiracao.
7. (Opcional) Configurar `AUTH_ACCESS_POLICY_ALLOW_ADMIN_BYPASS=true` e validar excecao para admin.

## 4. Evidencias esperadas

- captura da configuracao ativa;
- captura de acesso permitido;
- captura de acesso bloqueado;
- registro em log `Access denied by access policy` contendo usuario/IP/motivo.
