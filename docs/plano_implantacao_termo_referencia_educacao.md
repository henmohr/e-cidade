# Plano de Implantacao - Termo de Referencia E-Cidade (Educacao)

Base de referencia:
- Termo de Referencia dos servicos de E-Cidade para Educacao
- `docs/caracteristicas_gerais_basicas_obrigatorias.md`
- `docs/matriz_gap_poc_100.md`
- `docs/sps_modulos_unificados/README.md`

## 1. Objetivo

Estruturar a implantacao e a adequacao do E-Cidade para atender os requisitos tecnicos, funcionais e operacionais do Termo de Referencia da area de Educacao, cobrindo:

- infraestrutura cloud e disponibilidade;
- seguranca, autenticacao, auditoria e LGPD;
- backup, restore e governanca de dados;
- modulo escolar e secretaria;
- portal do aluno;
- matricula online e central de vagas;
- transporte escolar;
- recursos humanos integrado;
- BI, consultas e relatorios;
- migracao, treinamento, homologacao e entrada em producao.

## 2. Premissas do plano

- A base tecnologica atual do projeto e mantida em PHP.
- Os requisitos do edital que dependem de comprovacao operacional ou documental precisam de evidencias em ambiente real, nao apenas em codigo.
- A contratacao exige duas janelas de entrega:
  - implantacao inicial e operacionalizacao basica em ate 30 dias;
  - customizacoes e fechamento de 100% dos requisitos em ate 90 dias.
- O plano assume participacao da equipe tecnica da contratante para validacao, homologacao e fornecimento de dados de teste.
- Integracoes externas, certificados digitais e homologacoes formais podem deslocar parte do cronograma, sem alterar a ordem das dependencias criticas.

## 3. Eixos de cobertura do termo

### 3.1 Requisitos transversais

- datacenter cloud e alta disponibilidade;
- comprovacao em tempo real de infraestrutura e banco;
- backup, restore e retenao;
- acesso com certificado A3 para backup;
- dominio unico HTTPS;
- logs de autenticacao, sessao e auditoria;
- MFA, politicas de acesso e recuperacao de senha;
- acessibilidade, multiaba e uso web nativo.

### 3.2 Requisitos funcionais educacionais

- modulo escola;
- modulo secretaria de educacao;
- calendario escolar;
- portal do aluno;
- transporte escolar;
- BI educacional;
- recursos humanos da rede;
- matricula online e central de vagas;
- relatarios e exportacoes operacionais.

## 4. Mapa de aderencia por fase

| Bloco do termo | Fase do plano |
|---|---|
| 1.1, 1.2 e 1.3 transversais | Sprints 1, 2 e 3 |
| Regras de autenticacao, seguranca, auditoria e acesso | Sprints 1 e 2 |
| Cloud, HA, backup, restore e SLA | Sprint 3 |
| Modulo escola, secretaria e calendario | Sprint 4 |
| Portal do aluno e comunicacao com a comunidade escolar | Sprint 5 |
| Matricula online, central de vagas e transporte escolar | Sprint 6 |
| RH integrado, BI, consultas e relatorios | Sprint 7 |
| Migracao, treinamento, homologacao e go-live | Sprint 8 |

## 5. Roadmap proposto

### Sprint 1 - Descoberta, baseline e matriz de aderencia

Objetivo: fechar o diagnostico inicial e preparar o ambiente de trabalho.

Escopo:

- validar o inventario de requisitos do termo;
- mapear os requisitos para as areas do repositorio;
- revisar o estado atual dos modulos educacionais existentes;
- definir ambiente de homologacao, dados de teste e criterios de aceite;
- consolidar matriz de aderencia por requisito.

Entregas:

- matriz de aderencia do termo x repositorio;
- backlog priorizado por dependencia;
- plano de homologacao inicial;
- ambiente tecnico minimo para desenvolvimento e testes.

Aceite:

- todos os requisitos foram classificados em `Atende`, `Parcial` ou `Nao contemplado`;
- existe um backlog priorizado com dependencias claras;
- o ambiente de homologacao esta pronto para receber os proximos incrementos.

