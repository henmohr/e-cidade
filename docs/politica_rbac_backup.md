# Politica RBAC - Acesso a Backups

Objetivo:
- definir controle minimo de acesso aos artefatos de backup da PoC.

## 1. Papeis

1. `backup_admin`
- pode executar backup e restore;
- pode consultar manifestos e checksums;
- pode iniciar processo de entrega de backup para contratante.

2. `backup_operator`
- pode executar backup;
- pode consultar logs e manifestos;
- nao pode executar restore em producao sem aprovacao.

3. `backup_auditor`
- somente leitura em manifestos, checksums e evidencias;
- sem acesso ao conteudo bruto dos dumps.

4. `backup_requester`
- papel de solicitacao formal de exportacao/download;
- sem acesso direto aos artefatos.

## 2. Regras Minimas

1. Principio de menor privilegio para todos os papeis.
2. Separacao de funcao: quem gera backup nao aprova sozinho restore em producao.
3. Toda acao de backup/restore deve gerar log com usuario, data/hora e origem.
4. Acesso a diretorio de backups somente por grupo tecnico autorizado.
5. Entrega externa de backup requer protocolo de solicitacao e registro de auditoria.

## 3. Controles Operacionais

1. Permissao de filesystem:
- `active` e `archive`: leitura/escrita apenas para `backup_admin` e `backup_operator`.
- `manifest`: leitura permitida para `backup_auditor`.

2. Execucao de restore:
- obrigatoria com dupla aprovacao (operacao + seguranca) em producao.

3. Revisao periodica:
- revisao mensal de membros dos papeis;
- revogacao imediata de acessos desligados.

## 4. Evidencias para PoC

1. Lista de usuarios por papel.
2. Registro de execucao de backup.
3. Registro de um teste de restore em homologacao.
4. Registro de revisao de permissao.
