# 📚 Documentação e-Cidade

Bem-vindo à documentação do sistema e-Cidade! Este diretório contém documentos técnicos, guias de uso e planejamento do projeto.

---

## 📖 Índice de Documentos

### 🏛️ Visão Geral do Sistema

#### [MODULOS_IMPLEMENTADOS.md](./MODULOS_IMPLEMENTADOS.md)
**Módulos Implementados no e-Cidade**

Catálogo completo dos **61 módulos** atualmente implementados no sistema, organizados por categoria:
- 💰 Financeiro e Contabilidade (8 módulos)
- 🛒 Compras e Licitações (5 módulos)
- 👥 Recursos Humanos (6 módulos)
- 🏥 Saúde Pública (8 módulos)
- 📚 Educação (5 módulos)
- 💵 Tributação (6 módulos)
- 🏗️ Patrimônio e Infraestrutura (5 módulos)
- 🌐 Atendimento ao Cidadão (5 módulos)
- E mais...

**Inclui**: Descrição de funcionalidades, integrações governamentais ativas (eSocial, STN, PNCP), e comparação com roadmap de melhorias.

---

#### [ESTRUTURA_CODIGO_ATUAL.md](./ESTRUTURA_CODIGO_ATUAL.md)
**Estrutura Atual do Código e Diretórios**

Mapa técnico da base atual com foco em manutenção:
- arquitetura híbrida (moderno + legado);
- descrição dos diretórios principais e responsabilidades;
- visão dos módulos por domínio;
- orientação prática de onde evoluir com menor risco.

---

### 🗺️ Planejamento e Roadmap

#### [PLANO_MODERNIZACAO_ECIDADE_2026.md](./PLANO_MODERNIZACAO_ECIDADE_2026.md)
**Plano de Modernizacao Tecnica (2026)**

Plano estrategico de modernizacao incremental do e-cidade com foco em:
- seguranca e LGPD;
- evolucao arquitetural por fases;
- qualidade, testes, DevOps e governanca;
- continuidade operacional para municipios.

---

#### [sprint2_checklist_execucao.md](./sprint2_checklist_execucao.md)
**Checklist Executavel da Sprint 2**

Plano de execucao do ciclo de backup/restore e dados para PoC:
- scripts de backup com retencao 15/35 dias;
- script de restore com protecao operacional;
- pendencias externas (A3 e RBAC institucional).

---

#### [runbook_backup_restore_poc.md](./runbook_backup_restore_poc.md)
**Runbook de Backup e Restore para PoC**

Guia pratico de execucao e evidencias:
- geracao de dump completo + globais;
- validacao por checksum;
- restauracao controlada com `pg_restore`.

---

#### [politica_rbac_backup.md](./politica_rbac_backup.md)
**Politica RBAC para Artefatos de Backup**

Define papeis e controles minimos de acesso:
- segregacao de funcao para backup/restore;
- trilha de auditoria e revisao periodica;
- evidencias esperadas para PoC.

---

#### [desenho_fluxo_a3_backup.md](./desenho_fluxo_a3_backup.md)
**Desenho de Fluxo A3 para Download de Backup**

Documento tecnico do fluxo alvo para requisito 1.2.5:
- autenticacao por certificado A3;
- autorizacao RBAC e URL temporaria assinada;
- dependencias de homologacao externa.

---

#### [runbook_a3_backup_download_poc.md](./runbook_a3_backup_download_poc.md)
**Runbook de Validacao A3 para Backup**

Passo a passo de demonstracao para o requisito 1.2.5:
- acesso permitido com certificado A3 valido;
- bloqueio sem certificado;
- coleta de evidencias de log para PoC.

---

#### [sprint3_checklist_execucao.md](./sprint3_checklist_execucao.md)
**Checklist Executavel da Sprint 3**

Plano da sprint de disponibilidade e observabilidade:
- endpoints de saude (live/ready);
- coleta de amostras operacionais;
- relatorio de SLA por janela.

---

#### [runbook_observabilidade_sla_poc.md](./runbook_observabilidade_sla_poc.md)
**Runbook de Observabilidade e SLA para PoC**

Guia de demonstracao:
- saude da aplicacao em tempo real;
- coleta periodica para evidencias;
- calculo de disponibilidade com meta configuravel.

---

#### [sprint4_checklist_execucao.md](./sprint4_checklist_execucao.md)
**Checklist Executavel da Sprint 4**

