# 5.25 MÓDULO DO SIMPLES NACIONAL

Fonte: `/home/mohr/Downloads/cidade/sps.pdf` (seção 5 do Termo de Referência).

1.     Importar arquivos de períodos dos contribuintes do simples nacional;
2.     Importar arquivos contendo os eventos dos contribuintes do simples nacional;
3.     Visualizar períodos e eventos dos contribuintes enquadrados no simples nacional;
4.     Importar arquivos do DAS (Documento de Arrecadação do Simples Nacional);
5.     Importar arquivos da DASN (Declaração Anual do Simples Nacional);
6. Importar arquivos de períodos dos contribuintes enquadrados como Microempreendedor
Individual;
7. Importar arquivos contendo             os    eventos    dos    contribuintes   enquadrados   como
Microempreendedor Individual;
8.     Importar arquivos DASSENDA;
9.     Importar arquivos do parcelamento do simples nacional;
10. Visualizar períodos e eventos dos contribuintes enquadrados como Microempreendedor
Individual;
11. Importar arquivos do DAS-SIMEI (Documento de Arrecadação do Microempreendedor
Individual);
12. Importar arquivos da DASN-SIMEI (Declaração Anual do Microempreendedor Individual);
13. Consultar registros de importação do DAS (Documento de Arrecadação do Simples Nacional)
por dia, podendo ser adicionado comentário, como também listar os dias de pendências de
importação;
14. Consultar registros de importação do DASN (Documento de Arrecadação do Simples Nacional)
por dia, podendo ser adicionado comentário, como também listar os dias pendências de importação;
15. Importar contribuintes do Simples Nacional que estejam em débitos com a Receita Federal para
posterior inscrição em Dívida Ativa no sistema de tributos do Município;

16. Gerenciar quais contribuintes enquadrados no simples nacional com débitos, que serão inscritos
em Dívida Ativa;
17. Consultar as inconsistências de pagamento dos arquivos importados do DASN com as baixas de
pagamento do Município;
18. Emitir relatório de confronto de informações entre as declarações DAS e as declarações de
escrituração fiscal, listando as inconsistências; Permitindo filtrar por tipo de inconsistência e valor;
19. Emitir relatório de todas as informações importadas do DAS (Documento de Arrecadação do
Simples Nacional);
20. Emitir relatório de todas as informações importadas no DASN (Declaração Anual do Simples
Nacional);
21. Emitir relatório de empresas do Município que declararam receita para outros municípios;
22. Emitir relatório de empresas de outros Municípios que declararam receita para o Município;
23. Emitir relatório de contribuintes enquadrados no simples nacional sem pagamento e que não
foram inscritos em Dívida Ativa;
24. Emitir relatório de empresas do simples nacional que declaram receita isenta no DAS;
25. Emitir relatório de empresas do simples nacional que declaram sem recolhimento no DAS;





## Mapeamento de funcionalidades ja existentes no legado

Objetivo: evitar retrabalho, priorizando reaproveitamento das rotinas em `resources/legacy`.

Diretorios legados relacionados a este modulo:
- `resources/legacy/tributario` (arquivos no nivel do modulo: 33)
- `resources/legacy/fiscal` (arquivos no nivel do modulo: 347)
- `resources/legacy/issqn` (arquivos no nivel do modulo: 398)

Checklist de reaproveitamento antes de implementar novo codigo:
- [ ] Inventariar rotinas existentes nesses diretorios (telas, relatorios, RPCs e integracoes).
- [ ] Validar cobertura contra o TR/SPS do modulo e marcar gaps reais.
- [ ] Reutilizar regra de negocio legada quando aderente (evitar reescrita desnecessaria).
- [ ] Modernizar com abordagem incremental (estrangulamento), mantendo compatibilidade funcional.
