# Modulo 5.1 - Sprint 2 Backlog Tecnico Executavel

Data de referencia: 2026-02-18  
Origem: `docs/sps_modulos/5_01_plano_execucao_sprints.md` (Sprint 2)

## Escopo da Sprint 2

Itens do TR alvo:
- `5, 7, 8, 9, 11, 12, 13, 14, 16, 17, 18, 19`

## Entregas implementadas nesta iteracao

- [x] Servico de geracao de codigo reduzido para metas:
  - `app/Services/Financeiro/Planejamento/Ppa/PpaCodigoReduzidoService.php`
- [x] Servico de importacao entre versoes PPA (programas, receitas, metas, vinculos):
  - `app/Services/Financeiro/Planejamento/Ppa/PpaImportacaoService.php`
  - `app/Services/Financeiro/Planejamento/Ppa/Dto/PpaImportacaoResultado.php`
- [x] Endpoint de importacao de versao:
  - `POST /api/v1/financeiro/ppa/versoes/{versaoId}/importar`
- [x] Servico e endpoint de projecao anual por exercicio:
  - `app/Services/Financeiro/Planejamento/Ppa/PpaProjecaoService.php`
  - `GET /api/v1/financeiro/ppa/versoes/{versaoId}/projecao`
- [x] Expansao de repositorio para consultas de origem e criacao por tipo:
  - `app/Repositories/Financeiro/Planejamento/Ppa/PpaRepositoryInterface.php`
  - `app/Repositories/Financeiro/Planejamento/Ppa/PpaRepository.php`

## Avanco complementar (fora do escopo original da Sprint 2)

- [x] Itens `1` e `2` concluidos com trilha de audiencias publicas do PPA:
  - `POST /api/v1/financeiro/ppa/versoes/{versaoId}/audiencias-publicas`
  - `GET /api/v1/financeiro/ppa/versoes/{versaoId}/audiencias-publicas`
  - `POST /api/v1/financeiro/ppa/audiencias-publicas/{audienciaId}/anexos`
  - `GET /api/v1/financeiro/ppa/audiencias-publicas/{audienciaId}/anexos`
  - `GET /api/v1/financeiro/ppa/audiencias-publicas/anexos/{anexoId}/download`
- [x] Item `20` iniciado e implementado com endpoint de relatorio gerencial:
  - `GET /api/v1/financeiro/ppa/relatorios-gerenciais`
  - consolidacao de receitas por fonte, despesas por destinacao e transferencias financeiras.
- [x] Item `21` iniciado e implementado com endpoint de relatorios obrigatorios (`a` a `k`):
  - `GET /api/v1/financeiro/ppa/relatorios-obrigatorios`
  - consolidacao por versoes/entidades com posicao ate data.
- [x] Item `22` concluido com compatibilizacao PPA x LDO x LOA:
  - `GET /api/v1/financeiro/ppa/compatibilizacao`
  - comparativos de receitas e metas de despesa entre as tres pecas.
- [x] Item `24` iniciado e implementado com relatorio de avaliacao de resultados por programa/acao:
  - `GET /api/v1/financeiro/ppa/avaliacao-resultados`
  - suporta selecao de 1 exercicio ou horizonte completo do PPA.
- [x] Item `25` iniciado e implementado com demonstrativos de aplicacao:
  - `GET /api/v1/financeiro/ppa/indicadores-aplicacao`
  - apresenta valores e percentuais de saude, educacao e pessoal.
- [x] Trilha inicial LDO implementada (itens 26, 27, 30, 31, 32):
  - `POST /api/v1/financeiro/ldo/planos`
  - `POST /api/v1/financeiro/ldo/versoes/{versaoId}/importar`
  - estrutura de planos/versoes/vinculos/programas/receitas/despesas da LDO.
- [x] Trilha complementar LDO implementada (itens 28, 29, 33, 34):
  - `POST /api/v1/financeiro/ldo/versoes/{versaoId}/alteracoes-receita`
  - `GET /api/v1/financeiro/ldo/versoes/{versaoId}/alteracoes-receita`
  - `GET /api/v1/financeiro/ldo/versoes/{versaoId}/orcamento`
  - `PATCH /api/v1/financeiro/ldo/versoes/{versaoId}/despesas/{despesaId}/metas`
  - historico cronologico por `created_at` no repositorio de alteracoes.
