# 5.38 CONSTRUÇÃO CIVIL

Fonte: `/home/mohr/Downloads/cidade/sps.pdf` (seção 5 do Termo de Referência).

1. Infraestrutura e segurança
2. Segurança e infraestrutura na nuvem
3. O sistema deverá ser hospedado em nuvem, podendo ser acessado por navegadores de internet
populares sem a necessidade de instalação de programas externos;
4. A nuvem deverá possuir escalabilidade automática conforme a demanda; 5.1.1.3
5. O sistema deverá ser provido por conexão SSL e protocolo HTTPS para uma transmissão segura
de dados;
6. O banco de dados deverá ser provido por mecanismo de backup automático de dados no servidor
da contratada e no servidor do Município, de 06 (seis) em 06 (seis) horas;
7. As senhas cadastradas por usuários devem ser armazenadas de forma criptografada no banco de
dados;
8. Deve ser possível que usuários recuperem suas senhas através de formulário de “esqueci minha
senha”;
9. O mecanismo de recuperar a senha deve ser do tipo “redefinição de senha”, onde não é enviada a
senha para o usuário, mas sim um formulário com instruções para que sua senha seja alterada;
10. Deve ser possível configurar a sessão de login do usuário para que expire em 30 (trinta) minutos
de inatividade;
11. Considerando que o sistema precisará consumir dados de sistemas legados, como por exemplo
cadastro imobiliário, deve ser possível que o sistema permita, através de interface, gerenciar o
recebimento de informações por sistemas legados do município,
12. Considerando que o sistema precisará enviar dados para sistemas legados, como por exemplo
valores de taxas, deve ser possível que o sistema permita, através de interface, gerenciar o envio de
informações para legados do município;
13. Considerando que o sistema precisará consumir dados de planilhas e outros arquivos de dados,
deve ser possível que o sistema importe, consuma e visualize datasets nos formatos de XLS, CSV,
JSON, GeoJSON, Geopackage e Shapefile, através da interface do software;
14.O sistema deverá ser capaz de acessar dados legados através de serviços web, caso disponíveis,
utilizando os padrões SOAP ou REST;
15.O sistema deverá permitir a comunicação bilateral com qualquer software que o Município possa
vir a adquirir, disponibilizando as API( Application Programming Interface) de forma a integrar os
sistemas possibilitando benefícios como a segurança dos dados, com facilidade no intercâmbio de
informações com diferentes linguagens de programação e a monetização de acessos e também
todos os códigos necessários para essa comunicação.
16. Deve ser possível que a integração consulte os valores mais atuais retornados pelos
WebServices mesmo em processos em trâmite, assegurando a versão atualizada das informações;
17. Deve ser possível que o retorno da requisição do WebService seja salva permanentemente no
processo;
18. Deve ser possível que os dados integrados sejam utilizados para a validação de informações
inseridas por requerentes, ou auxiliem os analistas durante a análise;
19. O sistema deverá permitir o login através de dados que serão validados por sistema terceiro

através de Webservice SOAP ou REST, seguindo orientação via nota técnica que será fornecida
durante o período de implantação, onde com este login devidamente validado, o sistema deverá
importar os dados necessários para cadastro, estes vindos do sistema de gestão municipal para o
presente sistema através de Webservice SOAP ou REST que é fornecida pelo sistema de gestão
municipal.
20. O sistema deverá permitir que sistemas de terceiros possam recuperar informações do presente
sistema através de Webservices REST, sendo o mínimo os seguintes
21. Recuperar todos os processos do sistema, podendo-se filtrar através de, no mínimo, dados de
Cadastro Imobiliário dos Terrenos e Unidades, contendo os dados de listagem dos mesmos;
22. Recuperar todos os dados de um determinado processo, onde o filtro será através do código (ID)
ou então o número e ano do processo, a fim de recuperar todas as informações que sejam visíveis
no sistema e também, informações que possam não ser visíveis mas que são importantes para a
operação do sistema;
23. Recuperar/Baixar documentos que estejam anexados junto ao sistema, sejam estes documentos
criados pelo mesmo ou então anexados por algum usuário na operação do sistema,
24.Recuperar demais informações que possam ser necessárias que estejam vinculadas aos
processos do sistema, como por exemplo, dados de tipos de documentos, tipos de processos e
demais dados que possam ser necessários devido à recuperação dos dados dos processos.
25. Acessibilidade:
      a) O sistema deverá contar com uma interface responsiva, ou seja, que é adaptável ao uso de
      dispositivos móveis, como tablets e smartphones;

      b) Por interface responsiva, entende-se uma que seja otimizada ao uso de dispositivos
      móveis, principalmente ao uso de touch screen (tela sensível ao toque);

      c) O acesso ao sistema deve ser realizado através de ação de login, mediante usuário e
      senha pessoais e intransferíveis;

      d) Os usuários devem cadastrar suas credenciais de acesso através de formulário de
      cadastro online;

      e) Deve ser possível que o município edite os requisitos do formulário de cadastro através de
      interface de customização de formulários, sem a necessidade de solicitar alterações para a
      contratada;

      f) A interface de customização de formulários deverá ter alterações realizadas de forma
      automática, sem qualquer tipo de liberação ou homologação pela contratada;