Plano da sprint de auditoria e gestao de sessoes:
- consulta de sessoes ativas;
- encerramento remoto de sessao;
- trilha de log para evidencias de PoC.

---

#### [runbook_sessoes_auditoria_poc.md](./runbook_sessoes_auditoria_poc.md)
**Runbook de Sessoes e Auditoria para PoC**

Guia de validacao:
- duas sessoes simultaneas;
- revogacao remota;
- bloqueio efetivo da sessao encerrada.

---

#### [sprint5_checklist_execucao.md](./sprint5_checklist_execucao.md)
**Checklist Executavel da Sprint 5**

Plano da sprint de acessibilidade:
- contraste alto;
- ajuste de fonte;
- filtros de daltonismo;
- cobertura nas telas web modernas da PoC.

---

#### [runbook_acessibilidade_poc.md](./runbook_acessibilidade_poc.md)
**Runbook de Acessibilidade para PoC**

Guia de validacao de recursos:
- contraste;
- zoom de leitura;
- modos de daltonismo;
- persistencia de preferencia.

---

#### [runbook_politica_acesso_poc.md](./runbook_politica_acesso_poc.md)
**Runbook de Politica de Acesso (Dia/Horario/Grupo/Expiracao)**

Guia de validacao do requisito 1.3.28:
- regras por dia e horario;
- regras por grupo e por usuario;
- bloqueio por expiracao e evidencias em log.

---

#### [runbook_mfa_politica_poc.md](./runbook_mfa_politica_poc.md)
**Runbook de MFA por Perfil/Grupo/Usuario**

Guia de validacao incremental do MFA:
- exigencia por usuario e por grupo;
- fallback com politica de admins;
- evidencias funcionais e de log para PoC.

---

#### [runbook_identidade_externa_poc.md](./runbook_identidade_externa_poc.md)
**Runbook de Identidade Externa (GOVBR/Google/A1-A3)**

Guia de validacao da ponte de identidade externa:
- callback assinado por provedor;
- vinculo de usuario por CPF/login;
- sessao compativel com fluxo legado web.

---

#### [runbook_auditoria_web_transversal_poc.md](./runbook_auditoria_web_transversal_poc.md)
**Runbook de Auditoria Web Transversal**

Guia de evidencias para trilha operacional:
- middleware de auditoria no escopo web autenticado;
- logs estruturados por usuario, rota, status e duracao;
- filtro de campos sensiveis em entrada.

---

#### [sprint6_checklist_execucao.md](./sprint6_checklist_execucao.md)
**Checklist Executavel da Sprint 6**

Plano de fechamento da PoC:
- roteiro oficial de demonstracao;
- simulacao integral com evidencias;
- pacote documental final para banca.

---

#### [sprint7_checklist_execucao.md](./sprint7_checklist_execucao.md)
**Checklist Executavel da Sprint 7**

Plano de execucao do nucleo financeiro e aderencia ampliada:
- modulo 3 (execucao orcamentaria com bloqueios de ciclo);
- modulo 4 (tesouraria, conciliacao e fluxo de caixa);
- modulo 5 (despesas/receitas e retencoes);
- integracoes legais e relatorios/dashboard com evidencias.

---

#### [sprint7_evidencias_tecnicas.md](./sprint7_evidencias_tecnicas.md)
**Evidencias Tecnicas da Sprint 7**

Consolidado tecnico das entregas executadas:
- testes unitarios executados e resultados;
- artefatos implementados por frente (D e E);
- comandos operacionais para homologacao externa.

---

#### [sprint8_checklist_execucao.md](./sprint8_checklist_execucao.md)
**Checklist Executavel da Sprint 8**

Plano de fechamento de evidencias para licitacao:
- pacote automatizado de evidencias (manifesto/resumo/exportacoes);
- comandos operacionais para banca e homologacao;
- trilha de continuidade para homologacao externa formal.

---

#### [sprint8_homologacao_externa.md](./sprint8_homologacao_externa.md)
**Roteiro de Homologacao Externa - Sprint 8**

Guia operacional para fechamento com orgaos externos:
- lotes por sistema (SICONFI, TCE/UF, Portal Transparencia);
- comando para registro de protocolo por envio;
- relatorio consolidado de pendencias por integracao.

---

#### [anexos_homologacao_assinados/README.md](./anexos_homologacao_assinados/README.md)
**Anexos Assinados de Homologacao**

