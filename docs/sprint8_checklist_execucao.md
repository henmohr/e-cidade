# Checklist Executavel da Sprint 8 - Fechamento de Evidencias para Licitacao

Objetivo:
- consolidar artefatos objetivos de apresentacao para banca, reduzindo dependencia de coleta manual e acelerando homologacao externa.

Base de continuidade:
- `docs/sprint7_evidencias_tecnicas.md`
- `docs/requisitos_integracoes_conformidade_legal.md`
- `docs/requisitos_relatorios_dashboards.md`
- `docs/pacote_documental_poc.md`

## Frente A - Pacote automatizado de evidencias

- [x] Implementar geracao automatica de pacote de evidencias (manifesto + resumo + exportacoes).
- [x] Disponibilizar comando operacional para gerar pacote por periodo/sistema.
- [x] Cobrir o fluxo com teste unitario e validacao no console.

Evidencias minimas:
- comando `financeiro:gerar-pacote-evidencias` listado no `artisan`;
- manifesto JSON e resumo markdown gerados em diretorio de saida;
- teste unitario verde para o servico do pacote.

## Frente B - Homologacao externa assistida

- [x] Mapear lotes de homologacao por orgao (SICONFI, TCE/UF, Portal Transparencia).
- [x] Padronizar roteiro de coleta de protocolo externo (aceite/rejeicao) por envio.
- [x] Atualizar pacote documental com anexos de homologacao assinados.

Referencia da frente B:
- `docs/sprint8_homologacao_externa.md`
- `docs/anexos_homologacao_assinados/README.md`
- comandos: `financeiro:registrar-homologacao-integracao` e `financeiro:relatorio-homologacao-integracoes`
- comando: `financeiro:validar-anexos-homologacao`

## Definicao de pronto da Sprint 8 (parcial)

A frente A e considerada pronta quando:
1. pacote de evidencias for gerado por comando com parametrizacao de periodo;
2. artefatos forem reutilizaveis em ambiente de homologacao e banca;
3. teste automatizado estiver verde e versionado.
