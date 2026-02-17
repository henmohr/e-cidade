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

### 🗺️ Planejamento e Roadmap

#### [PLANO_MODERNIZACAO_ECIDADE_2026.md](./PLANO_MODERNIZACAO_ECIDADE_2026.md)
**Plano de Modernizacao Tecnica (2026)**

Plano estrategico de modernizacao incremental do e-cidade com foco em:
- seguranca e LGPD;
- evolucao arquitetural por fases;
- qualidade, testes, DevOps e governanca;
- continuidade operacional para municipios.

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

**Última atualização desta página**: 2025-11-04