Padrao documental para fechamento formal com orgaos externos:
- modelos assinados para SICONFI, TCE/UF e Portal da Transparencia;
- validacao automatizada de completude via comando `financeiro:validar-anexos-homologacao`.

---

#### [sprint9_checklist_execucao.md](./sprint9_checklist_execucao.md)
**Checklist Executavel da Sprint 9**

Plano de consolidacao de cobertura por sistema da licitacao:
- matriz estruturada de status por modulo/sistema;
- relatorio automatizado de atingimento e pendencias;
- trilha objetiva para fechamento da banca.

---

#### [sprint9_matriz_status_licitacao.yml](./sprint9_matriz_status_licitacao.yml)
**Matriz Estruturada de Status da Licitacao**

Arquivo base para governanca de cobertura:
- status por item TR (`atingido`, `parcial`, `pendente`);
- evidencia principal por sistema;
- observacoes de risco/pendencia.

---

#### [sprint9_relatorio_cobertura_licitacao.md](./sprint9_relatorio_cobertura_licitacao.md)
**Relatorio Consolidado de Cobertura da Licitacao**

Artefato gerado automaticamente via comando:
- totais por status;
- percentual de atingimento;
- lista objetiva de pendencias por sistema.

---

#### [sprint9_evidencias_itens_pendentes.md](./sprint9_evidencias_itens_pendentes.md)
**Evidencias dos Itens Antes Pendentes - Sprint 9**

Consolidado tecnico para reducao de pendencias:
- evidencias objetivas para ISSQN/NF-e (TR 25);
- evidencias objetivas para Plataforma da Camara (TR 38-57);
- criterio para evolucao futura de `parcial` para `atingido`.

---

#### [sprint9_evidencias_tr2_orcamentario.md](./sprint9_evidencias_tr2_orcamentario.md)
**Evidencias TR 2 - Orcamentario**

Dossie tecnico para classificacao de cobertura:
- servicos e comandos implementados para balancos e relatorios fiscais;
- resultado de testes unitarios associados ao escopo orcamentario.

---

#### [sprint9_evidencias_tr3_tesouraria.md](./sprint9_evidencias_tr3_tesouraria.md)
**Evidencias TR 3 - Tesouraria**

Dossie tecnico para classificacao de cobertura:
- servicos e comandos de conciliacao, fluxo de caixa, restos e dashboard;
- resultado de testes unitarios da trilha de tesouraria.

---

#### [sprint9_evidencias_tr4_prestacao_contas.md](./sprint9_evidencias_tr4_prestacao_contas.md)
**Evidencias TR 4 - Prestacao de Contas**

Dossie tecnico para classificacao de cobertura:
- trilha de status/homologacao e protocolos de integracao;
- referencias de testes unitarios e comandos operacionais.

---

#### [sprint9_evidencias_tr6_compras_licitacoes.md](./sprint9_evidencias_tr6_compras_licitacoes.md)
**Evidencias TR 6 - Compras e Licitacoes**

Dossie tecnico para classificacao de cobertura:
- evidencias de modulo implementado e fluxos de publicacao/integracao;
- rastreabilidade documental para suporte a banca.

---

#### [sprint9_evidencias_tr15_assinatura_a1_a3.md](./sprint9_evidencias_tr15_assinatura_a1_a3.md)
**Evidencias TR 15 - Assinatura A1/A3**

Dossie tecnico para classificacao de cobertura:
- fluxo tecnico A3 para operacao sensivel;
- trilha de anexos assinados e validacao automatizada.

---

#### [roteiro_oficial_demonstracao_poc.md](./roteiro_oficial_demonstracao_poc.md)
**Roteiro Oficial de Demonstracao da PoC**

Sequencia padronizada de apresentacao:
- seguranca, auditoria e sessoes;
- backup/restore e SLA;
- acessibilidade e modulos funcionais prioritarios.

---

#### [simulacao_integral_poc.md](./simulacao_integral_poc.md)
**Simulacao Integral da PoC**

Registro de ensaio completo pre-banca:
- preparacao de ambiente e equipe;
- execucao por etapas;
- consolidacao de pendencias e aptidao para apresentacao oficial.

---

#### [pacote_documental_poc.md](./pacote_documental_poc.md)
**Pacote Documental Final da PoC**

Indice de artefatos para entrega:
- documentos obrigatorios de governanca;
- evidencias funcionais por sistema;
- evidencias operacionais de seguranca e continuidade.