51. Considerando que o sistema deverá lidar com tarefas de licenciamentos que envolvem analistas
    e requerentes, será preciso o gerenciamento de permissões destes participantes, definindo o que
    cada agente poderá realizar;
52. Para que o município não dependa da contratada para gerenciar as permissões dos
    participantes, deverá haver uma interface que possibilite o município gerenciar as permissões
    dos usuários;
53. As permissões de usuários deverão ser classificadas conforme suas ações, tanto as de caráter
    processual, como as de caráter administrativo;
54. Permissões de caráter processual:
  a) Criação de um processo;

  b) Análise de um processo;

  c) Edição de um processo;

  d) Visualização de um processo;

  e) Visualização de alvará provisório antes de um processo estar deferido;

  f)Encaminhamento de um processo;

  g) Deferimento de um processo;

  h) Indeferimento de um processo;

  i) Geração de relatório de um processo;

  j) Reabertura de um processo;

  k)Permissões em processos de um setor;

55.        Permissões de caráter administrativo:

           a) Painel de criação de usuário interno;
           b) Customização de fluxos e formulários;
           c) Acesso ao painel de configuração de integrações;
           d) Visualização de informações dos usuários;
           e) Acesso a estatísticas;
           f) Geração de relatórios de um determinado servidor ou profissional;

56. Deverá ser possível criar grupos de permissão, como por exemplo, “Analista”, no qual seja
possível que todos os usuários atribuídos a este grupo, possuam as permissões padrões do grupo;

57. Além de permissões em grupo, deve ser possível aplicar permissões de forma individual aos
usuários;

