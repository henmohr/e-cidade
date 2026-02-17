# CARACTERÍSTICAS GERAIS BÁSICAS E OBRIGATÓRIAS DOS SISTEMAS (ATENDIMENTO DE 100%)

Fonte: `PE+09-2026+EDITAL+GESTAO+PUBLICA.pdf` (seção 17.6, bloco de critérios objetivos da PoC).

## 1.1 Do Provimento de Data Center (Ambiente Cloud)

1.1.1 A contratada deverá disponibilizar datacenter para alocação dos sistemas objeto da licitação, com capacidade de processamento (links, servidores, nobreaks, fontes alternativas de energia, virtualização, segurança e climatização), compatível com as necessidades e o volume de operações da contratante.

1.1.2 A comprovação dos requisitos de datacenter e banco de dados deverá ser apresentada em tela, com acesso em tempo real, não sendo aceita apenas documentação.

1.1.3 A estrutura de datacenter poderá ser própria ou terceirizada. Em caso de terceirização, a proponente deverá garantir atendimento às finalidades da licitação e às exigências do Termo de Referência.

1.1.4 A contratada é responsável por manter os sistemas básicos (sistema operacional, servidor de aplicação, servidor de banco de dados etc.) em atualização contínua, especialmente quando houver falhas de segurança reportadas pelos fabricantes/comunidade.

1.1.5 O datacenter deve respeitar boas práticas de segurança, alta disponibilidade e proteção ambiental.

1.1.6 Deve garantir SLA físico mínimo de 99,9%, com certificações e atestados de conformidade avaliados por auditor independente.

1.1.7 O ambiente deve apresentar controles de gerenciamento de segurança conforme ISO 27001.

1.1.8 O ambiente deve apresentar padrão de qualidade global conforme ISO 9001.

1.1.9 Os sistemas devem preferencialmente utilizar gerenciadores de banco de dados com licença Open Source.

1.1.10 O datacenter deverá possuir balanceador de carga em alta disponibilidade.

1.1.11 O datacenter deverá possuir ponto de restauração do ambiente no intervalo mínimo de 5 minutos até os últimos 30 dias.

1.1.12 O datacenter deverá possuir capacidade de crescimento horizontal (aumento da capacidade do cluster), sem prejuízo de disponibilidade.

## 1.2 Do Provimento de Banco de Dados (SGBD)

1.2.1 Caso a contratante opte por ferramenta proprietária compatível, o fornecimento da licença será de responsabilidade da contratante.

1.2.2 Deve ser utilizado SGBD relacional.

1.2.3 O backup deve permitir restauração completa diretamente na ferramenta de banco de dados do provedor, incluindo dados e metadados essenciais (ex.: chaves primárias e restrições), garantindo integridade e consistência.

1.2.4 A contratada deverá manter cópias de backup com as seguintes configurações:

1.2.4.a Garantir backup e integridade dos arquivos de estrutura do sistema, bem como relatórios e layouts específicos da entidade.

1.2.4.b Backups com rotina automatizada, mantidos em datacenter próprio ou terceirizado pela proponente.

1.2.4.c Disponibilizar cópia dos dados para a contratante com no mínimo 15 dias retroativos acessíveis pelo sistema.

1.2.4.d Manter retenção adicional de ao menos 35 dias retroativos de movimentação.

1.2.5 O acesso para visualização/download dos backups pela contratante deverá exigir autenticação com certificado A3, para atender à LGPD.

1.2.6 O SGBD deverá possuir controle de credenciais para impedir acesso não autorizado para consulta, alteração, impressão ou cópia.

1.2.7 O banco de dados (SGBD) deverá ser único, podendo ser compartilhado somente entre o SIAFIC da entidade, vedada inclusão de dados de entidades externas ao município.

## 1.3 Das Configurações da Plataforma do Software de Gestão

1.3.1 A plataforma deverá suportar execução simultânea de múltiplas instâncias de back-end para alta disponibilidade.

1.3.2 A infraestrutura deverá seguir conceito de imutabilidade, permitindo recriação/descartes de instâncias sem impacto na disponibilidade.

1.3.3 O acesso deverá ocorrer por domínio/subdomínio único da contratante, via HTTPS com certificado válido.

1.3.4 A contratada deverá fornecer ambiente de homologação com dados da contratante para treinamento, validação de funcionalidades e correções antes da produção.

1.3.5 Atualizações dos módulos devem ocorrer de forma transparente, sem desconectar usuários ou exigir nova autenticação.

1.3.6 Em manutenção agendada de módulo específico, os demais módulos devem permanecer operacionais.

