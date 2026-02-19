# 5.44 PORTAL DA TRANSPARÊNCIA

Fonte: `/home/mohr/Downloads/cidade/sps.pdf` (seção 5 do Termo de Referência).


1. Permitir a consulta da quantidade de alunos transportados por modalidade de ensino.
2. Permitir a consulta da quantidade de refeições servidas por estabelecimento de ensino e
modalidade de ensino na rede municipal de ensino.
3. Permitir a consulta da quantidade de matrículas por modalidade e estabelecimento de ensino.




## Mapeamento de funcionalidades ja existentes no legado

Objetivo: evitar retrabalho, priorizando reaproveitamento das rotinas em `resources/legacy`.

Diretorios legados relacionados a este modulo:
- `resources/legacy/site` (arquivos no nivel do modulo: 3)
- `resources/legacy/prefeitura` (arquivos no nivel do modulo: 57)
- `resources/legacy/controle_interno` (arquivos no nivel do modulo: 38)

Checklist de reaproveitamento antes de implementar novo codigo:
- [ ] Inventariar rotinas existentes nesses diretorios (telas, relatorios, RPCs e integracoes).
- [ ] Validar cobertura contra o TR/SPS do modulo e marcar gaps reais.
- [ ] Reutilizar regra de negocio legada quando aderente (evitar reescrita desnecessaria).
- [ ] Modernizar com abordagem incremental (estrangulamento), mantendo compatibilidade funcional.
