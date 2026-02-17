# Sprint 3 - Checklist Executavel (HA, Observabilidade e SLA)

Base:
- `docs/plano_execucao_sprints_poc.md`
- `docs/matriz_gap_poc_100.md`

Objetivo da sprint:
- entregar evidencias operacionais para disponibilidade e monitoramento da PoC.

Escopo tecnico principal:
- endpoints de saude (live/ready);
- coleta periodica de amostras;
- relatorio de SLA baseado em dados;
- runbook de operacao e evidencias.

## Arquivos/Componentes-Chave

- `routes/api.php`
- `app/Http/Controllers/HealthController.php`
- `app/Console/Commands/OpsHealthSnapshot.php`
- `app/Console/Commands/OpsSlaReport.php`
- `config/observability.php`
- `docs/runbook_observabilidade_sla_poc.md`

## Checklist (S3-xx)

## Dia 1-3: Sinalizacao de Saude

- [x] `S3-01` Implementar endpoint `live` para status basico da aplicacao.
- [x] `S3-02` Implementar endpoint `ready` com validacao de dependencia de banco.

## Dia 3-6: Coleta Operacional

- [x] `S3-03` Criar comando para snapshot de saude com opcao de log.
- [x] `S3-04` Definir arquivo de amostras e meta de SLA por configuracao.

## Dia 6-8: Relatorio de SLA

- [x] `S3-05` Criar comando para calcular disponibilidade por janela.
- [x] `S3-06` Padronizar saida para evidencias de PoC.

## Dia 8-10: Fechamento

- [x] `S3-07` Publicar runbook de observabilidade e SLA.
- [x] `S3-08` Atualizar matriz de gaps com status pos-sprint.
- [x] `S3-09` Habilitar coleta automatizada via scheduler com configuracao por ambiente.
- [x] `S3-10` Persistir relatorio de SLA para trilha historica de evidencias.

## Comandos de Verificacao

```bash
php -l app/Http/Controllers/HealthController.php
php -l app/Console/Commands/OpsHealthSnapshot.php
php -l app/Console/Commands/OpsSlaReport.php
php artisan ops:health-snapshot --base-url=http://localhost:8282 --append-log
php artisan ops:sla-report --hours=24
php artisan ops:sla-report --hours=24 --format=json --append-log
php artisan schedule:work
```

## Critério de Pronto da Sprint 3 (incremental)

- endpoints de saude ativos;
- coleta e relatorio de SLA reproduziveis;
- runbook com evidencias para auditoria da PoC.