---

#### [checklist_evidencias_contabilidade_publica_poc.md](./checklist_evidencias_contabilidade_publica_poc.md)
**Checklist de Evidencias - Contabilidade Publica (PoC)**

Checklist objetivo para decisao de atingimento no escopo contabil:
- cobertura funcional dos modulos;
- fluxo ponta a ponta;
- auditoria e rastreabilidade;
- relatorios e requisitos transversais.

---

#### [requisitos_modulo3_execucao_orcamentaria.md](./requisitos_modulo3_execucao_orcamentaria.md)
**Requisitos de Desenvolvimento - Modulo 3: Execucao Orcamentaria**

Baseline funcional para desenvolvimento incremental do modulo:
- escopo obrigatorio (dotacao, creditos, empenho, licitacao/dispensa, contratos, liquidacao e pagamento);
- regras de ciclo da despesa (fixacao -> empenho -> liquidacao -> pagamento);
- controles obrigatorios (LRF, restos a pagar, vinculacao de receitas e reserva de contingencia);
- criterios de aceite e backlog sprintavel para evidencias de licitacao.

---

#### [requisitos_modulo4_tesouraria_fluxo_caixa.md](./requisitos_modulo4_tesouraria_fluxo_caixa.md)
**Requisitos de Desenvolvimento - Modulo 4: Tesouraria e Fluxo de Caixa**

Baseline funcional para desenvolvimento incremental do modulo:
- escopo obrigatorio (conta unica, conciliacao bancaria, previsao de caixa, programacao financeira, aplicacao financeira e restos a pagar);
- dashboard operacional com saldo atual, projecao de 7 dias e alertas;
- fluxo de caixa projetado com receitas e despesas consolidadas;
- criterios de aceite, controles e backlog sprintavel para evidencias de licitacao.

---

#### [requisitos_modulo5_controle_despesas_receitas.md](./requisitos_modulo5_controle_despesas_receitas.md)
**Requisitos de Desenvolvimento - Modulo 5: Controle de Despesas e Receitas**

Baseline funcional para desenvolvimento incremental do modulo:
- escopo obrigatorio de despesas (credores, empenho, atesto, retencoes, diarias/passagens e folha);
- escopo obrigatorio de receitas (tributarias, contribuicoes, patrimoniais, servicos, transferencias e outras);
- classificacao obrigatoria em receitas correntes e de capital;
- criterios de aceite, controles e backlog sprintavel para evidencias de licitacao.

---

#### [requisitos_integracoes_conformidade_legal.md](./requisitos_integracoes_conformidade_legal.md)
**Requisitos de Desenvolvimento - Integracoes e Conformidade Legal**

Baseline funcional e regulatoria para o software:
- aderencia obrigatoria a Lei 4.320/1964, LRF, LAI, NBCT 16, SICONFI e prestacao aos tribunais de contas;
- escopo minimo de integracoes externas (SICONFI/STN, TCE/TCU, transparencia, bancos, SIAPE, NFS-e/NF-e e e-SIC);
- controles transversais de seguranca, LGPD, auditoria e monitoramento;
- criterios de aceite e backlog sprintavel para evidencias de licitacao.

---

#### [matriz_aderencia_legal_sprint7.md](./matriz_aderencia_legal_sprint7.md)
**Matriz de Aderencia Legal - Sprint 7**

Consolidado de aderencia legal e evidencia tecnica da frente de integracoes:
- mapeamento por norma (Lei 4.320, LRF, LAI, NBCT 16, SICONFI e TCU/TCE);
- referencia de implementacoes atuais no codigo;
- lacunas e continuidade para homologacao externa.

---

#### [requisitos_relatorios_dashboards.md](./requisitos_relatorios_dashboards.md)
**Requisitos de Desenvolvimento - Relatorios e Dashboards**

Baseline funcional para informacao gerencial e prestacao de contas:
- relatorios obrigatorios (balancos, DVP, DFC, RGF, RREO e prestacao de contas anual);
- dashboard executivo com paineis de receitas, despesas e execucao orcamentaria;
- alertas operacionais e legais (vencimentos, contratos, limites e pendencias);
- criterios de aceite, backlog sprintavel e evidencias minimas para licitacao.

---

#### [checklist_evidencias_demais_sistemas_poc.md](./checklist_evidencias_demais_sistemas_poc.md)
**Checklist de Evidencias - Demais Sistemas (PoC)**

