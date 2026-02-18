# Matriz de Aderencia Legal - Sprint 7 (Integracoes e Conformidade)

## Objetivo
Consolidar os requisitos legais minimos e suas evidencias tecnicas para o nucleo financeiro, com foco em Lei 4.320/1964, LRF, LAI, NBCT 16, SICONFI e TCU/TCE.

## Matriz

| Referencia legal/normativa | Requisito operacional minimo | Evidencia tecnica atual | Gap/acao de continuidade |
|---|---|---|---|
| Lei 4.320/1964 | Registro e demonstracao de execucao orcamentaria e financeira | Validacoes de ciclo da despesa e servicos de consolidacao (`app/Services/Financeiro/ExecucaoOrcamentaria`, `app/Services/Financeiro/Receita`) | Fechar exportacao oficial de todos demonstrativos obrigatorios |
| LRF (LC 101/2000) | Controles fiscais e bloqueios por inconsistencias de execucao | Bloqueios em empenho/liquidacao/pagamento e monitoramento de restos a pagar | Evoluir para alertas parametrizados por limites de pessoal/endividamento |
| LAI | Publicacao ativa de receitas, despesas e contratos | Servico de publicacao do portal de transparencia (`app/Services/Financeiro/Integracao/PublicacaoPortalTransparenciaService.php`) | Integrar endpoint/canal oficial do portal municipal em producao |
| NBCT 16 | Consistencia contabil no setor publico | Base contabil existente e trilhas de validacao no nucleo financeiro | Expandir cobertura automatizada para demonstracoes DVP/DFC/DMPL |
| SICONFI/STN | Fluxo de envio e controle de retorno | Trilha de status de integracoes com estados: pendente/enviado/aceito/rejeitado (`app/Services/Financeiro/Integracao/IntegracaoGovernamentalStatusService.php`) | Conector homologado por layout vigente e protocolo externo definitivo |
| TCU/TCE | Prestacao de contas e rastreabilidade de transmissao | Monitoramento de falhas e reprocessamento de integracoes (`financeiro:monitorar-integracoes`) | Completar adaptadores especificos por UF e validar recibo de entrega |

## Evidencias de sprint
- Trilhas de status de integracao implementadas com reprocessamento.
- Publicacao consolidada para portal da transparencia com resumo financeiro.
- Testes unitarios dos novos servicos de integracao/conformidade.

## Proximos passos
1. Acoplar conectores reais de SICONFI e TCE/UF na mesma trilha de status.
2. Versionar leiautes aceitos por orgao externo e registrar protocolo de envio/retorno.
3. Integrar dashboard executivo com indicadores de conformidade legal (SLA, rejeicoes, reprocessamentos).
