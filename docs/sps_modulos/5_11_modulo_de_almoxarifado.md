# 5.11 MÓDULO DE ALMOXARIFADO

Fonte: `/home/mohr/Downloads/cidade/sps.pdf` (seção 5 do Termo de Referência).

1. Possibilitar o controle de toda movimentação do estoque, sendo entrada, saída e transferência
de materiais. Realizando a atualização do estoque de acordo com cada movimentação realizada.
2. Possuir gerenciamento automático nas saídas através de requisições ao almoxarifado, anulando
as quantidades que não possui estoque e sugerindo as quantidades disponíveis em estoque.
3.     Permitir informar para controle os limites mínimos de saldo físico de estoque.
4. Permitir que seja estipulado limites de materiais mediante controle de cotas de consumo, para
poder delimitar ao departamento a quantidade limite que ele poderá requisitar ao almoxarifado
mensalmente.
5. Permitir consultar as últimas aquisições, com informação ao preço das últimas compras, para
estimativa de custo.
6. Possibilitar consultar e gerenciar a necessidade de reposição de materiais, possibilitando a
realização do pedido ao Compras por meio de requisição ao Compras.
7. Possibilitar integração com o sistema de compra para realização de entradas de materiais
importando dados oriundos de ordens de compra ou realizar entradas por meio de informações de
notas fiscais acesso ao centro de custos, materiais e fornecedores.
8. Permitir realizar requisições/pedidos de materiais ao responsável do almoxarifado, bem como
realizar o controle de pendências dos respectivos pedidos para fornecimento de materiais.
9. Manter controle efetivo sobre as requisições/pedidos de materiais, permitindo atendimento
parcial de requisições e mantendo o controle sobre o saldo não atendido das requisições.
10. Utilizar centros de custo (setores/departamentos) na distribuição de matérias, através das
requisições/pedidos de materiais e/ou saídas de materiais para controle do consumo.
11. Efetuar cálculo automático do preço médio dos materiais, bem como a sua atualização a cada
entrada de produto em estoque.
12. Registrar a abertura e o fechamento de inventários. Não permitindo a movimentação, seja de
entrada ou saída de materiais quando o estoque e/ou produto estiverem em inventário. Sua
movimentação somente poderá ocorrer após a conclusão do inventário.
13. Possuir rotina que permita que o responsável pelo almoxarifado realize bloqueios por depósito,
por produto ou por produto do depósito, a fim de não permitir nenhum tipo de movimentação
(entrada/saída).
14. Possuir a possibilidade de consulta rápida dos dados referente ao vencimento do estoque,
possibilitando ao menos a consulta dos vencidos, vencimentos em 30 dias.
15. Possuir integração com a contabilidade, para disponibilizar os dados referentes a entradas e
saídas de materiais para serem contabilizadas pelo departamento de contabilidade.
16. Possibilitara emissão de relatório da ficha de controle de estoque, mostrando as movimentações
por material e período com saldo anterior ao período (analítico/sintético).
17. Possibilitar a emissão de relatórios de entradas e saídas de materiais por produto, nota fiscal e
setor.
18. Possibilitar a emissão de relatório financeiro do depósito de estoque mostrando os movimentos
de entradas, saídas e saldo atual por período.
19. Emitir um resumo anual das entradas e saídas, mostrando o saldo financeiro mês a mês por
estoque e o resultado final no ano.
20. Emitir relatórios de controle de validade de lotes de materiais, possibilitando seleção por:
almoxarifado/deposito; período; materiais vencidos; materiais a vencer.
21. Permitir o gerenciamento integrado dos estoques de materiais existentes nos diversos
almoxarifados/depósitos.
22. Possuir registro do ano e mês, bem rotina de virada mensal para que seja realizada a
atualização do mês e ano do almoxarifado.





## Mapeamento de funcionalidades ja existentes no legado

Objetivo: evitar retrabalho, priorizando reaproveitamento das rotinas em `resources/legacy`.

Diretorios legados relacionados a este modulo:
- `resources/legacy/materiais` (arquivos no nivel do modulo: 314)

Checklist de reaproveitamento antes de implementar novo codigo:
- [ ] Inventariar rotinas existentes nesses diretorios (telas, relatorios, RPCs e integracoes).
- [ ] Validar cobertura contra o TR/SPS do modulo e marcar gaps reais.
- [ ] Reutilizar regra de negocio legada quando aderente (evitar reescrita desnecessaria).
- [ ] Modernizar com abordagem incremental (estrangulamento), mantendo compatibilidade funcional.
