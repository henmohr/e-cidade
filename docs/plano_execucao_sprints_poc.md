# Plano de Execução por Sprints - Adequação PoC (Requisitos Obrigatórios 100%)

Base de planejamento:
- `docs/matriz_gap_poc_100.md`
- `docs/caracteristicas_gerais_basicas_obrigatorias.md`

## Premissas

- Stack mantida: PHP (sem migração tecnológica).
- Time de referência para estimativa: 3 pessoas (1 backend full-time, 1 DevOps/SRE full-time, 1 QA/analista funcional parcial).
- Duração de sprint: 10 dias úteis.
- Estimativas abaixo em `dias úteis` e representam esforço líquido por frente.

## Objetivo

Atingir condição de aprovação na PoC para os itens obrigatórios de 100%, priorizando:
1. autenticação e segurança;
2. backup/restore e governança de dados;
3. disponibilidade/SLA e evidências operacionais;
4. cobertura de auditoria e roteiros de demonstração.

## Roadmap (7 Sprints)

## Sprint 1 - Segurança de Acesso (Crítico)

Meta: fechar lacunas de identidade e MFA.

- Frente A: Autenticação unificada por CPF e desenho de identidade.
  Estimativa: 6 dias
  Requisitos: 1.3.14
- Frente B: MFA obrigatório por perfil.
  Estimativa: 4 dias
  Requisitos: 1.3.15.d
- Frente C: Política de senhas e recuperação aderente.
  Estimativa: 3 dias
  Requisitos: 1.3.15.c, 1.3.16
- Frente D: Testes de autenticação e cenários de falha.
  Estimativa: 2 dias

Total estimado da sprint: 15 dias
Risco: integração com provedores externos (GOVBR/A1/A3) pode puxar parte para Sprint 2.

## Sprint 2 - Backup, Restore e Dados (Crítico)

Meta: comprovar restauração e retenção exigidas.

- Frente A: Pipeline de backup automatizado e retenção 15/35 dias.
  Estimativa: 4 dias
  Requisitos: 1.2.4.a-d
- Frente B: Procedimento de restore completo com metadados e testes.
  Estimativa: 4 dias
  Requisitos: 1.2.3, 1.1.11
- Frente C: Controle de credenciais do banco + RBAC mínimo.
  Estimativa: 3 dias
  Requisitos: 1.2.6, 1.2.7
- Frente D: Prova de acesso protegido para download de backup (A3).
  Estimativa: 5 dias
  Requisitos: 1.2.5

Total estimado da sprint: 16 dias
Risco: requisito de certificado A3 pode depender de componente externo e homologação com cliente.

## Sprint 3 - Alta Disponibilidade e Infra de PoC (Crítico)

Meta: fechar gaps de cloud operacional e disponibilidade.

- Frente A: Topologia com múltiplas instâncias + balanceador.
  Estimativa: 5 dias
  Requisitos: 1.1.10, 1.3.1
- Frente B: Observabilidade em tempo real (infra + banco + app).
  Estimativa: 4 dias
  Requisitos: 1.1.2
- Frente C: Medição de SLA (SLO/SLI) e relatório de disponibilidade.
  Estimativa: 3 dias
  Requisitos: 1.1.6
- Frente D: Plano de atualização segura de componentes base.
  Estimativa: 2 dias
  Requisitos: 1.1.4, 1.1.5

Total estimado da sprint: 14 dias
Risco: dependência de infraestrutura e operação fora do repositório.

## Sprint 4 - Auditoria, Sessões e Governança

Meta: consolidar rastreabilidade e gestão de sessões.

- Frente A: Cobertura de trilha de auditoria nas rotinas críticas.
  Estimativa: 5 dias
  Requisitos: 1.3.21
- Frente B: Tela de sessões ativas e encerramento de sessões.
  Estimativa: 3 dias
  Requisitos: 1.3.9
- Frente C: Alertas de falha de autenticação para usuário.
  Estimativa: 2 dias
  Requisitos: 1.3.8
- Frente D: Evidências de logs de autenticação (login/logout).
  Estimativa: 2 dias
  Requisitos: 1.3.7

