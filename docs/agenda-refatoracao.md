# Plano de Refatoração do Diretório `agenda`

## Objetivo
Modernizar o módulo `agenda` de forma incremental, mantendo compatibilidade com o legado e sem trocar stack.

## Diagnóstico Atual (inventário rápido)
- Arquivos totais em `agenda/`: 70
- Arquivos `func_*.php`: 19
- Arquivos `funcoes/db_func_*.php`: 14
- Arquivos `classes/db_*_classe.php`: 14
- Padrões legados predominantes:
  - `<?` short tags
  - uso de `$HTTP_POST_VARS` / `$HTTP_SERVER_VARS`
  - SQL montado por concatenação de string
  - alto acoplamento entre tela, regra e acesso a dados
  - includes repetidos em vários arquivos

## Estratégia (sem ruptura)
1. Congelar comportamento atual com testes de fumaça.
2. Refatorar primeiro infraestrutura compartilhada (bootstrap, request, resposta).
3. Migrar telas/rotas por fatias pequenas (strangler interno do próprio módulo).
4. Padronizar acesso a dados e remover SQL dinâmico inseguro.
5. Só depois atacar redesign estrutural maior.

## Fase 1 - Segurança e Base (baixo risco, alto retorno)
1. Criar bootstrap único para `agenda`:
   - centralizar `require/include` comuns.
   - remover duplicação de inicialização.
2. Padronizar entrada HTTP:
   - substituir `$HTTP_*_VARS` por `$_POST`, `$_GET`, `$_SERVER`, `$_REQUEST`.
   - encapsular leitura em helpers (`AgendaRequest`).
3. Uniformizar tags PHP:
   - migrar `<?` para `<?php` (evita problemas de configuração de servidor).
4. Tratar SQL crítico:
   - priorizar consultas com entrada do usuário (ex.: filtros de busca).
   - usar parâmetros/prepared statements onde possível.

## Fase 2 - Organização por Camadas
1. Separar responsabilidades:
   - `Controller` (fluxo HTTP)
   - `Service` (regra de negócio)
   - `Repository` (persistência)
2. Reduzir lógica nas páginas `func_*.php`:
   - transformar páginas em adaptadores finos.
3. Criar contratos de resposta:
   - padronizar mensagens, erros e payload de retorno.

## Fase 3 - Confiabilidade e Qualidade
1. Testes de regressão para fluxos críticos:
   - pesquisa/seleção de agenda
   - geração de arquivos/relatórios
   - rotinas de empenho vinculadas ao módulo
2. Testes unitários em serviços novos.
3. Checklist de equivalência funcional por tela.

## Priorização Recomendada (ordem prática)
1. `agenda/func_empage.php` + `agenda/funcoes/db_func_empage.php` + `agenda/classes/db_empage_classe.php`
2. `agenda/func_empageconf*.php` e variantes de configuração
3. `agenda/func_empageforma*.php` e rotinas de forma/movimentação
4. rotinas de geração (`emp2_*`, `cai2_*`) após cobertura mínima

## Riscos e Mitigações
- Risco: quebra silenciosa em telas legadas.
  - Mitigação: teste de fumaça antes/depois de cada refactor.
- Risco: regressão em SQL antigo.
  - Mitigação: mudanças pequenas, uma consulta por vez, com validação funcional.
- Risco: refactor grande demais.
  - Mitigação: commits pequenos por arquivo/função.

## Backlog Técnico Inicial (Sprint)
1. Criar `AgendaRequest` helper para leitura sanitizada de entrada.
2. Refatorar `agenda/func_empage.php` para usar helper de request.
3. Remover short tags em `agenda/func_empage.php`.
4. Extrair construção de filtros SQL para função dedicada.
5. Validar comportamento com smoke test manual da tela.

## Métricas de Progresso
- `%` de arquivos `agenda` sem short tags.
- `%` de telas `func_*` usando helper de request.
- `%` de consultas com parâmetro seguro.
- número de telas com smoke test documentado.

## Critério de Pronto por Arquivo
- Sem short tags.
- Sem `$HTTP_*_VARS`.
- Entrada validada.
- SQL sem concatenação de dados não confiáveis.
- Fluxo funcional validado.
