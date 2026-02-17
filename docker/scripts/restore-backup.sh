#!/usr/bin/env bash
set -euo pipefail

# Restore controlado de backup PostgreSQL.
# Uso:
#   ./docker/scripts/restore-backup.sh --file /caminho/backup.dump --force

BACKUP_FILE=""
TARGET_DB="${RESTORE_PGDATABASE:-${DB_DATABASE:-ecidade}}"
PGHOST="${RESTORE_PGHOST:-${DB_HOST:-localhost}}"
PGPORT="${RESTORE_PGPORT:-${DB_PORT:-5432}}"
PGUSER="${RESTORE_PGUSER:-${DB_USERNAME:-ecidade}}"
PGPASSWORD="${RESTORE_PGPASSWORD:-${DB_PASSWORD:-}}"
FORCE="false"
export PGPASSWORD

while [[ $# -gt 0 ]]; do
  case "$1" in
    --file)
      BACKUP_FILE="${2:-}"
      shift 2
      ;;
    --db)
      TARGET_DB="${2:-}"
      shift 2
      ;;
    --force)
      FORCE="true"
      shift
      ;;
    *)
      echo "Parametro invalido: $1"
      exit 1
      ;;
  esac
done

if [[ -z "${BACKUP_FILE}" ]]; then
  echo "Uso: $0 --file /caminho/backup.dump [--db nome_db] --force"
  exit 1
fi

if [[ ! -f "${BACKUP_FILE}" ]]; then
  echo "Arquivo nao encontrado: ${BACKUP_FILE}"
  exit 1
fi

if [[ "${FORCE}" != "true" ]]; then
  echo "Restore exige --force para evitar execucao acidental."
  exit 1
fi

echo "Iniciando restore em ${TARGET_DB} a partir de ${BACKUP_FILE}"

pg_restore \
  --host="${PGHOST}" \
  --port="${PGPORT}" \
  --username="${PGUSER}" \
  --dbname="${TARGET_DB}" \
  --clean \
  --if-exists \
  --no-owner \
  --no-privileges \
  --verbose \
  "${BACKUP_FILE}"

echo "Restore concluido com sucesso."
