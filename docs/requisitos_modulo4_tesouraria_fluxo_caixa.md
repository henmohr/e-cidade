# Requisitos de Desenvolvimento - Modulo 4: Tesouraria e Fluxo de Caixa

Data: 2026-02-18
Status: Baseline de requisitos para desenvolvimento incremental

## 1. Objetivo

Definir os requisitos funcionais minimos do Modulo 4 (Tesouraria e Fluxo de Caixa) para orientar implementacao, testes e evidencias de aceite em licitacao.

## 2. Escopo Funcional Obrigatorio

### 2.1 Conta Unica
- Integrar com a conta bancaria unica do ente publico.
- Disponibilizar saldo atualizado e historico de movimentacoes.
- Registrar trilha de auditoria por usuario, data e origem da operacao.

### 2.2 Conciliacao Bancaria
- Executar conciliacao diaria entre extrato bancario e registros internos.
- Identificar automaticamente divergencias e pendencias.
- Permitir classificacao de pendencias (a confirmar, ajustada, rejeitada).

### 2.3 Previsao de Caixa
- Projetar entradas e saidas para janelas de curto prazo (minimo 7 dias).
- Considerar receitas previstas, pagamentos programados e compromissos legais.
- Exibir impacto projetado no saldo diario.

### 2.4 Programacao Financeira
- Manter cronograma mensal de pagamentos com priorizacao legal.
- Priorizar itens criticos: folha, encargos, fornecedores e obrigacoes legais.
- Bloquear programacao sem disponibilidade financeira estimada.

### 2.5 Aplicacao Financeira
- Registrar aplicacoes de curto prazo para recursos ociosos.
- Controlar parametros minimos: valor aplicado, prazo, taxa, instituicao e resgate.
- Garantir rastreabilidade do retorno financeiro e da movimentacao de caixa.

### 2.6 Restos a Pagar
- Controlar restos a pagar processados (liquidados e nao pagos).
- Controlar restos a pagar nao processados (empenhados e nao liquidados).
- Alertar prazos criticos e riscos de inconsistencia no fechamento do exercicio.

## 3. Dashboard de Tesouraria (Obrigatorio)

O painel deve apresentar em tempo real:
1. Saldo atual disponivel em conta.
2. Projecao de fluxo para os proximos 7 dias.
3. Alertas de compromissos vencidos e limites proximos de estouro.

Regras:
- Atualizacao periodica configuravel (ex.: 5 a 15 minutos).
- Evidencia de data/hora da ultima atualizacao.
- Permitir filtro por fonte de recurso e unidade gestora.

## 4. Fluxo de Caixa Projetado (Obrigatorio)

- Gerar visao grafica da evolucao do saldo projetado.
- Consolidar receitas previstas (tributos, transferencias e servicos).
- Consolidar despesas programadas (folha, fornecedores, encargos e outras obrigacoes).
- Destacar periodos de risco de insuficiencia de caixa e periodos de sobra.

## 5. Controles Especificos Obrigatorios

### 5.1 Integridade Financeira
- Bloquear pagamento programado sem disponibilidade projetada.
- Bloquear baixa manual sem justificativa e perfil autorizado.

### 5.2 Alertas Operacionais
- Alertar vencimentos proximos e compromissos em atraso.
- Alertar divergencias recorrentes de conciliacao.

### 5.3 Auditoria e Rastreabilidade
- Registrar todos os eventos criticos: conciliacao, reclassificacao, programacao e pagamento.
- Disponibilizar consulta auditavel para orgaos de controle.

## 6. Criterios de Aceite (Definition of Done)

Para considerar o modulo como "Atingido":
- Fluxos 2.1 a 2.6 executados em homologacao com evidencias.
- Dashboard com indicadores minimos do item 3 funcionando.
- Fluxo de caixa projetado com entradas e saidas validado no item 4.
- Controles do item 5 demonstrados com pelo menos um cenario de bloqueio/alerta.
- Evidencias anexadas: capturas, relatorios e logs de auditoria.

## 7. Backlog Tecnico Inicial (Sprintavel)

Prioridade Alta:
1. Conciliacao bancaria diaria com deteccao de divergencias.
2. Programacao financeira com bloqueio por insuficiencia projetada.
3. Dashboard de saldo + projecao de 7 dias + alertas.
4. Controle de restos a pagar processados e nao processados.

Prioridade Media:
1. Camada de regras para aplicacoes financeiras de curto prazo.
2. Relatorios gerenciais de fluxo de caixa por periodo e fonte.
3. Aperfeicoamento de alertas com classificacao de risco.

## 8. Evidencias Minimas para Licitacao

- Cenario completo com conciliacao bancaria e tratamento de pendencia.
- Cenario de programacao financeira com prioridade legal e bloqueio por caixa.
- Cenario de projecao de fluxo (7 dias) com alertas de risco.
- Cenario de controle de restos a pagar (processado e nao processado).
- Exportacao de relatorio operacional de tesouraria.