### Sprint 2 - Identidade, seguranca e controle de acesso

Objetivo: fechar os controles de autenticacao e seguranca exigidos no bloco transversal.

Escopo:

- autenticacao unica vinculada ao CPF;
- suporte a provedores e certificados previstos no termo;
- MFA;
- politica de senha e recuperacao;
- politicas de acesso por grupo, dia e horario;
- log de login, logout, falhas e alertas;
- consultas de sessoes ativas e encerramento de sessoes;
- trilha de auditoria para operacoes criticas.

Entregas:

- fluxo de autenticacao consolidado;
- telas e servicos de gerenciamento de sessoes;
- registros de auditoria e eventos de autenticacao;
- checklist de evidencia para homologacao.

Aceite:

- login por CPF operando em ambiente de teste;
- MFA validado por perfil;
- logs e sessoes visiveis para auditoria;
- regras de acesso aplicadas sem bypass.

### Sprint 3 - Cloud, backup, restore e disponibilidade

Objetivo: completar os requisitos de infraestrutura e protecao de dados.

Escopo:

- datacenter cloud e topologia de alta disponibilidade;
- balanceador de carga;
- observabilidade de infraestrutura, app e banco;
- SLA e relatorio de disponibilidade;
- backup automatizado com retencao;
- restore completo com metadados;
- download de backup com certificado A3;
- politica de atualizacao segura da base tecnologica.

Entregas:

- arquitetura alvo de operacao;
- rotina de backup e restore validada;
- painel de observabilidade;
- relatorio de SLA;
- procedimento de operacao e recuperacao.

Aceite:

- restore executado com sucesso em ambiente de testes;
- backup e retencao comprovados;
- evidencias de disponibilidade e monitoramento geradas;
- acesso ao backup protegido por A3.

### Sprint 4 - Modulo escola, secretaria e calendario

Objetivo: consolidar o nucleo de operacao escolar.

Escopo:

- cadastro da escola e da secretaria;
- calendario escolar;
- etapas, modalidades, turmas e disciplinas;
- procedimentos de avaliacao;
- historico escolar;
- documentos e relatorios da secretaria;
- integracao com o cadastro unico.

Referencia de reuso no repositorio:

- `docs/sps_modulos_unificados/modulo_de_gestao_educacional_secretaria.md`
- `docs/sps_modulos_unificados/modulo_de_gestao_do_calendario_escolar.md`
- `docs/sps_modulos_unificados/modulo_de_censo_escolar.md`

Entregas:

- fluxo escolar basico operante;
- cadastro de calendario e turmas;
- relatorios essenciais da secretaria;
- integracao com a base de pessoas.

Aceite:

- cadastro escolar e calendario funcionando;
- operacoes de turma e avaliacao basica disponiveis;
- relatorios principais emitidos com dados consistentes.

### Sprint 5 - Portal do aluno e comunicacao

Objetivo: habilitar acesso de alunos, pais e responsaveis aos dados escolares.

Escopo:

- portal do aluno responsivo;
- consulta de notas, frequencia, historico e calendario;
- consulta de horarios e dados da escola;
- avisos e comunicacao com a comunidade escolar;
- relatorios de acesso ao portal;
- permissao para administracao do portal.

Referencia de reuso no repositorio:

- `docs/sps_modulos_unificados/portal_do_professor.md`
- `docs/sps_modulos_unificados/servicos_online.md`

Entregas:

- portal com consulta de dados do estudante;
- notificacoes e avisos operacionais;
- relatorio de usuarios com acesso;
- acesso web responsivo.

Aceite:

- aluno e responsavel conseguem consultar os dados essenciais;
- portal acessivel em navegador e mobile;
- comunicacao com a escola operando.

### Sprint 6 - Matricula online, central de vagas e transporte escolar

Objetivo: cobrir os fluxos de ingresso, alocacao e transporte.

Escopo:

