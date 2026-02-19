# 5.33 MÓDULO DE SERVIÇOS PÚBLICOS

Fonte: `/home/mohr/Downloads/cidade/sps.pdf` (seção 5 do Termo de Referência).

1.     Permitir cadastrar origem de ocorrência.
2.     Permitir cadastrar tipo de ocorrência.
3. Possuir gerenciador de ocorrência de serviços e manutenções, com possibilidade de filtrar por
tipo de ocorrência, origem de ocorrência e situação de ocorrência.
4. Possuir cadastro de ocorrência, com possibilidade de informar o tipo de ocorrência, o solicitante,
o endereço da ocorrência e a descrição.
5. Ter o cadastro de ocorrência integrado com o Google Maps, considerando o endereço
cadastrado.
6.     Permitir visualizar, alterar ou excluir uma ocorrência na situação aberta.

7. Permitir programar uma ocorrência cadastrada, informando a data de execução, o responsável
pela execução e o tipo (vistoria, execução, fiscalização).
8. Permitir consultar as programações de um determinado serviço de manutenção possibilitando
verificar o histórico dessas programações em ordem cronológica.
9. Permitir vincular mais de uma ocorrência na mesma programação, permitindo consultar para
cada programação as ocorrências a ela vinculadas.
10. Permitir informar para cada programação a equipe responsável pela execução da atividade.
11. Permitir a impressão da programação com os dados da ocorrência bem como da equipe
responsável pela execução do serviço.
12. Permitir reprogramar uma programação, informando a data, o responsável, o motivo da
reprogramação e o tipo.
13. Permitir incluir para cada ocorrência de serviço a quantidade unitária orçada de material, e o
valor unitário, sendo que o sistema deve calcular automaticamente com base em valor informado
pelo usuário de material o valor previsto de material e mão de obra.
14. Permitir informar o valor executado de quantidade e valor unitário de material e o sistema deve
calcular automaticamente o valor executado de material e mão de obra.
15. Permitir cancelar uma ocorrência cadastrada mantendo o registro disponível para visualização e
consulta.
16. Permitir anexar imagens e documento à ocorrência incluída.
17. Permitir imprimir a ocorrência.
18. Permitir o registro de ocorrências através do autoatendimento da entidade.
19. Emitir Relatório de Serviços Executados.




## Mapeamento de funcionalidades ja existentes no legado

Objetivo: evitar retrabalho, priorizando reaproveitamento das rotinas em `resources/legacy`.

Diretorios legados relacionados a este modulo:
- `resources/legacy/atendimento` (arquivos no nivel do modulo: 194)
- `resources/legacy/prefeitura` (arquivos no nivel do modulo: 57)
- `resources/legacy/protocolo` (arquivos no nivel do modulo: 395)

Checklist de reaproveitamento antes de implementar novo codigo:
- [ ] Inventariar rotinas existentes nesses diretorios (telas, relatorios, RPCs e integracoes).
- [ ] Validar cobertura contra o TR/SPS do modulo e marcar gaps reais.
- [ ] Reutilizar regra de negocio legada quando aderente (evitar reescrita desnecessaria).
- [ ] Modernizar com abordagem incremental (estrangulamento), mantendo compatibilidade funcional.
