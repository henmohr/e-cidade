# Modulo Legado: transporteescolar

## Objetivo
Rotinas de transporte escolar e operacao.

## O que tem neste diretorio
- Scripts legados em PHP usados por telas, processos e rotinas do modulo.
- Total de arquivos funcionais no nivel raiz: 18.
- Pode conter formularios, consultas, relatorios e utilitarios operacionais.

## Exemplos de arquivos
- `tre1_linhasfrequencia.RPC.php`
- `tre1_linhastransporte001.php`
- `tre1_linhastransporte002.php`
- `tre1_linhastransporte003.php`
- `tre1_pontosparada001.php`
- `tre1_pontosparada002.php`
- `tre1_pontosparada003.php`
- `tre1_veiculo001.php`

## O que o modulo faz
- Executa funcionalidades operacionais do dominio `transporteescolar` no legado.
- Atende fluxos historicos ainda em producao e usados por usuarios finais.
- Serve como base para rastreabilidade na modernizacao incremental.

## Diretrizes de modernizacao
- Nao alterar comportamento legado sem evidencia e teste de regressao.
- Priorizar encapsulamento por APIs/servicos antes de substituir telas.
- Registrar paridade funcional com os requisitos de licitacao (TR/SPS).
