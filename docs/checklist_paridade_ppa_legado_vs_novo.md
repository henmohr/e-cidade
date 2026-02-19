# Checklist de Paridade - PPA (Legado vs Novo)

Data: 2026-02-19
Escopo principal: fluxo de **Cadastro de Indicadores PPA > Inclusao**.

## Referencias tecnicas

- Legado (rotina atual em producao):
  - `resources/legacy/orcamento/orc1_orcindica001.php` (Inclusao)
  - `resources/legacy/orcamento/orc1_orcindica011.php` (formulario de cadastro)
  - `resources/legacy/orcamento/orc1_orcindica012.php` (edicao)
- Novo (modernizacao em Laravel):
  - `app/Http/Controllers/Financeiro/Planejamento/PpaController.php`
  - `routes/api.php` (endpoints de PPA)
  - `app/Http/Controllers/Financeiro/Planejamento/PpaAudienciasWebController.php`
  - `resources/views/financeiro/planejamento/ppa/audiencias.blade.php`

## Diagnostico objetivo

- O **fluxo legado de cadastro de indicadores PPA** existe e esta operacional.
- O **novo** cobre varias capacidades de PPA por API (planos, versoes, metas, receitas, importacoes, audiencias), mas **nao possui tela equivalente** ao cadastro legado de indicadores (Inclusao/Alteracao/Exclusao no mesmo fluxo).
- Decisao recomendada no momento: **manter legado como caminho oficial do fluxo de indicadores PPA** e evoluir o novo ate paridade funcional.

## Checklist de paridade funcional (Indicadores PPA)

Legenda:
- `[x]` atendido
- `[ ]` nao atendido
- `[~]` parcial

| Item | Legado | Novo | Observacao |
|---|---|---|---|
| Abrir rotina por menu/busca (Desktop) | [x] | [~] | Novo depende de atalhos dedicados; legado ja integrado ao menu nativo. |
| Tela de Inclusao de indicador PPA | [x] | [ ] | Legado: `orc1_orcindica001.php`; novo sem UI equivalente. |
| Tela de Alteracao de indicador PPA | [x] | [ ] | Legado: `orc1_orcindica012.php`; novo sem UI equivalente. |
| Persistencia do cadastro no fluxo classico | [x] | [ ] | No novo ha APIs de planejamento, mas nao o mesmo fluxo de cadastro de indicador legado. |
| Permissoes por item de menu legado | [x] | [~] | Novo usa middleware/autenticacao, mas ainda sem paridade do modelo legado item-a-item para esse fluxo. |
| Auditoria/trilha de alteracoes | [~] | [~] | Ambos possuem trilhas em partes do modulo; validar requisito especifico de indicador PPA. |
| Cobertura de testes automatizados do fluxo | [ ] | [~] | Novo possui testes de servicos/controladores do modulo PPA; fluxo legado de tela nao esta coberto por teste automatizado equivalente. |
| UX consolidada para usuario final (sem trocar de contexto) | [x] | [ ] | Usuario de negocio hoje encontra o fluxo pronto no legado. |

## Checklist de paridade tecnica (base para migracao)

- [ ] Implementar UI Laravel para cadastro de indicadores PPA (incluir/alterar/excluir) com mesma regra de negocio do legado.
- [ ] Mapear e reproduzir validacoes do legado (`forms/db_frmorcindica.php` e regras associadas) no novo.
- [ ] Garantir controle de permissao equivalente ao acesso atual via menu.
- [ ] Criar testes de regressao do fluxo de indicadores (feature + integracao + smoke e2e).
- [ ] Definir plano de transicao com feature flag (legado x novo) e rollback simples.
- [ ] Homologar com usuarios chave comparando resultado legado vs novo no mesmo cenario.

## Criterios de "pronto para substituir legado"

- [ ] 100% dos cenarios criticos do fluxo de indicadores PPA executam no novo.
- [ ] Sem regressao funcional em homologacao (comparacao lado a lado com legado).
- [ ] Permissoes e auditoria validadas.
- [ ] Evidencias documentadas para banca/licitacao (prints, logs, roteiro de teste).

## Conclusao atual

- **Status atual**: manter o fluxo legado para operacao.
- **Status da modernizacao**: continuar evolucao no novo ate atingir os criterios acima, entao planejar cutover controlado.
