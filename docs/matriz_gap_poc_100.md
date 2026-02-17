# Matriz de Gaps - PoC (Requisitos Obrigatórios 100%)

Base: `docs/caracteristicas_gerais_basicas_obrigatorias.md`.

Legenda de status:
- `Atende`: evidência objetiva no sistema atual.
- `Parcial`: existe implementação parcial, mas faltam itens obrigatórios para PoC.
- `Não atende`: sem evidência suficiente no código/configuração atual.

## Resumo Executivo

- Total avaliado (macroitens): 26
- `Atende`: 7
- `Parcial`: 14
- `Não atende`: 5
- Conclusão: **há base funcional forte**, mas os requisitos obrigatórios de **infra cloud, segurança avançada e autenticação** ainda têm gaps relevantes para PoC.

## Matriz

| Requisito | Status | Evidência atual | Gap principal | Prioridade | Ação sugerida |
|---|---|---|---|---|---|
| 1.1.1 Datacenter com capacidade e componentes exigidos | Parcial | Há `docker/` e configuração de app, mas sem comprovação formal de ambiente cloud produtivo | Faltam evidências operacionais/auditoria da infraestrutura | Alta | Documentar arquitetura alvo e comprovar ambiente de produção na PoC |
| 1.1.2 Comprovação em tempo real de datacenter e BD | Não atende | Sem painéis/artefatos de observabilidade prontos no repositório | Falta demonstração operacional auditável em tempo real | Alta | Preparar runbook PoC com dashboard de infra, banco e disponibilidade |
| 1.1.3 Datacenter próprio/terceirizado aderente ao TR | Parcial | Estrutura de deploy existe, sem termo/evidência contratual no código | Faltam comprovantes formais (fornecedor/certificações/escopo) | Alta | Levantar documentação técnica e contratual da hospedagem |
| 1.1.4 Atualização contínua de base tecnológica | Parcial | Há componentes antigos (ex.: PHP 7.4 em `README.md:11`) | Processo de patching e atualização de segurança não está evidenciado | Alta | Definir política formal de atualização e trilha de execução |
| 1.1.5 Boas práticas de segurança e alta disponibilidade | Parcial | Existem configs de segurança pontuais (`config/session.php`, `config/cors.php`) | Falta desenho completo de HA + controles operacionais | Alta | Padronizar baseline de segurança e checklist de operação |
| 1.1.6 SLA 99,9% comprovado | Não atende | Sem métricas/SLO/SLA formal no repositório | Falta medição e prova histórica de disponibilidade | Alta | Instrumentar monitoramento e relatório de SLA |
| 1.1.7 ISO 27001 | Não atende | Nenhuma evidência documental no código | Falta certificação/equivalente do ambiente | Alta | Vincular provedor certificado ou plano de conformidade |
| 1.1.8 ISO 9001 | Não atende | Nenhuma evidência documental no código | Falta certificação/equivalente do ambiente | Média | Levantar certificação da operação/provedor |
| 1.1.9 Preferência SGBD Open Source | Atende | Uso de PostgreSQL (`README.md:12`, `.env.example:12`) | Sem gap relevante | Média | Manter evidência técnica na PoC |
| 1.1.10 Balanceador de carga | Parcial | Sem evidência explícita de LB em produção no repositório | Falta arquitetura HA comprovável | Alta | Definir e demonstrar LB no ambiente PoC |
| 1.1.11 Restauração 5 min até 30 dias | Não atende | Não há política técnica de PITR/restore versionada | Falta processo e evidência de restore testado | Alta | Implementar PITR + testes de restauração documentados |
| 1.1.12 Escalabilidade horizontal | Parcial | Arquitetura modular/híbrida existe | Falta prova operacional de scale-out sem indisponibilidade | Alta | Definir estratégia de scaling e teste de carga |
| 1.2.2 SGBD relacional | Atende | PostgreSQL é relacional (`README.md:12`) | Sem gap relevante | Média | Manter para evidência |
| 1.2.3 Backup completo com metadados e restauração íntegra | Parcial | Não há rotina/versionamento de backup no repositório | Falta demonstração de backup/restore fim a fim | Alta | Criar runbook de backup/restore com teste gravado |
| 1.2.4.a-d Rotina e retenção de backups (15/35 dias) | Não atende | Sem política declarada no código para retenções exigidas | Falta conformidade objetiva de retenção e acesso | Alta | Configurar política de retenção e auditoria |
| 1.2.5 Download backup com certificado A3 | Não atende | Sem mecanismo A3 específico para esse fluxo | Requisito específico não implementado/evidenciado | Alta | Projetar fluxo de autenticação A3 para acesso a backup |
| 1.2.6 Controle de credenciais no BD | Parcial | Há autenticação no sistema (`config/auth.php`), mas sem hardening DB evidenciado | Falta evidência de política de acesso do banco | Alta | Definir RBAC de banco e trilha de auditoria |
| 1.2.7 Base única por entidade SIAFIC | Parcial | Há gestão por instituição/sessão (`config/modern_routes.php:100`) | Necessita comprovar isolamento e governança de dados | Alta | Validar modelo de dados e controles por entidade |
| 1.3.1 Múltiplas instâncias back-end | Parcial | Estrutura moderna+legado coexistente (`routes/web.php:9`) | Falta evidência de instâncias simultâneas com HA | Alta | Definir topologia PoC multi-instância |
| 1.3.3 Domínio único HTTPS válido | Parcial | Há suporte a HTTPS em config/webservice (`src/WebService/DBSoapServer.php:61`) | Falta padrão único de domínio e comprovação ponta a ponta | Alta | Consolidar gateway de entrada e certificado válido |
| 1.3.4 Ambiente de homologação com dados da contratante | Parcial | Não há processo formal versionado no repositório | Falta protocolo de homologação/mascaramento de dados | Média | Definir processo e critérios de promoção |
| 1.3.7/1.3.8/1.3.9 Logs de autenticação e sessões ativas | Parcial | Auditoria existe (`routes/web.php` + módulo de auditoria), sessão legada compartilhada (`config/modern_routes.php:100`) | Falta centralização e UX de sessão ativa/alerta de falhas | Alta | Criar trilha de autenticação central e tela de sessões |
| 1.3.11/1.3.12 Arquitetura nativa web, multiabas, multiusuário | Parcial | Sistema web consolidado, mas com forte legado PHP | PoC exige prova explícita sem emulação/limitações | Média | Demonstrar cenários de uso simultâneo em múltiplas abas |
| 1.3.14 Autenticação única por CPF + provedores (Google, GOVBR etc.) + A1/A3 | Parcial | Login por CPF no provider legado e hardening em `app/Providers/Auth/LegacyUserProvider.php` e `config/auth.php` | Ainda faltam integrações externas/SSO (Google, GOVBR, A1/A3) e fechamento de fluxo único completo | Crítica | Concluir camada de identidade unificada com provedores externos exigidos |
| 1.3.15.d MFA obrigatório | Parcial | MFA implementado com enforcement em rotas protegidas (`app/Services/Auth/MfaService.php`, `app/Http/Middleware/AuthEcidadeUser.php`, `routes/web.php`) | Política atual cobre cenário inicial (admins); falta ampliar cobertura total por perfil e canais de entrega | Crítica | Expandir política de MFA por perfil e evidências operacionais completas |
| 1.3.17/1.3.18 Cadastro único compartilhado | Parcial | Há módulos integrados e base comum em várias áreas (`docs/MODULOS_IMPLEMENTADOS.md`) | Necessita prova prática de unicidade sem redundância | Alta | Mapear entidade mestre e fluxos cross-módulo para PoC |
| 1.3.21 Trilhas de auditoria completas | Parcial | Existe módulo de auditoria (`routes/web.php:20`) | Falta prova de cobertura total em todas as rotinas críticas | Alta | Definir matriz de cobertura de auditoria por módulo |
| 1.3.24/1.3.25 Gerador de consultas/relatórios com recursos avançados | Parcial | Há base de relatórios e consultas, mas sem validação formal de todos os recursos exigidos | Falta comprovação item a item na PoC | Média | Montar roteiro de demonstração com checklist de cada subitem |
| 1.3.26 Integrações imprescindíveis (Compras, Almox., Tributos, RH, Patrimônio) | Atende | Evidência de múltiplos módulos integrados (`docs/MODULOS_IMPLEMENTADOS.md`) | Requer prova funcional ao vivo | Alta | Preparar cenários integrados ponta a ponta |
| 1.3.27 Acessibilidade (zoom/contraste + daltonismo) | Não atende | Sem evidência clara de recursos de contraste/daltonismo no front | Gap direto de UX/acessibilidade | Alta | Implementar pacote mínimo de acessibilidade exigido |
| 1.3.28 Políticas de acesso por dia/horário/grupo/expiração | Parcial | Existe gerenciamento de usuários/perfis (base) | Falta política completa por janela de acesso e expiração avançada | Alta | Evoluir controle de acesso temporal e por grupo |

## Backlog Imediato (30 dias)

1. Expandir `MFA` e concluir autenticação unificada com provedores externos (1.3.14 e 1.3.15.d).
2. Estruturar trilha de `backup/restore` com retenção exigida e evidência de teste (1.1.11, 1.2.3, 1.2.4, 1.2.5).
3. Definir e comprovar arquitetura de `HA + SLA` para PoC (1.1.6, 1.1.10, 1.1.12, 1.3.1).
4. Fechar lacunas de `auditoria e sessões` (1.3.7, 1.3.8, 1.3.9, 1.3.21).
5. Implementar acessibilidade mínima exigida (1.3.27).

## Observações

- Esta matriz é uma versão inicial (`v1`) baseada em evidências de código e configuração do repositório.
- Itens de infraestrutura/certificação (ISO/SLA/cloud) dependem também de comprovação operacional e documental fora do código.