1.3.7 O sistema deve registrar logs de autenticação (login/logout e dados adicionais).

1.3.8 Se houver falha de autenticação no último uso do login, o sistema deverá alertar por e-mail e listar últimos acessos.

1.3.9 O usuário local deve poder consultar sessões ativas (aplicações abertas, início, último acesso, tempo de sessão, IP de origem) e encerrar sessões.

1.3.10 Os módulos devem aplicar legislação vigente (federal e estadual), com adequações sem custo adicional.

1.3.11 É vedado uso de aplicações desktop/cliente-servidor emuladas em navegador ou acesso remoto por RDP.

1.3.12 O sistema deve ser nativo web e conter, no mínimo:

1.3.12.a Linguagem nativa web (ex.: Java, C#, Python, entre outras).

1.3.12.b Operação nos navegadores Firefox, Chrome, Edge e Safari, além de Android e iOS.

1.3.12.c Responsividade para tablets e smartphones.

1.3.12.d Camada cliente com tecnologias padrão amplamente difundidas (HTML, CSS, JavaScript), sem plugins/runtime adicional.

1.3.12.e Operação multiusuário, integração total entre módulos, cadastro único, multientidade (Prefeitura/Câmara) e uso de exercícios anteriores sem troca de sistema.

1.3.12.f Acesso transparente por domínio/subdomínio único da contratada, exclusivo da contratante.

1.3.12.g Operação por múltiplas abas/janelas, alternando exercícios e entidades sem fechar aplicação.

1.3.12.h Estrutura em “n” camadas: front-end, servidor de aplicação (podendo ser distribuído) e banco de dados.

1.3.12.i Acesso por HTTPS com criptografia dos dados em trânsito.

1.3.12.j Proteção de código-fonte da aplicação (sem exposição de regras de negócio no “exibir código fonte”).

1.3.12.k Tráfego cliente-servidor mínimo, preferencialmente com conteúdo em JSON para camada front-end.

1.3.12.l Validações básicas no cliente (ex.: CPF/CNPJ, campos obrigatórios).

1.3.12.m Feedback imediato de ações; em operações transacionais, mensagem somente após conclusão (sucesso/falha).

1.3.12.n Acesso ilimitado de usuários simultâneos, sem novas licenças de softwares da solução.

1.3.12.q Permitir abertura de novas guias/janelas a partir do menu principal.

1.3.12.r Permitir múltiplas guias/janelas independentes, preservando contexto em atualização/reload.

1.3.12.s Permitir compartilhamento de URLs diretas de cadastros/rotinas.

1.3.12.t Permitir abertura simultânea de vários módulos (inclusive o mesmo módulo em várias abas), incluindo uso em dois monitores.

1.3.12.u Estrutura de navegação com identificação da página atual e acesso rápido aos níveis superiores.

1.3.13 Garantir integridade referencial no banco, impedindo exclusão de informações vinculadas a registros ativos.

1.3.14 Formulário de autenticação único vinculado ao CPF, sem duplicidade de usuários, com opções mínimas: usuário/senha, Facebook, Google, GOVBR, Microsoft, token de certificado A3 e certificado A1.

1.3.15 Gerenciador central de usuários e permissões, contendo no mínimo:

1.3.15.a Relacionar usuário a um ou mais perfis (pré-definidos ou personalizados).

1.3.15.b Aplicar privilégios de perfis para consulta, inclusão, alteração, exclusão e demais ações.

1.3.15.c Garantir senha trafegada e armazenada de forma criptografada/hash (ex.: MD5/SHA), sem exibição em telas.

1.3.15.d Permitir autenticação multifator (MFA).

1.3.16 Ambiente próprio do usuário para manutenção/verificação de dados, com no mínimo:

1.3.16.a Alteração de senha.

1.3.16.b Vincular/desvincular contas de redes sociais.

1.3.16.c Recuperação de senha por e-mail principal, e-mail secundário e SMS.

1.3.16.d Recuperação via link de redirecionamento para cadastro de nova senha.

1.3.16.e Exigir troca de senha no primeiro login quando redefinida por administrador.

1.3.17 Cadastro Único com compartilhamento de dados (não integração por artifícios), contendo no mínimo:

1.3.17.a Cadastro de pessoas.

1.3.17.b Entidades.

1.3.17.c Bancos.

1.3.17.d Agências.

1.3.17.e Legislação.

1.3.17.f Cidades.

1.3.17.g Bairros.

1.3.17.h País.

1.3.17.i Logradouros.

1.3.18 O Cadastro Único deve compartilhar dados com todos os demais módulos do sistema.

1.3.19 Funcionalidades mínimas no cadastro de pessoas:

1.3.19.a Definição de pessoa física/jurídica.

1.3.19.b Vinculação de endereços comercial, residencial e correspondência, vinculados a logradouros.

1.3.19.c Cadastro de contatos (telefone residencial, celular, e-mail).

1.3.19.d Vincular certidões e vigência/estado ativo.

1.3.19.e Anexar arquivos digitais da pessoa.

1.3.19.f Cadastro de ocorrências restritivas/não restritivas, com bloqueio quando restritiva (ex.: licitações).

1.3.19.g Registro de alterações de razão social por data de vigência.

1.3.19.h Administrador local deve visualizar dados do usuário: data de cadastro, status ativo/inativo, último acesso, com ordenação.

1.3.20 Funcionalidades mínimas no cadastro de legislação:

1.3.20.a Identificação da abrangência (municipal, estadual, federal).

1.3.20.b Registro de veículos e datas de publicação, com múltiplas publicações.

1.3.20.c Registro de alterações de lei/ato e leis/atos alteradores.

1.3.20.d Upload de arquivos para leis/atos.

1.3.20.e Definição de categorias/assuntos com upload obrigatório.

1.3.20.f Cadastro de gestões/legislaturas com inclusão de pessoas e cargos vinculados.

1.3.20.g Inclusão de autores por gestão/legislatura, relacionados à lei/ato.

1.3.20.h Envio por e-mail da lei/ato a partir do cadastro.

1.3.21 Manter log de auditoria para inclusões, alterações e exclusões, registrando: tipo da operação, usuário, operação e dados incluídos/alterados/excluídos.

1.3.22 Todas as telas de consulta (inclusive consultas personalizadas) devem permitir:

1.3.22.a Filtros personalizáveis por chaves de acesso, isolados e combinados.

1.3.22.b Operadores como: menor/igual, maior/igual, inicia com, contém, diferente, igual, maior, menor.

1.3.22.c Ordenação ascendente/descendente.

1.3.22.d Seleção de registros por página e navegação entre páginas.

1.3.22.e Remoção dos filtros aplicados.

1.3.23 Estrutura para campos adicionais em rotinas:

1.3.23.a Adição de campos por configuração simples, sem customização.

1.3.23.b Agrupamento de campos em áreas específicas das janelas de entrada de dados.

1.3.23.c Definição de regras de validação entre campos adicionais.

1.3.24 Gerador de consultas com, no mínimo:

1.3.24.a Seleção por metadados/modelagem ou SQL, com definição de nome, formato (monetário, data, numérico, texto), agrupadores e totalizadores.

1.3.24.b Definição dos sistemas onde a consulta ficará disponível, sem limite.

1.3.24.c Acesso direto pelos menus dos módulos.

1.3.24.d Definição de agrupamentos e totalizadores padrão.

1.3.24.e Aplicação de agrupadores/totalizadores na execução conforme necessidade do usuário.

1.3.24.f Ordenação dos registros retornados.

1.3.24.g Exportação para HTML, TXT, PDF, CSV, XLS, DOC, XML e JSON.

1.3.24.i Disponibilização da consulta apenas para o cliente autor, quando aplicável.

1.3.24.j Controle de versões, incluindo retorno a versão anterior.

1.3.24.k Execução antes da publicação para homologação.

1.3.24.l Exibição das alterações realizadas por versão.

1.3.25 Gerador de relatórios customizados com, no mínimo:

1.3.25.a Seleção de dados por metadados/modelagem ou SQL, inclusive sub-relatórios.

1.3.25.b Edição avançada e/ou criação de novos relatórios (formatação, imagens, agrupamentos etc.), podendo usar ferramenta externa sem custo adicional.

1.3.25.c Versionamento de relatórios, com rascunho sem impacto aos usuários e possibilidade de restauração.

1.3.25.d Emissão de relatórios com dados de diversos módulos (ex.: empenhos e licitações por fornecedor).

1.3.26 Integrações imprescindíveis (compras, almoxarifado, tributos, RH e patrimônio) devem estar disponíveis, garantindo compatibilidade, padronização e integração entre áreas.

1.3.27 Acessibilidade com opções de zoom e contraste: normal, escuro, protanopia, deuteranopia e tritanopia.

1.3.28 Criação de políticas de acesso com, no mínimo:

1.3.28.a Mais de uma política por grupo/usuário.

1.3.28.b Dias da semana permitidos para acesso.

1.3.28.c Usuários/grupos vinculados às políticas.

1.3.28.d Horários de permissão de acesso.

1.3.28.e Tempo de expiração de senhas por usuário/grupo.
