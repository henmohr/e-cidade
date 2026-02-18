# Requisitos de Desenvolvimento - Modulo 2: Orcamento e Planejamento

Data: 2026-02-18
Status: Baseline de requisitos para desenvolvimento incremental

## 1. Objetivo

Definir os requisitos funcionais minimos do Modulo 2 (Orcamento e Planejamento) para orientar implementacao, testes e evidencias de aceite em licitacao.

## 2. Escopo Funcional Obrigatorio

### 2.1 PPA (Plano Plurianual)
- Gerenciar programas com horizonte de quatro anos.
- Permitir cadastro de metas, indicadores e valores por exercicio.
- Garantir rastreabilidade de alteracoes por usuario, data e hora.

### 2.2 LDO (Lei de Diretrizes Orcamentarias)
- Registrar metas e prioridades do exercicio subsequente.
- Definir diretrizes de alocacao de recursos.
- Manter historico de versoes e justificativas tecnicas.

### 2.3 LOA (Lei Orcamentaria Anual)
- Consolidar previsao anual de receitas e fixacao de despesas.
- Distribuir por orgao, unidade, programa e acao.
- Bloquear aprovacao com inconsistencias de classificacao.

### 2.4 Elaboracao Orcamentaria
- Criar dotacoes com natureza de despesa e fonte de recurso.
- Validar limites legais e vinculacoes constitucionais.
- Registrar emendas e ajustes com auditoria completa.

### 2.5 Cronograma de Desembolso
- Gerar previsao mensal de desembolso por prioridade legal.
- Integrar disponibilidade prevista de caixa.
- Alertar incompatibilidade entre cronograma e arrecadacao prevista.

### 2.6 Projecoes e Simulacoes
- Simular cenarios de arrecadacao e despesa.
- Comparar cenarios otimista, base e restritivo.
- Registrar premissas de cada simulacao para auditoria.

## 3. Fluxo de Elaboracao Orcamentaria (Regra de Processo)

Fluxo obrigatorio:
1. Diagnostico institucional
2. Proposicao de metas
3. Consolidacao do projeto
4. Discussao e ajustes tecnicos
5. Aprovacao legislativa
6. Execucao do orcamento aprovado

Regras:
- Nao permitir avancar sem validacoes minimas por etapa.
- Manter trilha de auditoria em toda mudanca de versao.
- Preservar historico para comparacao entre proposta e aprovado.

## 4. Estrutura Programatica (Obrigatoria)

- Programa > Acao > Subacao (quando aplicavel).
- Natureza da despesa com categoria economica, grupo, modalidade e elemento.
- Fonte de recurso obrigatoria em todos os registros orcamentarios.
- Localizador geografico disponivel quando exigido.

## 5. Criterios de Aceite (Definition of Done)

Para considerar o modulo como "Atingido":
- Fluxos 2.1 a 2.6 executados em homologacao com evidencias.
- Regras de processo do item 3 validadas.
- Estrutura programatica do item 4 aplicada nos cenarios de teste.
- Evidencias anexadas: capturas, relatorios e logs de auditoria.

## 6. Backlog Tecnico Inicial (Sprintavel)

Prioridade Alta:
1. Cadastro estruturado de PPA/LDO/LOA com versionamento.
2. Regras de consistencia de classificacao programatica e fonte.
3. Simulacao de cenarios com premissas auditaveis.
4. Consolidacao de proposta para aprovacao legislativa.

Prioridade Media:
1. Dashboards de comparativo planejado x aprovado x executado.
2. Relatorios consolidados por orgao, programa e acao.
3. Alertas proativos de risco de desequilibrio orcamentario.

## 7. Evidencias Minimas para Licitacao

- Cenario completo PPA -> LDO -> LOA.
- Cenario com simulacao de arrecadacao e reflexo na despesa.
- Cenario de inconsistencia bloqueada por regra de classificacao.
- Exportacao de relatorio consolidado de planejamento.
