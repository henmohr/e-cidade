# Checklist Executavel da Sprint 9 - Cobertura da Licitacao por Modulo

Objetivo:
- consolidar status objetivo por sistema da licitacao com rastreabilidade de evidencia, reduzindo subjetividade na banca.

## Frente A - Matriz estruturada de cobertura

- [x] Criar matriz estruturada em YAML para status por sistema/modulo.
- [x] Definir status padrao (`atingido`, `parcial`, `pendente`) e campo de evidencia principal.
- [x] Versionar baseline inicial da cobertura da licitacao.
- [x] Reclassificar itens antes pendentes com evidencias tecnicas objetivas (quando localizadas).

Referencia:
- `docs/sprint9_evidencias_itens_pendentes.md`

## Frente B - Relatorio automatizado de cobertura

- [x] Implementar servico para sumarizar cobertura e pendencias.
- [x] Implementar comando para gerar relatorio markdown consolidado.
- [x] Cobrir fluxo com teste unitario.

## Frente C - Evolucao de status com evidencia objetiva

- [x] Converter TR 2 (Orcamentario) de `parcial` para `atingido` com dossier tecnico.
- [x] Converter TR 3 (Tesouraria) de `parcial` para `atingido` com dossier tecnico.
- [x] Converter TR 4 (Prestacao de Contas/TCE) de `parcial` para `atingido` com dossier tecnico.
- [x] Converter TR 6 (Compras e Licitacoes) de `parcial` para `atingido` com dossier tecnico.
- [x] Converter TR 15 (Assinatura A1/A3) de `parcial` para `atingido` com dossier tecnico.
- [x] Converter TR 25 (ISSQN/NF-e) de `parcial` para `atingido` com dossier tecnico.
- [x] Converter TR 34 (Data Center) de `parcial` para `atingido` com dossier tecnico.
- [x] Converter TR 38-57 (Plataforma da Camara) de `parcial` para `atingido` com dossier tecnico.
- [x] Regerar relatorio consolidado apos reclassificacao.

Referencias:
- `docs/sprint9_evidencias_tr2_orcamentario.md`
- `docs/sprint9_evidencias_tr3_tesouraria.md`
- `docs/sprint9_evidencias_tr4_prestacao_contas.md`
- `docs/sprint9_evidencias_tr6_compras_licitacoes.md`
- `docs/sprint9_evidencias_tr15_assinatura_a1_a3.md`
- `docs/sprint9_evidencias_tr25_issqn_nfe.md`
- `docs/sprint9_evidencias_tr34_datacenter.md`
- `docs/sprint9_evidencias_tr38_57_camara.md`

Evidencias minimas:
- comando `financeiro:relatorio-cobertura-licitacao` listado no `artisan`;
- arquivo `docs/sprint9_relatorio_cobertura_licitacao.md` gerado via comando;
- testes unitarios verdes para o servico de cobertura.

## Definicao de pronto da Sprint 9 (parcial)

A sprint e considerada pronta nesta etapa quando:
1. a matriz estiver versionada e atualizavel por ciclo de homologacao;
2. o relatorio consolidado for gerado automaticamente;
3. pendencias estiverem listadas por sistema com evidencia principal.
