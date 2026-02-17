#!/usr/bin/env bash
set -euo pipefail

# Backup completo do PostgreSQL com retencao 15/35 dias.
# - <=15 dias: backups "ativos" para acesso imediato.
# - 16..35 dias: backups "arquivo" para retenção adicional.
# - >35 dias: remocao automatica.

BACKUP_ENABLED="${BACKUP_ENABLED:-true}"
if [[ "${BACKUP_ENABLED}" != "true" ]]; then
  echo "Backup desabilitado (BACKUP_ENABLED=${BACKUP_ENABLED})."
  exit 0
fi

BACKUP_BASE_DIR="${BACKUP_DIR:-/var/backups/ecidade}"
ACTIVE_DAYS="${BACKUP_RETENTION_ACTIVE_DAYS:-15}"
ARCHIVE_DAYS="${BACKUP_RETENTION_ARCHIVE_DAYS:-35}"

PGHOST="${BACKUP_PGHOST:-${DB_HOST:-localhost}}"
PGPORT="${BACKUP_PGPORT:-${DB_PORT:-5432}}"
PGDATABASE="${BACKUP_PGDATABASE:-${DB_DATABASE:-ecidade}}"
PGUSER="${BACKUP_PGUSER:-${DB_USERNAME:-ecidade}}"
PGPASSWORD="${BACKUP_PGPASSWORD:-${DB_PASSWORD:-}}"
export PGPASSWORD

TS="$(date +%Y%m%d_%H%M%S)"
ACTIVE_DIR="${BACKUP_BASE_DIR}/active"
ARCHIVE_DIR="${BACKUP_BASE_DIR}/archive"
MANIFEST_DIR="${BACKUP_BASE_DIR}/manifest"
TMP_DIR="${BACKUP_BASE_DIR}/tmp"

mkdir -p "${ACTIVE_DIR}" "${ARCHIVE_DIR}" "${MANIFEST_DIR}" "${TMP_DIR}"

BACKUP_FILE="${ACTIVE_DIR}/ecidade_${PGDATABASE}_${TS}.dump"
GLOBALS_FILE="${ACTIVE_DIR}/ecidade_globals_${TS}.sql"
CHECKSUM_FILE="${MANIFEST_DIR}/ecidade_${TS}.sha256"
MANIFEST_FILE="${MANIFEST_DIR}/ecidade_${TS}.txt"

echo "Iniciando backup: ${BACKUP_FILE}"
pg_dump \
  --host="${PGHOST}" \
  --port="${PGPORT}" \
  --username="${PGUSER}" \
  --format=custom \
  --blobs \
  --verbose \
  --file="${BACKUP_FILE}" \
  "${PGDATABASE}"

echo "Gerando backup de globais: ${GLOBALS_FILE}"
pg_dumpall \
  --host="${PGHOST}" \
  --port="${PGPORT}" \
  --username="${PGUSER}" \
  --globals-only \
  > "${GLOBALS_FILE}"

sha256sum "${BACKUP_FILE}" "${GLOBALS_FILE}" > "${CHECKSUM_FILE}"

{
  echo "timestamp=${TS}"
  echo "database=${PGDATABASE}"
  echo "host=${PGHOST}"
  echo "port=${PGPORT}"
  echo "user=${PGUSER}"
  echo "backup_file=${BACKUP_FILE}"
  echo "globals_file=${GLOBALS_FILE}"
  echo "checksum_file=${CHECKSUM_FILE}"
  echo "active_retention_days=${ACTIVE_DAYS}"
  echo "archive_retention_days=${ARCHIVE_DAYS}"
} > "${MANIFEST_FILE}"

echo "Aplicando politica de retencao..."

# Move de active para archive quando ultrapassa janela ativa.
find "${ACTIVE_DIR}" -type f -name "*.dump" -mtime +"${ACTIVE_DAYS}" -print0 | while IFS= read -r -d '' f; do
  mv "${f}" "${ARCHIVE_DIR}/"
done
find "${ACTIVE_DIR}" -type f -name "*.sql" -mtime +"${ACTIVE_DAYS}" -print0 | while IFS= read -r -d '' f; do
  mv "${f}" "${ARCHIVE_DIR}/"
done

# Remove arquivos mais antigos que retenção total.
find "${ARCHIVE_DIR}" -type f \( -name "*.dump" -o -name "*.sql" \) -mtime +"${ARCHIVE_DAYS}" -delete
find "${MANIFEST_DIR}" -type f -mtime +"${ARCHIVE_DAYS}" -delete

echo "Backup concluido com sucesso."
