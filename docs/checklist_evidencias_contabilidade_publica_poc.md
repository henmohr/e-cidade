# Checklist de Evidencias - Contabilidade Publica (PoC)

Objetivo:
- apoiar decisao objetiva de "atingido ou nao atingido" para o escopo de Contabilidade Publica na licitacao.

## 1. Status de Atingimento

Marcar:
- [ ] Atingido (100%)
- [ ] Parcial
- [ ] Nao atingido

Data da avaliacao: ____/____/____
Responsavel tecnico: ____________________
Representante funcional/cliente: ____________________

## 2. Evidencias Minimas Obrigatorias

## 2.1 Cobertura Funcional de Modulos Contabeis

- [ ] Evidencia de acesso ao modulo de Contabilidade.
- [ ] Evidencia de acesso ao modulo de Orcamento.
- [ ] Evidencia de acesso ao modulo de Empenho.
- [ ] Evidencia de acesso ao modulo de Tesouraria.
- [ ] Evidencia de acesso ao modulo STN/SICONFI (quando aplicavel no ambiente).

Evidencias aceitas:
- captura de tela da funcionalidade;
- video curto da execucao;
- registro de usuario/data/hora.

## 2.2 Fluxo Ponta a Ponta (Cenario de PoC)

- [ ] Cadastro/parametrizacao inicial do exercicio.
- [ ] Execucao de lancamento relevante no fluxo contabil.
- [ ] Reflexo do lancamento em consulta/relatorio.
- [ ] Integracao entre modulos (ex.: Orcamento -> Empenho -> Contabilidade/Tesouraria).
- [ ] Exportacao/geracao de arquivo ou demonstrativo exigido no cenario.

Evidencias aceitas:
- roteiro executado sem intervencao manual fora do procedimento;
- log funcional do cenario;
- validacao assinada pelo avaliador.

## 2.3 Auditoria e Rastreabilidade

- [ ] Log de autenticacao do usuario que executou o cenario.
- [ ] Log de operacao funcional (inclusao/alteracao/exclusao quando aplicavel).
- [ ] Registro de data/hora e IP de origem.
- [ ] Evidencia de usuario/perfil com permissao adequada.

Referencias no projeto:
- `docs/runbook_sessoes_auditoria_poc.md`
- `resources/views/audits/index.blade.php`

## 2.4 Relatorios e Saidas de Contabilidade Publica

- [ ] Demonstrativo gerado no modulo contabil conforme cenario.
- [ ] Consistencia basica dos valores entre telas/relatorios usados na PoC.
- [ ] Evidencia de consulta por filtros minimos (periodo, entidade, exercicio).
- [ ] Exportacao/impressao quando aplicavel no roteiro.

## 2.5 Requisitos Transversais de Plataforma (impactam Contabilidade)

- [ ] Disponibilidade basica observada no periodo da demo (`/api/health/live` e `/api/health/ready`).
- [ ] Evidencia de coleta de SLA no periodo (`ops:health-snapshot` e `ops:sla-report`).
- [ ] Evidencia de backup/restore aplicavel ao ambiente da PoC.

Referencias no projeto:
- `docs/runbook_observabilidade_sla_poc.md`
- `docs/runbook_backup_restore_poc.md`

## 3. Mapa de Evidencias (preenchimento)

| Item | Evidencia | Local/Arquivo | Responsavel | Resultado |
|---|---|---|---|---|
| Cobertura funcional |  |  |  | [ ] OK [ ] NOK |
| Fluxo ponta a ponta |  |  |  | [ ] OK [ ] NOK |
| Auditoria |  |  |  | [ ] OK [ ] NOK |
| Relatorios |  |  |  | [ ] OK [ ] NOK |
| Requisitos transversais |  |  |  | [ ] OK [ ] NOK |

## 4. Criterio Objetivo para "Atingido"

Considerar "Atingido (100%)" somente quando:
1. todos os blocos 2.1 a 2.5 estiverem marcados como concluidos;
2. o fluxo ponta a ponta estiver validado pelo avaliador funcional;
3. as evidencias estiverem anexadas e rastreaveis.

Caso qualquer bloco critico esteja pendente, classificar como "Parcial".

## 5. Observacoes da Banca/Avaliacao

- _______________________________________________
- _______________________________________________
- _______________________________________________