Total estimado da sprint: 12 dias
Risco: pontos legados sem padronização podem demandar refatoração localizada.

## Sprint 5 - Acessibilidade e Operação Web

Meta: fechar requisitos de UX obrigatórios e demonstráveis.

- Frente A: Implementar contraste/temas e filtros de daltonismo.
  Estimativa: 4 dias
  Requisitos: 1.3.27
- Frente B: Validação formal de multiabas/multiusuário e domínio único.
  Estimativa: 3 dias
  Requisitos: 1.3.11, 1.3.12, 1.3.3
- Frente C: Políticas de acesso por dia/horário/grupo/expiração.
  Estimativa: 4 dias
  Requisitos: 1.3.28

Total estimado da sprint: 11 dias
Risco: impactos em UI legada e controle de sessão compartilhada.

## Sprint 6 - Fechamento de PoC e Simulação Completa

Meta: preparar aprovação formal da PoC.

- Frente A: Roteiro oficial de demonstração item a item (100%).
  Estimativa: 3 dias
- Frente B: Simulação integral da PoC com gravação de evidências.
  Estimativa: 4 dias
- Frente C: Pacote documental técnico-operacional (infra, segurança, backup, SLA).
  Estimativa: 4 dias
- Frente D: Correções finais de aderência e regressão.
  Estimativa: 4 dias

Total estimado da sprint: 15 dias
Risco: ajustes de última hora em integração externa.

## Sprint 7 - Nucleo Financeiro e Aderencia Ampliada

Meta: operacionalizar os requisitos dos modulos financeiros centrais e reforcar conformidade legal com evidencias.
Status: concluida em desenvolvimento interno (2026-02-18), pendente apenas de homologacoes externas formais.

- Frente A: Execucao Orcamentaria (ciclo fixacao -> empenho -> liquidacao -> pagamento com bloqueios).
  Estimativa: 5 dias
  Requisitos: `docs/requisitos_modulo3_execucao_orcamentaria.md`
- Frente B: Tesouraria e Fluxo de Caixa (conciliacao, previsao 7 dias, programacao e restos a pagar).
  Estimativa: 4 dias
  Requisitos: `docs/requisitos_modulo4_tesouraria_fluxo_caixa.md`
- Frente C: Controle de Despesas e Receitas (credores, retencoes, classificacao de receitas).
  Estimativa: 5 dias
  Requisitos: `docs/requisitos_modulo5_controle_despesas_receitas.md`
- Frente D: Integracoes e Conformidade Legal (SICONFI, TCE/TCU, transparencia e trilhas).
  Estimativa: 4 dias
  Requisitos: `docs/requisitos_integracoes_conformidade_legal.md`
- Frente E: Relatorios e Dashboard Executivo (balancos, DVP/DFC, RGF/RREO, exportacoes).
  Estimativa: 4 dias
  Requisitos: `docs/requisitos_relatorios_dashboards.md`

Total estimado da sprint: 22 dias
Risco: dependencias de homologacao e janelas de integracao com orgaos externos.

## Estimativa Consolidada

- Esforço total estimado: 105 dias úteis
- Com paralelização proposta (3 pessoas): 7 sprints de 10 dias úteis + margem de estabilizacao (aprox. 15 semanas)
- Margem recomendada: +2 semanas para riscos externos (infra/certificados/homologações)

## Dependências Externas Críticas

1. Disponibilização de ambiente cloud de PoC com acesso para auditoria em tempo real.
2. Definição do provedor/estratégia para exigências de certificado A3 no fluxo de backup.
3. Evidências formais de SLA e, quando exigido no processo, comprovação de certificações do ambiente.
4. Janela com equipe funcional do órgão para validar roteiros de demonstração.

## Critérios de Pronto por Sprint

- Código versionado e revisado.
- Evidência de teste funcional por requisito da sprint.
- Evidência operacional (logs, dashboard, relatório) quando aplicável.
- Atualização da matriz de gaps com status novo (`Atende`, `Parcial`, `Não atende`).

## Próximo Passo Recomendado

Criar `Sprint 1` em tarefas executáveis (quebra por arquivo/componente), com dono e prazo diário.
