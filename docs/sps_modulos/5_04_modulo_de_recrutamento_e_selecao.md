# 5.4 MÓDULO DE RECRUTAMENTO E SELEÇÃO

Fonte: `/home/mohr/Downloads/cidade/sps.pdf` (seção 5 do Termo de Referência).

1. Permitir controlar bolsas de estudos concedidas aos funcionários indicando data início e final,
serviço comunitário prestado, e prazo de final permanência.
2. Permitir configurar motivos de afastamentos e rescisão que não podem ser lançados para
funcionários com bolsas de estudo conforme o prazo final de permanência, gerando alerta.
3. Possuir rotina de cadastro de currículos de candidatos a vagas, indicando no mínimo os cursos
que o candidato possui e referências pessoais para contato.
4. Possuir rotina para registro de avaliação dos currículos, indicando notas para cada etapa do
processo de avaliação.
5. Possuir rotina para controle de requisições de funcionários, permitindo indicar o tipo de
requisição, funcionário a ser reposto, cargo, função, local de trabalho, e justificativa para a requisição,
devendo permitir registrar as etapas da requisição.
6. Possuir rotina para cadastro de concursos públicos e processos seletivos, contendo os dados do
edital, as datas (data do edital, data de publicação, data de validade, data de prorrogação) e
permitindo incluir anexos.
7. Permitir relacionar aos concursos públicos e processos seletivos a quantidade total de vagas de
ampla concorrência para cada cargo e especialidade, permitindo indicar ainda o salário base, função,
grau de instrução exigido, local de trabalho, regime, e se for o caso as vagas para cadastro de
reserva, deficientes, afrodescendentes e indígenas.
8. Permitir relacionar aos concursos públicos e processos seletivos os candidatos inscritos,
indicando o cargo e especialidade para o qual o candidato se inscreveu, se foi aprovado ou não, sua
nota final, sua classificação geral, e se for o caso sua classificação na lista de deficientes,
afrodescendentes e indígenas, devendo permitir ainda o registro da situação da respectiva inscrição
(por exemplo: inscrito, desistente, nomeado, admitido, etc.).
9. Permitir registrar resultados dos candidatos inscritos nos concursos públicos e processos
seletivos por etapas (por exemplo: prova teórica, prova prática, prova de títulos, etc.)
10. Permitir cadastrar e controlar os fiscais e locais de prova nos concursos públicos e processos
seletivos.
11. Possuir rotina para importação dos dados dos concursos públicos e processos seletivos para o
sistema, conforme layout próprio da contratada, dispondo pelo menos de opções para importação
dos dados gerais do concurso, cargos e especialidades, candidatos, etapas e resultados das etapas,

devendo o processo de importação realizar o registro automático da pessoa física do candidato caso
o mesmo ainda não o possua na base de dados.
12. Possuir serviço no portal que permita a inscrições de candidatos em concursos públicos e
processos seletivos, permitindo indicar data início e final do período de inscrição, devendo permitir
ainda ao candidato inscrito que faça emissão de um comprovante de inscrição.
13. Permitir que no serviço no portal para inscrições de candidatos em concursos públicos e
processos seletivos seja configurada emissão de cobrança de taxa de inscrição (vinculado ao Módulo
de Arrecadação), permitindo configuração de valor geral ou valor específico por cargo e
especialidade.
14. Permitir verificar na consulta de candidatos de concursos públicos e processos seletivos a
situação da respectiva taxa de inscrição, bem como seu valor e número de lançamento.





## Mapeamento de funcionalidades ja existentes no legado

Objetivo: evitar retrabalho, priorizando reaproveitamento das rotinas em `resources/legacy`.

Diretorios legados relacionados a este modulo:
- `resources/legacy/recursoshumanos` (arquivos no nivel do modulo: 224)
- `resources/legacy/pessoal` (arquivos no nivel do modulo: 1160)

Checklist de reaproveitamento antes de implementar novo codigo:
- [ ] Inventariar rotinas existentes nesses diretorios (telas, relatorios, RPCs e integracoes).
- [ ] Validar cobertura contra o TR/SPS do modulo e marcar gaps reais.
- [ ] Reutilizar regra de negocio legada quando aderente (evitar reescrita desnecessaria).
- [ ] Modernizar com abordagem incremental (estrangulamento), mantendo compatibilidade funcional.
