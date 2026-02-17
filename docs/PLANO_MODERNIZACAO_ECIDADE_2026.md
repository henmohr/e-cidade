# Plano de Modernizacao do e-cidade

Documento Tecnico | Versao: 1.0 | Data: Fevereiro/2026

## 1. Visao Geral

Plano de modernizacao tecnica do e-cidade com foco em:
- conformidade LGPD;
- evolucao incremental de arquitetura;
- seguranca, qualidade e governanca;
- continuidade operacional para municipios.

## 2. Diagnostico Atual

Panorama observado no repositorio:
- base hibrida (legado + Laravel);
- divida tecnica relevante no legado;
- cobertura de testes ainda limitada para refatoracoes amplas;
- documentacao tecnica em evolucao;
- avancos recentes em autenticacao e MFA.

Riscos centrais:
- regressao em fluxos criticos municipais;
- complexidade de migracao em modulos acoplados;
- lacunas de evidencias para requisitos obrigatorios de licitacao/PoC.

## 3. Objetivos da Modernizacao

1. Modernizacao arquitetural incremental com baixo risco operacional.
2. Evolucao de seguranca e privacidade (LGPD by design).
3. Melhoria de qualidade/testabilidade para sustentar mudancas.
4. Estrategia API-first para integracoes externas.
5. Padronizacao de DevOps, observabilidade e governanca tecnica.

## 4. Roteiro de Upgrade (Fases)

### Fase 1 - Fundacao (1-2 meses)
- consolidar hardening de autenticacao/autorizacao;
- ampliar trilha de auditoria e controles de sessao;
- reforcar baseline de seguranca em rotas legadas.

Entregas:
- checklist de seguranca por modulo;
- padrao de logs de acesso sensivel;
- backlog de correcao de queries sem binding.

### Fase 2 - Estrangulamento do Legado (3-6 meses)
- aplicar strangler pattern por modulo priorizado;
- mover regras de negocio para camadas modernas;
- manter compatibilidade por contrato durante transicao.

Prioridade sugerida:
1. autenticacao e usuarios;
2. tributario/ISS;
3. contabilidade;
4. RH;
5. licitacoes/patrimonio;
6. modulos de menor criticidade.

### Fase 3 - Qualidade e Conformidade (2-3 meses)
- testes unitarios para codigo critico;
- testes de integracao para fluxos ponta a ponta;
- consolidacao de controles LGPD (inventario, base legal, retencao, auditoria).

### Fase 4 - Evolucao de Interface (opcional)
- modernizacao progressiva de telas criticas;
- padrao de acessibilidade e usabilidade;
- suporte a futuros canais digitais sem ruptura.

## 5. Ferramentas Recomendadas

- Analise estatica: PHPStan (+ Larastan), PHPCS/CS Fixer.
- Testes: PHPUnit, testes de integracao por modulo.
- Seguranca: scanner de dependencias, validacao OWASP basica no CI.
- Observabilidade: logs estruturados + monitoramento de saude.
- Pipeline: GitHub Actions com quality gates.

## 6. Plano de Acao Imediato (30 dias)

1. Fechar pendencias de autenticacao e evidencias de PoC.
2. Criar inventario LGPD de endpoints e dados pessoais.
3. Mapear queries legadas sem parameter binding e priorizar correcao.
4. Definir quality gates minimos no CI (lint, teste alvo, analise estatica).
5. Publicar roteiro de migracao por modulo com criterios de aceite.

## 7. Riscos e Mitigacoes

- Quebra de compatibilidade:
  mitigacao: testes de regressao, rollout gradual, rollback documentado.
- Incidente de seguranca:
  mitigacao: plano de resposta, trilha de auditoria, isolamento rapido.
- Estouro de prazo:
  mitigacao: sprints curtas, priorizacao por risco, revisao quinzenal.

## 8. Metricas de Sucesso

- cobertura de testes em codigo critico;
- reducao de itens criticos de seguranca;
- reducao de gaps obrigatorios na matriz de PoC;
- estabilidade operacional durante migracoes.

## 9. Referencias no Repositorio

- `docs/PAPEL_ARQUITETO_MODERNIZACAO.md`
- `docs/matriz_gap_poc_100.md`
- `docs/plano_execucao_sprints_poc.md`
- `docs/sprint1_checklist_execucao.md`
- `docs/roteiro_poc_autenticacao_sprint1.md`