- [x] Trilha de consolidacao e relatorios LDO implementada (itens 35, 36, 37):
  - `GET /api/v1/financeiro/ldo/consolidacao-entidades`
  - `GET /api/v1/financeiro/ldo/confronto`
  - `GET /api/v1/financeiro/ldo/relatorios-gerenciais`
  - consolidacao de versoes e entidades com posicao `ate_data`.
- [x] Trilha de obras e conservacao LDO implementada (itens 38, 39):
  - `POST /api/v1/financeiro/ldo/versoes/{versaoId}/obras`
  - `GET /api/v1/financeiro/ldo/obras/demonstrativo`
  - inclui entidade responsavel, descricao, data inicio, valores previsto/conservacao/novos projetos/ano LDO.
- [x] Controle de versao LDO implementado (item 40):
  - `POST /api/v1/financeiro/ldo/planos/{planoId}/versoes`
  - `GET /api/v1/financeiro/ldo/versoes/{versaoId}`
  - consultas e relatorios da trilha LDO operam por `versaoId`.
- [x] Demonstrativos de aplicacao LDO implementados (itens 41, 42):
  - `GET /api/v1/financeiro/ldo/relatorios-aplicacao`
  - `GET /api/v1/financeiro/ldo/indicadores-aplicacao`
  - entrega demonstrativos de MDE, saude e pessoal com respectivos percentuais.
- [x] Memorias de calculo STN implementadas (itens 43, 44):
  - `POST /api/v1/financeiro/ldo/versoes/{versaoId}/memorias-calculo`
  - `GET /api/v1/financeiro/ldo/relatorios-memoria-calculo`
  - cadastro e emissao de relatorio por versao/entidade com metodologia, premissas e valor base.
- [x] Trilha inicial LOA implementada (itens 45, 46):
  - `POST /api/v1/financeiro/loa/planos`
  - `POST /api/v1/financeiro/loa/planos/{planoId}/versoes`
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/receitas`
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/despesas`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}`
- [x] Importacao e rateio de receitas LOA implementados (itens 47, 48):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/importar`
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/receitas/rateio`
  - suporta origem em LOA anterior e LDO, com distribuicao por fonte de recurso.
- [x] Atualizacao de receitas LOA com historico implementada (item 49):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/alteracoes-receita`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/alteracoes-receita`
  - mantem historico cronologico e saldo atualizado por conta/fonte/exercicio.
- [x] Inclusao de novas naturezas de receita LOA implementada (item 50):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/naturezas-receita`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/naturezas-receita`
  - permite cadastrar natureza nao prevista inicialmente e manter o catalogo por versao.
- [x] Lancamento contabil automatico para alteracao de receita LOA (item 51):
  - registro automatico em `loa_lancamentos_contabeis_receita` no fluxo:
    `POST /api/v1/financeiro/loa/versoes/{versaoId}/alteracoes-receita`
  - vinculo direto entre alteracao de receita e lancamento contabil gerado.
- [x] Consulta de orcamento LOA por data e consolidacao de entidades (item 52):
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/orcamento`
  - suporta filtros `ate_data` e `entidades_ids[]`, com retorno de receita/despesa/saldo atualizados.
- [x] Controle de alteracoes e emendas LOA por lote (item 53):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/alteracoes-receita/lotes`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/alteracoes-receita/lotes`
  - permite incluir alteracoes em lote e consultar lotes por periodo (`data_inicio`, `data_fim`).
- [x] Item 55 coberto pela mesma trilha funcional do item 53 (redacao duplicada no TR):
  - controle de alteracoes e emendas por lote
  - consulta de lotes por data
- [x] Cadastro e consulta de despesas orcamentarias LOA (item 54):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/despesas`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/despesas`
  - identifica elemento de despesa, destinacao de recurso, exercicio e valor previsto.
- [x] Alteracoes orcamentarias de despesa por Lei/Decreto com multiplas dotacoes/fontes (item 56):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/alteracoes-despesa/lotes`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/alteracoes-despesa/lotes`
  - exige lote com movimentos de `adicao` e `subtracao` para uma mesma norma.
- [x] Item 57 coberto pela mesma trilha funcional do item 56 (redacao equivalente no TR).
- [x] Historico cronologico de alteracoes orcamentarias (item 58):
  - receitas ordenadas por `created_at` em `GET /alteracoes-receita`
  - lotes de despesa ordenados por `data_documento`/`id` em `GET /alteracoes-despesa/lotes`.
