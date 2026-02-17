# Runbook PoC - Observabilidade e SLA

Objetivo:
- demonstrar monitoramento basico em tempo real e disponibilidade mensuravel.

## 1. Endpoints de Saude

- `GET /api/health/live`
  - valida se a aplicacao esta respondendo.
- `GET /api/health/ready`
  - valida aplicacao + dependencia de banco.

Resultado esperado:
- `live` retorna HTTP 200.
- `ready` retorna HTTP 200 quando banco esta operacional, ou 503 em degradacao.

## 2. Coleta de Amostras

Executar periodicamente (cron/agendador externo):

```bash
php artisan ops:health-snapshot --base-url=http://localhost:8282 --append-log
```

Saida:
- JSON com status `live` e `ready`.
- amostra gravada em `SLA_SAMPLE_LOG_PATH`.

## 3. Calculo de SLA

Gerar relatorio:

```bash
php artisan ops:sla-report --hours=24
```

Saida:
- total de amostras;
- amostras disponiveis;
- percentual de SLA;
- comparacao contra meta (`SLA_TARGET_PERCENT`).

## 4. Evidencias para PoC

1. Captura do endpoint `/api/health/live`.
2. Captura do endpoint `/api/health/ready`.
3. Trecho do arquivo de amostras.
4. Tabela do comando `ops:sla-report`.

## 5. Configuracoes

Variaveis:
- `SLA_TARGET_PERCENT` (padrao 99.9)
- `SLA_SAMPLE_LOG_PATH` (padrao `storage/logs/sla_samples.log`)

## 6. Limites Conhecidos

- monitoramento atual e baseline de PoC;
- em producao, recomenda-se complementar com dashboard e alertas (NOC/SRE).