Matriz consolidada para os demais sistemas da licitacao:
- status por sistema (atingido/parcial/nao atingido);
- criterios minimos de evidencia;
- bloco padrao de registro e aceite funcional.

---

#### [PAPEL_ARQUITETO_MODERNIZACAO.md](./PAPEL_ARQUITETO_MODERNIZACAO.md)
**Diretriz Operacional do Papel Arquitetural**

Documento base com o papel a ser seguido nas execucoes do projeto:
- modernizacao incremental sem ruptura operacional;
- seguranca, LGPD e rastreabilidade como prioridade;
- estrategia de qualidade, testes, APIs, DevOps e governanca;
- formato padrao de entrega (diagnostico, acao, esforco, dependencia e metrica).

**Uso recomendado**: leitura obrigatoria antes de planejar sprints e propostas arquiteturais.

---

#### [ROADMAP_MELHORIAS.md](./ROADMAP_MELHORIAS.md)
**Roadmap de Melhorias e Novos Módulos**

Documento completo com **27 melhorias planejadas**, incluindo:
- 🔴 **15 itens de alta prioridade**: Processo Eletrônico, Ponto Eletrônico, SAC, e-SIC, SST, Pronto Atendimento, Portal da Transparência, Diário Oficial, etc.
- 🟡 **12 itens de média prioridade**: BI expandido, Rastreamento Veicular, Gestão de Obras, Laboratório, TFD, etc.

Cada melhoria contém:
- Descrição detalhada
- Funcionalidades esperadas
- Benefícios
- Dependências
- Prioridade
- Integrações necessárias

**Inclui**: Timeline sugerido (curto, médio e longo prazo) e guia de contribuição.

---

#### [ROADMAP_CHECKLIST.md](./ROADMAP_CHECKLIST.md)
**Checklist de Acompanhamento do Roadmap**

Versão resumida do roadmap em formato checklist para acompanhamento rápido do progresso:
- ✅ Itens concluídos
- 🔄 Itens em andamento
- ⏳ Itens planejados

**Ideal para**: Reuniões de planejamento, acompanhamento de sprints e visão geral do progresso.

---

### 🏗️ Arquitetura e Desenvolvimento

#### [MODERN_LEGACY_ROUTING.md](./MODERN_LEGACY_ROUTING.md)
**Sistema de Roteamento Modern/Legacy**

Documentação técnica completa do sistema de migração gradual de código legado para moderno usando Laravel:

**Principais componentes**:
- **LegacyProxyMiddleware**: Middleware que decide entre código moderno (Laravel) e legado (PHP)
- **FeatureFlag Service**: Sistema de feature flags com suporte a rollout gradual (0-100%)
- **API v2**: Nova API REST moderna coexistindo com API v1 legada (Silex)
- **FrontController**: Roteamento modificado (`/api/v2/*` → Laravel, `/api/v1/*` → Silex)

**Guias práticos**:
- Como adicionar novos endpoints modernos
- Como migrar rotas legadas gradualmente
- Gerenciamento de feature flags via CLI
- Exemplos de integração com banco legado
- Testes e debugging

**Ideal para**: Desenvolvedores que precisam adicionar funcionalidades modernas sem quebrar o sistema legado.

---

## 🎯 Guia de Uso Rápido

### Para Gestores e Tomadores de Decisão
1. ✅ Leia **MODULOS_IMPLEMENTADOS.md** para conhecer as capacidades atuais do sistema
2. 🗺️ Consulte **ROADMAP_MELHORIAS.md** para planejar investimentos e prioridades
3. 📊 Use **ROADMAP_CHECKLIST.md** para acompanhar o progresso do projeto

### Para Desenvolvedores
1. 📚 Comece com **MODULOS_IMPLEMENTADOS.md** para entender a estrutura existente
2. 🏗️ Leia **MODERN_LEGACY_ROUTING.md** para implementar novas funcionalidades
3. 🧪 Siga os exemplos de código para integração moderna/legado
4. 🗺️ Consulte **ROADMAP_MELHORIAS.md** para alinhar desenvolvimento com planejamento

### Para Analistas e Consultores
1. 📊 Use **MODULOS_IMPLEMENTADOS.md** para diagnóstico de funcionalidades
2. 🎯 Consulte **ROADMAP_MELHORIAS.md** para propor soluções alinhadas ao planejamento
3. ✅ Acompanhe **ROADMAP_CHECKLIST.md** para status de implementações

