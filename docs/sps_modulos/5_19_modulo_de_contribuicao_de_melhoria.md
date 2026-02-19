# 5.19 MÓDULO DE CONTRIBUIÇÃO DE MELHORIA

Fonte: `/home/mohr/Downloads/cidade/sps.pdf` (seção 5 do Termo de Referência).

1.   Cadastrar melhoria relacionando os imóveis;
2.   Parametrizar todas as rotinas de cálculo conforme a obra;
3.     Permitir que se busque as informações do Cadastro imobiliário, para selecionar os imóveis;
4.     Parcelar e reparcelar débitos, com emissão dos respectivos termos;
5.     Permitir que seja efetuado o relacionamento de todos os imóveis situados no endereço da obra;
6.     Cadastrar os tipos de obras;
7.     Permitir cadastrar arquivos/imagens no cadastro da obra;
8. No cadastro da obra poder configurar dados parametrizavel podendo adicionar informações
adicionais da obra exemplo: Data de publicação, quantidade de imóveis, controle do andamento da
obra etc;
9.     Poder identificar e relacionar imóveis na obra de forma geral e individual.





## Mapeamento de funcionalidades ja existentes no legado

Objetivo: evitar retrabalho, priorizando reaproveitamento das rotinas em `resources/legacy`.

Diretorios legados relacionados a este modulo:
- `resources/legacy/arrecadacao` (arquivos no nivel do modulo: 109)
- `resources/legacy/tributario` (arquivos no nivel do modulo: 33)

Checklist de reaproveitamento antes de implementar novo codigo:
- [ ] Inventariar rotinas existentes nesses diretorios (telas, relatorios, RPCs e integracoes).
- [ ] Validar cobertura contra o TR/SPS do modulo e marcar gaps reais.
- [ ] Reutilizar regra de negocio legada quando aderente (evitar reescrita desnecessaria).
- [ ] Modernizar com abordagem incremental (estrangulamento), mantendo compatibilidade funcional.