58. Protocolo e tramitação:

           a) O software deverá possibilitar que todo o trâmite processual seja realizado de forma digital,
           desde o protocolo, até o deferimento e emissão automática de documentos;

           b) Por trâmite digital, entende-se:

      1.     Possibilidade do requerente protocolar a demanda;

      2. Possibilidade do sistema de forma automática validar informações inseridas pelo
         requerente, seja através do cruzamento de dados, ou através de cálculos matemáticos;

      3. Possibilidade de triagem da solicitação;

      4. Possibilidade de um analista escolher trabalhar com a demanda, através da interface;

      5. Possibilidade do analista iniciar sua análise sobre o processo, através da interface;

      6. Possibilidade de no mesmo processo, o analista inserir comentários e reprovar
         individualmente os campos inseridos pelo requerente, através da interface;

      7. Possibilidade do requerente promover as adequações solicitados pelo analista no mesmo
         processo, através da interface;

      8. Possibilidade do analista deferir, indeferir ou encaminhar o processo, através da interface;

      9. Possibilidade de no ato do deferimento, documentos como alvarás e certidões sejam
         emitidos de forma automática, utilizando de dados gerados no processo e sem a
         necessidade de digitação por parte do analista;

      10. Possibilidade de armazenar histórico de informações inseridas pelo requerente e analisadas
          pelo analista, através da interface;

      11. Possibilidade de consultar o histórico de informações inseridas pelo requerente e analisadas
          pelo analista, através da interface;


       12. Possibilidade reabrir processos finalizados, através da interface;

       13. Possibilidade de bloquear processos deferidos e indeferidos para edição, podendo somente
           serem editados caso sejam reabertos;


  c)            Processos deferidos ou indeferidos deverão ser bloqueados para edições, visando
            resguardar suas informações e reprimir a possibilidade não deve ser possível realizar ações
            neste.

  d)            Para que processos bloqueados possam ser editados, deverá ser preciso reabri-los, ação
            a qual deverá registrar o usuário que requereu a reabertura e o motivo.

  e)            Deverá ser possível que um processo seja encaminhado para diferentes usuários, visto
            que dependendo da etapa do fluxo, a competência poderá ser de outro analista ou setor.

  f)            Para preservar a integridade das informações, somente um usuário envolvido no processo
            poderá efetuar alterações, neste caso:

  •             Quando o processo estiver com o requerente para que este realize adequações, somente
            o requerente poderá promover edições;

  •            Quando o processo estiver em mãos do analista para análise, somente o analista poderá
            promover edições;

  •            Caso um analista encaminhe o processo para um novo analista, somente o novo analista
            poderá promover edições;

            g) Para ações que resutam na transferência de capacidade de edições de um processo, como
            por exemplo deferimento, indeferimento, encaminhamento e reabertura, antes de tais ações
            serem realizadas, deverá ser exibida uma caixa de diálogo para confirmar a ação por parte do
            usuário;

59. Considerando que mudanças em fluxos e requisitos são frequentes no âmbito do licenciamento
    de obras, deverá haver interface que permita a customização de fluxos e formulários pelo
    município, sem a necessidade de solicitar modificações para a contratada;
60. No mecanismo de customização de fluxos, deverá ser possível:
       a)        Editar etapas;

       b)        Editar a triagem de analistas e setores conforme suas etapas;

36.Por edição de formulários, entende-se:
        1. Editar campos de formulários;
        2. Remover campos de formulários;
        3. Adicionar campos de formulários;
        4. Customizar validadores em campos de formulários;
        5. Aplicar regras de validações em formulários de protocolos, como exemplo a validação de
        coeficientes construtivos;
        6. Editar formatos de arquivos aceitos em determinado campo de anexo;
37. Os formulários deverão ser autonômos, ou seja, regras e validações aplicadas a um formulário
    ou campo de formulário, não deverão vincular outros campos e outros formulários;
38. A limitação de formatos aceitos em campos de anexo, não deverá vincular outros campos de
    anexos no sistema, ou seja, deve ser possível que somente um campo de anexo não permita
    determinado formato de arquivo, porém os outros campos possam aceitá-lo.
39. Para que a análise seja mais célere, deverá ser possível aplicar mecanismos de validação em
    campos de formulários a serem preenchidos pelo requerente, de forma que o próprio sistema
    possa validar critérios objetivos;
40. Deve ser possível validar campos de valores máximos e valores mínimos a partir de cálculos
    entre variáveis, ou seja, quando o solicitante inserir um valor acima ou abaixo do permitido, o
    formulário deve impedir o prosseguimento para a próxima fase;
41. As regras de validações devem poder serem parametrizadas através de interface do sistema,
    para que o analista possa efetuar alterações sem uma nova atualização da aplicação por parte
    da contratada;
42. Deve ser possível, cruzar um dado fornecido pelo requerente no formulário, com uma
    informação de base de dados externa, em tempo real, durante o preenchimento do formulário;
43. Deve ser possível utilizar uma informação inserida pelo usuário em um campo, para através de
    cálculos e outras manipulações de dados, completar outros campos do formulário.
44. Como exemplo, a partir do CEP, preencher os campos de endereço;
45. Deve ser possível cadastrar e atualizar regras de validação no sistema de forma independente
    de um formulário específico, ou seja, deve ser possível referenciar uma regra de validação em
    vários formulários;
46. Na medida que se altera essa regra (como por exemplo uma multiplicação entre valores,
    verificações de metadados, etc), a alteração deve ser propagada automaticamente aos
    formulários que a possuem;
