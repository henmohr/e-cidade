# Runbook PoC - Backup e Restore

Objetivo:
- demonstrar capacidade de backup completo e restore íntegro;
- gerar evidências para requisitos 1.2.3 e 1.2.4 (retenção 15/35).

## 1. Pré-requisitos

1. Ambiente com `pg_dump`, `pg_dumpall` e `pg_restore`.
2. Variáveis configuradas (ou valores default em `.env`):
   - `BACKUP_ENABLED`
   - `BACKUP_DIR`
   - `BACKUP_RETENTION_ACTIVE_DAYS`
   - `BACKUP_RETENTION_ARCHIVE_DAYS`
   - `BACKUP_PGHOST`, `BACKUP_PGPORT`, `BACKUP_PGDATABASE`, `BACKUP_PGUSER`, `BACKUP_PGPASSWORD`
3. Permissão de escrita no diretório de backup.

## 2. Execução de Backup

```bash
./docker/scripts/backup-retention.sh
```

Saídas esperadas:
- `.../active/ecidade_<db>_<timestamp>.dump`
- `.../active/ecidade_globals_<timestamp>.sql`
- `.../manifest/ecidade_<timestamp>.sha256`
- `.../manifest/ecidade_<timestamp>.txt`

## 3. Verificação de Integridade

```bash
sha256sum -c /caminho/manifest/ecidade_<timestamp>.sha256
```

Resultado esperado:
- todas as entradas com `OK`.

## 4. Simulação de Restore

1. Escolher dump válido em `active` ou `archive`.
2. Executar restore com confirmação explícita:

```bash
./docker/scripts/restore-backup.sh --file /caminho/ecidade_<db>_<timestamp>.dump --force
```

Resultado esperado:
- `Restore concluído com sucesso.`
- dados e metadados restaurados sem erro crítico.

## 5. Evidências para PoC

Registrar:
1. Log da execução do backup.
2. Lista de artefatos gerados.
3. Saída da validação de checksum.
4. Log da execução do restore.
5. Capturas de tela/terminal com timestamp.

## 6. Política de Retenção (Implementada)

- até 15 dias: backups em `active` (acesso rápido);
- 16 a 35 dias: backups movidos para `archive`;
- acima de 35 dias: remoção automática.

## 7. Limites Conhecidos

- requisito 1.2.5 (download com certificado A3) depende de integração externa e política institucional;
- RBAC de acesso aos artefatos depende do ambiente operacional do contratante/provedor.
