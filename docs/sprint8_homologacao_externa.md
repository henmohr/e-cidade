# Sprint 8 - Roteiro de Homologacao Externa (SICONFI, TCE/UF, Portal Transparencia)

Data base: 2026-02-18

## 1. Objetivo

Padronizar a homologacao externa das integracoes criticas, com coleta rastreavel de protocolo de aceite/rejeicao para anexacao no pacote de evidencias da licitacao.

## 2. Lotes de homologacao

### Lote 1 - SICONFI/STN

Escopo minimo:
- envio de demonstrativo contabil/orcamentario no layout vigente;
- retorno com protocolo externo;
- classificacao do envio como `aceito` ou `rejeitado`.

Critero de aceite:
- protocolo externo registrado no sistema;
- status final da integracao atualizado;
- evidencias anexadas ao manifesto da sprint.

### Lote 2 - TCE/UF (ex.: TCE-PR)

Escopo minimo:
- transmissao de lote de prestacao de contas no formato exigido pela UF;
- retorno do orgao com protocolo e situacao de processamento;
- tratamento de rejeicao com reprocessamento.

Critero de aceite:
- protocolo externo registrado por lote;
- status final `aceito` ou `rejeitado` com mensagem tecnica;
- pendencias destacadas no relatorio de homologacao.

### Lote 3 - Portal da Transparencia

Escopo minimo:
- publicacao de receitas, despesas e contratos;
- validacao de disponibilidade publica do recorte publicado;
- registro de data/hora e identificador da publicacao.

Critero de aceite:
- publicacao validada com trilha de auditoria;
- status da integracao atualizado;
- referencia de URL/identificador externo no protocolo.

## 3. Comandos operacionais da homologacao

### 3.1 Gerar pacote consolidado da banca

`php artisan financeiro:gerar-pacote-evidencias --data-inicial=2026-01-01 --data-final=2026-01-31 --sistemas=SICONFI,TCE_PR,PORTAL_TRANSPARENCIA`

### 3.2 Registrar resultado de homologacao com protocolo

`php artisan financeiro:registrar-homologacao-integracao {codigo} {resultado} {protocolo} --mensagem="texto"`

Exemplos:
- `php artisan financeiro:registrar-homologacao-integracao 101 enviado PROT-STN-2026-0001`
- `php artisan financeiro:registrar-homologacao-integracao 101 aceito PROT-STN-2026-0001 --mensagem="lote aceito sem ressalvas"`
- `php artisan financeiro:registrar-homologacao-integracao 204 rejeitado PROT-TCEPR-2026-0098 --mensagem="layout divergente no campo X"`

### 3.3 Gerar relatorio de pendencias por sistema

`php artisan financeiro:relatorio-homologacao-integracoes --sistema=SICONFI`

`php artisan financeiro:relatorio-homologacao-integracoes --sistema=TCE_PR`

`php artisan financeiro:relatorio-homologacao-integracoes --sistema=PORTAL_TRANSPARENCIA`

## 4. Checklist de coleta de protocolo externo

Para cada envio homologado:
- registrar `codigo` interno do envio;
- registrar `sistema` alvo (SICONFI/TCE_PR/PORTAL_TRANSPARENCIA);
- registrar `protocolo externo` emitido pelo orgao;
- registrar `resultado` (`enviado`, `aceito` ou `rejeitado`);
- registrar `mensagem tecnica` em caso de rejeicao;
- anexar evidencia (captura, recibo, log tecnico) no pacote documental.

## 5. Entregaveis para banca

- manifesto consolidado (`manifesto_evidencias.json`);
- resumo executivo (`resumo_evidencias.md`);
- relatorios por sistema de homologacao (`financeiro:relatorio-homologacao-integracoes`);
- anexos de protocolo externo por lote homologado.
