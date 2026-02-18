# Pacote Documental Final - PoC Licitacao

Objetivo:
- consolidar os artefatos tecnicos, operacionais e funcionais para apresentacao formal da PoC.

## 1. Documentos obrigatorios (base de governanca)

- [ ] `docs/matriz_gap_poc_100.md`
- [ ] `docs/plano_execucao_sprints_poc.md`
- [ ] `docs/sprint6_checklist_execucao.md`
- [ ] `docs/roteiro_oficial_demonstracao_poc.md`
- [ ] `docs/simulacao_integral_poc.md`

## 2. Evidencias por escopo funcional

- [ ] `docs/checklist_evidencias_contabilidade_publica_poc.md`
- [ ] `docs/checklist_evidencias_demais_sistemas_poc.md`
- [ ] anexos de tela/video por sistema avaliado
- [ ] registro de aceite funcional por sistema
- [ ] `docs/anexos_homologacao_assinados/siconfi_homologacao_assinada.md`
- [ ] `docs/anexos_homologacao_assinados/tce_uf_homologacao_assinada.md`
- [ ] `docs/anexos_homologacao_assinados/portal_transparencia_homologacao_assinada.md`

## 3. Evidencias de seguranca, operacao e continuidade

- [ ] `docs/roteiro_poc_autenticacao_sprint1.md`
- [ ] `docs/runbook_sessoes_auditoria_poc.md`
- [ ] `docs/runbook_backup_restore_poc.md`
- [ ] `docs/runbook_a3_backup_download_poc.md`
- [ ] `docs/runbook_observabilidade_sla_poc.md`
- [ ] `docs/runbook_acessibilidade_poc.md`

## 4. Criterio de completude do pacote

Considerar pacote final concluido somente quando:
1. todos os documentos obrigatorios estiverem atualizados e versionados;
2. todos os checklists estiverem preenchidos com status e evidencias;
3. houver aceite funcional registrado para os blocos de demonstracao;
4. pendencias residuais estiverem listadas com plano e prazo.

## 5. Checklist de entrega para banca

- [ ] pasta de evidencias organizada por bloco (seguranca, operacao, funcional);
- [ ] links para documentos internos validados;
- [ ] assinatura dos responsaveis tecnico e funcional;
- [ ] versao final do pacote registrada com data.

## 6. Validacao automatizada de anexos assinados

Comando:
- `php artisan financeiro:validar-anexos-homologacao --diretorio=docs/anexos_homologacao_assinados`

Regra:
- o pacote somente pode ser considerado fechado quando o comando retornar status `ok`.
