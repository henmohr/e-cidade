# Requisitos de Desenvolvimento - Modulo 5: Controle de Despesas e Receitas

Data: 2026-02-18
Status: Baseline de requisitos para desenvolvimento incremental

## 1. Objetivo

Definir os requisitos funcionais minimos do Modulo 5 (Controle de Despesas e Receitas) para orientar implementacao, testes e evidencias de aceite em licitacao.

## 2. Escopo Funcional Obrigatorio - Despesas

### 2.1 Cadastro de Credores
- Manter base de fornecedores e beneficiarios com validacao de CNPJ/CPF.
- Registrar situacao cadastral, documentos obrigatorios e historico de pagamentos.
- Bloquear operacao financeira para credor com pendencia documental critica.

### 2.2 Notas de Empenho
- Emitir e controlar notas de empenho com vinculo orcamentario.
- Integrar automaticamente com os registros contabil/orcamentario.
- Gerar informacoes necessarias para retencoes e comunicacao ao credor.

### 2.3 Verificacao de Pagamento
- Exigir atesto de servico ou confirmacao de entrega de bem.
- Permitir upload e vinculacao de documentos comprobatorios.
- Bloquear pagamento sem conferencias minimas e documentos obrigatorios.

### 2.4 Retencoes Tributarias
- Calcular e reter automaticamente IRRF, PIS, COFINS, CSLL, ISS e INSS conforme regra.
- Gerar guias e demonstrativos de recolhimento (ex.: DARF e equivalentes).
- Manter trilha de auditoria dos calculos e bases utilizadas.

### 2.5 Diarias e Passagens
- Controlar solicitacao, aprovacao e pagamento de diarias.
- Controlar emissao/reserva de passagens vinculadas a viagem oficial.
- Exigir prestacao de contas com validacoes de prazo e documentos.

### 2.6 Folha de Pagamento
- Integrar com sistema de RH para importacao de eventos da folha.
- Consolidar valores para pagamento e obrigacoes acessorias.
- Disponibilizar informacoes para contracheques e informes de rendimentos.

## 3. Escopo Funcional Obrigatorio - Receitas

### 3.1 Receitas Tributarias
- Controlar arrecadacao de IPTU, ISS, ITBI, taxas e contribuicoes.
- Integrar com sistemas de tributacao municipal quando aplicavel.

### 3.2 Receitas de Contribuicoes
- Registrar e acompanhar contribuicoes de melhoria e especiais.

### 3.3 Receitas Patrimoniais
- Controlar receitas de alugueis, alienacao de bens, dividendos e similares.

### 3.4 Receitas Agropecuarias e Industriais
- Registrar receitas de atividades agropecuarias e industriais do ente.

### 3.5 Receitas de Servicos
- Controlar arrecadacao proveniente de servicos publicos.

### 3.6 Transferencias Intergovernamentais
- Acompanhar repasses de FPM, ICMS, IPI-Exportacao, FUNDEB e similares.
- Registrar transferencia constitucional e voluntaria com origem/destino.

### 3.7 Outras Receitas
- Registrar multas, juros de mora e demais receitas diversas.

## 4. Classificacao da Receita (Obrigatoria)

### 4.1 Receitas Correntes
- Classificar e consolidar receitas correntes:
  tributarias, contribuicoes, patrimoniais, agropecuarias, industriais, servicos,
  transferencias correntes e outras correntes.

### 4.2 Receitas de Capital
- Classificar e consolidar receitas de capital:
  operacoes de credito, alienacao de bens, amortizacao de emprestimos concedidos,
  transferencias de capital e outras de capital.

Regras:
- Bloquear lancamento de receita sem classificacao valida.
- Permitir trilha de reclassificacao com usuario, data/hora e justificativa.

## 5. Controles Especificos Obrigatorios

### 5.1 Integridade da Despesa
- Bloquear despesa sem credor regular e sem documentacao minima.
- Bloquear pagamento sem atesto e sem validacao de retencoes aplicaveis.

### 5.2 Integridade da Receita
- Impedir duplicidade de arrecadacao para o mesmo identificador de receita.
- Alertar divergencia entre arrecadacao prevista e realizada por categoria.

### 5.3 Auditoria e Rastreabilidade
- Registrar eventos criticos de cadastro, empenho, atesto, retencao e pagamento.
- Registrar eventos criticos de classificacao e arrecadacao da receita.

## 6. Criterios de Aceite (Definition of Done)

Para considerar o modulo como "Atingido":
- Fluxos de despesas (2.1 a 2.6) executados em homologacao com evidencias.
- Fluxos de receitas (3.1 a 3.7) executados em homologacao com evidencias.
- Classificacao da receita corrente/capital validada com cenario real.
- Controles do item 5 demonstrados com pelo menos um bloqueio e um alerta.
- Evidencias anexadas: capturas, relatorios, logs e memoria de calculo.

## 7. Backlog Tecnico Inicial (Sprintavel)

Prioridade Alta:
1. Cadastro de credores com validacao documental e bloqueios operacionais.
2. Fluxo de empenho -> atesto -> pagamento com retencoes tributarias.
3. Classificacao obrigatoria da receita (corrente/capital) com validacoes.
4. Integracao inicial com receitas tributarias e transferencias intergovernamentais.

Prioridade Media:
1. Fluxo completo de diarias e passagens com prestacao de contas.
2. Integracao ampliada com folha de pagamento.
3. Relatorios gerenciais de arrecadacao por categoria e comparativo previsto x realizado.

## 8. Evidencias Minimas para Licitacao

- Cenario de despesa completo: credor -> empenho -> atesto -> pagamento com retencoes.
- Cenario de diaria/passagem com prestacao de contas.
- Cenario de arrecadacao tributaria e transferencia intergovernamental.
- Cenario de classificacao de receita corrente e de capital.
- Exportacao de relatorios consolidados de despesas e receitas.
