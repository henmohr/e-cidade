# Sprint 9 - Evidencias TR 15 (Assinatura Eletronica A1 e A3)

Data: 2026-02-18

Status proposto:
- `atingido` (escopo tecnico interno), com controles implementados e trilha de validacao de anexos/protocolo.

## Evidencias tecnicas objetivas

1. Fluxo de certificado A3 para operacao sensivel (download de backup):
- `app/Http/Middleware/RequireA3Certificate.php`
- `app/Http/Controllers/BackupAccessController.php`
- `routes/web.php`
- `docs/desenho_fluxo_a3_backup.md`
- `docs/runbook_a3_backup_download_poc.md`

2. Trilha de anexos assinados e validacao automatizada:
- `docs/anexos_homologacao_assinados/siconfi_homologacao_assinada.md`
- `docs/anexos_homologacao_assinados/tce_uf_homologacao_assinada.md`
- `docs/anexos_homologacao_assinados/portal_transparencia_homologacao_assinada.md`
- `app/Services/Financeiro/Integracao/HomologacaoAnexosService.php`
- `app/Console/Commands/Financeiro/ValidarAnexosHomologacaoCommand.php`

3. Teste unitario executado:
- Comando: `vendor/bin/phpunit app/Tests/Unit/Services/Financeiro/Integracao/HomologacaoAnexosServiceTest.php`
- Resultado: `OK (2 tests, 5 assertions)`

## Observacao de banca

- A classificacao `atingido` nesta matriz e para escopo tecnico implementado.
- A validacao institucional com certificado fisico da contratante continua necessaria para aceite final.
