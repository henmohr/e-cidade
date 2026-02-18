# Matriz de Aderencia - Modulo 1: Contabilidade Publica

Data: 2026-02-18  
Repositorio: `henmohr/e-cidade`

## Resultado Executivo

Status atual do modulo para o escopo solicitado: **PARCIAL (avancado)**.

- Base funcional de contabilidade publica existe e e ampla.
- Ha cobertura clara para PCASP, lancamentos e principais relatorios DCASP.
- Ainda faltam evidencias objetivas para classificar como **100% atingido** no formato de licitacao.

## Matriz de Aderencia por Requisito

| Requisito da Licitacao | Status | Evidencias no codigo | Gap para 100% |
|---|---|---|---|
| Plano de Contas (PCASP, NBCT 16.1) | Atendido | `src/Financeiro/Contabilidade/PlanoDeContas/PCASP/Importacao/Importacao.php`, `src/Financeiro/Contabilidade/PlanoDeContas/PCASP/Modelo.php`, `app/Support/Session/LegacySession.php` | Consolidar evidencias funcionais (tela + execucao de importacao/manutencao) |
| Lancamentos Contabeis (debitos/creditos) | Atendido (tecnico) | `db_libcontabilidade.php`, `src/Financeiro/Contabilidade/LancamentoContabil/Documento.php`, `resources/legacy/contabilidade/con4_processalancamentos001.php` | Demonstrar validacao funcional de saldo e conferencia em roteiro homologado |
| Balanco Patrimonial | Atendido | `resources/legacy/contabilidade/con2_balancopatrimonial_2015.php`, `resources/legacy/contabilidade/con2_relatoriosdcasp001.php` | Evidencia de geracao no ambiente da PoC com filtros exigidos |
| Balanco Financeiro | Atendido | `src/Financeiro/Contabilidade/Relatorio/DCASP/BalancoFinanceiroFactory.php`, `resources/legacy/contabilidade/con2_balancofinanceiro_2015.php` | Evidencia de saida e rastreabilidade da execucao |
| Balanco Orcamentario | Atendido | `src/Financeiro/Contabilidade/Relatorio/DCASP/Repository/BalancoOrcamentario.php`, `resources/legacy/contabilidade/con2_balancoorcamentario_2015.php` | Evidencia de consistencia entre lancamento e demonstrativo |
| DVP (Demonstracao das Variacoes Patrimoniais) | Atendido | `resources/legacy/contabilidade/con2_variacoespatrimoniais_2015.php` | Validacao funcional assinada em roteiro de PoC |
| DFC (Demonstracao dos Fluxos de Caixa) | Atendido | `src/Financeiro/Contabilidade/Relatorio/DCASP/FluxoCaixaFactory.php`, `resources/legacy/contabilidade/con2_fluxocaixaDCASP002_2015.php` | Evidencia de exportacao/entrega conforme exigencia da banca |
| DCA (Demonstracao das Contas Analiticas) | Parcial | Evidencias indiretas em rotinas DCASP e relatorios contabeis | Identificar rotina oficial equivalente e comprovar execucao |
| DMPL (Demonstracao das Mutacoes do Patrimonio Liquido) | Nao evidenciado claramente | Nao localizado artefato claro com nomenclatura/fluxo DMPL | Mapear implementacao ou registrar plano de entrega |
| Depreciacao e amortizacao (NBC T 16.4) | Parcial | `classes/db_bens_classe.php`, `classes/db_benshistoricocalculo_classe.php` | Comprovar fluxo automatico completo: calculo + lancamento contabil + reflexo em relatorio |
| Relatorios obrigatorios com exportacao (PDF/Excel/XML SICONFI) | Parcial | `src/Financeiro/Contabilidade/Relatorio/DCASP/Model/BalancoOrcamentarioDCASP2017.php`, `resources/legacy/contabilidade/con2_balancetemsc002.php`, `resources/legacy/contabilidade/con4_msc.RPC.php` | Fechar matriz de formatos exigidos por item da licitacao e anexar evidencias |
| Dashboard contabil com indicadores/alertas | Nao evidenciado claramente | Nao localizado painel dedicado do modulo contabil com os itens descritos | Definir se sera atendido por tela existente + BI ou por nova entrega |

## Avaliacao Objetiva para Licitacao (agora)

Classificacao recomendada hoje: **PARCIAL**.

Motivos para ainda nao marcar 100%:
- falta de evidencias fechadas (roteiro + captura + validacao funcional) para todos os itens;
- itens com lacuna funcional/documental (principalmente DMPL, DCA e dashboard especifico);
- necessidade de demonstracao ponta a ponta para depreciacao/amortizacao com reflexo contabil.

## Plano Curto para virar 100%

1. Fechar trilha de evidencias em PoC para os itens ja implementados (contabilidade + relatorios + exportacoes).
2. Confirmar formalmente DCA e DMPL (funcao equivalente, tela, relatorio, formato de saida).
3. Executar e registrar um cenario completo de depreciacao com lancamento automatico e reflexo em demonstrativo.
4. Definir tratamento do requisito de dashboard (composicao por telas atuais ou entrega complementar).
5. Atualizar checklist oficial: `docs/checklist_evidencias_contabilidade_publica_poc.md`.

## Referencias internas

- `docs/checklist_evidencias_contabilidade_publica_poc.md`
- `docs/roteiro_oficial_demonstracao_poc.md`
- `docs/simulacao_integral_poc.md`
