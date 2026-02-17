# Sprint 4 - Checklist Executavel (Auditoria, Sessoes e Governanca)

Base:
- `docs/plano_execucao_sprints_poc.md`
- `docs/matriz_gap_poc_100.md`

Objetivo da sprint:
- consolidar rastreabilidade de autenticacao e gestao de sessoes.

Escopo tecnico principal:
- tela de sessoes ativas por usuario;
- encerramento de sessao remota;
- bloqueio de sessao revogada;
- evidencias de log para auditoria.

## Arquivos/Componentes-Chave

- `app/Services/Auth/SessionActivityService.php`
- `app/Http/Controllers/Auth/SessionController.php`
- `app/Http/Middleware/AuthEcidadeUser.php`
- `resources/views/auth/sessions.blade.php`
- `routes/web.php`

## Checklist (S4-xx)

## Dia 1-3: Sessao Ativa

- [x] `S4-01` Registrar atividade de sessao por usuario autenticado.
- [x] `S4-02` Expor tela de consulta de sessoes ativas.

## Dia 3-6: Encerramento de Sessao

- [x] `S4-03` Implementar encerramento remoto de sessao.
- [x] `S4-04` Bloquear acesso de sessao revogada no middleware.

## Dia 6-8: Auditoria

- [x] `S4-05` Registrar log de revogacao de sessao com contexto.
- [ ] `S4-06` Cobrir trilha de auditoria em outros fluxos criticos da sprint.

## Dia 8-10: Fechamento

- [x] `S4-07` Publicar checklist/runbook da sprint.
- [x] `S4-08` Atualizar matriz de gaps com status pos-sprint.

## Comandos de Verificacao

```bash
php -l app/Services/Auth/SessionActivityService.php
php -l app/Http/Controllers/Auth/SessionController.php
php -l app/Http/Middleware/AuthEcidadeUser.php
php -l routes/web.php
```

## Criterio de Pronto da Sprint 4 (incremental)

- usuario consulta sessoes ativas;
- usuario encerra sessoes remotas;
- sessao revogada nao continua autenticada;
- logs de revogacao prontos para evidencia de PoC.