47. Deve ser possível optar por aplicar ou não as atualizações de regras de validação em processos
    em andamento, ou seja, uma alteração de regra de validação pode ou não ser propagada a
    processos já criados;
48. Deve ser possível aplicar regras de validação específicas em um campo com base em valores
    de outro campo;
49. Deve ser possível que um campo seja resultado de uma expressão matemática entre valores de
    diversos campos, customizável através da interface de edição de processos;
50. Deverá haver interface de controle de processos do tipo caixa de entrada, para que requerentes
    e analistas possam ver suas demandas e tarefas;
51. Como requerente, deverão ser estruturadas pelo menos dois tipos de caixas de entradas:
      a) Caixa de entrada de processos em trâmite:
           • Deverá exibir todos os processos em andamento do requerente;

            •   Deverá haver mecanismo que sinalize quais processos necessitam de ações por
                parte do requerente, como por exemplo um processo que o analista solicitou
                adequações;

      b) Caixa de entrada de processos finalizados:
           •    Deverá ser composta por todos os processos já criados pelo requerente e que
               foram finalizados (deferidos e indeferidos);

       c) Como analista, deverão constar as seguintes caixas de entradas:
      1. Caixa de entrada de processos em análise:
           • Deverá ser composta pelos processos em trâmite que estão sob responsabilidade
               de análise do analista;

            •   Deverá haver mecanismo que sinalize quais processos necessitam de ações por
                parte do analista, como por exemplo um processo readequado pelo requerente;

      2. Caixa de arquivo de processos já analisados:

            •   Deverá exibir processos finalizados e que foram de responsabilidade do analista;

      3. Caixa do setor - processos em andamento:
           • Deverá exibir todos os processos que estão em andamento no setor, com seus
               prazos e analista responsável;

     4. Caixa do setor - processos finalizados:
             • Deverá exibir todos os processos finalizados no setor, com indicação do analista
                responsável;

52. Deverá haver mecanismo de controle de prazos, para a sinalização nas caixas de entradas de
    processos, sendo possível visualizar processos atrasados (urgentes), com prazo médio e recém
    protocolados;
53. Os processos em caixas de entrada deverão exibir no mínimo as seguintes informações:
a)   Data de recebimento;
b)   Requerente;
c)   Status do trâmite;
d)   Prazo;
e)   Data da última ação;
53. Considerando que os processos só podem estar em posse de um analista por vez, deve haver
interface que permita a distribuição de processos de um analista, visto que o analista poderá estar
em período de férias ou ser relotado;
54. No procedimento de redistribuição de processo, deverá ser possível selecionar quais processos
serão distribuídos e qual o analista que será responsável;
55. Processos em mãos do requerente para adequações e que não sejam realizadas no prazo de 30
(trinta) dias, o sistema poderá disponibilizar o indeferimento automático em casos excepcionais.
56. O sistema deverá possibilitar que nenhum dado seja substituído, mas que todos sejam
armazenados e consultados;
57. Devem ser armazenadas diferentes versões de protocolos, análises, adequações e reanálises,
para que seja sempre possível identificar quem inseriu determinado dado e quando;
58. Deverá haver interface para consulta destas diferentes versões;
59. Mecanismos de análises:
      a) A análise deverá ser realizada através de mecanismo que permita a exibição do campo
      inserido pelo requerente ao lado do campo de observação (em caixa de texto provida por
      formatação) a ser inserido pelo analista;

      b) Além do campo de observação escrita, deverá haver mecanismo que permita sinalizar o
      campo como adequado ou em necessidade de adequações;

      c) O campo de observação escrito deverá também permitir o anexo de imagens e
      documentos;

      d) Ao requerente, deverá ser exibido os campos sinalizados como adequados, os que
      necessitam adequações e todas as observações inseridas pelo analista;

      e) Quando o requerente efetua as adequações, deverá ser sinalizado ao analista todos os
      campos que sofreram modificações pelo requerente;

      f) O requerente deverá ser notificado via e-mail quando o seu processo sofrer
      movimentações;


      60. Autenticação Digital:
      a) A autenticação digital de processos e documentos, deverá garantir a identificação do autor
      e a integridade do documento ou processo expedido em meio eletrônico, sendo realizada
      através da utilização das credenciais do usuário;

      b) O procedimento de autenticação de documentos não deverá prejudicar a qualidade e
      proporções de imagens que possam fazer parte deste;

      C) Os documentos gerados pelo software deverão ser compostos por informações e anexos

      gerados ao decorrer do processo, sem a necessidade de inserção de informações adicionais
      pelo analista;

      D) Os documentos gerados pelo software deverão ser autenticados de forma automática;

      E) No quesito de autenticação de pranchas, deve ser possível que o analista, através de
      edição de chaves, escolha quais serão os campos de imagens utilizados para a geração de
      pranchas;

      F) Deverá ser possível autenticar qualquer documento inserido no processo, com a
      possibilidade de escolher quais serão assinados através de interface de configuração;

      G) Deverá ser possível definir diferentes layouts de assinatura para um mesmo processo, no
      entanto para arquivos anexados diferentes;

      H) Além da possibilidade de autenticar documentos através das credenciais do usuário,
      deverá haver interface que possibilita a autenticação de documentos através de certificados
      digitais padrão ICP, no formato PFX;