- matricula online;
- pre-matricula e protocolo;
- central de vagas;
- validacao de CPF e regras de elegibilidade;
- cancelamento, fila e classificacao;
- transporte escolar vinculado a matricula;
- relatorios de vagas, alocacao e transporte.

Referencia de reuso no repositorio:

- `docs/sps_modulos_unificados/modulo_de_central_de_vagas.md`
- `docs/sps_modulos_unificados/modulo_transporte_escolar.md`

Entregas:

- fluxo de inscricao e protocolo;
- painel de vagas e alocacao;
- integracao com transporte escolar;
- relatorios operacionais da fila e da demanda.

Aceite:

- pre-matricula registrada com protocolo;
- regras de selecao e alocacao aplicadas;
- transporte escolar visivel no fluxo de matricula.

### Sprint 7 - RH integrado, BI, consultas e relatorios

Objetivo: atender os requisitos de gerencia, analise e tomada de decisao.

Escopo:

- cadastro e consulta de recursos humanos da rede;
- relatorios nominais e gerenciais;
- BI com indicadores educacionais;
- dashboards e graficos;
- consultas personalizadas e exportacoes;
- relatarios multicamadas com dados de varios modulos;
- compatibilidade com a base de contratos e lotacao escolar.

Referencia de reuso no repositorio:

- `docs/sps_modulos_unificados/modulo_de_pessoal_e_folha_de_pagamento.md`
- `docs/requisitos_relatorios_dashboards.md`

Entregas:

- painel gerencial educacional;
- relatorios de RH e escola;
- consultas com filtros e exportacao;
- base para BI educacional.

Aceite:

- relatorios e consultas executam sem inconsistencias;
- BI mostra dados consolidados e exportaveis;
- RH integrado a escola e secretaria.

### Sprint 8 - Migracao, treinamento, homologacao e go-live

Objetivo: finalizar a implantacao e fechar a entrega contratual.

Escopo:

- migracao de dados da operacao anterior;
- treinamento da equipe da secretaria de educacao;
- validacao funcional com a comissao tecnica;
- correcoes finais;
- checklist de entrada em producao;
- estabilizacao assistida apos go-live.

Entregas:

- plano de migracao com evidencias;
- roteiro de treinamento;
- termo de homologacao;
- relatorio final de aderencia;
- plano de suporte pos-go-live.

Aceite:

- dados migrados e conferidos;
- equipe treinada;
- sistema homologado para producao;
- pendencias remanescentes classificadas e priorizadas.

## 6. Cronograma macro

### Janela inicial de 30 dias

- Sprints 1, 2 e parte da Sprint 3;
- foco em discovery, seguranca, identidade, backups e ambiente minimo operante.

### Janela de 31 a 60 dias

- conclusao da Sprint 3;
- Sprint 4 e Sprint 5;
- foco em infra definitiva, modulo escola, secretaria e portal do aluno.

### Janela de 61 a 90 dias

- Sprint 6, Sprint 7 e Sprint 8;
- foco em matricula online, transporte, RH, BI, migracao e homologacao final.

## 7. Dependencias criticas

1. Acesso da equipe tecnica ao ambiente de homologacao e aos dados de teste.
2. Credenciais e certificados necessarios para os fluxos de autenticacao e backup.
3. Definicao da estrategia de hospedagem cloud e observabilidade.
4. Validador funcional da contratante para demonstracao e aceite.
5. Base de dados de migracao e regras de saneamento.

## 8. Criticos de aceite do plano

- evidencias objetivas por requisito;
- demonstracao funcional em ambiente real;
- logs, relatorios e capturas anexados;
- matriz de aderencia atualizada por sprint;
- aprovacao formal da comissao tecnica nos marcos principais.

## 9. Observacao final

Este plano complementa o roadmap tecnico ja existente no repositorio e expande a cobertura para os modulos educacionais do Termo de Referencia, mantendo a separacao entre:

- requisitos transversais de plataforma;
- requisitos funcionais da educacao;
- etapas de migracao, treinamento e homologacao.
