# Sprint 9 - Evidencias TR 2 (Orcamentario: PPA, LDO e LOA)

Data: 2026-02-18

Status proposto:
- `atingido` (escopo de desenvolvimento interno), com rastreabilidade de comandos, testes e artefatos da Sprint 7.

## Evidencias tecnicas objetivas

1. Servicos e comandos implementados para relatorios orcamentarios:
- `app/Services/Financeiro/Relatorio/BalancoService.php`
- `app/Services/Financeiro/Relatorio/RelatorioFiscalService.php`
- `app/Console/Commands/Financeiro/GerarBalancosCommand.php`
- `app/Console/Commands/Financeiro/GerarRelatoriosFiscaisCommand.php`

2. Testes unitarios executados:
- Comando: `vendor/bin/phpunit app/Tests/Unit/Services/Financeiro/Relatorio/BalancoServiceTest.php app/Tests/Unit/Services/Financeiro/Relatorio/RelatorioFiscalServiceTest.php`
- Resultado: `OK (3 tests, 9 assertions)`

3. Comandos disponiveis no console:
- `financeiro:gerar-balancos`
- `financeiro:gerar-relatorios-fiscais`

## Observacao de banca

- Classificacao como `atingido` nesta matriz considera a entrega tecnica e testada no repositorio.
- Validacao funcional final com contratante continua necessaria para aceite formal da PoC.
