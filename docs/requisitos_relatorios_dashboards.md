# Requisitos de Desenvolvimento - Relatorios e Dashboards

Data: 2026-02-18
Status: Baseline de requisitos para desenvolvimento incremental

## 1. Objetivo

Definir os requisitos minimos de relatorios obrigatorios e dashboards executivos para suporte a gestao publica, prestacao de contas e evidencias de licitacao.

## 2. Relatorios Obrigatorios

### 2.1 Balanco Patrimonial
- Gerar demonstracao de ativos, passivos e patrimonio liquido.
- Emissao anual conforme Lei 4.320/1964.

### 2.2 Balanco Orcamentario
- Demonstrar receitas e despesas previstas x realizadas.
- Consolidar execucao orcamentaria do exercicio.

### 2.3 Balanco Financeiro
- Demonstrar movimentacao financeira do ente.
- Evidenciar receitas arrecadadas e despesas pagas.

### 2.4 Demonstracao das Variacoes Patrimoniais
- Evidenciar alteracoes patrimoniais do exercicio.
- Seguir normas contabeis aplicadas ao setor publico.

### 2.5 Demonstracao dos Fluxos de Caixa
- Evidenciar entradas e saidas por atividade:
  operacional, investimento e financiamento.

### 2.6 Relatorio de Gestao Fiscal (RGF)
- Emitir demonstrativo quadrimestral conforme LRF.
- Evidenciar metas e limites fiscais relevantes.

### 2.7 Relatorio Resumido da Execucao Orcamentaria (RREO)
- Emitir sintese da execucao orcamentaria em periodicidade legal.
- Disponibilizar versoes mensal e quadrimestral conforme configuracao normativa.

### 2.8 Prestacao de Contas
- Consolidar relatorio anual para envio ao tribunal competente.
- Registrar protocolo, data/hora e responsavel pela geracao/envio.

## 3. Dashboard Executivo (Obrigatorio)

### 3.1 Painel de Receitas
- Mostrar previsto, realizado, percentual de execucao e tendencia de arrecadacao.

### 3.2 Painel de Despesas
- Mostrar valores empenhados, liquidados e pagos.
- Mostrar indicador de comprometimento orcamentario.

### 3.3 Execucao Orcamentaria
- Exibir grafico/medidor com percentual de execucao ao longo do exercicio.

### 3.4 Alertas e Notificacoes
- Empenhos prestes a vencer.
- Contratos proximos do termino.
- Limite de despesa com pessoal proximo/excedido.
- Prestacoes de contas pendentes.

## 4. Requisitos Tecnicos Minimos

- Permitir filtros por periodo, unidade gestora, orgao e fonte de recurso.
- Permitir exportacao minima em PDF e planilha.
- Registrar trilha de auditoria para geracao e publicacao dos relatorios.
- Garantir consistencia entre valores dos dashboards e relatorios oficiais.

## 5. Criterios de Aceite (Definition of Done)

Para considerar este requisito como "Atingido":
- Todos os relatorios do item 2 gerados em homologacao com dados validos.
- Dashboard executivo com todos os paineis/alertas do item 3 ativos.
- Exportacao e filtros do item 4 validados com evidencias.
- Evidencias anexadas: capturas, arquivos exportados, logs e memoria de validacao.

## 6. Backlog Tecnico Inicial (Sprintavel)

Prioridade Alta:
1. Motor de geracao dos relatorios obrigatorios (2.1 a 2.8).
2. Dashboard de receitas, despesas e execucao orcamentaria.
3. Regras de alerta para vencimentos, limites e pendencias legais.
4. Exportacao PDF/planilha com trilha de auditoria.

Prioridade Media:
1. Painel de tendencia com comparativo historico por exercicio.
2. Parametrizacao de layouts e assinatura de relatorios.
3. Otimizacoes de performance para consultas consolidadas.

## 7. Evidencias Minimas para Licitacao

- Emissao de Balanco Patrimonial, Orcamentario e Financeiro.
- Emissao de DVP e DFC com consistencia de valores.
- Emissao de RGF e RREO com periodicidade configurada.
- Geracao de Prestacao de Contas anual com rastreabilidade.
- Demonstracao do dashboard executivo com alertas ativos.
