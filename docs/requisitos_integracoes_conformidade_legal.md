# Requisitos de Desenvolvimento - Integracoes e Conformidade Legal

Data: 2026-02-18
Status: Baseline de requisitos para desenvolvimento incremental

## 1. Objetivo

Definir os requisitos minimos de conformidade legal e integracoes externas obrigatorias para o e-cidade, orientando implementacao, testes e evidencias de aceite em licitacao.

## 2. Conformidade com Legislacao (Obrigatoria)

### 2.1 Lei 4.320/1964
- Garantir aderencia as normas gerais de direito financeiro para orcamento, execucao e balancos.
- Manter rastreabilidade das rotinas que suportam elaboracao e controle orcamentario/contabil.

### 2.2 LRF (LC 101/2000)
- Controlar limites fiscais relevantes (despesa com pessoal, endividamento e operacoes de credito).
- Emitir alertas preventivos de risco de extrapolacao de limites.

### 2.3 Lei de Acesso a Informacao (LAI)
- Disponibilizar publicacao de dados em portal de transparencia com acesso publico.
- Garantir trilha de atualizacao e historico de publicacoes.

### 2.4 NBCT 16
- Assegurar estrutura contabil aderente as normas tecnicas aplicadas ao setor publico.
- Validar consistencia de demonstrativos e classificacoes contabeis.

### 2.5 SICONFI
- Permitir geracao e envio de dados contabeis/fiscais para a STN.
- Validar formato, consistencia e integridade antes da transmissao.

### 2.6 TCU/TCE
- Permitir prestacao de contas por meio de relatorios e arquivos exigidos pelos tribunais.
- Manter historico de remessas e protocolos.

## 3. Integracoes Externas (Obrigatorias)

### 3.1 SICONFI/STN
- Envio de balancos e demonstrativos contabeis conforme leiaute vigente.
- Registrar status de envio (pendente, enviado, aceito, rejeitado) e motivo de rejeicao.

### 3.2 TCE/TCU
- Transmitir dados de prestacao de contas conforme exigencia do orgao de controle.
- Registrar protocolo, data/hora e usuario responsavel pelo envio.

### 3.3 Portal da Transparencia
- Publicar automaticamente dados de receitas, despesas e contratos.
- Permitir reprocessamento e auditoria de publicacoes.

### 3.4 Banco Central / Integracao Bancaria
- Suportar consulta de contas e extratos, e operacoes permitidas por convenio.
- Aplicar controles de seguranca, autorizacao e trilha de auditoria nas operacoes.

### 3.5 SIAPE / Sistemas de Pessoal
- Importar dados de folha e cadastro de servidores conforme layout acordado.
- Validar consistencia dos dados importados e apontar rejeicoes.

### 3.6 Portal e-SF / NFS-e
- Suportar emissao/recepcao de notas fiscais de servico quando aplicavel.
- Armazenar metadados da transacao e retorno do provedor.

### 3.7 Nota Fiscal Eletronica
- Integrar com emissores/receptores de NF-e conforme necessidade do ente.
- Validar documento fiscal antes de vincular aos fluxos de despesa/receita.

### 3.8 e-SIC
- Integrar com sistema de pedidos de acesso a informacao.
- Permitir rastrear solicitacao, prazo e resposta associada.

## 4. Controles Transversais Obrigatorios

### 4.1 Seguranca e LGPD
- Garantir controle de acesso por perfil para operacoes sensiveis.
- Registrar logs auditaveis de leitura, alteracao, envio e publicacao.
- Proteger dados pessoais conforme base legal e principio da minimizacao.

### 4.2 Governanca de Integracoes
- Catalogar cada integracao com responsavel tecnico, frequencia e SLA.
- Definir politica de retentativa, fallback e tratamento de indisponibilidade externa.

### 4.3 Confiabilidade Operacional
- Monitorar filas de integracao, falhas e reprocessamentos.
- Disponibilizar painel de status com indicadores minimos de sucesso/erro/latencia.

## 5. Criterios de Aceite (Definition of Done)

Para considerar este requisito como "Atingido":
- Matriz de aderencia legal (Lei 4.320, LRF, LAI, NBCT 16, SICONFI, TCU/TCE) publicada.
- Integracoes criticas habilitadas em homologacao com evidencias de envio/retorno.
- Controles transversais (seguranca, auditoria, monitoramento) demonstrados.
- Evidencias anexadas: capturas, protocolos, logs e relatorios de validacao.

## 6. Backlog Tecnico Inicial (Sprintavel)

Prioridade Alta:
1. Matriz de conformidade legal por requisito e evidencia.
2. Pipeline de envio SICONFI/STN com validacao e tratamento de rejeicao.
3. Publicacao automatica no Portal da Transparencia com trilha auditavel.
4. Trilhas de auditoria e monitoramento de integracoes externas.

Prioridade Media:
1. Conectores padronizados para TCE/TCU por layout.
2. Integracao com SIAPE/sistema de pessoal e validacoes de consistencia.
3. Integracao e-SIC e consolidacao de indicadores de atendimento.

## 7. Evidencias Minimas para Licitacao

- Cenario de envio para SICONFI/STN com retorno de aceite/rejeicao.
- Cenario de transmissao ao TCE/TCU com protocolo registrado.
- Cenario de publicacao no Portal da Transparencia com trilha de auditoria.
- Cenario de importacao de dados de pessoal (SIAPE/similar) com validacao.
- Cenario de atendimento e-SIC com rastreabilidade de prazo e resposta.
