# Matriz de Gaps - PoC (Requisitos Obrigatórios 100%)

Base: `docs/caracteristicas_gerais_basicas_obrigatorias.md`.

Legenda de status:
- `Atende`: evidência objetiva no sistema atual.
- `Parcial`: existe implementação parcial, mas faltam itens obrigatórios para PoC.
- `Não atende`: sem evidência suficiente no código/configuração atual.

## Resumo Executivo

- Total avaliado (macroitens): 26
- `Atende`: 7
- `Parcial`: 18
- `Não atende`: 1
- Conclusão: **há base funcional forte**, mas os requisitos obrigatórios de **infra cloud, segurança avançada e autenticação** ainda têm gaps relevantes para PoC.

## Matriz

| Requisito | Status | Evidência atual | Gap principal | Prioridade | Ação sugerida |
|---|---|---|---|---|---|
| 1.1.1 Datacenter com capacidade e componentes exigidos | Parcial | Há `docker/` e configuração de app, mas sem comprovação formal de ambiente cloud produtivo | Faltam evidências operacionais/auditoria da infraestrutura | Alta | Documentar arquitetura alvo e comprovar ambiente de produção na PoC |
| 1.1.2 Comprovação em tempo real de datacenter e BD | Parcial | Endpoints de saúde e coleta operacional implementados (`routes/api.php`, `app/Http/Controllers/HealthController.php`, `app/Console/Commands/OpsHealthSnapshot.php`) | Falta painel em tempo real e integração com monitoramento institucional | Alta | Integrar a coleta em dashboard operacional com alertas e retenção de métricas |
| 1.1.3 Datacenter próprio/terceirizado aderente ao TR | Parcial | Estrutura de deploy existe, sem termo/evidência contratual no código | Faltam comprovantes formais (fornecedor/certificações/escopo) | Alta | Levantar documentação técnica e contratual da hospedagem |
| 1.1.4 Atualização contínua de base tecnológica | Parcial | Há componentes antigos (ex.: PHP 7.4 em `README.md:11`) | Processo de patching e atualização de segurança não está evidenciado | Alta | Definir política formal de atualização e trilha de execução |
| 1.1.5 Boas práticas de segurança e alta disponibilidade | Parcial | Existem configs de segurança pontuais (`config/session.php`, `config/cors.php`) | Falta desenho completo de HA + controles operacionais | Alta | Padronizar baseline de segurança e checklist de operação |
| 1.1.6 SLA 99,9% comprovado | Parcial | Relatório de disponibilidade implementado (`app/Console/Commands/OpsSlaReport.php`) e automação por scheduler em `app/Console/Kernel.php` com meta configurável (`config/observability.php`) | Falta homologação da coleta contínua em janela contratual e aceite formal do contratante | Alta | Executar coleta contínua em homologação/produção e anexar relatório formal por período |
| 1.1.7 ISO 27001 | Não atende | Nenhuma evidência documental no código | Falta certificação/equivalente do ambiente | Alta | Vincular provedor certificado ou plano de conformidade |
| 1.1.8 ISO 9001 | Não atende | Nenhuma evidência documental no código | Falta certificação/equivalente do ambiente | Média | Levantar certificação da operação/provedor |
| 1.1.9 Preferência SGBD Open Source | Atende | Uso de PostgreSQL (`README.md:12`, `.env.example:12`) | Sem gap relevante | Média | Manter evidência técnica na PoC |
| 1.1.10 Balanceador de carga | Parcial | Sem evidência explícita de LB em produção no repositório | Falta arquitetura HA comprovável | Alta | Definir e demonstrar LB no ambiente PoC |
| 1.1.11 Restauração 5 min até 30 dias | Não atende | Não há política técnica de PITR/restore versionada | Falta processo e evidência de restore testado | Alta | Implementar PITR + testes de restauração documentados |
| 1.1.12 Escalabilidade horizontal | Parcial | Arquitetura modular/híbrida existe | Falta prova operacional de scale-out sem indisponibilidade | Alta | Definir estratégia de scaling e teste de carga |
| 1.2.2 SGBD relacional | Atende | PostgreSQL é relacional (`README.md:12`) | Sem gap relevante | Média | Manter para evidência |
| 1.2.3 Backup completo com metadados e restauração íntegra | Parcial | Scripts e runbook adicionados (`docker/scripts/backup-retention.sh`, `docker/scripts/restore-backup.sh`, `docs/runbook_backup_restore_poc.md`) | Falta validação oficial em ambiente de PoC com evidência assinada pelo contratante | Alta | Executar ciclo completo em homologação e anexar evidências formais |
| 1.2.4.a-d Rotina e retenção de backups (15/35 dias) | Parcial | Retenção técnica 15/35 implementada em script (`docker/scripts/backup-retention.sh`) e parametrizada em `.env.example` | Falta institucionalizar rotina agendada e governança de acesso/entrega para contratante | Alta | Configurar agendamento operacional e trilha de auditoria da retenção |
| 1.2.5 Download backup com certificado A3 | Parcial | Fluxo técnico implementado em `app/Http/Middleware/RequireA3Certificate.php`, `app/Http/Controllers/BackupAccessController.php` e `routes/web.php`, além do desenho em `docs/desenho_fluxo_a3_backup.md` | Falta homologação final com certificado A3 físico no ambiente do contratante | Alta | Executar homologação com certificado A3 real e anexar evidência auditável |
| 1.2.6 Controle de credenciais no BD | Parcial | Há autenticação no sistema (`config/auth.php`), mas sem hardening DB evidenciado | Falta evidência de política de acesso do banco | Alta | Definir RBAC de banco e trilha de auditoria |
| 1.2.7 Base única por entidade SIAFIC | Parcial | Há gestão por instituição/sessão (`config/modern_routes.php:100`) | Necessita comprovar isolamento e governança de dados | Alta | Validar modelo de dados e controles por entidade |
| 1.3.1 Múltiplas instâncias back-end | Parcial | Estrutura moderna+legado coexistente (`routes/web.php:9`) | Falta evidência de instâncias simultâneas com HA | Alta | Definir topologia PoC multi-instância |
| 1.3.3 Domínio único HTTPS válido | Parcial | Há suporte a HTTPS em config/webservice (`src/WebService/DBSoapServer.php:61`) | Falta padrão único de domínio e comprovação ponta a ponta | Alta | Consolidar gateway de entrada e certificado válido |
| 1.3.4 Ambiente de homologação com dados da contratante | Parcial | Não há processo formal versionado no repositório | Falta protocolo de homologação/mascaramento de dados | Média | Definir processo e critérios de promoção |
| 1.3.7/1.3.8/1.3.9 Logs de autenticação e sessões ativas | Parcial | Tela de sessões e revogação implementadas, com histórico de eventos (`login_success`, `login_failed`, `login_external_success`, `logout`, `mfa_*`, `session_revoked`, `session_revoke_others`, `backup_*`) em `app/Services/Auth/AuthEventService.php` e `resources/views/auth/sessions.blade.php` | Falta centralização total dos eventos em fluxos legados fora do escopo web moderno | Alta | Expandir trilha de autenticação para módulos legados e consolidar evidências em painel único |
| 1.3.11/1.3.12 Arquitetura nativa web, multiabas, multiusuário | Parcial | Sistema web consolidado, mas com forte legado PHP | PoC exige prova explícita sem emulação/limitações | Média | Demonstrar cenários de uso simultâneo em múltiplas abas |
| 1.3.14 Autenticação única por CPF + provedores (Google, GOVBR etc.) + A1/A3 | Parcial | Base de login CPF no provider legado (`app/Providers/Auth/LegacyUserProvider.php`) e ponte de identidade externa com callback assinado, validação de `expires_at`, controle de replay por `nonce` e rate limit dedicado (`throttle:external-idp`) em `app/Http/Controllers/Auth/ExternalIdentityController.php` + `app/Services/Auth/ExternalIdentityService.php` | Falta homologar conectores oficiais de provedores (GOVBR/Google/A1-A3) e validação formal com ambiente da contratante | Crítica | Executar homologação guiada (`docs/runbook_identidade_externa_poc.md`) com credenciais institucionais e evidências da banca |
| 1.3.15.d MFA obrigatório | Parcial | MFA com política ampliada por perfil/grupo/usuário e proteção anti brute-force de verificação (limite de tentativas + bloqueio temporário) em `config/mfa.php` e `app/Services/Auth/MfaService.php`, com enforcement em `app/Http/Middleware/AuthEcidadeUser.php` | Falta homologação funcional formal com perfis da contratante e evidências completas da banca | Crítica | Executar validação guiada (`docs/runbook_mfa_politica_poc.md`) e anexar evidências de aceite |
| 1.3.17/1.3.18 Cadastro único compartilhado | Parcial | Há módulos integrados e base comum em várias áreas (`docs/MODULOS_IMPLEMENTADOS.md`) | Necessita prova prática de unicidade sem redundância | Alta | Mapear entidade mestre e fluxos cross-módulo para PoC |
| 1.3.21 Trilhas de auditoria completas | Parcial | Módulo de auditoria existente e trilha transversal web adicionada com `app/Http/Middleware/WebAuditTrailMiddleware.php` + `app/Services/Auth/WebAuditTrailService.php` + canal `web_audit` em `config/logging.php` | Falta expandir cobertura para rotinas legadas fora do escopo web moderno e obter aceite formal da banca | Alta | Executar validação guiada (`docs/runbook_auditoria_web_transversal_poc.md`) e ampliar gradualmente por módulo legado crítico |
| 1.3.24/1.3.25 Gerador de consultas/relatórios com recursos avançados | Parcial | Há base de relatórios e consultas, mas sem validação formal de todos os recursos exigidos | Falta comprovação item a item na PoC | Média | Montar roteiro de demonstração com checklist de cada subitem |
| 1.3.26 Integrações imprescindíveis (Compras, Almox., Tributos, RH, Patrimônio) | Atende | Evidência de múltiplos módulos integrados (`docs/MODULOS_IMPLEMENTADOS.md`) | Requer prova funcional ao vivo | Alta | Preparar cenários integrados ponta a ponta |
| 1.3.27 Acessibilidade (zoom/contraste + daltonismo) | Parcial | Toolbar de acessibilidade implementada com contraste, ajuste de fonte e filtros de daltonismo (`public/assets/css/accessibility.css`, `public/assets/js/accessibility.js`) | Falta expandir cobertura para todo o legado fora do escopo web moderno | Alta | Estender os recursos de acessibilidade para telas legadas e consolidar validação funcional completa |
| 1.3.28 Políticas de acesso por dia/horário/grupo/expiração | Parcial | Política técnica implementada em `config/auth_access.php`, `app/Services/Auth/AccessPolicyService.php` e enforcement em `app/Http/Middleware/AuthEcidadeUser.php` com parâmetros `AUTH_ACCESS_*` | Falta homologação funcional completa no ambiente do contratante com evidências formais da banca | Alta | Executar PoC guiada (`docs/runbook_politica_acesso_poc.md`) e anexar evidências de aceite |

## Backlog Imediato (30 dias)

1. Expandir `MFA` e concluir autenticação unificada com provedores externos (1.3.14 e 1.3.15.d).
2. Estruturar trilha de `backup/restore` com retenção exigida e evidência de teste (1.1.11, 1.2.3, 1.2.4, 1.2.5).
3. Definir e comprovar arquitetura de `HA + SLA` para PoC (1.1.6, 1.1.10, 1.1.12, 1.3.1).
4. Fechar lacunas de `auditoria e sessões` (1.3.7, 1.3.8, 1.3.9, 1.3.21).
5. Implementar acessibilidade mínima exigida (1.3.27).

## Observações

- Esta matriz é uma versão inicial (`v1`) baseada em evidências de código e configuração do repositório.
- Itens de infraestrutura/certificação (ISO/SLA/cloud) dependem também de comprovação operacional e documental fora do código.