- [x] Visualizacao dos lancamentos contabeis por alteracao de despesa (item 59):
  - lancamentos gerados automaticamente no registro do lote de alteracao de despesa
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/alteracoes-despesa/lotes/{loteId}/lancamentos`.
- [x] Gestao de dotacoes de creditos adicionais especiais/extraordinarios (item 60):
  - classificacao do lote por `tipo_credito` (`suplementar`, `especial`, `extraordinario`)
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/creditos-adicionais/despesas` com filtros.
- [x] Item 61 coberto pela mesma trilha funcional dos itens 56/57 (redacao equivalente no TR).
- [x] Disponibilizacao do orcamento aprovado para execucao (item 62):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/disponibilizar-execucao`
  - transiciona versao para `em_execucao` com validacoes minimas de aprovacao (receita/despesa e equilibrio opcional).
- [x] Relatorio de alteracao orcamentaria com dados da Lei/Decreto (item 63):
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/alteracoes-orcamentarias/relatorio`
  - demonstra valores de receita, despesa (adicao/subtracao), transferencia financeira e dados normativos.
- [x] Gestao de codigos reduzidos para receita orcamentaria e consignacao (item 64):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/codigos-reduzidos`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/codigos-reduzidos`
  - suporta tipos `receita_orcamentaria` e `consignacao`, com geracao automatica quando nao informado.
- [x] Item 69 coberto pela mesma trilha funcional do item 64 (redacao equivalente no TR).
- [x] Solicitacao de alteracao orcamentaria para envio ao Legislativo e efetivacao sem redigitacao (item 65):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/solicitacoes-alteracao`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/solicitacoes-alteracao`
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/solicitacoes-alteracao/{solicitacaoId}/efetivar`
  - efetivacao reaproveita payload da solicitacao para gerar lotes de receita/despesa automaticamente.
- [x] Item 70 coberto pela mesma trilha funcional do item 65 (redacao equivalente no TR).
- [x] Consistencia de dados entre PPA, LDO e LOA (item 66):
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/consistencia-planejamento`
  - compara receitas por fonte de recurso e despesas por exercicio/destinacao entre as tres pecas.
  - identifica divergencias por item e inconsistencias gerais de saldo.
- [x] Item 73 coberto pela mesma trilha funcional do item 66 (redacao equivalente no TR).
- [x] Cronograma mensal de desembolso por entidade (item 67):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/cronograma-desembolso`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/cronograma-desembolso`
  - suporta cadastro/consulta mensal por entidade, exercicio e opcionalmente por fonte de recurso.
- [x] Metas mensais de arrecadacao por entidade (item 68):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/metas-arrecadacao`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/metas-arrecadacao`
  - permite registrar meta mensal por modalidade/fonte e consolidar total por consulta.
- [x] Solicitacao de alteracoes durante o exercicio (item 71):
  - rotina de solicitacao habilitada para versoes LOA em `em_execucao`.
  - fluxo de solicitacao e efetivacao preserva reaproveitamento de dados sem redigitacao.
- [x] Bloqueio/desbloqueio automatico de dotacao em anulacao (item 72):
  - ao solicitar anulacao (`tipo_movimento=subtracao`), o valor e bloqueado por dotacao/fonte.
  - ao efetivar a solicitacao, os bloqueios associados sao desbloqueados automaticamente.
- [x] Relatorio de cronograma de desembolso por fonte e mes (item 74):
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/cronograma-desembolso/relatorio`
  - consolida o cronograma por `fonte_recurso` e `mes`, com total geral.
- [x] Relatorio de metas de arrecadacao por fonte e mes (item 75):
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/metas-arrecadacao/relatorio`
  - consolida as metas por `fonte_recurso` e `mes`, com total geral.
- [x] Impressao/geracao do decreto para suplementacao (item 76):
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/decretos/suplementacao`
  - filtra lotes de despesa `tipo_credito=suplementar`, calcula adicao/subtracao e gera texto de decreto.
- [x] Implantacao do orcamento para inicio de execucao (item 77):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/implantar-orcamento`
  - transiciona a versao para `em_execucao`, bloqueando cadastros diretos e mantendo trilha por alteracoes.
- [x] Emissao dos relatorios da Lei 4320/64 com opcao de publicacao (item 78):
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/relatorios-lei-4320`
  - retorna balanco orcamentario/financeiro/patrimonial sinteticos e status de publicacao no portal.
- [x] Controle de cotas de despesa por entidade (item 79):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/cotas-despesa`
  - gera cotas por entidade/fonte de recurso a partir das despesas da LOA.
- [x] Configuracao de cotas por periodicidade (item 80):
  - suporta `mensal`, `bimestral`, `trimestral`, `quadrimestral` e `semestral`.
