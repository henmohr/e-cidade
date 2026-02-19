# 5.29 MÓDULO DE OUVIDORIA

Fonte: `/home/mohr/Downloads/cidade/sps.pdf` (seção 5 do Termo de Referência).

30. Tramitar processos inteiramente em ambiente digital com dispensa do trâmite em papel.
2. Configurar roteiro interno de cumprimento automático para cada assunto, inclusive com a
definição de prazo para que cada etapa seja realizada.
3. Notificar requerentes e demais interessados a cada trâmite processual, através de envio de e-
mail.
4. Disponibilizar no momento da abertura da ouvidoria via portal de autoatendimento e aplicativo, a
possibilidade de registrar o pedido indicando o tipo de identificação do requerente, baseado na
Legislação 13.460/2017:

a)   Identificação com restrição de dados.
b)   Identificação sem restrição de dados.
c)   Não deseja ser identificado.
5. Permitir que somente o ouvidor da entidade tenha acesso aos dados do requerente, quando
registrado o processo com identificação e restrição de dados.
6. Possibilitar que processos registrados com tipo anônimo, mesmo que o requerente inseriu o
registro logado no portal de autoatendimento, não sejam revelados os seus dados cadastrais.
7. Dispor de relatórios para acompanhar o andamento dos processos de ouvidoria, permitindo
filtrar por centro de custo, assunto, subassunto, requerente, data de abertura, entre outros.
8.   Permitir tramitar as solicitações entre setores ou para determinados usuários.
9. Emitir relatórios de assunto, subassunto, documento e listagem de processo por meio de telas
de consulta.
10. Emitir relatórios estatísticos com opção de agrupamento por: assunto, subassunto, centro de
custo atual, requerente, parecer e situação.
11. Controlar prazos da solicitação de acordo com o definido em roteiro, classificando os processos
pendentes através de cores (prazo final ou da etapa atual).
12. Permitir cadastrar processos de ouvidoria com requerente anônimo e sem login, podendo
informar telefone e/ou e-mail para contato, desde que configurado.
13. Na abertura do processo via sistema, permitir especificar a sua finalidade, sendo: atendimento
ao público ou processo interno da entidade.
14. Dispor de opção para paralisar e reabrir os processos de ouvidoria.
15. Inserir textos de abertura e movimentações dos processos sem limite de caracteres, permitindo
adicionar anexos.
16. Permitir ao gestor a visualização de todos os processos, independente do centro de custos em
que o processo esteja localizado.
17. Permitir ao requerente acompanhar sua solicitação por meio de serviço de ouvidoria via portal
de autoatendimento e aplicativo, sendo necessário informar o número do processo e o código
verificador ou CPF/CNPJ.
18. Possibilitar ao requerente adicionar novas informações ao processo de ouvidoria, por meio de
serviço disponível no portal de autoatendimento e aplicativo, com a utilização de login.
19. Aos usuários internos do sistema, dispor de parametrização que permita visualizar apenas os
processos do seu setor.
20. Gerenciar os processos com no mínimo os filtros: situação, número, ano, requerente, assunto,
subassunto, data abertura, observação, entre outros.
21. Manter histórico de tudo que foi realizado com o processo, inclusive as alterações executadas
em observação de abertura, nome de requerente, assunto e subassunto.
22. Dispor de repositório de modelos (Templates), que poderão ser utilizados como base para a
criação de novos documentos dentro dos processos de ouvidoria.
23. Gerenciar documentos salvando o arquivo editado como anexo do processo.
24. Disponibilizar no gerenciador de processos, a ordenação por: data da última movimentação e
podendo visualizar os últimos processos movimentados.
25. Permitir que processos de ouvidoria abertos pelo portal, os dados não sejam alterados por quem
está analisando, mediante parametrização.
26. Configurar envio de e-mail e notificação push ao requerente nas seguintes etapas do processo:
abertura, cancelamento, trâmite e encerramento;
27. Permitir pesquisar os processos por situação: em análise, aberto, tramitando, cancelado,
paralisado, arquivado.
28. Permitir abrir processos de ouvidoria via sistema.





## Mapeamento de funcionalidades ja existentes no legado

Objetivo: evitar retrabalho, priorizando reaproveitamento das rotinas em `resources/legacy`.

Diretorios legados relacionados a este modulo:
- `resources/legacy/ouvidoria` (arquivos no nivel do modulo: 79)
- `resources/legacy/atendimento` (arquivos no nivel do modulo: 194)

Checklist de reaproveitamento antes de implementar novo codigo:
- [ ] Inventariar rotinas existentes nesses diretorios (telas, relatorios, RPCs e integracoes).
- [ ] Validar cobertura contra o TR/SPS do modulo e marcar gaps reais.
- [ ] Reutilizar regra de negocio legada quando aderente (evitar reescrita desnecessaria).
- [ ] Modernizar com abordagem incremental (estrangulamento), mantendo compatibilidade funcional.
