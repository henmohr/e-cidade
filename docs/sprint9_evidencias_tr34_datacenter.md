# Sprint 9 - Evidencias TR 34 (Provimento de Data Center)

Data: 2026-02-18

Status proposto:
- `atingido` (escopo tecnico interno), com evidencias de observabilidade, backup/restore, seguranca de acesso e trilha documental de operacao.

## Evidencias tecnicas objetivas

1. Observabilidade e SLA:
- `app/Console/Commands/OpsHealthSnapshot.php`
- `app/Console/Commands/OpsSlaReport.php`
- `app/Console/Kernel.php`
- `docs/runbook_observabilidade_sla_poc.md`

2. Backup/restore e retencao:
- `docker/scripts/backup-retention.sh`
- `docker/scripts/restore-backup.sh`
- `docs/runbook_backup_restore_poc.md`
- `docs/politica_rbac_backup.md`

3. Controle de acesso sensivel com certificado A3:
- `app/Http/Middleware/RequireA3Certificate.php`
- `app/Http/Controllers/BackupAccessController.php`
- `docs/desenho_fluxo_a3_backup.md`
- `docs/runbook_a3_backup_download_poc.md`

4. Pacote documental com anexos assinados e validacao automatica:
- `docs/anexos_homologacao_assinados/README.md`
- `php artisan financeiro:validar-anexos-homologacao --diretorio=docs/anexos_homologacao_assinados`

## Observacao de banca

- Esta classificacao cobre o escopo tecnico interno da plataforma.
- Exigencias contratuais externas (ex.: certificacoes ISO do provedor) dependem de evidencia institucional complementar fora do codigo.
