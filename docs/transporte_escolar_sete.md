# Transporte Escolar - Integração SETE

Este documento descreve o contrato JSON usado na integração SETE do módulo de Transporte Escolar.

## Endpoints

- `GET /web/transporte-escolar/export/sete.json`
- `POST /web/transporte-escolar/import/sete`

## Estrutura exportada

O arquivo exportado contém:

- `metadados`
- `linhas`
- `veiculos`
- `vinculos`
- `alunos`

## Regras de importação

- A importação é transacional.
- Linhas são identificadas por `codigo`.
- Veículos são identificados por `placa`.
- Vínculos são resolvidos por `linha_codigo` + `veiculo_placa`.
- Alunos usam `aluno_cpf` quando disponível; caso contrário, a combinação `aluno_nome` + `escola_nome`.

## Observação

Este contrato foi montado para atender a troca de dados do módulo no repositório atual. Se houver um layout oficial do SETE fornecido pelo município, o mapeamento pode ser ajustado para aderir ao formato exato.