61. Deverá haver mecanismo que permita consultar a veracidade e integridade dos documentos e
processos emitidos eletronicamente;
62. Este mecanismo deverá ser provido por métodos de verificação acessíveis, como códigos de
barras, ou URL, ou QR-CODE;
63. A partir da verificação através do mecanismo, deverá ser possível consultar se o documento ou
processo bate com o que foi apresentado;
64. Deve ser possível que documentos gerados pela plataforma sejam cancelados, onde ao
consultar sua veracidade e integridade, deverá ser exibida uma informação de que o documento foi
cancelado;
65. Deverá haver interface para sinalizar documentos expedidos como cancelados;
66. Deverá ser possível gerar relatórios completos de um processo, através de interface disponível
para os analistas;
67. Os relatórios completos de um processo, deverão ser gerados em formato PDF e possuírem
mecanismo de autenticação compatível com o preconizado no item 9.18.1;
68. Para controle de qual usuário gerou o relatório, deverá haver um log que registre o horário,
processo e analista que gerou o relatório;
69. O relatório deverá conter todos os dados escritos pelo requerente, bem como:
      a) Todas as versões do processo (versões de dados inseridos pelo requerente e analista);
      b) Miniaturas de documentos anexados pelo requerente;
70. Anexos que sejam compostos por arquivos de imagem, deverão aparecer no relatório com a
imagem anexada;
71. O relatório não deverá ser dividido em vários arquivos, ou seja, deverá conter todas as
informações em um único arquivo.
72. O licenciamento de obras é constituído por uma pluralidade de fluxos definidos em lei, com a
finalidade de obtenção de alvarás, licenças, certidões e documentos;
73. Considerando a obrigatoriedade do município de Marechal Cândido Rondon PR, ao envio de
relatórios de alvarás e cartas habite-se expedidos, por meio do sistema SisobraPref, da Receita
Federal, é necessário que o software tenha funcionalidade que permita:
a) Compilar todos os alvarás e carta habite-se em arquivo que atenda aos padrões do SisobraPref e
   possa ser encaminhado(ou de forma automática), sem a necessidade dos analistas montarem o
   arquivo de forma manual, de forma a permitir a comunicação bilateral com qualquer software que
   o Município venha a adquirir, integralizado com o sitema IPM, já utilizado na Prefeitura Municipal

 74. Reitera-se que todo o trâmite de licenciamento de obras deverá contemplar todas as demais

 funcionalidades do item 5, no caso:
      a)   Infraestrutura e segurança:
      b)   Acessibilidade e permissões:
      c)   Processos eletrônicos:
      d)   Análise:
      e)   Mecanismos de autenticação e tecnologia em documentos:
      f)   Dados e estatísticas:
      g)   Mecanismos de ajuda:

75   Buscas de conteúdo no sistema:
1. Deverá ser possível buscar todas as informações inseridas pelo requerente e analistas no
sistema, ou seja, deverá ser possível:
      a) Buscar conteúdo de qualquer campo de formulário preenchido pelo requerente;
      b) Buscar conteúdo de metadados de documentos inseridos pelo requerente;
      c) Buscar conteúdos de análises realizadas por analistas;
