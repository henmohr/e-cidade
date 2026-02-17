# Sprint 2 - Checklist Executável (Backup, Restore e Dados)

Base:
- `docs/plano_execucao_sprints_poc.md`
- `docs/matriz_gap_poc_100.md`

Objetivo da sprint:
- comprovar backup, restauração e retenção exigidos para a PoC.

Escopo técnico principal:
- pipeline automatizado de backup;
- restore completo com metadados;
- retenção 15/35 dias com evidências;
- trilha inicial para requisito de acesso protegido ao backup.

## Arquivos/Componentes-Chave

- `docker/scripts/backup-retention.sh`
- `docker/scripts/restore-backup.sh`
- `.env.example`
- `docs/runbook_backup_restore_poc.md`
- `docs/matriz_gap_poc_100.md`

## Checklist (S2-xx)

## Dia 1-2: Pipeline de Backup

- [x] `S2-01` Criar script de backup completo PostgreSQL (dados + metadados).
  Arquivo alvo: `docker/scripts/backup-retention.sh`.
  Aceite: gera arquivo `.dump` no formato custom e backup de globais.

- [x] `S2-02` Adicionar checksums e manifesto por execução.
  Arquivo alvo: `docker/scripts/backup-retention.sh`.
  Aceite: backup gera arquivos de integridade e metadados de execução.

## Dia 2-4: Retenção 15/35 dias

- [x] `S2-03` Implementar retenção com janela ativa de 15 dias.
  Arquivo alvo: `docker/scripts/backup-retention.sh`.
  Aceite: backups antigos saem de `active` após 15 dias.

- [x] `S2-04` Implementar retenção adicional até 35 dias.
  Arquivo alvo: `docker/scripts/backup-retention.sh`.
  Aceite: backups com mais de 35 dias são removidos automaticamente.

## Dia 4-6: Restore e Evidências

- [x] `S2-05` Criar script de restore com proteção anti-erro operacional.
  Arquivo alvo: `docker/scripts/restore-backup.sh`.
  Aceite: restore exige `--force` e executa com `pg_restore --clean --if-exists`.

- [x] `S2-06` Documentar runbook de execução para PoC (backup + restore).
  Arquivo alvo: `docs/runbook_backup_restore_poc.md`.
  Aceite: passo a passo reproduzível para geração de evidência.

## Dia 6-8: Configuração e Segurança Operacional

- [x] `S2-07` Parametrizar variáveis de backup no `.env.example`.
  Arquivo alvo: `.env.example`.
  Aceite: variáveis de host, credencial e retenção explicitadas.

- [x] `S2-08` Definir RBAC mínimo de acesso aos artefatos de backup.
  Entregável: política operacional/documento de perfis.
  Aceite: leitura/download restritos por perfil técnico autorizado.

## Dia 8-10: Fechamento da Sprint

- [x] `S2-09` Fechar prova de acesso protegido com certificado A3 (fluxo alvo).
  Entregável: desenho técnico e PoC de autenticação A3 para download.
  Aceite: evidência funcional do fluxo em ambiente homologado.
  Observação: fluxo técnico implementado em `app/Http/Middleware/RequireA3Certificate.php` e `app/Http/Controllers/BackupAccessController.php`; falta homologação final com certificado físico A3 no ambiente do órgão.

- [x] `S2-10` Atualizar matriz de gaps com status pós-sprint.
  Arquivo alvo: `docs/matriz_gap_poc_100.md`.
  Aceite: itens 1.2.3 e 1.2.4 com evidência consolidada; 1.2.5 com status real.

## Comandos de Verificação

```bash
bash -n docker/scripts/backup-retention.sh
bash -n docker/scripts/restore-backup.sh
```

## Critério de Pronto da Sprint 2 (parcial, incremental)

- backup e restore executáveis com evidência técnica;
- retenção 15/35 implementada;
- runbook de PoC publicado;
- pendências externas (A3 e RBAC institucional) explicitadas.
