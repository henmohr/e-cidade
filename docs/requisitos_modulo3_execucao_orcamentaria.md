# Requisitos de Desenvolvimento - Modulo 3: Execucao Orcamentaria

Data: 2026-02-18
Status: Baseline de requisitos para desenvolvimento incremental

## 1. Objetivo

Definir os requisitos funcionais minimos do Modulo 3 (Execucao Orcamentaria) para orientar implementacao, testes e evidencias de aceite em licitacao.

## 2. Escopo Funcional Obrigatorio

### 2.1 Dotacao Inicial
- Registrar o orcamento aprovado pelo Legislativo.
- Manter classificacoes completas: programa, acao, natureza de despesa e fonte de recurso.
- Garantir rastreabilidade de inclusao/alteracao por usuario, data e hora.

### 2.2 Creditos Adicionais
- Suportar creditos suplementares, especiais e extraordinarios.
- Exigir indicacao de fonte de recursos para abertura/alteracao quando aplicavel.
- Registrar fundamentacao legal e valores autorizados.

### 2.3 Empenho
- Reservar dotacao para despesa certa.
- Emitir Nota de Empenho com identificacao do credor.
- Validar saldo disponivel antes da confirmacao do empenho.

### 2.4 Licitacao e Dispensa
- Controlar processos de contratacao (licitacao, dispensa e casos equivalentes previstos).
- Integrar com cadastro de edital, propostas e atas.
- Disponibilizar vinculo com itens/orcamento para rastreabilidade da despesa.

### 2.5 Contratos
- Controlar vigencia, aditivos, reajustes, medicoes e saldos.
- Impedir execucao financeira fora de vigencia sem justificativa/legalidade.
- Manter historico de alteracoes contratuais.

### 2.6 Liquidacao
- Validar entrega de bem/servico, atesto e documentos fiscais.
- Exigir consistencia entre empenho, contrato/medicao e documento fiscal.
- Registrar evento de liquidacao com trilha de auditoria.

### 2.7 Pagamento
- Liberar pagamento somente para liquidacoes validas.
- Controlar retencoes tributarias e valor liquido.
- Emitir documento/ordem bancaria e manter conciliacao do pagamento.

## 3. Ciclo da Despesa Publica (Regra de Processo)

Fluxo obrigatorio:
1. Fixacao
2. Empenho
3. Liquidacao
4. Pagamento

Regras:
- Nao permitir salto de etapa sem permissao/regra formal.
- Bloquear pagamento sem liquidacao valida.
- Bloquear liquidacao sem empenho correspondente.

## 4. Controles Especificos Obrigatorios

### 4.1 Limitacao de Empenho
- Alertas e bloqueios configuraveis por limite orcamentario/LRF.
- Visao de restos a pagar para apoio a decisao de bloqueio.

### 4.2 Prevencao de Restos a Pagar sem Lastro
- Alertar empenhos de fim de exercicio com baixa probabilidade de liquidacao.
- Exigir justificativa quando houver excecao.

### 4.3 Vinculacao de Receitas
- Garantir uso de recurso vinculado apenas nas finalidades permitidas.
- Bloquear combinacoes invalidas de fonte/finalidade.

### 4.4 Reserva de Contingencia
- Controlar constituicao, uso e saldo da reserva.
- Exigir motivacao legal para uso em despesa imprevista/calamidade.

## 5. Criterios de Aceite (Definition of Done)

Para considerar o modulo como "Atingido":
- Todos os fluxos 2.1 a 2.7 executados ponta a ponta em ambiente de homologacao.
- Regras de processo do item 3 validadas com evidencias.
- Controles do item 4 com alertas/bloqueios demonstrados.
- Evidencias anexadas: captura de tela, log de auditoria e relatorio de saida.

## 6. Backlog Tecnico Inicial (Sprintavel)

Prioridade Alta:
1. Validacao de sequencia do ciclo Fixacao -> Empenho -> Liquidacao -> Pagamento.
2. Regras de saldo/disponibilidade para Empenho e Liquidacao.
3. Alertas de restos a pagar e limitacao de empenho.
4. Vinculacao obrigatoria de fonte de recurso e bloqueios de inconsistencia.

Prioridade Media:
1. Regras avancadas para reserva de contingencia.
2. Dashboards operacionais do ciclo da despesa.
3. Relatorios consolidados por etapa (empenhado, liquidado, pago, saldo).

## 7. Evidencias Minimas para Licitacao

- Cenario completo com um processo real:
  Fixacao -> Empenho -> Liquidacao -> Pagamento.
- Cenario com credito adicional e reflexo na dotacao.
- Cenario com alerta/bloqueio de regra (LRF/restos a pagar/vinculacao).
- Exportacao de relatorio de controle por etapa.
