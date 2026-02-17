# Desenho de Fluxo - Download de Backup com Certificado A3

Objetivo:
- preparar implementacao do requisito 1.2.5 (download de backup com A3).

Status:
- desenho tecnico pronto;
- implementacao funcional depende de homologacao com ambiente de certificado.

## 1. Fluxo Alvo

1. Usuario solicita download de backup pelo portal administrativo.
2. Sistema exige autenticacao forte com certificado A3 (ICP-Brasil).
3. Gateway valida cadeia do certificado e status (incluindo revogacao/validade).
4. Sistema verifica autorizacao RBAC para papel permitido.
5. Sistema gera URL temporaria assinada para download unico.
6. Evento e trilha de auditoria sao registrados.

## 2. Componentes Necessarios

1. Provedor de autenticacao A3 (driver/token/servico homologado).
2. Camada de autorizacao integrada ao RBAC de backup.
3. Servico de emissao de URL temporaria e expirada.
4. Log de auditoria com:
- identificacao do certificado;
- usuario vinculado;
- timestamp, IP e artefato acessado.

## 3. Regras de Seguranca

1. Download com validade curta (ex.: 5 minutos).
2. Vinculo 1:1 entre solicitacao e artefato autorizado.
3. Bloqueio de reuso de URL.
4. Registro de tentativas negadas.

## 4. Dependencias Externas

1. Definicao do componente homologado para A3 no ambiente do contratante.
2. Certificados validos para testes de homologacao.
3. Janela de validacao conjunta com equipe de seguranca/juridico.

## 5. Criterio de Aceite (PoC)

1. Usuario com A3 valido consegue baixar backup autorizado.
2. Usuario sem A3 ou sem permissao recebe bloqueio auditavel.
3. Evidencias de log e trilha de auditoria apresentadas em tempo real.
