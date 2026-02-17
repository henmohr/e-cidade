# Runbook PoC - Download de Backup com A3

Objetivo:
- demonstrar fluxo protegido de acesso a backup com certificado A3.

## 1. Pré-requisitos

1. Fluxo de backup ativo (arquivos em `active` ou `archive`).
2. Variáveis:
   - `BACKUP_DOWNLOAD_ENABLED=true`
   - `BACKUP_A3_REQUIRED=true`
3. Proxy TLS configurado para repassar headers de certificado cliente.

Headers esperados:
- `SSL_CLIENT_VERIFY=SUCCESS`
- `SSL_CLIENT_S_DN=<subject>`
- `SSL_CLIENT_I_DN=<issuer>`

## 2. Fluxo de Demonstração

1. Acessar `GET /web/backup`.
2. Selecionar arquivo e gerar link temporário.
3. Fazer download pelo link assinado.

Resultado esperado:
- acesso permitido apenas com certificado válido;
- tentativa sem certificado retorna `403`;
- logs de geração e download registrados.

## 3. Evidências para PoC

1. Captura do acesso autorizado com A3.
2. Captura da tentativa negada sem A3.
3. Trecho de logs:
   - `Backup download link generated`
   - `Backup download executed`
   - `A3 certificate validation failed` (cenário negativo)

## 4. Observações

- Em ambiente local de desenvolvimento, pode ser usado bypass controlado:
  - `BACKUP_A3_ALLOW_BYPASS=true`
  - header `X-A3-Test-Bypass: 1`
- Para aprovação final na licitação, usar certificado A3 físico homologado.
