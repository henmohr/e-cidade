# 5.36 MEMORANDO

Fonte: `/home/mohr/Downloads/cidade/sps.pdf` (seção 5 do Termo de Referência).

1. Deve permitir realizar a inclusão dos tipos de comunicados internos, sendo estes definidos
     minimamente como Memorando, Ofício, Circular, Portaria, Instrução Normativa, entre outros tipos
     adicionais, considerando a necessidade da contratante.
2. Possibilitar a inclusão de privilégios por setor em cada tipo, para que seja permitida a criação de
    tipos específicos para cada setor.
3. Deve possibilitar o cadastro de tags, para que sejam utilizadas como marcadores dos
    comunicados.
4. Deve permitir personalizar as cores das tags.
5. Permitir realizar a inclusão de dados referentes ao usuário logado, como cargo e CPF. Estes
    dados serão exibidos no corpo do texto dos comunicados durante as inclusões, respostas e
    encaminhamentos das comunicações.
6. Possuir abas referentes ao gerenciamento das comunicações, que sejam minimamente definidas
    como entrada, saída e arquivadas.
7. Deverá possuir caixa de entrada dos comunicados, apresentando todas as comunicações em que
    os setores relacionados ao usuário logado esteja envolvido.
8. Permitir gerenciar os comunicados, de forma que a exibição destes seja realizada em uma única
    caixa de entrada, sem a necessidade de trocar de tela para realizar o gerenciamento geral.
9. Deverá possuir caixa de saída dos comunicados, onde serão apresentadas todas as
    comunicações enviadas pelos setores relacionados ao usuário logado.
10. Permitir o gerenciamento dos comunicados de todos os setores cujo usuário logado está
    relacionado de forma simultânea, sem a necessidade de realizar a troca de telas.
11. Permitir que o controle de numeração seja realizado por ano e tipo.
12. Permitir que a contratada também possa realizar o controle da numeração por setor, órgão e/ou
    unidade.
13. Permitir salvar um comunicado como rascunho, caso a digitação não tenha sido finalizada.
14. Possuir aba específica com os comunicados que foram salvos como rascunho.
15. Permitir editar os rascunhos para que o envio definitivo do comunicado seja realizado.
16. Permitir inserir modelos de documentos para que estes sejam utilizados durante a inclusão de
    comunicados.
17. Permitir configurar se os comunicados poderão ser assinados por meio eletrônico, considerando
    a Lei nº 14.063/2020, que prevê as hipóteses pela assinatura eletrônica classificadas em simples,
    avançada e qualificada, de forma obrigatória ou opcional, de acordo com o tipo de comunicado, a
    partir das hipóteses previstas para a utilização de cada, conforme disposto na mesma lei.
18. Permitir configurar se os comunicados poderão ser definidos como urgentes durante a sua
    inclusão.
19. Permitir configurar se os comunicados poderão ser inseridos com indicativo de prazo.
20. Permitir adicionar arquivos para que estes sejam relacionados aos anexos da comunicação
    durante a inclusão desta.
21. Permitir realizar o upload de arquivos .doc ou .docx para que este seja utilizado como modelo no
    corpo do texto dos comunicados.
22. Permitir inserir comunicados sigilosos, onde estes poderão ser visualizados somente pelos
    usuários envolvidos.
23. As tags vinculadas aos comunicados deverão ser exibidas na tela de gerenciamento destes.




24. Deverá permitir cadastrar comunicados que sejam do tipo ‘Circular’. Estes comunicados não
    poderão ser respondidos e encaminhados, visto que serão comunicações para ciência dos
    setores.
25. Deve permitir durante a inclusão, resposta e encaminhamento definir um usuário como A/C (aos
    cuidados).
26. Permitir durante a visualização dos comunicados, que as movimentações enviadas aos cuidados
    do usuário logado tenham indicativo que diferencie a movimentação das demais.
27. Possuir filtro no gerenciamento que demonstre somente os comunicados que foram enviados aos
    cuidados do usuário logado.
28. Permitir responder comunicados, onde somente deverão ser exibidos como destinatários os
    setores previamente envolvidos na comunicação selecionada.
29. Permitir encaminhar comunicados, onde deverão ser exibidos todos os setores da entidade, a fim
    de compartilhar a comunicação entre os demais setores.
30. Durante as respostas e encaminhamentos, permitir vincular arquivos definindo-os como anexos
    do comunicado.
31. Todas as movimentações devem ser visualizadas em linha do tempo, onde cada movimentação
    de resposta, encaminhamento e/ou arquivamento deve gerar um novo registro.
32. Deverá permitir marcar um comunicado como lido.
33. Possuir indicativo visual dos comunicados que já foram lidos, diferenciando-os dos que ainda
    possuem movimentações que não foram visualizadas.
34. Deverá permitir arquivar comunicados para o setor do usuário logado.
35. Os comunicados que forem arquivados não devem ser exibidos na caixa de entrada do usuário,
    até que novas movimentações sejam realizadas.
36. Deverá permitir a reabertura de comunicado que foi arquivado, caso sejam necessárias novas
    movimentações.
37. Deverá permitir filtrar no gerenciamento de comunicados somente os comunicados com
    movimentações que ainda não foram lidas.
38. Possuir consulta que demonstre todos os usuários que já visualizaram, exibindo minimamente o
    nome do usuário e a data/hora da visualização do comunicado selecionado.
39. Deverá permitir favoritar comunicados para todo o setor ou somente para o usuário logado.
40. Deverá existir caixa de comunicados favoritados, a fim de facilitar o gerenciamento destes.
41. Deverá enviar notificação pelo sistema a cada nova movimentação dos comunicados.
42. Deverá permitir configurar a mensagem enviada nas notificações dos comunicados.
43. Deverá possuir serviço destinado aos usuários terceiros que recebem ofícios, que permita realizar
    a visualização desta comunicação.
44. Deverá permitir configurar se os ofícios poderão receber respostas complementares dos usuários
    terceiros por meio do serviço de comunicados.
45. Permitir realização a impressão da folha de rosto do comunicado.
46. Permitir realizar a impressão de cada movimentação dos comunicados, em relatório que poderá
    ser configurado de acordo com a necessidade da entidade.
47. Permitir realizar a impressão de todas as movimentações comunicado, em relatório que poderá
    ser configurado de acordo com a necessidade da entidade.




## Mapeamento de funcionalidades ja existentes no legado

Objetivo: evitar retrabalho, priorizando reaproveitamento das rotinas em `resources/legacy`.

Diretorios legados relacionados a este modulo:
- `resources/legacy/protocolo` (arquivos no nivel do modulo: 395)
- `resources/legacy/diversos` (arquivos no nivel do modulo: 34)

Checklist de reaproveitamento antes de implementar novo codigo:
- [ ] Inventariar rotinas existentes nesses diretorios (telas, relatorios, RPCs e integracoes).
- [ ] Validar cobertura contra o TR/SPS do modulo e marcar gaps reais.
- [ ] Reutilizar regra de negocio legada quando aderente (evitar reescrita desnecessaria).
- [ ] Modernizar com abordagem incremental (estrangulamento), mantendo compatibilidade funcional.
