# Checklist Executavel da Sprint 7 - Nucleo Financeiro (Modulos 3, 4 e 5)

Objetivo:
- transformar os requisitos dos modulos de Execucao Orcamentaria, Tesouraria e Controle de Despesas/Receitas em entregas implementaveis, testaveis e auditaveis.

Base de requisitos:
- `docs/requisitos_modulo3_execucao_orcamentaria.md`
- `docs/requisitos_modulo4_tesouraria_fluxo_caixa.md`
- `docs/requisitos_modulo5_controle_despesas_receitas.md`
- `docs/requisitos_integracoes_conformidade_legal.md`
- `docs/requisitos_relatorios_dashboards.md`

Janela sugerida:
- 10 dias uteis.

## Frente A - Modulo 3: Execucao Orcamentaria

- [x] Criar base tecnica de validacao de ciclo da despesa (service + repository + command + testes).
- [x] Ampliar comando de validacao para evidenciar cenario negativo de bloqueio esperado por empenho/ordem.
- [x] Integrar validacao de ciclo em fluxos modernos ativos (EmpEmpenhoService, EmpVeiculosService e UpdateAdesaoRegPrecos).
- [x] Habilitar guard de liquidacao/pagamento em models modernos (Empnota e Empord) com flag de configuracao.
- [x] Aplicar validacao de ciclo nos pontos legados centrais de gravacao (classes `db_empnota_classe.php`, `db_pagordem_classe.php`, `db_empord_classe.php`).
- [x] Implementar validacao de sequencia obrigatoria: Fixacao -> Empenho -> Liquidacao -> Pagamento.
- [x] Bloquear liquidacao sem empenho correspondente.
- [x] Bloquear pagamento sem liquidacao valida.
- [x] Validar saldo/disponibilidade de dotacao no empenho.
- [x] Implementar trilha de auditoria para eventos de empenho, liquidacao e pagamento.

Evidencias minimas:
- fluxo completo executado em homologacao;
- um cenario bloqueado por violacao de sequencia;
- log de auditoria por etapa.

## Frente B - Modulo 4: Tesouraria e Fluxo de Caixa

- [x] Implementar conciliacao bancaria diaria com status de pendencias.
- [x] Implementar previsao de fluxo de caixa de 7 dias.
- [x] Implementar programacao financeira com bloqueio por insuficiencia de caixa projetada.
- [x] Implementar controle de restos a pagar (processado e nao processado).
- [x] Exibir dashboard de tesouraria com saldo atual, projecao e alertas.

Evidencias minimas:
- conciliacao com ao menos uma divergencia tratada;
- cenario de bloqueio por falta de caixa projetado;
- captura do dashboard com data/hora de atualizacao.

## Frente C - Modulo 5: Controle de Despesas e Receitas

- [x] Validar cadastro de credores (CNPJ/CPF e pendencias documentais).
- [x] Implementar fluxo minimo: credor -> empenho -> atesto -> pagamento.
- [x] Implementar calculo de retencoes tributarias (IRRF, ISS, INSS e demais quando aplicavel).
- [x] Implementar classificacao obrigatoria de receitas correntes e de capital.
- [x] Implementar controle de receitas tributarias e transferencias intergovernamentais.

Evidencias minimas:
- um fluxo de despesa completo com retencoes;
- um fluxo de receita corrente e um de receita de capital;
- relatorio consolidado de despesas/receitas.

## Frente D - Integracoes e Conformidade Legal

- [x] Criar matriz de aderencia legal (Lei 4.320, LRF, LAI, NBCT 16, SICONFI, TCU/TCE).
- [x] Implementar trilha de status para envio de integracoes (pendente, enviado, aceito, rejeitado).
- [x] Implementar publicacao automatica no Portal da Transparencia (receitas, despesas e contratos).
- [x] Implementar monitoramento minimo de falhas/reprocessamento de integracoes.

Evidencias minimas:
- protocolo de envio/retorno (ou mock homologado) para SICONFI/STN;
- registro de publicacao no portal com trilha de auditoria;
- relatorio de falhas e reprocessamento.

## Frente E - Relatorios e Dashboard Executivo

- [x] Gerar Balanco Patrimonial, Balanco Orcamentario e Balanco Financeiro.
- [x] Gerar DVP e DFC com consistencia de totais.
- [x] Gerar RGF e RREO conforme periodicidade configurada.
- [x] Disponibilizar dashboard executivo (receitas, despesas, execucao orcamentaria, alertas).
- [x] Habilitar exportacao minima em PDF e planilha.

Evidencias minimas:
- arquivos exportados de cada relatorio obrigatorio;
- dashboard com dados reais de homologacao;
- validacao de consistencia entre dashboard e relatorios.

## Definicao de Pronto da Sprint 7

A sprint e considerada concluida quando:
1. todas as frentes A-E tiverem checklist com evidencias objetivas;
2. bloqueios de regra critica estiverem demonstrados em homologacao;
3. artefatos de auditoria e exportacao estiverem anexados;
4. `docs/README.md` e a matriz de gaps estiverem atualizados com o novo status.

## Riscos e dependencias externas

- homologacao de integracoes com orgaos externos pode exigir janelas do contratante;
- variacao de layout de envio (TCE/TCU) por jurisdicao;
- necessidade de massa de dados representativa para validar relatorios consolidados.