- [x] Atualizacao automatica de cotas nas alteracoes orcamentarias (item 81):
  - alteracoes de despesa passam a ajustar o valor atualizado das cotas associadas por entidade/fonte.
- [x] Relatorio de acompanhamento das cotas de despesa (item 82):
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/cotas-despesa`
  - demonstra valores previstos, atualizados, utilizados e saldo disponivel.
- [x] Contingenciamento de cotas/orcamento por percentual (item 83):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/cotas-despesa/contingenciamento`
  - permite aplicar reducao percentual sobre todo o escopo filtrado ou dotacao/fonte especifica.
- [x] Liberacao dos valores contingenciados (item 84):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/cotas-despesa/liberacao`
  - libera total ou percentual dos valores atualmente contingenciados.
- [x] Redistribuicao de cotas de periodos fechados para abertos (item 85):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/cotas-despesa/redistribuicao`
  - remove saldo nao utilizado de periodos fechados e redistribui em periodos abertos.
- [x] Copia automatica de configuracoes/relacionamentos na implantacao (item 86):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/implantar-orcamento`
  - suporte a `copiar_configuracoes_base=true` com `versao_origem_id` para replicar vinculos, codigos reduzidos e naturezas.
- [x] Rotina de compatibilizacao LOA x PPA x LDO (item 87):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/compatibilizacao-planejamento`
  - executa analise de consistencia e opcionalmente aplica ajustes de receita por fonte.
- [x] Registro/acompanhamento de projetos e despesas de conservacao (item 88):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/projetos-conservacao`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/projetos-conservacao`
- [x] Cadastro de renuncia de receita com compensacao e relatorio (item 89):
  - `POST /api/v1/financeiro/loa/versoes/{versaoId}/renuncias-receita`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/renuncias-receita`
  - `GET /api/v1/financeiro/loa/versoes/{versaoId}/renuncias-receita/relatorio`

## Itens do TR - Status parcial

- [x] `5` Importar vinculos utilizados na LOA a cada novo PPA.
  Observacao: endpoint `importar-loa` implementado com carga de vinculos a partir da base orcamentaria da LOA.
- [x] `7` Importar acoes e programas da LOA.
  Observacao: endpoint `importar-loa` implementado com carga de programas da LOA.
- [x] `8` Criacao automatica de codigo reduzido de despesa do PPA.
  Observacao: fluxo consolidado com geracao automatica na inclusao de meta do PPA, incluindo validacao de composicao minima e padronizacao.
- [x] `9` Importar receitas e despesas de PPA anterior e da LOA.
  Observacao: importacao de versao PPA e importacao LOA disponiveis.
- [x] `11` Rateio automatico das receitas por conta/fonte.
  Observacao: endpoint dedicado para rateio percentual por fonte e criacao automatica das programacoes.
- [x] `12` Projecao de arrecadacao e gasto por ano do PPA.
  Observacao: implementada projecao por versao com consolidado anual de receita prevista, despesa prevista e saldo.
- [x] `13` Alteracoes orcamentarias da receita com consulta de historico.
  Observacao: implementado fluxo de inclusao e consulta por versao/conta com endpoint dedicado.
- [x] `14` Consulta do orcamento da receita/despesa atualizado por data.
  Observacao: endpoint de consulta por versao com filtro `ate_data` e consolidacao receita/despesa.
- [x] `16` Previsao de transferencias financeiras por entidade.
  Observacao: fluxo de cadastro e consulta por versao com identificacao da entidade destino.
- [x] `17` Historico cronologico de alteracoes orcamentarias.
  Observacao: consulta ordenada por `created_at` e `id` no repositorio da API de alteracoes.
- [x] `18` Consolidacao de duas ou mais entidades nas rotinas de previsao/alteracao.
  Observacao: consolidacao implementada para previsao de receita, despesa, alteracao de receita e transferencia financeira por entidades selecionadas.
- [x] `19` Confronto receita x despesa por fonte/destinacao consolidado.
  Observacao: confronto com consolidacao por multiplas versoes, filtro opcional por entidades e posicao ate data.

## Proximas tarefas (sequencia recomendada)

1. Implementar `PpaProjecaoService` (item 12) com base anual por exercicio.
2. Implementar `PpaConsolidacaoService` (itens 18, 19).
3. Evoluir relatorios e consultas avancadas do orcamento (item 14 - detalhamento por data).
4. Integrar origem LOA na geracao de relatorios gerenciais (item 20).
5. Iniciar trilha de emissao dos relatorios do item 21.