---

## 📊 Estatísticas do Projeto

- **Módulos implementados**: 61
- **Melhorias planejadas**: 27
- **Integrações governamentais ativas**: 5 (eSocial, EFD-Reinf, SICONFI/STN, TCE/MG, PNCP)
- **Arquitetura**: Migração gradual PHP legado → Laravel moderno
- **Tecnologias**: PHP 7.4+, Laravel 9, PostgreSQL 12, Docker

---

## 🔄 Atualizações

| Data | Documento | Descrição |
|------|-----------|-----------|
| 2025-11-04 | MODULOS_IMPLEMENTADOS.md | Criação inicial com 61 módulos catalogados |
| 2025-11-04 | ROADMAP_MELHORIAS.md | Criação inicial com 27 melhorias planejadas |
| 2025-11-04 | ROADMAP_CHECKLIST.md | Criação de checklist resumido |
| 2025-11-04 | MODERN_LEGACY_ROUTING.md | Sistema de roteamento implementado e documentado |
| 2026-02-17 | PAPEL_ARQUITETO_MODERNIZACAO.md | Registro do papel arquitetural para guiar planejamento e execucao |
| 2026-02-17 | PLANO_MODERNIZACAO_ECIDADE_2026.md | Plano tecnico de modernizacao incremental (versao 1.0) |
| 2026-02-17 | sprint2_checklist_execucao.md | Checklist de execucao da Sprint 2 (backup/restore e dados) |
| 2026-02-17 | runbook_backup_restore_poc.md | Runbook de backup/restore com evidencias para PoC |
| 2026-02-17 | politica_rbac_backup.md | Politica RBAC de acesso aos artefatos de backup |
| 2026-02-17 | desenho_fluxo_a3_backup.md | Desenho tecnico do fluxo de download com certificado A3 |
| 2026-02-17 | runbook_a3_backup_download_poc.md | Runbook para validacao do download de backup com A3 |
| 2026-02-17 | sprint3_checklist_execucao.md | Checklist da Sprint 3 (HA, observabilidade e SLA) |
| 2026-02-17 | runbook_observabilidade_sla_poc.md | Runbook operacional de saude e medicao de SLA |
| 2026-02-17 | sprint4_checklist_execucao.md | Checklist da Sprint 4 (auditoria e gestao de sessoes) |
| 2026-02-17 | runbook_sessoes_auditoria_poc.md | Runbook de validacao de sessoes ativas e revogacao |
| 2026-02-17 | sprint5_checklist_execucao.md | Checklist da Sprint 5 (acessibilidade minima obrigatoria) |
| 2026-02-17 | runbook_acessibilidade_poc.md | Runbook de validacao de contraste, fonte e daltonismo |
| 2026-02-17 | checklist_evidencias_contabilidade_publica_poc.md | Checklist de atingimento para Contabilidade Publica na PoC |
| 2026-02-17 | checklist_evidencias_demais_sistemas_poc.md | Checklist consolidado de evidencias para os demais sistemas da licitacao |

---

## 🤝 Como Contribuir

### Sugerir Melhorias na Documentação
1. Abra uma issue com a tag `documentation`
2. Descreva claramente o que pode ser melhorado
3. Se possível, sugira o texto ou estrutura

### Propor Novas Funcionalidades
1. Verifique se não está no **ROADMAP_MELHORIAS.md**
2. Abra uma issue com a tag `enhancement`
3. Inclua: descrição, benefícios, prioridade sugerida, dependências

### Reportar Problemas Técnicos
1. Consulte **MODERN_LEGACY_ROUTING.md** para questões de arquitetura
2. Abra uma issue com a tag `bug` ou `technical-debt`
3. Inclua logs, capturas de tela e passos para reproduzir

---

## 📞 Suporte

- **Repositório**: [GitHub - e-Cidade](https://github.com/e-cidade/e-cidade)
- **Comunidade**: Fórum oficial do e-Cidade
- **Wiki**: [Wiki do projeto](https://github.com/e-cidade/e-cidade/wiki)

---

## 📜 Licença

Este projeto é mantido pela comunidade e distribui-se sob licença de software livre. Consulte o arquivo LICENSE na raiz do projeto para mais informações.

---

**Ultima atualizacao desta pagina**: 2026-02-17
