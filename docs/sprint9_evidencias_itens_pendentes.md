# Sprint 9 - Evidencias dos Itens Antes Pendentes

Data: 2026-02-18

Objetivo:
- registrar evidencias tecnicas objetivas para os itens que estavam `pendente` na matriz de cobertura da licitacao e reclassifica-los para `parcial`.

## Item TR 25 - ISSQN/Nota Fiscal Eletronica

Status atualizado:
- `pendente` -> `parcial`

Evidencias localizadas:
- `docs/MODULOS_IMPLEMENTADOS.md` (secao "34. ISSQN")
- `src/Tributario/Arrecadacao/EmissaoGeral`
- `src/Tributario/Arrecadacao/Repository`
- `src/Tributario/Integracao`

Leitura tecnica:
- o repositorio possui base de dominio tributario ativa e modulo ISSQN documentado como implementado;
- falta fechar evidencia funcional de homologacao (roteiro de cenario + aceite formal da banca).

## Item TR 38-57 - Plataforma da Camara

Status atualizado:
- `pendente` -> `parcial`

Evidencias localizadas:
- `resources/legacy/contabilidade/con2_gastoscomfolhacamara_002.php`
- `resources/legacy/sicom/sic1_subsidiovereadores001.php`
- `resources/legacy/sicom/sic1_subsidiovereadores002.php`
- `src/Financeiro/Contabilidade/Relatorio/RGF/V2017/AnexoI.php` (tratamento de instituicao com camara)

Leitura tecnica:
- existem rotinas legadas e relatórios com regras específicas para contexto legislativo/câmara;
- ainda nao ha pacote de evidencia funcional completo por modulo do Lote II para classificar como `atingido`.

## Proximo passo para virar "atingido"

1. Executar roteiro funcional guiado por modulo (ISSQN/NFS-e e Camara) com evidencias de tela, log e relatorio.
2. Registrar aceite funcional por representante da banca/contratante.
3. Atualizar `docs/sprint9_matriz_status_licitacao.yml` para `atingido` quando houver evidencias formais completas.
