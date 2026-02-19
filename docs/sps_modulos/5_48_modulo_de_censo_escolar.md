# 5.48 MÓDULO DE CENSO ESCOLAR

Fonte: `/home/mohr/Downloads/cidade/sps.pdf` (seção 5 do Termo de Referência).

1. Disponibilizar os registros padrões das tabelas auxiliares utilizadas na exportação do censo
escolar, como línguas indígenas, etapas escolares e instrumentos pedagógicos.
2. Permitir configurar as regras de validação do censo escolar para o sistema pré analisar pendências
nos cadastros que devem ser exportados ao Educacenso.
3. Gerenciar os Layouts de Importação e Exportação do Educacenso para cada ano letivo.
4. Permitir as seguintes validações conforme as regras do censo escolar: validar e gerar dados do
Estabelecimento de Ensino, dados cadastrais das turmas, dados cadastrais de alunos, dados
cadastrais de matrículas, dados cadastrais dos profissionais escolares, dados da situação da
matrícula do aluno, podendo alterá-los se necessário, antes de gerar o arquivo e enviar para o
Educacenso.
5. Permitir gerar os arquivos de migração de todas as etapas do Educacenso conforme layouts
definidos pelo Instituto Nacional de Estudos e Pesquisas Educacionais Anísio Teixeira (INEP).
6. Emitir relatório da frequência mensal dos alunos para o programa Bolsa Família, contendo o nome
do aluno, dados da matrícula e a frequência atual do aluno.





## Mapeamento de funcionalidades ja existentes no legado

Objetivo: evitar retrabalho, priorizando reaproveitamento das rotinas em `resources/legacy`.

Diretorios legados relacionados a este modulo:
- `resources/legacy/educacao` (arquivos no nivel do modulo: 1082)
- `resources/legacy/secretariadeeducacao` (arquivos no nivel do modulo: 6)

Checklist de reaproveitamento antes de implementar novo codigo:
- [ ] Inventariar rotinas existentes nesses diretorios (telas, relatorios, RPCs e integracoes).
- [ ] Validar cobertura contra o TR/SPS do modulo e marcar gaps reais.
- [ ] Reutilizar regra de negocio legada quando aderente (evitar reescrita desnecessaria).
- [ ] Modernizar com abordagem incremental (estrangulamento), mantendo compatibilidade funcional.