2.   Deverá haver busca avançada que possibilite:
      a) Efetuar buscas de dados em um determinado campo de formulário;
      b) Efetuar buscas por número de processo;
      c) Efetuar buscas por nome do requerente;
      d) Efetuar buscas por tipo de processo;
      e) Efetuar buscas por fase do processo;
3.   Deverá ser possível filtrar os resultados das buscas em ordenação por:
      a) nome do requerente;
      b) número do processo;
      c) status do trâmite;
      d) usuário em posse do processo;
      e) data de última ação;
      f) data de criação;
4. A funcionalidade de busca, deverá também funcionar por proximidade de informação,ou seja,
ao inserir um termo, devem ser retornados também resultados semelhantes ao informado;
5.   Deverá ser possível efetuar buscas em campos de formulários do tipo “select” e “radio”;
6.   Deverá ser possível buscar usuários no sistema, a partir de:
      a) E-mail;
      b) Nome;
7. Deverá ser possível buscar todas as informações inseridas pelo usuário no formulário de
cadastro, com exceção da senha;
8. Deverá ser possível enviar e-mails para os usuários a partir do sistema, sem a necessidade de
abrir sistemas externos; O e-mail deverá ser enviado através de endereço e domínio administrados
pela contratada, porém com indicação de que é um e-mail enviado por analista do município;
9.   A interface do sistema deverá contar com uma linha do tempo de ações realizadas no software;
10. A linha do tempo corresponde a um histórico das últimas ações efetuadas no sistema pelos
analistas;
11. A linha do tempo deverá exibir, por linha ou outro mecanismo de divisão, o número do processo,
ação realizada, quem realizou a ação:

1. Por ação realizada, entende-se uma das seguintes:
  a) Análise;
  b) Encaminhamento;
  c) Deferimento;
  d) Indeferimento;
  e) Emissão de taxas;
2. Por quem realizou a ação, entende-se uma das seguintes:
      a) O analista;
      b) O setor;
12. Deve ser possível que o analista acesse um processo da linha do tempo, ao clicar sobre este;
13. Visando a transparência do procedimento de licenciamento de obras, deverá haver uma
interface que possibilite a exibição dos dados gerados em estatísticas pertinentes;
14. O painel de estatísticas deverá possuir no mínimo as seguintes informações:
        a) Número de processos, podendo serem segmentados pelo tipo de fluxo;
        b) Processos protocolados, analisados, deferidos ou indeferidos em um período que pode ser
        selecionado;
        c) Tempo médio para um processo ser deferido, podendo ser segmentado conforme o fluxo
        do processo;
     d) Média de análises realizadas em um processo até o deferimento;
     e) Gráfico sobre a eficiência de deferimento de processos em uma semana;
     f) Gráfico sobre usuários ativos no sistema em uma semana;
15. Deverá ser possível exportar os dados gerados nas estatísticas em arquivo no formato PDF ou
CSV;
16. Ajuda guiada:
        a) Deverá haver uma funcionalidade que permita uma ajuda guiada do usuário na ferramenta,
        ou seja, que sejam exibidas na própria tela do sistema, passos a passos sobre a utilização do
        sistema;
        b) A ajuda guiada deverá ser construída em modelo de passo a passo, onde o usuário deverá
        clicar para passar ao próximo passo do procedimento de instrução;
        c) O usuário deverá poder escolher por fazer ou não fazer o procedimento da ajuda guiada;
17. Considerando que os protocolos de licenciamentos apresentam complexidade e peculiaridades,
no ato do preenchimento de campos de formulários, deverão haver campos de auxílio lado a lado ao
campo em preenchimento pelo requerente;
18. Para que o município tenha liberdade para modificar fluxos e campos de ajudas, deverá haver
uma interface que permita a customização do conteúdo dos campos de ajuda;
19. A interface de customização dos campos de ajuda deve possibilitar a inserção de texto
formatado, com no mínimo negrito, sublinhado e modificação de cor da fonte;



